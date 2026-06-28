<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BangLuong;
use App\Models\ChamCong;
use App\Models\Combo;
use App\Models\DatPhong;
use App\Models\DichVu;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\Phong;
use App\Models\SuDungDichVu;
use App\Models\ThanhToan;
use App\Models\Voucher;
use App\Mail\XacNhanDatPhong;
use App\Services\RoomAvailabilityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DatPhongController extends Controller
{
    // Kiểm tra thông tin cá nhân khách hàng
    public function checkCustomer($type, $id)
    {
        session(['booking_type' => $type, 'booking_id' => $id]);
        $khachHang = KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();

        if (!$khachHang) {
            return redirect()->route('booking.customer');
        }
        return redirect()->route('booking.services');
    }

    public function showCustomerForm()
    {
        return view('user.infkhachhang');
    }

    public function saveCustomer(Request $request)
    {
        $request->validate([
            'ho_ten' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|string|max:10',
            'so_dien_thoai' => ['required', 'regex:/^[0-9]{1,12}$/'],
            'email' => 'required|email|max:100',
            'cccd' => ['required', 'regex:/^[0-9]+$/', 'max:20'],
            'dia_chi' => 'nullable|string|max:200',
        ], [
            'ho_ten.regex' => 'Tên không được chứa số hoặc ký tự đặc biệt.',
            'so_dien_thoai.regex' => 'Số điện thoại chỉ gồm chữ số và không quá 12 ký tự.',
            'email.email' => 'Email phải đúng định dạng.',
            'cccd.regex' => 'Số định danh cá nhân chỉ được phép chứa chữ số.',
        ]);

        KhachHang::create([
            'tai_khoan_khachhang_id' => Auth::id(),
            'ho_ten'                 => $request->ho_ten,
            'ngay_sinh'              => $request->ngay_sinh,
            'gioi_tinh'              => $request->gioi_tinh,
            'so_dien_thoai'          => $request->so_dien_thoai,
            'email'                  => $request->email,
            'cccd'                   => $request->cccd,
            'dia_chi'                => $request->dia_chi,
        ]);

        return redirect()->route('booking.services');
    }

    // Trang chọn ngày và dịch vụ
    public function showServiceForm()
    {
        $type = session('booking_type');
        $id = session('booking_id');

        $item = ($type == 'phong') ? Phong::find($id) : Combo::find($id);
        $dichVus = DichVu::all();

        $luuTruKeywords = ['Ăn', 'Spa', 'Giặt', 'Phòng', 'Buffet'];
        $dvLuuTru = $dichVus->filter(function ($dv) use ($luuTruKeywords) {
            foreach ($luuTruKeywords as $key) {
                if (str_contains($dv->ten_dich_vu, $key) || str_contains($dv->loai_dich_vu, $key)) {
                    return true;
                }
            }
            return false;
        });
        $vouchers = Voucher::where('trang_thai', 1)
            ->whereNull('id_khachhang')
            ->where('ngay_het_han', '>=', now()->toDateString())
            ->get();
        $dvNgoaiLe = $dichVus->diff($dvLuuTru);

        $defaultCheckin = Carbon::today()->toDateString();
        // Nếu là combo, mặc định ngày đi phải khớp với số đêm của combo
        if ($type == 'combo' && isset($item->so_dem_luu_tru)) {
            $defaultCheckout = Carbon::today()->addDays($item->so_dem_luu_tru)->toDateString();
        } else {
            $defaultCheckout = Carbon::today()->addDay()->toDateString();
        }

        // Lấy danh sách ngày đã bị đặt cho phòng này (nếu là đặt lẻ)
        $disabledDates = ($type == 'phong') ? RoomAvailabilityService::getDisabledDates($id) : [];

        return view('user.dichvubooking', compact('vouchers','item', 'type', 'dvLuuTru', 'dvNgoaiLe', 'defaultCheckin', 'defaultCheckout', 'disabledDates'));
    }

    // Xử lý dữ liệu chọn ngày và dịch vụ bổ trợ
    public function saveServices(Request $request)
    {
        $request->validate([
            'ngay_nhan' => 'required|date|after_or_equal:today',
            'ngay_tra'  => 'required|date|after:ngay_nhan',
            'id_voucher' => 'nullable|exists:voucher,id_voucher',
        ]);

        $type = session('booking_type');
        $booking_id = session('booking_id');
        $ngay_nhan = $request->ngay_nhan;
        $ngay_tra = $request->ngay_tra;

        $ngay_nhan_carbon = \Carbon\Carbon::parse($ngay_nhan);
        $ngay_tra_carbon = \Carbon\Carbon::parse($ngay_tra);
        $so_dem = $ngay_nhan_carbon->diffInDays($ngay_tra_carbon);

        // ✅ KIỂM TRA XUNG ĐỘT LỊ SUẤT ĐẶT PHÒNG
        if ($type === 'phong') {
            // Kiểm tra phòng lẻ
            if ($so_dem <= 0) {
                return redirect()->back()->withErrors(['date' => 'Ngày trả phòng phải sau ngày nhận phòng ít nhất 1 đêm!']);
            }
            if (!RoomAvailabilityService::isRoomAvailable($booking_id, $ngay_nhan, $ngay_tra)) {
                return redirect()->back()
                    ->withErrors([
                        'availability' => 'Phòng này đã được đặt trong khoảng thời gian bạn chọn.
                                        Vui lòng chọn ngày khác hoặc chọn phòng khác!'
                    ]);
            }
        } else {
            // Kiểm tra combo - tìm phòng khả dụng
            $combo = Combo::find($booking_id);
            $available_room = RoomAvailabilityService::findAvailableRoomByType(
                $combo->loai_phong_ap_dung,
                $ngay_nhan,
                $ngay_tra
            );

            if (!$available_room) {
                return redirect()->back()
                    ->withErrors([
                        'availability' => 'Hiện không có phòng ' . $combo->loai_phong_ap_dung .
                                        ' nào trống trong khoảng thời gian bạn chọn. Vui lòng chọn ngày khác!'
                    ]);
            }
        }

        $dichVuIds = $request->input('dich_vu', []);
        $soLuongDichVu = $request->input('so_luong', []);

        $selectedDichVus = \App\Models\DichVu::whereIn('id_dichvu', $dichVuIds)->get();

        $tong_tien_dich_vu = 0;
        $dich_vu_session_data = [];

        foreach ($selectedDichVus as $dv) {
            $qty = isset($soLuongDichVu[$dv->id_dichvu]) ? intval($soLuongDichVu[$dv->id_dichvu]) : 1;
            if ($qty <= 0) $qty = 1;

            $gia_dv_goc = (int)$dv->gia;
            $thanh_tien_dv = $gia_dv_goc * $qty;
            $tong_tien_dich_vu += $thanh_tien_dv;

            $dich_vu_session_data[$dv->id_dichvu] = [
                'id_dichvu'  => $dv->id_dichvu,
                'ten_dich_vu' => $dv->ten_dich_vu,
                'so_luong'   => $qty,
                'gia'        => $gia_dv_goc,
                'thanh_tien' => $thanh_tien_dv
            ];
        }

        // Lưu thêm ID Voucher vào session để hàm showConfirmation tính toán lại
        session([
            'ngay_nhan'          => $ngay_nhan,
            'ngay_tra'           => $ngay_tra,
            'so_dem'             => $so_dem,
            'booking_dich_vus'   => $dich_vu_session_data,
            'tong_tien_dich_vu'  => (int)$tong_tien_dich_vu,
            'applied_voucher_id' => $request->input('id_voucher'),
        ]);

        return redirect()->route('booking.confirm');
    }

    // Trang xác nhận thông tin tổng quan
    public function showConfirmation()
    {
        $khachHang = KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();
        $type = session('booking_type');
        $item = ($type == 'phong') ? Phong::find(session('booking_id')) : Combo::find(session('booking_id'));

        $bookingServices = session('booking_dich_vus', []);
        $serviceTotal = (int)session('tong_tien_dich_vu', 0);
        $ngay_nhan = session('ngay_nhan');
        $ngay_tra = session('ngay_tra');
        $so_dem = session('so_dem', 0);

        if ($type == 'phong') {
            $roomPrice = (int)$item->gia_hien_tai;
            $roomTotal = $roomPrice * $so_dem;
        } else {
            $roomPrice = (int)$item->gia_combo;
            $roomTotal = $roomPrice;
        }

        $discountAmount = 0;
        $voucher = null;
        $appliedVoucherId = session('applied_voucher_id');

        if ($appliedVoucherId) {
            $voucher = Voucher::where('id_voucher', $appliedVoucherId)
                ->where('trang_thai', 1)
                ->where('ngay_het_han', '>=', now()->toDateString())
                ->first();
        }

        if ($voucher) {
            $totalBeforeDiscount = $roomTotal + $serviceTotal;
            if ($voucher->loai_voucher === 'PHONG') {
                $discountAmount = $voucher->is_percent ? round($roomTotal * ($voucher->muc_giam / 100)) : (int)$voucher->muc_giam;
                if ($discountAmount > $roomTotal) {
                    $discountAmount = $roomTotal;
                }
            } elseif ($voucher->loai_voucher === 'DICH_VU') {
                $discountAmount = $voucher->is_percent ? round($serviceTotal * ($voucher->muc_giam / 100)) : (int)$voucher->muc_giam;
                if ($discountAmount > $serviceTotal) {
                    $discountAmount = $serviceTotal;
                }
            } else {
                $discountAmount = $voucher->is_percent ? round(($roomTotal + $serviceTotal) * ($voucher->muc_giam / 100)) : (int)$voucher->muc_giam;
                if ($discountAmount > $totalBeforeDiscount) {
                    $discountAmount = $totalBeforeDiscount;
                }
            }
        }

        $totalAmount = max(0, $roomTotal + $serviceTotal - $discountAmount);
        $depositAmount = (int)round($totalAmount * 0.3);

        session(['tong_thanh_toan' => (int)$totalAmount]);

        return view('user.xacnhanbooking', compact(
            'khachHang', 'item', 'type', 'bookingServices', 'serviceTotal',
            'ngay_nhan', 'ngay_tra', 'so_dem', 'roomTotal', 'totalAmount', 'depositAmount', 'discountAmount', 'voucher'
        ));
    }

    // Trang hiển thị lựa chọn phương thức thanh toán
    public function showPayment()
    {
        $tong_thanh_toan = (int)session('tong_thanh_toan', 0);
        $tien_coc = (int)round($tong_thanh_toan * 0.30);

        if ($tien_coc < 5000 && $tong_thanh_toan > 0) {
            $tien_coc = 10000;
        }

        return view('user.thanhtoanbooking', compact('tong_thanh_toan', 'tien_coc'));
    }

    // KHỞI CHẠY TÍCH HỢP GỌI API VNPAY
    public function vnpayPayment(Request $request)
    {
        $tong_thanh_toan = (int)session('tong_thanh_toan', 0);

        if ($tong_thanh_toan <= 0) {
            dd('LỖI GỬI ĐI: Mất Session, hãy thử đặt lại phòng.');
        }

        $tien_coc = (int)round($tong_thanh_toan * 0.30);
        if ($tien_coc < 5000) {
            $tien_coc = 10000;
        }

        $vnp_Amount     = (int)($tien_coc * 100);
        $vnp_TmnCode    = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_Url        = env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        // Cho hệ thống tự bắt link thay vì fix cứng trong .env
        $vnp_Returnurl  = route('booking.vnpay_return');

        $vnp_TxnRef    = 'DH_' . time();
        $vnp_OrderInfo = 'ThanhToanTienCocDatPhong';
        $vnp_OrderType = 'other';
        $vnp_Locale    = 'vn';

        $vnp_IpAddr = $request->ip();
        if ($vnp_IpAddr === '::1' || empty($vnp_IpAddr)) {
            $vnp_IpAddr = '127.0.0.1';
        }

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => $vnp_Amount,
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $vnp_IpAddr,
            "vnp_Locale"     => $vnp_Locale,
            "vnp_OrderInfo"  => $vnp_OrderInfo,
            "vnp_OrderType"  => $vnp_OrderType,
            "vnp_ReturnUrl"  => $vnp_Returnurl,
            "vnp_TxnRef"     => $vnp_TxnRef,
        ];

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

        $vnp_Url = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        return redirect($vnp_Url);
    }

    // TIẾP NHẬN PHẢN HỒI (ĐÃ ĐỔI redirect THÀNH dd ĐỂ BẮT ĐÚNG BỆNH NẾU CÓ LỖI)
    public function vnpayReturn(Request $request)
    {
        $vnp_SecureHash = $request->input('vnp_SecureHash');

        if (!$vnp_SecureHash) {
            dd('LỖI TỪ VNPAY: Không nhận được mã băm (Hash) hoặc khách hàng đã bấm hủy thanh toán.');
        }

        $vnp_HashSecret = env('VNP_HASH_SECRET');
        if (!$vnp_HashSecret) {
            dd('LỖI CẤU HÌNH: Thiếu VNP_HASH_SECRET trong file .env');
        }

        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

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

        if ($secureHash === $vnp_SecureHash) {

            if ($request->input('vnp_ResponseCode') == '00') {

                // Kiểm tra Session
                $session = session();
                if (!$session || !$session->has('tong_thanh_toan')) {
                    dd('LỖI MẤT SESSION: Trình duyệt đã reset bộ nhớ khi từ VNPay về. Hãy chắc chắn mày không chạy lộn xộn giữa localhost và 127.0.0.1');
                }

                $tong_thanh_toan = (int)session('tong_thanh_toan', 0);
                $tien_coc_he_thong = (int)round($tong_thanh_toan * 0.30);

                if ($tien_coc_he_thong < 5000) {
                    $tien_coc_he_thong = 10000;
                }

                $tien_vnpay_thuc_nhan = (int)($request->input('vnp_Amount') / 100);

                if ($tien_vnpay_thuc_nhan != $tien_coc_he_thong) {
                    dd("LỖI SỐ TIỀN: Khách trả {$tien_vnpay_thuc_nhan} VNĐ, nhưng hệ thống tính là {$tien_coc_he_thong} VNĐ.");
                }

                $khachHang = \App\Models\KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();
                if (!$khachHang) {
                    dd('LỖI AUTHENTICATION: Hệ thống quên mất mày là ai (Không tìm thấy khách hàng). Auth session bị xóa sau khi redirect.');
                }

                $type = session('booking_type');
                $booking_id = session('booking_id');
                $ngay_nhan = session('ngay_nhan');
                $ngay_tra = session('ngay_tra');

                DB::beginTransaction();
                try {
                    $id_phong_final = null;
                    $id_combo_final = null;

                    // ✅ CHECK XUNG ĐỘT LẦN CUỐI trước khi lưu (race condition prevention)
                    if ($type === 'phong') {
                        $id_phong_final = $booking_id;
                        // Lock phòng để kiểm tra
                        Phong::where('id_phong', $id_phong_final)->lockForUpdate()->first();

                        // Kiểm tra lần cuối xung đột
                        if (!RoomAvailabilityService::isRoomAvailable($id_phong_final, $ngay_nhan, $ngay_tra)) {
                            throw new \Exception(
                                'Phòng này đã bị đặt bởi khách khác lúc bạn thanh toán. Vui lòng thử lại!'
                            );
                        }
                    } else {
                        $id_combo_final = $booking_id;
                        $combo = Combo::find($id_combo_final);

                        // Tìm tất cả phòng cùng loại để kiểm tra tính khả dụng
                        $available_phong_id = RoomAvailabilityService::findAvailableRoomByType(
                            $combo->loai_phong_ap_dung,
                            $ngay_nhan,
                            $ngay_tra
                        );

                        if (!$available_phong_id) {
                            throw new \Exception(
                                'Hiện không có phòng ' . $combo->loai_phong_ap_dung .
                                ' nào trống. Vui lòng thử lại hoặc chọn ngày khác!'
                            );
                        }

                        $id_phong_final = $available_phong_id;
                        // Lock phòng đã chọn
                        Phong::where('id_phong', $id_phong_final)->lockForUpdate()->first();
                    }

                    //  Lưu Đặt Phòng (datphong)
                    $datPhongData = [
                        'id_khachhang'  => $khachHang->id_khachhang,
                        'ngay_dat'      => Carbon::now('Asia/Ho_Chi_Minh'),
                        'ngay_nhan'     => $ngay_nhan,
                        'ngay_tra'      => $ngay_tra,
                        'loai_hinh_dat' => ($type == 'phong') ? 'LẺ' : 'COMBO',
                        'tong_tien_phai_tra' => $tong_thanh_toan,
                        'trang_thai'    => 'Đã xác nhận',
                        'id_phong'      => $id_phong_final,
                        'id_combo'      => $id_combo_final
                    ];

$datPhongModel = DatPhong::create($datPhongData);
                        $id_datphong = $datPhongModel->id_datphong;

                    //  Lưu Sử dụng Dịch vụ (sudungdichvu)
                    $sessionDichVus = session('booking_dich_vus', []);
                    if (!empty($sessionDichVus)) {
                        foreach ($sessionDichVus as $dv) {
                            SuDungDichVu::create([
                                'id_datphong' => $id_datphong,
                                'id_dichvu'   => $dv['id_dichvu'],
                                'so_luong'    => $dv['so_luong'],
                                'thanh_tien'  => $dv['thanh_tien'],
                            ]);
                        }
                    }

                    //  Lưu Hóa đơn (hoadon) chỉ ghi số tiền thực giao dịch hiện tại
                    $hoaDon = HoaDon::create([
                        'id_datphong' => $id_datphong,
                        'tong_tien'   => $tien_coc_he_thong,
                        'ngay_xuat'   => Carbon::now('Asia/Ho_Chi_Minh'),
                    ]);

                    //  Lưu Lịch sử Thanh toán vào bảng thanhtoan
                    ThanhToan::create([
                        'id_datphong'        => $id_datphong,
                        'ngay_thanh_toan'    => Carbon::now('Asia/Ho_Chi_Minh'),
                        'so_tien'            => $tien_coc_he_thong,
                        'hinh_thuc'          => 'VNPAY',
                        'loai_thanh_toan'    => 'Đặt cọc 30%',
                        'vnp_transaction_no' => $request->input('vnp_TransactionNo'),
                        'vnp_response_code'  => $request->input('vnp_ResponseCode'),
                        'ghi_chu'            => 'Thanh toán VNPay (cổng thanh toán) - cọc 30% thành công',
                    ]);

                    // ✅ KHÔNG CẬP NHẬT TRẠNG THÁI PHÒNG - phòng vẫn 'Trống' trong DB
                    // Tính khả dụng dựa vào bảng datphong, không dùng trạng thái phòng
                    // Điều này cho phép đặt phòng ngày khác mà không bị khoá

                    DB::commit();

                    $item = ($type == 'phong') ? \App\Models\Phong::find($id_phong_final) : \App\Models\Combo::find($id_combo_final);
                    $selectedDichVus = !empty($sessionDichVus) ? \App\Models\DichVu::whereIn('id_dichvu', array_keys($sessionDichVus))->get() : collect([]);

                    $donDat = DatPhong::with(['khachhang', 'phong', 'combo'])
                        ->find($id_datphong);

                    if ($donDat && $donDat->phong) {
                        $donDat->so_phong = $donDat->phong->so_phong;
                        $donDat->loai_phong = $donDat->phong->loai_phong;
                    }

                    if ($donDat && $donDat->combo) {
                        $donDat->ten_combo = $donDat->combo->ten_combo;
                    }

                    $serviceTotal = !empty($sessionDichVus) ? array_sum(array_column($sessionDichVus, 'thanh_tien')) : 0;
                    $tienCoc = $tien_coc_he_thong;
                    $soTienConLai = max(0, $tong_thanh_toan - $tienCoc);

                    if ($type === 'phong') {
                        $itemAmount = $item ? ((int)$item->gia_hien_tai * Carbon::parse($ngay_nhan)->diffInDays(Carbon::parse($ngay_tra))) : 0;
                    } else {
                        $itemAmount = $item ? (int)$item->gia_combo : 0;
                    }

                    if ($donDat) {
                        $donDat->tong_tien_phong_combo = $itemAmount;
                        $donDat->tong_tien_dich_vu = $serviceTotal;
                        $donDat->tong_tien_tam_tinh = $tong_thanh_toan;
                        $donDat->tien_coc = $tienCoc;
                        $donDat->so_tien_con_lai = $soTienConLai;
                    }

                    try {
                        Mail::to($khachHang->email)->send(new XacNhanDatPhong($donDat));
                    } catch (\Exception $mailEx) {
                        Log::error("KHÔNG GỬI ĐƯỢC MAIL XÁC NHẬN (ID Đơn: $id_datphong): " . $mailEx->getMessage());
                    }

                    $sess = session();
                    if ($sess) {
                        $sess->forget(['booking_type', 'booking_id', 'ngay_nhan', 'ngay_tra', 'so_dem', 'booking_dich_vus', 'tong_tien_dich_vu', 'tong_thanh_toan', 'applied_voucher_id']);
                    }

                    return view('user.phieuxacnhan', compact('khachHang', 'item', 'type', 'selectedDichVus', 'tien_coc_he_thong'));

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('LỖI LƯU DATABASE: ' . $e->getMessage() . ' - Tại dòng: ' . $e->getLine());

                    // Trả về trang thanh toán với thông báo lỗi
                    return redirect()->route('booking.payment')
                        ->withErrors([
                            'booking_error' => 'Lỗi khi xử lý đơn đặt phòng: ' . $e->getMessage() .
                                             '. Vui lòng liên hệ với hỗ trợ khách hàng!'
                        ]);
                }
            }
            dd('LỖI GIAO DỊCH VNPAY: Mã lỗi ' . $request->input('vnp_ResponseCode'));
        }
        dd('LỖI BẢO MẬT: Chữ ký Hash sai lệch.');
    }

    // XEM LỊCH SỬ PHÒNG / COMBO ĐÃ ĐẶT
    public function lichSuDatPhong()
    {
        $khachHang = \App\Models\KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();

        if (!$khachHang) {
            $danhSachDat = [];
            return view('user.lichsu_datphong', compact('danhSachDat'));
        }

        $danhSachDat = DatPhong::with(['phong', 'combo'])
            ->where('id_khachhang', $khachHang->id_khachhang)
            ->orderByDesc('id_datphong')
            ->get()
            ->map(function ($booking) {
                if ($booking->phong) {
                    $booking->so_phong = $booking->phong->so_phong;
                    $booking->loai_phong = $booking->phong->loai_phong;
                }
                if ($booking->combo) {
                    $booking->ten_combo = $booking->combo->ten_combo;
                }
                return $booking;
            });

        return view('user.lichsu_datphong', compact('danhSachDat'));
    }

    // XEM CHI TIẾT MỘT ĐƠN ĐẶT PHÒNG
    public function chiTietDatPhong($id)
    {
        $khachHang = \App\Models\KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();
        if (!$khachHang) {
            return redirect()->route('home')->with('error', 'Không tìm thấy hồ sơ khách hàng.');
        }

        $donDat = DatPhong::with(['phong', 'combo'])
            ->where('id_datphong', $id)
            ->where('id_khachhang', $khachHang->id_khachhang)
            ->first();

        if (!$donDat) {
            return redirect()->route('booking.history')->with('error', 'Đơn đặt phòng không tồn tại hoặc bạn không có quyền xem.');
        }

        if ($donDat->phong) {
            $donDat->so_phong = $donDat->phong->so_phong;
            $donDat->loai_phong = $donDat->phong->loai_phong;
            $donDat->gia_phong = $donDat->phong->gia_phong;
        }

        if ($donDat->combo) {
            $donDat->ten_combo = $donDat->combo->ten_combo;
            $donDat->gia_combo = $donDat->combo->gia_combo;
        }

        $dichVuDaDung = SuDungDichVu::with('dichvu')
            ->where('id_datphong', $id)
            ->get();

        $giaoDich = ThanhToan::where('id_datphong', $id)->first();

        $ngayNhan = \Carbon\Carbon::parse($donDat->ngay_nhan);
        $ngayTra = \Carbon\Carbon::parse($donDat->ngay_tra);
        $soDem = $ngayNhan->diffInDays($ngayTra);

        return view('user.chitiet_datphong', compact('donDat', 'dichVuDaDung', 'giaoDich', 'soDem'));
    }

    // ✅ API: Kiểm tra tính khả dụng phòng và lấy lịch đã book
    public function getAvailability(Request $request)
    {
        $id_phong = $request->input('id_phong');
        $ngay_nhan = $request->input('ngay_nhan');
        $ngay_tra = $request->input('ngay_tra');

        if (!$id_phong) {
            return response()->json(['error' => 'ID phòng không hợp lệ'], 400);
        }

        // Lấy lịch đã book
        $bookedDates = RoomAvailabilityService::getBookedDatesJSON($id_phong);

        // Kiểm tra xung đột nếu user đã chọn ngày
        $isAvailable = true;
        if ($ngay_nhan && $ngay_tra) {
            $isAvailable = RoomAvailabilityService::isRoomAvailable(
                $id_phong,
                $ngay_nhan,
                $ngay_tra
            );
        }

        return response()->json([
            'available' => $isAvailable,
            'booked_dates' => json_decode($bookedDates, true),
            'disabled_dates' => RoomAvailabilityService::getDisabledDates($id_phong)
        ]);
    }

    // ✅ API: Lấy lịch đã book cho 1 loại phòng (dùng cho combo)
    public function getAvailabilityByType(Request $request)
    {
        $loai_phong = $request->input('loai_phong');
        $ngay_nhan = $request->input('ngay_nhan');
        $ngay_tra = $request->input('ngay_tra');

        if (!$loai_phong) {
            return response()->json(['error' => 'Loại phòng không hợp lệ'], 400);
        }

        // Lấy tất cả các phòng có loại này
        $phongs = Phong::where('loai_phong', $loai_phong)
            ->where('trang_thai', '!=', 'Bảo trì')
            ->get();

        $bookingData = [];
        $hasAvailable = false;

        foreach ($phongs as $phong) {
            $isAvailable = !$ngay_nhan || !$ngay_tra ||
                          RoomAvailabilityService::isRoomAvailable(
                              $phong->id_phong,
                              $ngay_nhan,
                              $ngay_tra
                          );

            if ($isAvailable && !$hasAvailable) {
                $hasAvailable = true;
            }

            $bookingData[] = [
                'id_phong' => $phong->id_phong,
                'so_phong' => $phong->so_phong,
                'available' => $isAvailable,
                'disabled_dates' => RoomAvailabilityService::getDisabledDates($phong->id_phong)
            ];
        }

        return response()->json([
            'loai_phong' => $loai_phong,
            'has_available' => $hasAvailable,
            'rooms' => $bookingData
        ]);
    }
}
