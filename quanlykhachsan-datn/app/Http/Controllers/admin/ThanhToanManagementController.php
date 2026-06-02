<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ThanhToanManagementController extends Controller
{
    // 0. Hiển thị danh sách các phòng chờ thanh toán (Khi bấm từ Sidebar)
    public function index(Request $request)
    {
        // Lấy danh sách các đơn đặt phòng đang ở trạng thái cần thanh toán
        $danhSachChoThanhToan = DB::table('datphong')
            ->join('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->join('khachhang', 'datphong.id_khachhang', '=', 'khachhang.id_khachhang')
            ->select('datphong.*', 'datphong.tong_tien_phai_tra as tong_tien', 'phong.so_phong as ten_phong', 'khachhang.ho_ten', 'khachhang.so_dien_thoai')
            // SỬA CHÍNH XÁC CHỖ NÀY: Lọc theo trạng thái 'Đã xác nhận' cho khớp với ENUM trong DB
            ->where('datphong.trang_thai', 'Đã xác nhận')
            ->orderBy('datphong.id_datphong', 'desc')
            ->paginate(15);

        // Trả về view danh sách chờ thanh toán
        return view('admin.quanlythanhtoan', compact('danhSachChoThanhToan'));
    }
// 1. Hiển thị màn hình tính tiền Checkout (Giữ nguyên)
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
        $tongTienPhong = $datPhong->tong_tien ?? 0;
        $tienPhongConLai = $tongTienPhong - $tienCoc;

        $dichVus = DB::table('sudungdichvu')
            ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->where('sudungdichvu.id_datphong', $id)
            ->select('sudungdichvu.*', 'dichvu.ten_dich_vu', 'dichvu.gia as don_gia')
            ->get();

        $tongTienDichVu = $dichVus->sum(function($dv) { return $dv->so_luong * $dv->don_gia; });

        return view('admin.checkout_detail', compact('datPhong', 'tienCoc', 'tongTienPhong', 'tienPhongConLai', 'dichVus', 'tongTienDichVu'));
    }

    // 2. Xử lý rẽ nhánh thanh toán (VNPay hoặc Tiền mặt)
    public function processCheckout(Request $request, $id)
    {
        $request->validate([
            'tien_boi_thuong' => 'nullable|numeric|min:0',
            'ghi_chu_boi_thuong' => 'nullable|string',
            'hinh_thuc' => 'required|string',
        ]);

        $tienBoiThuong = $request->tien_boi_thuong ?? 0;
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
        $tongThanhToanCuoi = $tienPhongConLai + $tongTienDichVu + $tienBoiThuong;

        // Lưu tạm ghi chú bồi thường vào Session để xử lý sau khi VNPay trả về
        $ghiChu = "Thanh toán trả phòng. ";
        if ($tienBoiThuong > 0) {
            $ghiChu .= "Thu thêm bồi thường: " . number_format($tienBoiThuong) . "đ (Lý do: $ghiChuBoiThuong).";
        }

        // ---------- NẾU CHỌN VNPAY ----------
        if ($request->hinh_thuc == 'VNPay') {
            // Lưu Session tạm
            session(["checkout_info_{$id}" => [
                'so_tien' => $tongThanhToanCuoi,
                'ghi_chu' => $ghiChu
            ]]);

            // Cấu hình VNPay
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = route('admin.thanhtoan.vnpay_return', $id);
            $vnp_TmnCode = env('VNP_TMN_CODE');
            $vnp_HashSecret = env('VNP_HASH_SECRET');

            $vnp_TxnRef = time() . '_' . $id; // Mã tham chiếu (Unique)
            $vnp_OrderInfo = "Thanh toan tra phong ma " . $id;
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $tongThanhToanCuoi * 100; // VNPay nhân 100
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
            // Bắn sang VNPay
            return redirect($vnp_Url);
        }

        // ---------- NẾU CHỌN TIỀN MẶT / CHUYỂN KHOẢN (LƯU LUÔN) ----------
        DB::beginTransaction();
        try {
            DB::table('thanhtoan')->insert([
                'id_datphong' => $id,
                'ngay_thanh_toan' => Carbon::now(),
                'so_tien' => $tongThanhToanCuoi,
                'hinh_thuc' => $request->hinh_thuc,
                'ghi_chu' => $ghiChu,
                'loai_thanh_toan' => 'Thanh toán phần còn lại',
                'vnp_transaction_no' => null,
                'vnp_response_code' => null,
            ]);

            DB::table('datphong')->where('id_datphong', $id)->update(['trang_thai' => 'Hoàn thành']);
            DB::table('phong')->where('id_phong', $datPhong->id_phong)->update(['trang_thai' => 'Trống']);

            DB::commit();
            return redirect()->route('admin.thanhtoan.invoice', $id)->with('success', 'Đã thu tiền mặt/chuyển khoản và trả phòng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi thanh toán: ' . $e->getMessage());
        }
    }

    // 3. Hàm xử lý Callback khi VNPay trả kết quả về
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
        Log::info("VNPay Return Debug", [
            'id_datphong' => $id,
            'vnp_ResponseCode' => $request->vnp_ResponseCode,
            'secureHash_match' => ($secureHash == $vnp_SecureHash),
            'request_all' => $request->all()
        ]);

        // Kiểm tra tính toàn vẹn dữ liệu
        if ($secureHash == $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                // Khách thanh toán thành công
                $checkoutData = session("checkout_info_{$id}");
                if (!$checkoutData) {
                    return redirect()->route('admin.thanhtoan.checkout', $id)->with('error', 'Phiên thanh toán đã hết hạn, vui lòng thao tác lại!');
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

                    DB::table('datphong')->where('id_datphong', $id)->update(['trang_thai' => 'Đã thanh toán']);
                    DB::table('phong')->where('id_phong', $datPhong->id_phong)->update(['trang_thai' => 'Trống']);

                    DB::commit();
                    session()->forget("checkout_info_{$id}"); // Xóa session dọn dẹp

                    return redirect()->route('admin.thanhtoan.invoice', $id)->with('success', 'Khách đã thanh toán qua VNPay thành công!');
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("VNPay Save Error: " . $e->getMessage(), ['id_datphong' => $id]);
                    return redirect()->route('admin.thanhtoan.checkout', $id)->with('error', 'Lỗi hệ thống khi lưu kết quả VNPay: ' . $e->getMessage());
                }
            } else {
                // Khách hủy thanh toán hoặc thanh toán lỗi
                Log::warning("VNPay Payment Failed", ['id_datphong' => $id, 'response_code' => $request->vnp_ResponseCode]);
                session()->forget("checkout_info_{$id}");
                return redirect()->route('admin.thanhtoan.checkout', $id)->with('error', 'Giao dịch VNPay thất bại hoặc bị hủy (Mã lỗi: ' . $request->vnp_ResponseCode . ')');
            }
        } else {
            Log::error("VNPay Hash Mismatch", ['id_datphong' => $id, 'expected' => $secureHash, 'received' => $vnp_SecureHash]);
            return redirect()->route('admin.thanhtoan.checkout', $id)->with('error', 'Chữ ký VNPay không hợp lệ. Phát hiện gian lận!');
        }
    }

    // 4. Hiển thị Giao diện Hóa Đơn (Invoice) (Giữ nguyên)
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
    // 5. Hiển thị danh sách Lịch sử Hóa Đơn đã xuất
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

        // Lấy hóa đơn mới nhất xếp lên đầu
        $danhSachHoaDon = $query->orderBy('hoadon.id_hoadon', 'desc')->paginate(20);

        return view('admin.quanlyhoadon', compact('danhSachHoaDon', 'search'));
    }
}
