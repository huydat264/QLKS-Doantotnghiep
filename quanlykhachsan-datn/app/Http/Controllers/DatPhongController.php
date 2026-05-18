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

            $thanh_tien_dv = $dv->gia * $qty;
            $tong_tien_dich_vu += $thanh_tien_dv;

            $dich_vu_session_data[$dv->id_dichvu] = [
                'id_dichvu'  => $dv->id_dichvu,
                'ten_dich_vu' => $dv->ten_dich_vu,
                'so_luong'   => $qty,
                'gia'        => $dv->gia,
                'thanh_tien' => $thanh_tien_dv
            ];
        }

        session([
            'ngay_nhan'          => $request->ngay_nhan,
            'ngay_tra'           => $request->ngay_tra,
            'so_dem'             => $so_dem,
            'booking_dich_vus'   => $dich_vu_session_data,
            'tong_tien_dich_vu'  => $tong_tien_dich_vu
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
        $serviceTotal = session('tong_tien_dich_vu', 0);
        $ngay_nhan = session('ngay_nhan');
        $ngay_tra = session('ngay_tra');
        $so_dem = session('so_dem', 0);

        if ($type == 'phong') {
            $roomTotal = $item->gia_phong * $so_dem;
        } else {
            $roomTotal = $item->gia_combo;
        }

        $totalAmount = $roomTotal + $serviceTotal;
        $depositAmount = round($totalAmount * 0.3);
        session(['tong_thanh_toan' => $totalAmount]);

        return view('user.xacnhanbooking', compact(
            'khachHang', 'item', 'type', 'bookingServices', 'serviceTotal',
            'ngay_nhan', 'ngay_tra', 'so_dem', 'roomTotal', 'totalAmount', 'depositAmount'
        ));
    }

    // Trang hiển thị lựa chọn phương thức thanh toán
    public function showPayment()
    {
        $tong_thanh_toan = session('tong_thanh_toan');
        $tien_coc = $tong_thanh_toan * 0.30;

        return view('user.thanhtoanbooking', compact('tong_thanh_toan', 'tien_coc'));
    }

// =========================================================================
    // KHỞI CHẠY TÍCH HỢP GIẢ LẬP CỔNG THANH TOÁN VNPAY (CÓ HIỆN QR)
    // =========================================================================
    public function vnpayPayment(Request $request)
    {
        $tong_thanh_toan = session('tong_thanh_toan');
        $tien_coc = $tong_thanh_toan * 0.30;

        // 1. Tự sinh dữ liệu giả lập như VNPay thật trả về
        $fake_TxnRef = 'DH_' . time();
        $fake_TransactionNo = 'MOCK_VNP_' . rand(100000, 999999);
        $fake_ResponseCode = '00';

        // 2. Tạo URL điều hướng chứa dữ liệu giả lập
        $returnUrl = env('VNP_RETURNURL', route('booking.vnpay_return'));
        $queryString = http_build_query([
            'vnp_ResponseCode'  => $fake_ResponseCode,
            'vnp_TransactionNo' => $fake_TransactionNo,
            'vnp_TxnRef'        => $fake_TxnRef,
            'vnp_Amount'        => $tien_coc * 100,
        ]);

        $finalReturnUrl = $returnUrl . '?' . $queryString;

        // Thay vì redirect thẳng, trả về một trang hiển thị mã QR
        return view('user.vnpay_mock', compact('tien_coc', 'fake_TxnRef', 'finalReturnUrl'));
    }

    // =========================================================================
    // TIẾP NHẬN PHẢN HỒI (GIẢ LẬP) VÀ LƯU DATABASE
    // =========================================================================
    public function vnpayReturn(Request $request)
    {
        if ($request->vnp_ResponseCode == '00') {

            $khachHang = \App\Models\KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();
            $type = session('booking_type');
            $booking_id = session('booking_id');
            $tong_thanh_toan = session('tong_thanh_toan');
            $tien_coc = $tong_thanh_toan * 0.30;

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

                // Bước 4: Lưu Lịch sử Thanh toán (ĐÃ SỬA LỖI XÓA CỘT id_hoadon)
                DB::table('thanhtoan')->insert([
                    'id_datphong'        => $id_datphong,
                    'ngay_thanh_toan'    => Carbon::now(),
                    'so_tien'            => $tien_coc,
                    'hinh_thuc'          => 'Chuyển khoản',
                    'loai_thanh_toan'    => 'Đặt cọc 30%',
                    'vnp_transaction_no' => $request->vnp_TransactionNo,
                    'vnp_response_code'  => $request->vnp_ResponseCode,
                    'ghi_chu'            => 'Giả lập thanh toán VNPay cọc 30% thành công.'
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

                return view('user.phieuxacnhan', compact('khachHang', 'item', 'type', 'selectedDichVus', 'tien_coc'));

            } catch (\Exception $e) {
                DB::rollBack();
                // Nếu vẫn lỗi, nó sẽ in thẳng tên lỗi ra để mày biết đường sửa thay vì chỉ nói chung chung
                return redirect()->route('booking.services')->with('error', 'Lỗi DB: ' . $e->getMessage());
            }
        }

        return redirect()->route('booking.services')->with('error', 'Bạn đã hủy giao dịch!');
    }
    // =========================================================================
    // XEM LỊCH SỬ PHÒNG / COMBO ĐÃ ĐẶT (MVC - CONTROLLER)
    // =========================================================================
    public function lichSuDatPhong()
    {
        // 1. Lấy thông tin khách hàng dựa theo tài khoản đang đăng nhập
        $khachHang = \App\Models\KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();

        // Nếu tài khoản mới chưa từng đặt phòng, chưa có data khách hàng thì cho mảng rỗng
        if (!$khachHang) {
            $danhSachDat = [];
            return view('user.lichsu_datphong', compact('danhSachDat'));
        }

        // 2. Query dữ liệu đặt phòng, kết nối sang bảng Phong và Combo để lấy tên (Đóng vai trò Model xử lý dữ liệu)
        $danhSachDat = DB::table('datphong')
            ->leftJoin('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->leftJoin('combo', 'datphong.id_combo', '=', 'combo.id_combo')
            ->where('datphong.id_khachhang', $khachHang->id_khachhang)
            ->orderBy('datphong.id_datphong', 'desc') // Đơn mới nhất xếp lên đầu
            ->select('datphong.*', 'phong.so_phong', 'phong.loai_phong', 'combo.ten_combo')
            ->get();

        // 3. Trả kết quả sang giao diện hiển thị (View)
        return view('user.lichsu_datphong', compact('danhSachDat'));
    }
    // =========================================================================
    // XEM CHI TIẾT MỘT ĐƠN ĐẶT PHÒNG (MVC - CONTROLLER)
    // =========================================================================
    public function chiTietDatPhong($id)
    {
        // 1. Kiểm tra tài khoản khách hàng để bảo mật (tránh người khác dò id xem trộm)
        $khachHang = \App\Models\KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();
        if (!$khachHang) {
            return redirect()->route('home')->with('error', 'Không tìm thấy hồ sơ khách hàng.');
        }

        // 2. Lấy thông tin tổng quan của đơn đặt phòng này
        $donDat = DB::table('datphong')
            ->leftJoin('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->leftJoin('combo', 'datphong.id_combo', '=', 'combo.id_combo')
            ->where('datphong.id_datphong', $id)
            ->where('datphong.id_khachhang', $khachHang->id_khachhang) // Bảo mật chính chủ
            ->select('datphong.*', 'phong.so_phong', 'phong.loai_phong', 'phong.gia_phong', 'combo.ten_combo', 'combo.gia_combo')
            ->first();

        if (!$donDat) {
            return redirect()->route('booking.history')->with('error', 'Đơn đặt phòng không tồn tại hoặc bạn không có quyền xem.');
        }

        // 3. Lấy danh sách các dịch vụ đi kèm được sử dụng trong đơn này
        $dichVuDaDung = DB::table('sudungdichvu')
            ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->where('sudungdichvu.id_datphong', $id)
            ->select('sudungdichvu.*', 'dichvu.ten_dich_vu')
            ->get();

        // 4. Lấy thông tin lịch sử giao dịch thanh toán cọc tiền
        $giaoDich = DB::table('thanhtoan')
            ->where('id_datphong', $id)
            ->first();

        // Tính số đêm lưu trú thực tế
        $ngayNhan = \Carbon\Carbon::parse($donDat->ngay_nhan);
        $ngayTra = \Carbon\Carbon::parse($donDat->ngay_tra);
        $soDem = $ngayNhan->diffInDays($ngayTra);

        // 5. Trả toàn bộ dữ liệu sang View chi tiết
        return view('user.chitiet_datphong', compact('donDat', 'dichVuDaDung', 'giaoDich', 'soDem'));
    }
}
