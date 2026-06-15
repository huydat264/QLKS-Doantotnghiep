<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\Phong;
use App\Models\SuDungDichVu;
use App\Models\ThanhToan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ThanhToanManagementController extends Controller
{
    // 0. Hiển thị danh sách các phòng chờ thanh toán
    public function index(Request $request)
    {
        $danhSachChoThanhToan = DatPhong::with(['phong', 'khachhang'])
            ->where('trang_thai', 'Đã xác nhận')
            ->orderByDesc('id_datphong')
            ->paginate(15);

        $danhSachChoThanhToan->getCollection()->transform(function ($booking) {
            $booking->tong_tien = $booking->tong_tien_phai_tra;
            $booking->ten_phong = $booking->phong?->so_phong;
            $booking->ho_ten = $booking->khachhang?->ho_ten;
            $booking->so_dien_thoai = $booking->khachhang?->so_dien_thoai;
            return $booking;
        });

        return view('admin.quanlythanhtoan', compact('danhSachChoThanhToan'));
    }

    // 1. Hiển thị màn hình tính tiền Checkout
    public function checkout($id)
    {
        $datPhong = DatPhong::with(['phong', 'khachhang'])->find($id);

        if (!$datPhong) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin đặt phòng!');
        }

        $tienCoc = ThanhToan::where('id_datphong', $id)
            ->where('loai_thanh_toan', 'like', '%cọc%')
            ->sum('so_tien');

        $tienTamUngCo = ThanhToan::where('id_datphong', $id)
            ->where('loai_thanh_toan', 'Tạm ứng đã chọn')
            ->sum('so_tien');

        $tongTienPhong = $datPhong->tong_tien_phai_tra ?? 0;
        $tienPhongConLai = $tongTienPhong - $tienCoc;

        $dichVus = SuDungDichVu::with('dichvu')
            ->where('id_datphong', $id)
            ->get();

        $tongTienDichVu = $dichVus->sum(function ($dv) {
            return $dv->so_luong * ($dv->dichvu->gia ?? 0);
        });

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
            ThanhToan::where('id_datphong', $id)
                ->where('loai_thanh_toan', 'Tạm ứng đã chọn')
                ->delete();

            if ($tienNew > 0) {
                ThanhToan::create([
                    'id_datphong' => $id,
                    'ngay_thanh_toan' => Carbon::now('Asia/Ho_Chi_Minh'),
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

        $datPhong = DatPhong::find($id);
        if (!$datPhong) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin đặt phòng!');
        }

        $tienCoc = ThanhToan::where('id_datphong', $id)
            ->where('loai_thanh_toan', 'like', '%cọc%')
            ->sum('so_tien');

        $tongTienDichVu = SuDungDichVu::with('dichvu')
            ->where('id_datphong', $id)
            ->get()
            ->sum(function ($dv) {
                return $dv->so_luong * ($dv->dichvu->gia ?? 0);
            });

        $tienPhongConLai = ($datPhong->tong_tien_phai_tra ?? 0) - $tienCoc;

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
                'invoice_amount' => $tongThanhToanCuoi,
                'total_bill' => $tongHoaDonInvoice
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
            ThanhToan::create([
                'id_datphong' => $id,
                'ngay_thanh_toan' => Carbon::now('Asia/Ho_Chi_Minh'),
                'so_tien' => $tongThanhToanCuoi,
                'hinh_thuc' => $request->hinh_thuc,
                'ghi_chu' => $ghiChu,
                'loai_thanh_toan' => 'Thanh toán phần còn lại',
            ]);

            HoaDon::create([
                'id_datphong' => $id,
                'tong_tien' => $tongThanhToanCuoi,
                'ngay_xuat' => Carbon::now('Asia/Ho_Chi_Minh'),
            ]);

            $datPhong->update(['trang_thai' => 'Đã thanh toán']);
            if ($datPhong->phong) {
                $datPhong->phong->update(['trang_thai' => 'Trống']);
            }

            DB::commit();

            session()->forget([
                'booking_type', 'booking_id', 'ngay_nhan', 'ngay_tra', 'so_dem',
                'booking_dich_vus', 'tong_tien_dich_vu', 'tong_thanh_toan', 'applied_voucher_id'
            ]);

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
                    $datPhong = DatPhong::find($id);
                    if (!$datPhong) {
                        throw new \Exception('Không tìm thấy thông tin đặt phòng');
                    }

                    ThanhToan::create([
                        'id_datphong' => $id,
                        'ngay_thanh_toan' => Carbon::now('Asia/Ho_Chi_Minh'),
                        'so_tien' => $checkoutData['so_tien'],
                        'hinh_thuc' => 'VNPAY',
                        'ghi_chu' => $checkoutData['ghi_chu'],
                        'loai_thanh_toan' => 'Thanh toán phần còn lại',
                        'vnp_transaction_no' => $request->vnp_TransactionNo,
                        'vnp_response_code' => $request->vnp_ResponseCode,
                    ]);

                    HoaDon::create([
                        'id_datphong' => $id,
                        'tong_tien' => $checkoutData['invoice_amount'],
                        'ngay_xuat' => Carbon::now('Asia/Ho_Chi_Minh'),
                    ]);

                    $datPhong->update(['trang_thai' => 'Đã thanh toán']);
                    if ($datPhong->phong) {
                        $datPhong->phong->update(['trang_thai' => 'Trống']);
                    }

                    DB::commit();
                    session()->forget("checkout_info_{$id}");

                    // Clear booking session khi thanh toán thành công
                    session()->forget([
                        'booking_type', 'booking_id', 'ngay_nhan', 'ngay_tra', 'so_dem',
                        'booking_dich_vus', 'tong_tien_dich_vu', 'tong_thanh_toan', 'applied_voucher_id'
                    ]);

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

    // 4. Hiển thị Giao diện Hóa Đơn
    public function showInvoice($id)
    {
        $datPhong = DatPhong::with(['phong', 'khachhang'])->find($id);

        if (!$datPhong) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin đặt phòng!');
        }

        $dichVus = SuDungDichVu::with('dichvu')
            ->where('id_datphong', $id)
            ->get();

        $thanhToans = ThanhToan::where('id_datphong', $id)->get();

        return view('admin.invoice_template', compact('datPhong', 'dichVus', 'thanhToans'));
    }

    // 5. Hiển thị danh sách Lịch sử Hóa Đơn
    public function danhSachHoaDon(Request $request)
    {
        $search = $request->input('search');

        $query = HoaDon::with(['datphong.phong', 'datphong.khachhang']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id_hoadon', 'LIKE', "%{$search}%")
                  ->orWhereHas('datphong', function ($q2) use ($search) {
                      $q2->where('id_datphong', 'LIKE', "%{$search}%")
                         ->orWhereHas('khachhang', function ($q3) use ($search) {
                             $q3->where('ho_ten', 'LIKE', "%{$search}%")
                                ->orWhere('so_dien_thoai', 'LIKE', "%{$search}%");
                         })
                         ->orWhereHas('phong', function ($q3) use ($search) {
                             $q3->where('so_phong', 'LIKE', "%{$search}%");
                         });
                  });
            });
        }

        $danhSachHoaDon = $query->orderByDesc('id_hoadon')->paginate(20);

        $danhSachHoaDon->getCollection()->transform(function ($invoice) {
            $payment = ThanhToan::where('id_datphong', $invoice->id_datphong)
                ->where('so_tien', $invoice->tong_tien)
                ->whereDate('ngay_thanh_toan', Carbon::parse($invoice->ngay_xuat)->toDateString())
                ->orderByRaw("ABS(TIMESTAMPDIFF(SECOND, ngay_thanh_toan, '{$invoice->ngay_xuat}'))")
                ->first();

            $invoice->payment_type = $payment?->loai_thanh_toan;
            $invoice->paid_amount = $payment?->so_tien;
            return $invoice;
        });

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
        $danhSachQuaHan = DatPhong::with('phong')
            ->where('trang_thai', 'Đã xác nhận')
            ->where(function ($q) use ($now) {
                $q->whereDate('ngay_tra', '<', $now->toDateString())
                  ->orWhere(function ($q2) use ($now) {
                      $q2->whereDate('ngay_tra', '=', $now->toDateString())
                         ->whereTime(DB::raw('CURRENT_TIME()'), '>=', '12:00:00');
                  });
            })
            ->get();

        $count = 0;

        foreach ($danhSachQuaHan as $booking) {
            DB::beginTransaction();
            try {
                $tienCoc = ThanhToan::where('id_datphong', $booking->id_datphong)
                    ->where('loai_thanh_toan', 'like', '%cọc%')
                    ->sum('so_tien');

                $tienTamUng = ThanhToan::where('id_datphong', $booking->id_datphong)
                    ->where('loai_thanh_toan', 'like', '%tạm ứng%')
                    ->sum('so_tien');

                $tongTienDichVu = SuDungDichVu::with('dichvu')
                    ->where('id_datphong', $booking->id_datphong)
                    ->get()
                    ->sum(function ($dv) {
                        return $dv->so_luong * ($dv->dichvu->gia ?? 0);
                    });

                $tienPhongConLai = ($booking->tong_tien_phai_tra ?? 0) - $tienCoc;
                $tongThuCuoi = $tienPhongConLai + $tongTienDichVu - $tienTamUng;
                if ($tongThuCuoi < 0) {
                    $tongThuCuoi = 0;
                }

                ThanhToan::create([
                    'id_datphong' => $booking->id_datphong,
                    'ngay_thanh_toan' => Carbon::now('Asia/Ho_Chi_Minh'),
                    'so_tien' => $tongThuCuoi,
                    'hinh_thuc' => 'Tiền mặt',
                    'ghi_chu' => 'AUTO-CHECKOUT: Hệ thống tự động khấu trừ tiền tạm ứng do quá hạn 12h trưa ngày trả.',
                    'loai_thanh_toan' => 'Thanh toán phần còn lại',
                ]);

                HoaDon::create([
                    'id_datphong' => $booking->id_datphong,
                    'tong_tien' => $tongThuCuoi,
                    'ngay_xuat' => Carbon::now('Asia/Ho_Chi_Minh'),
                ]);

                $booking->update(['trang_thai' => 'Đã thanh toán']);
                if ($booking->phong) {
                    $booking->phong->update(['trang_thai' => 'Trống']);
                }

                DB::commit();
                $count++;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Lỗi Auto Checkout phòng ID {$booking->id_datphong}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "Đã quét và tự động checkout/khấu trừ tạm ứng thành công {$count} phòng quá hạn.");
    }
}
