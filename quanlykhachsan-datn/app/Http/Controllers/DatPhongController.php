<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhachHang;
use App\Models\Phong;
use App\Models\Combo;
use App\Models\DichVu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'ho_ten' => 'required|string|max:100',
            'so_dien_thoai' => 'required|string|max:15',
            'email' => 'required|email|max:100',
            'cccd' => 'required|string|max:20',
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
        $dvNgoaiLe = $dichVus->diff($dvLuuTru);

        $defaultCheckin = Carbon::today()->toDateString();
        $defaultCheckout = Carbon::today()->addDay()->toDateString();

        return view('user.dichvubooking', compact('item', 'type', 'dvLuuTru', 'dvNgoaiLe', 'defaultCheckin', 'defaultCheckout'));
    }

    // Xử lý dữ liệu chọn ngày và dịch vụ bổ trợ
    public function saveServices(Request $request)
    {
        $request->validate([
            'ngay_nhan' => 'required|date|after_or_equal:today',
            'ngay_tra'  => 'required|date|after:ngay_nhan',
        ]);

        $ngay_nhan = \Carbon\Carbon::parse($request->ngay_nhan);
        $ngay_tra = \Carbon\Carbon::parse($request->ngay_tra);
        $so_dem = $ngay_nhan->diffInDays($ngay_tra);

        if ($so_dem <= 0) {
            return redirect()->back()->withErrors(['date' => 'Ngày trả phòng phải sau ngày nhận phòng ít nhất 1 đêm!']);
        }

        $dichVuIds = $request->input('dich_vu', []);
        $soLuongDichVu = $request->input('so_luong', []);

        $selectedDichVus = \App\Models\DichVu::whereIn('id_dichvu', $dichVuIds)->get();

        $tong_tien_dich_vu = 0;
        $dich_vu_session_data = [];

        foreach ($selectedDichVus as $dv) {
            $qty = isset($soLuongDichVu[$dv->id_dichvu]) ? intval($soLuongDichVu[$dv->id_dichvu]) : 1;
            if ($qty <= 0) $qty = 1;

            // SỬA: Thay thế biến chứa ký tự tiếng Việt có dấu tránh lỗi bộ giải mã byte
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

        session([
            'ngay_nhan'          => $request->ngay_nhan,
            'ngay_tra'           => $request->ngay_tra,
            'so_dem'             => $so_dem,
            'booking_dich_vus'   => $dich_vu_session_data,
            'tong_tien_dich_vu'  => (int)$tong_tien_dich_vu
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
            $roomTotal = (int)$item->gia_phong * $so_dem;
        } else {
            $roomTotal = (int)$item->gia_combo;
        }

        $totalAmount = $roomTotal + $serviceTotal;
        $depositAmount = (int)round($totalAmount * 0.3);

        session(['tong_thanh_toan' => (int)$totalAmount]);

        return view('user.xacnhanbooking', compact(
            'khachHang', 'item', 'type', 'bookingServices', 'serviceTotal',
            'ngay_nhan', 'ngay_tra', 'so_dem', 'roomTotal', 'totalAmount', 'depositAmount'
        ));
    }

    // Trang hiển thị lựa chọn phương thức thanh toán
    public function showPayment()
    {
        $tong_thanh_toan = (int)session('tong_thanh_toan', 0);
        $tien_coc = (int)round($tong_thanh_toan * 0.30);

        // Bảo vệ: Nếu số tiền cọc tính toán quá bé để test, nâng tạm lên 10,000 VNĐ ở view hiển thị
        if ($tien_coc < 5000 && $tong_thanh_toan > 0) {
            $tien_coc = 10000;
        }

        return view('user.thanhtoanbooking', compact('tong_thanh_toan', 'tien_coc'));
    }

    // =========================================================================
    // KHỞI CHẠY TÍCH HỢP GỌI API VNPAY
    // =========================================================================
    public function vnpayPayment(Request $request)
{
    $tong_thanh_toan = (int)session('tong_thanh_toan', 0);

    if ($tong_thanh_toan <= 0) {
        return redirect()->route('booking.services')->with('error', 'Phiên làm việc đã hết hạn, vui lòng thao tác lại!');
    }

    $tien_coc = (int)round($tong_thanh_toan * 0.30);
    if ($tien_coc < 5000) {
        $tien_coc = 10000;
    }

    $vnp_Amount     = (int)($tien_coc * 100);
    $vnp_TmnCode    = env('VNP_TMN_CODE');
    $vnp_HashSecret = env('VNP_HASH_SECRET');
    $vnp_Url        = env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
    $vnp_Returnurl  = env('VNP_RETURNURL', route('booking.vnpay_return'));

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
    // =========================================================================
    // TIẾP NHẬN PHẢN HỒI KIỂM TRA CHỮ KÝ BẢO MẬT & ĐỐI CHIẾU SỐ TIỀN VNPAY TRẢ VỀ
    // =========================================================================
    public function vnpayReturn(Request $request)
    {
        $vnp_SecureHash = $request->input('vnp_SecureHash');

        if (!$vnp_SecureHash) {
            return redirect()->route('booking.services')->with('error', 'Giao dịch thanh toán đã bị hủy hoặc không nhận được phản hồi hợp lệ từ VNPay!');
        }

        $vnp_HashSecret = env('VNP_HASH_SECRET', '96IBAWESGPZIDKDXJZKJZVEMZCOHLXKA');

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

                $tong_thanh_toan = (int)session('tong_thanh_toan', 0);
                $tien_coc_he_thong = (int)round($tong_thanh_toan * 0.30);

                if ($tien_coc_he_thong < 5000) {
                    $tien_coc_he_thong = 10000; // Đồng bộ hạn mức giả lập khi check kết quả trả về
                }

                $tien_vnpay_thuc_nhan = (int)($request->input('vnp_Amount') / 100);

                if ($tien_vnpay_thuc_nhan != $tien_coc_he_thong) {
                    return redirect()->route('booking.services')->with('error', 'Lỗi bảo mật: Số tiền cọc thanh toán không trùng khớp hệ thống!');
                }

                $khachHang = \App\Models\KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();
                $type = session('booking_type');
                $booking_id = session('booking_id');

                DB::beginTransaction();
                try {
                    // Bước 1: Lưu Đặt Phòng (datphong)
                    $datPhongData = [
                        'id_khachhang'  => $khachHang->id_khachhang,
                        'ngay_dat'      => Carbon::now()->toDateString(),
                        'ngay_nhan'     => session('ngay_nhan'),
                        'ngay_tra'      => session('ngay_tra'),
                        'loai_hinh_dat' => ($type == 'phong') ? 'LẺ' : 'COMBO',
                        'tong_tien_phai_tra' => $tong_thanh_toan,
                        'trang_thai'    => 'Đã xác nhận'
                    ];

                    if ($type == 'phong') {
                        $datPhongData['id_phong'] = $booking_id;
                        $datPhongData['id_combo'] = null;
                    } else {
                        $datPhongData['id_phong'] = null;
                        $datPhongData['id_combo'] = $booking_id;
                    }

                    $id_datphong = DB::table('datphong')->insertGetId($datPhongData);

                    // Bước 2: Lưu Sử dụng Dịch vụ (sudungdichvu)
                    $sessionDichVus = session('booking_dich_vus', []);
                    if (!empty($sessionDichVus)) {
                        foreach ($sessionDichVus as $dv) {
                            DB::table('sudungdichvu')->insert([
                                'id_datphong' => $id_datphong,
                                'id_dichvu'   => $dv['id_dichvu'],
                                'so_luong'    => $dv['so_luong'],
                                'thanh_tien'  => $dv['thanh_tien']
                            ]);
                        }
                    }

                    // Bước 3: Lưu Hóa đơn (hoadon)
                    $id_hoadon = DB::table('hoadon')->insertGetId([
                        'id_datphong' => $id_datphong,
                        'tong_tien'   => $tong_thanh_toan,
                        'ngay_xuat'   => Carbon::now()->toDateString()
                    ]);

                    // Bước 4: Lưu Lịch sử Thanh toán vào bảng thanhtoan
                    DB::table('thanhtoan')->insert([
                        'id_datphong'        => $id_datphong,
                        'ngay_thanh_toan'    => Carbon::now(),
                        'so_tien'            => $tien_coc_he_thong,
                        'hinh_thuc'          => 'Chuyển khoản VNPay',
                        'loai_thanh_toan'    => 'Đặt cọc 30%',
                        'vnp_transaction_no' => $request->input('vnp_TransactionNo'),
                        'vnp_response_code'  => $request->input('vnp_ResponseCode'),
                        'ghi_chu'            => 'Thanh toán qua cổng API VNPay thành công.'
                    ]);

                    // Bước 5: Cập nhật sơ đồ phòng
                    if ($type == 'phong') {
                        DB::table('phong')
                            ->where('id_phong', $booking_id)
                            ->update(['trang_thai' => 'Đã đặt']);
                    }

                    DB::commit();

                    $item = ($type == 'phong') ? \App\Models\Phong::find($booking_id) : \App\Models\Combo::find($booking_id);
                    $selectedDichVus = \App\Models\DichVu::whereIn('id_dichvu', array_keys($sessionDichVus))->get();

                    session()->forget(['booking_type', 'booking_id', 'ngay_nhan', 'ngay_tra', 'so_dem', 'booking_dich_vus', 'tong_tien_dich_vu', 'tong_thanh_toan']);

                    return view('user.phieuxacnhan', compact('khachHang', 'item', 'type', 'selectedDichVus', 'tien_coc_he_thong'));

                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect()->route('booking.services')->with('error', 'Lỗi DB: ' . $e->getMessage());
                }
            }
            return redirect()->route('booking.services')->with('error', 'Giao dịch thanh toán thất bại hoặc đã bị hủy!');
        }
        return redirect()->route('booking.services')->with('error', 'Lỗi bảo mật: Dữ liệu phản hồi sai chữ ký mã hóa bí mật!');
    }

    // =========================================================================
    // XEM LỊCH SỬ PHÒNG / COMBO ĐÃ ĐẶT
    // =========================================================================
    public function lichSuDatPhong()
    {
        $khachHang = \App\Models\KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();

        if (!$khachHang) {
            $danhSachDat = [];
            return view('user.lichsu_datphong', compact('danhSachDat'));
        }

        $danhSachDat = DB::table('datphong')
            ->leftJoin('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->leftJoin('combo', 'datphong.id_combo', '=', 'combo.id_combo')
            ->where('datphong.id_khachhang', $khachHang->id_khachhang)
            ->orderBy('datphong.id_datphong', 'desc')
            ->select('datphong.*', 'phong.so_phong', 'phong.loai_phong', 'combo.ten_combo')
            ->get();

        return view('user.lichsu_datphong', compact('danhSachDat'));
    }

    // =========================================================================
    // XEM CHI TIẾT MỘT ĐƠN ĐẶT PHÒNG
    // =========================================================================
    public function chiTietDatPhong($id)
    {
        $khachHang = \App\Models\KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();
        if (!$khachHang) {
            return redirect()->route('home')->with('error', 'Không tìm thấy hồ sơ khách hàng.');
        }

        $donDat = DB::table('datphong')
            ->leftJoin('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->leftJoin('combo', 'datphong.id_combo', '=', 'combo.id_combo')
            ->where('datphong.id_datphong', $id)
            ->where('datphong.id_khachhang', $khachHang->id_khachhang)
            ->select('datphong.*', 'phong.so_phong', 'phong.loai_phong', 'phong.gia_phong', 'combo.ten_combo', 'combo.gia_combo')
            ->first();

        if (!$donDat) {
            return redirect()->route('booking.history')->with('error', 'Đơn đặt phòng không tồn tại hoặc bạn không có quyền xem.');
        }

        $dichVuDaDung = DB::table('sudungdichvu')
            ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->where('sudungdichvu.id_datphong', $id)
            ->select('sudungdichvu.*', 'dichvu.ten_dich_vu')
            ->get();

        $giaoDich = DB::table('thanhtoan')
            ->where('id_datphong', $id)
            ->first();

        $ngayNhan = \Carbon\Carbon::parse($donDat->ngay_nhan);
        $ngayTra = \Carbon\Carbon::parse($donDat->ngay_tra);
        $soDem = $ngayNhan->diffInDays($ngayTra);

        return view('user.chitiet_datphong', compact('donDat', 'dichVuDaDung', 'giaoDich', 'soDem'));
    }
}
