<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ThanhToanManagementController extends Controller
{
    // 0. Hiển thị danh sách các phòng chờ thanh toán
    public function index(Request $request)
    {
        $danhSachChoThanhToan = DB::table('datphong')
            ->join('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->join('khachhang', 'datphong.id_khachhang', '=', 'khachhang.id_khachhang')
            ->select('datphong.*', 'datphong.tong_tien_phai_tra as tong_tien', 'phong.so_phong as ten_phong', 'khachhang.ho_ten', 'khachhang.so_dien_thoai')
            ->where('datphong.trang_thai', 'Đã xác nhận')
            ->orderBy('datphong.id_datphong', 'desc')
            ->paginate(15);

        return view('admin.quanlythanhtoan', compact('danhSachChoThanhToan'));
    }

    // 1. Hiển thị màn hình tính tiền Checkout
    public function checkout($id)
    {
        $datPhong = DB::table('datphong')
            ->join('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->join('khachhang', 'datphong.id_khachhang', '=', 'khachhang.id_khachhang')
            ->select('datphong.*', 'datphong.tong_tien_phai_tra as tong_tien', 'phong.so_phong as ten_phong', 'khachhang.ho_ten', 'khachhang.so_dien_thoai')
            ->where('datphong.id_datphong', $id)
            ->first();

        if (!$datPhong) return redirect()->back()->with('error', 'Không tìm thấy thông tin đặt phòng!');

        $tienCoc = DB::table('thanhtoan')
            ->where('id_datphong', $id)
            ->where('loai_thanh_toan', 'like', '%cọc%')
            ->sum('so_tien');
        // Lấy giá trị tạm ứng đã chọn (từ bản ghi "Tạm ứng đã chọn")
        $tienTamUngCo = DB::table('thanhtoan')
            ->where('id_datphong', $id)
            ->where('loai_thanh_toan', 'Tạm ứng đã chọn')
            ->sum('so_tien');
        $tongTienPhong = $datPhong->tong_tien ?? 0;
        $tienPhongConLai = $tongTienPhong - $tienCoc;

        $dichVus = DB::table('sudungdichvu')
            ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->where('sudungdichvu.id_datphong', $id)
            ->select('sudungdichvu.*', 'dichvu.ten_dich_vu', 'dichvu.gia as don_gia')
            ->get();

        $tongTienDichVu = $dichVus->sum(function($dv) { return $dv->so_luong * $dv->don_gia; });

        return view('admin.checkout_detail', compact('datPhong', 'tienCoc', 'tienTamUngCo', 'tongTienPhong', 'tienPhongConLai', 'dichVus', 'tongTienDichVu'));
    }

    // 1.5 Lưu giá trị tạm ứng được khóa vào DB (gọi via AJAX)
    public function saveTamUng(Request $request, $id)
    {
        $request->validate([
            'tien_tam_ung_new' => 'required|numeric|min:0',
        ]);

        $tienNew = $request->tien_tam_ung_new;

        DB::beginTransaction();
        try {
            // Xóa bản ghi tạm ứng cũ nếu có (loại "Tạm ứng đã chọn")
            DB::table('thanhtoan')
                ->where('id_datphong', $id)
                ->where('loai_thanh_toan', 'Tạm ứng đã chọn')
                ->delete();

            // Lưu giá trị tạm ứng mới
            if ($tienNew > 0) {
                DB::table('thanhtoan')->insert([
                    'id_datphong' => $id,
                    'ngay_thanh_toan' => Carbon::now(),
                    'so_tien' => $tienNew,
                    'hinh_thuc' => 'Tiền mặt',
                    'ghi_chu' => 'Giá trị tạm ứng được khóa cho checkout',
                    'loai_thanh_toan' => 'Tạm ứng đã chọn',
                ]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Đã lưu giá trị tạm ứng']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // 2. Xử lý rẽ nhánh thanh toán (VNPay hoặc Tiền mặt)
    public function processCheckout(Request $request, $id)
    {
        $request->validate([
            'tien_boi_thuong' => 'nullable|numeric|min:0',
            'ghi_chu_boi_thuong' => 'nullable|string',
            'tien_tam_ung' => 'nullable|numeric|min:0', // Xác thực tiền tạm ứng
            'hinh_thuc' => 'required|string',
        ]);

        $tienBoiThuong = $request->tien_boi_thuong ?? 0;
        $tienTamUng = $request->tien_tam_ung ?? 0;
        $ghiChuBoiThuong = $request->ghi_chu_boi_thuong ?? '';

        // Tính toán lại server-side cho an toàn
        $datPhong = DB::table('datphong')
            ->select('datphong.*', 'datphong.tong_tien_phai_tra as tong_tien')
            ->where('id_datphong', $id)
            ->first();
        $tienCoc = DB::table('thanhtoan')
            ->where('id_datphong', $id)
            ->where('loai_thanh_toan', 'like', '%cọc%')
            ->sum('so_tien');
        $tongTienDichVu = DB::table('sudungdichvu')
            ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->where('sudungdichvu.id_datphong', $id)
            ->sum(DB::raw('sudungdichvu.so_luong * dichvu.gia'));

        $tienPhongConLai = ($datPhong->tong_tien ?? 0) - $tienCoc;

        // Trừ tiền tạm ứng ra khỏi tổng cần thu cuối
        $tongThanhToanCuoi = $tienPhongConLai + $tongTienDichVu + $tienBoiThuong - $tienTamUng;
        if ($tongThanhToanCuoi < 0) $tongThanhToanCuoi = 0;

        // Tổng hóa đơn để lưu bảng hoadon (Bao gồm tất cả: cọc + tạm ứng + phần thu cuối)
        $tongHoaDonInvoice = $tienCoc + $tienTamUng + $tongThanhToanCuoi;

        // Ghi chú linh hoạt
        $ghiChu = "Thanh toán trả phòng. ";
        if ($tienBoiThuong > 0) {
            $ghiChu .= "Phụ phí/Bồi thường: " . number_format($tienBoiThuong) . "đ (Lý do: $ghiChuBoiThuong). ";
        }
        if ($tienTamUng > 0) {
            $ghiChu .= "Đã khấu trừ tiền tạm ứng: " . number_format($tienTamUng) . "đ. ";
        }

        // ---------- NẾU CHỌN VNPAY ----------
        if ($request->hinh_thuc == 'VNPay') {
            session(["checkout_info_{$id}" => [
                'so_tien' => $tongThanhToanCuoi,
                'ghi_chu' => $ghiChu,
                'tong_hoadon' => $tongHoaDonInvoice // Truyền tổng invoice qua session
            ]]);

            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = route('admin.thanhtoan.vnpay_return', $id);
            $vnp_TmnCode = env('VNP_TMN_CODE');
            $vnp_HashSecret = env('VNP_HASH_SECRET');

            $vnp_TxnRef = time() . '_' . $id;
            $vnp_OrderInfo = "Thanh toan tra phong ma " . $id;
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $tongThanhToanCuoi * 100;
            $vnp_Locale = 'vn';
            $vnp_IpAddr = request()->ip();

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
            );

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }
            return redirect($vnp_Url);
        }

        // ---------- NẾU CHỌN TIỀN MẶT ----------
        DB::beginTransaction();
        try {
            // 1. Lưu giao dịch thanh toán
            DB::table('thanhtoan')->insert([
                'id_datphong' => $id,
                'ngay_thanh_toan' => Carbon::now(),
                'so_tien' => $tongThanhToanCuoi,
                'hinh_thuc' => $request->hinh_thuc,
                'ghi_chu' => $ghiChu,
                'loai_thanh_toan' => 'Thanh toán phần còn lại',
            ]);

            // 2. TẠO HÓA ĐƠN ĐỂ IN
            DB::table('hoadon')->insert([
                'id_datphong' => $id,
                'tong_tien' => $tongHoaDonInvoice,
                'ngay_xuat' => Carbon::now()
            ]);

            // 3. ĐỔI TRẠNG THÁI (Fix lỗi: Không dùng 'Hoàn thành' mà dùng 'Đã thanh toán' theo ENUM)
            DB::table('datphong')->where('id_datphong', $id)->update(['trang_thai' => 'Đã thanh toán']);
            DB::table('phong')->where('id_phong', $datPhong->id_phong)->update(['trang_thai' => 'Trống']);

            DB::commit();
            return redirect()->route('admin.thanhtoan.invoice', $id)->with('success', 'Đã thu tiền mặt và xuất hoá đơn thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    // 3. Hàm xử lý Callback VNPay
    public function vnpayReturn(Request $request, $id)
    {
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_SecureHash = $request->vnp_SecureHash;

        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                $checkoutData = session("checkout_info_{$id}");
                if (!$checkoutData) {
                    return redirect()->route('admin.thanhtoan.checkout', $id)->with('error', 'Phiên thanh toán đã hết hạn!');
                }

                DB::beginTransaction();
                try {
                    $datPhong = DB::table('datphong')->where('id_datphong', $id)->first();

                    DB::table('thanhtoan')->insert([
                        'id_datphong' => $id,
                        'ngay_thanh_toan' => Carbon::now(),
                        'so_tien' => $checkoutData['so_tien'],
                        'hinh_thuc' => 'VNPAY',
                        'ghi_chu' => $checkoutData['ghi_chu'],
                        'loai_thanh_toan' => 'Thanh toán phần còn lại',
                        'vnp_transaction_no' => $request->vnp_TransactionNo,
                        'vnp_response_code' => $request->vnp_ResponseCode,
                    ]);

                    // VNPay cũng phải xuất hóa đơn
                    DB::table('hoadon')->insert([
                        'id_datphong' => $id,
                        'tong_tien' => $checkoutData['tong_hoadon'],
                        'ngay_xuat' => Carbon::now()
                    ]);

                    DB::table('datphong')->where('id_datphong', $id)->update(['trang_thai' => 'Đã thanh toán']);
                    DB::table('phong')->where('id_phong', $datPhong->id_phong)->update(['trang_thai' => 'Trống']);

                    DB::commit();
                    session()->forget("checkout_info_{$id}");

                    return redirect()->route('admin.thanhtoan.invoice', $id)->with('success', 'Thanh toán VNPay và xuất hóa đơn thành công!');
                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect()->route('admin.thanhtoan.checkout', $id)->with('error', 'Lỗi lưu VNPay: ' . $e->getMessage());
                }
            } else {
                session()->forget("checkout_info_{$id}");
                return redirect()->route('admin.thanhtoan.checkout', $id)->with('error', 'Giao dịch VNPay thất bại hoặc bị hủy.');
            }
        } else {
            return redirect()->route('admin.thanhtoan.checkout', $id)->with('error', 'Chữ ký VNPay không hợp lệ!');
        }
    }

    // 4. Hiển thị Giao diện Hóa Đơn (Giữ nguyên)
    public function showInvoice($id)
    {
        $datPhong = DB::table('datphong')
            ->join('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->join('khachhang', 'datphong.id_khachhang', '=', 'khachhang.id_khachhang')
            ->select('datphong.*', 'phong.so_phong as ten_phong', 'khachhang.ho_ten', 'khachhang.so_dien_thoai', 'khachhang.dia_chi')
            ->where('datphong.id_datphong', $id)
            ->first();

        $dichVus = DB::table('sudungdichvu')
            ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->where('sudungdichvu.id_datphong', $id)
            ->select('sudungdichvu.*', 'dichvu.ten_dich_vu', 'dichvu.gia as don_gia')
            ->get();

        $thanhToans = DB::table('thanhtoan')->where('id_datphong', $id)->get();

        return view('admin.invoice_template', compact('datPhong', 'dichVus', 'thanhToans'));
    }

    // 5. Hiển thị danh sách Lịch sử Hóa Đơn (Giữ nguyên)
    public function danhSachHoaDon(Request $request)
    {
        $search = $request->input('search');

        $query = DB::table('hoadon')
            ->join('datphong', 'hoadon.id_datphong', '=', 'datphong.id_datphong')
            ->join('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->join('khachhang', 'datphong.id_khachhang', '=', 'khachhang.id_khachhang')
            ->select('hoadon.*', 'datphong.id_datphong', 'phong.so_phong as ten_phong', 'khachhang.ho_ten', 'khachhang.so_dien_thoai');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('khachhang.ho_ten', 'LIKE', "%{$search}%")
                  ->orWhere('khachhang.so_dien_thoai', 'LIKE', "%{$search}%")
                  ->orWhere('phong.so_phong', 'LIKE', "%{$search}%")
                  ->orWhere('hoadon.id_hoadon', 'LIKE', "%{$search}%");
            });
        }

        $danhSachHoaDon = $query->orderBy('hoadon.id_hoadon', 'desc')->paginate(20);

        return view('admin.quanlyhoadon', compact('danhSachHoaDon', 'search'));
    }

    // =========================================================================
    // HÀM MỚI BỔ SUNG: AUTO CHECKOUT PHÒNG QUÁ HẠN 12H TRƯA VÀ CẤN TRỪ TẠM ỨNG
    // Bạn có thể thiết lập Laravel Scheduler (Cronjob) gọi hàm này mỗi phút
    // hoặc gán nó vào 1 nút bấm thủ công trong màn hình Quản lý.
    // =========================================================================
    public function autoCheckoutQuaHan()
    {
        $now = Carbon::now();

        // Truy vấn các phòng Đã xác nhận (đang ở), mà Ngày trả < Hôm nay
        // HOẶC Ngày trả = Hôm nay nhưng Giờ hiện tại >= 12:00:00
        $danhSachQuaHan = DB::table('datphong')
            ->where('trang_thai', 'Đã xác nhận')
            ->where(function($q) use ($now) {
                $q->whereDate('ngay_tra', '<', $now->toDateString())
                  ->orWhere(function($q2) use ($now) {
                      $q2->whereDate('ngay_tra', '=', $now->toDateString())
                         ->whereTime(DB::raw('CURRENT_TIME()'), '>=', '12:00:00');
                  });
            })
            ->get();

        $count = 0;

        foreach ($danhSachQuaHan as $phong) {
            DB::beginTransaction();
            try {
                // Tính toán toàn bộ chi phí
                $tienCoc = DB::table('thanhtoan')->where('id_datphong', $phong->id_datphong)->where('loai_thanh_toan', 'like', '%cọc%')->sum('so_tien');
                $tienTamUng = DB::table('thanhtoan')->where('id_datphong', $phong->id_datphong)->where('loai_thanh_toan', 'like', '%tạm ứng%')->sum('so_tien');
                $tongTienDichVu = DB::table('sudungdichvu')
                    ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
                    ->where('sudungdichvu.id_datphong', $phong->id_datphong)
                    ->sum(DB::raw('sudungdichvu.so_luong * dichvu.gia'));

                $tienPhongConLai = ($phong->tong_tien_phai_tra ?? 0) - $tienCoc;
                $tongThuCuoi = $tienPhongConLai + $tongTienDichVu - $tienTamUng;
                if ($tongThuCuoi < 0) {
                    $tongThuCuoi = 0;
                }
                $tongHoaDon = $phong->tong_tien_phai_tra + $tongTienDichVu;

                // Tạo giao dịch cấn trừ "Tiền tạm ứng" hệ thống tự thu
                DB::table('thanhtoan')->insert([
                    'id_datphong' => $phong->id_datphong,
                    'ngay_thanh_toan' => Carbon::now(),
                    'so_tien' => $tongThuCuoi,
                    'hinh_thuc' => 'Tiền mặt', // Mặc định chuyển sang tiền mặt/tạm ứng
                    'ghi_chu' => 'AUTO-CHECKOUT: Hệ thống tự động khấu trừ tiền tạm ứng do quá hạn 12h trưa ngày trả.',
                    'loai_thanh_toan' => 'Thanh toán phần còn lại',
                ]);

                // Tạo hoá đơn
                DB::table('hoadon')->insert([
                    'id_datphong' => $phong->id_datphong,
                    'tong_tien' => $tongHoaDon,
                    'ngay_xuat' => Carbon::now()
                ]);

                // Cập nhật trạng thái phòng và đặt phòng
                DB::table('datphong')->where('id_datphong', $phong->id_datphong)->update(['trang_thai' => 'Đã thanh toán']);
                DB::table('phong')->where('id_phong', $phong->id_phong)->update(['trang_thai' => 'Trống']);

                DB::commit();
                $count++;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Lỗi Auto Checkout phòng ID {$phong->id_datphong}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "Đã quét và tự động checkout/khấu trừ tạm ứng thành công {$count} phòng quá hạn.");
    }
}
