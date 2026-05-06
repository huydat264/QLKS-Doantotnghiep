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
    // Khởi tạo luồng: kiểm tra thông tin cá nhân khách hàng
    public function checkCustomer($type, $id)
    {
        // Lưu thông tin loại hình đặt (phong hoặc combo) và ID vào Session
        session(['booking_type' => $type, 'booking_id' => $id]);

        // Kiểm tra xem user hiện tại đã có hồ sơ khách hàng chưa (Khớp với cột tai_khoan_khachhang_id trong DB)
        $khachHang = KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();

        if (!$khachHang) {
            return redirect()->route('booking.customer');
        }

        return redirect()->route('booking.services');
    }

    // Bước 1: Hiển thị form nhập thông tin khách hàng lần đầu
    public function showCustomerForm()
    {
        return view('user.infkhachhang');
    }

    // Bước 1: Lưu thông tin khách hàng vào DB
    public function saveCustomer(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:100',
            'so_dien_thoai' => 'required|string|max:15',
            'email' => 'required|email|max:100',
            'cccd' => 'required|string|max:20',
        ]);

        KhachHang::create([
            'tai_khoan_khachhang_id' => Auth::id(), // Đã sửa khớp sơ đồ DB
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

    // Bước 2: Hiển thị trang chọn ngày và dịch vụ đi kèm
    public function showServiceForm()
    {
        $type = session('booking_type');
        $id = session('booking_id');

        // Khớp khóa chính id_phong hoặc id_combo
        $item = ($type == 'phong') ? Phong::find($id) : Combo::find($id);
        $dichVus = DichVu::all();

        return view('user.dichvubooking', compact('item', 'dichVus', 'type'));
    }

    // Bước 2: Xử lý dữ liệu chọn ngày và dịch vụ bổ trợ
    public function saveServices(Request $request)
    {
        $request->validate([
            'ngay_nhan' => 'required|date',
            'ngay_tra'  => 'required|date|after:ngay_nhan',
        ]);

        $ngay_nhan = Carbon::parse($request->ngay_nhan);
        $ngay_tra = Carbon::parse($request->ngay_tra);
        $so_dem = $ngay_nhan->diffInDays($ngay_tra);

        if ($so_dem <= 0) {
            return redirect()->back()->withErrors(['date' => 'Ngày trả phòng phải sau ngày nhận phòng ít nhất 1 đêm!']);
        }

        // Lấy danh sách dịch vụ đi kèm được chọn
        $dichVuIds = $request->input('dich_vu', []);

        // Mảng lưu số lượng của từng dịch vụ (mặc định nếu không truyền là 1)
        $soLuongDichVu = $request->input('so_luong', []);

        // Khớp với cột khóa chính id_dichvu trong DB
        $selectedDichVus = DichVu::whereIn('id_dichvu', $dichVuIds)->get();

        $tong_tien_dich_vu = 0;
        $dich_vu_session_data = [];

        foreach ($selectedDichVus as $dv) {
            $qty = isset($soLuongDichVu[$dv->id_dichvu]) ? intval($soLuongDichVu[$dv->id_dichvu]) : 1;
            if ($qty <= 0) $qty = 1;

            // Trong DB cột giá dịch vụ là 'gia' chứ không phải 'gia_dich_vu'
            $thanh_tien_dv = $dv->gia * $qty;
            $tong_tien_dich_vu += $thanh_tien_dv;

            $dich_vu_session_data[$dv->id_dichvu] = [
                'id_dichvu'  => $dv->id_dichvu,
                'so_luong'   => $qty,
                'thanh_tien' => $thanh_tien_dv
            ];
        }

        // Lưu toàn bộ thông tin tính toán tạm thời vào session
        session([
            'ngay_nhan'          => $request->ngay_nhan,
            'ngay_tra'           => $request->ngay_tra,
            'so_dem'             => $so_dem,
            'booking_dich_vus'   => $dich_vu_session_data,
            'tong_tien_dich_vu'  => $tong_tien_dich_vu
        ]);

        return redirect()->route('booking.confirm');
    }

    // Bước 3: Trang xác nhận thông tin tổng quan
    public function showConfirmation()
    {
        $khachHang = KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();
        $type = session('booking_type');

        $item = ($type == 'phong') ? Phong::find(session('booking_id')) : Combo::find(session('booking_id'));

        $sessionDichVus = session('booking_dich_vus', []);
        $selectedDichVus = DichVu::whereIn('id_dichvu', array_keys($sessionDichVus))->get();

        // Tính tổng tiền phòng/combo hóa đơn
        if ($type == 'phong') {
            // Đối với PHÒNG: Giá phòng tính theo số đêm ở
            $gia_goc = $item->gia_phong * session('so_dem');
        } else {
            // Đối với COMBO: Đặt combo ăn theo giá trị trọn gói của combo đó
            $gia_goc = $item->gia_combo;
        }

        $tong_thanh_toan = $gia_goc + session('tong_tien_dich_vu');
        session(['tong_thanh_toan' => $tong_thanh_toan]);

        return view('user.xacnhanbooking', compact('khachHang', 'item', 'type', 'selectedDichVus', 'sessionDichVus', 'tong_thanh_toan'));
    }

    // Bước 4: Trang hiển thị lựa chọn phương thức thanh toán & số tiền cọc
    public function showPayment()
    {
        $tong_thanh_toan = session('tong_thanh_toan');
        $tien_coc = $tong_thanh_toan * 0.30; // Tiền đặt cọc trước 30% theo quy định

        return view('user.thanhtoanbooking', compact('tong_thanh_toan', 'tien_coc'));
    }

    // Bước 4: Khởi chạy tích hợp giả lập cổng thanh toán Sandbox VNPay
    public function vnpayPayment(Request $request)
    {
        // Giả lập tạo URL kết nối VNPay Sandbox và tự động callback trả về thành công '00'
        return redirect()->route('booking.vnpay_return', [
            'vnp_ResponseCode'  => '00',
            'vnp_TransactionNo' => 'VNP' . time()
        ]);
    }

    // Bước 5: Tiếp nhận phản hồi từ VNPay và ghi dữ liệu đồng bộ xuống Hệ thống CSDL
    public function vnpayReturn(Request $request)
    {
        if ($request->vnp_ResponseCode == '00') {

            $khachHang = KhachHang::where('tai_khoan_khachhang_id', Auth::id())->first();
            $type = session('booking_type');
            $booking_id = session('booking_id');

            $tong_thanh_toan = session('tong_thanh_toan');
            $tien_coc = $tong_thanh_toan * 0.30;

            // Sử dụng Database Transaction để đảm bảo tính toàn vẹn dữ liệu khi ghi vào nhiều bảng cùng lúc
            DB::beginTransaction();
            try {
                // 1. Khởi tạo bản ghi đặt phòng mới (Bảng `datphong`)
                $datPhongData = [
                    'id_khachhang'  => $khachHang->id_khachhang,
                    'ngay_dat'      => Carbon::now()->toDateString(),
                    'ngay_nhan'     => session('ngay_nhan'),
                    'ngay_tra'      => session('ngay_tra'),
                    'loai_hinh_dat' => ($type == 'phong') ? 'LẺ' : 'COMBO', // Enum: 'LẺ' hoặc 'COMBO'
                    'tong_tien_phai_tra' => $tong_thanh_toan,
                    'trang_thai'    => 'Đã xác nhận' // Đặt cọc thành công chuyển thẳng trạng thái
                ];

                if ($type == 'phong') {
                    $datPhongData['id_phong'] = $booking_id;
                    $datPhongData['id_combo'] = null;
                } else {
                    $datPhongData['id_phong'] = null;
                    $datPhongData['id_combo'] = $booking_id;
                }

                // Ghi vào bảng `datphong` lấy ID vừa sinh tự động
                $id_datphong = DB::table('datphong')->insertGetId($datPhongData);

                // 2. Kiểm tra ghi nhận các dịch vụ gia tăng đi kèm (Bảng `sudungdichvu`)
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

                // 3. Khởi tạo hóa đơn tài chính đi kèm luồng giao dịch (Bảng `hoadon`)
                $id_hoadon = DB::table('hoadon')->insertGetId([
                    'id_datphong' => $id_datphong,
                    'tong_tien'   => $tong_thanh_toan,
                    'ngay_xuat'   => Carbon::now()->toDateString()
                ]);

                // 4. Khởi tạo thông tin chi tiết thanh toán khoản tiền cọc 30% (Bảng `thanhtoan`)
                DB::table('thanhtoan')->insert([
                    'id_hoadon'       => $id_hoadon,
                    'id_datphong'     => $id_datphong,
                    'ngay_thanh_toan' => Carbon::now(),
                    'so_tien'         => $tien_coc,
                    'hinh_thuc'       => 'Chuyển khoản', // Enum: 'Tiền mặt', 'Chuyển khoản'
                    'loai_thanh_toan' => 'Đặt cọc 30%',  // Nhãn tiếng Việt theo yêu cầu thiết lập DB
                    'ghi_chu'         => 'Thanh toán đặt cọc trực tuyến qua cổng VNPay. Mã giao dịch: ' . $request->vnp_TransactionNo
                ]);

                // 5. Cập nhật lại trạng thái phòng nghỉ trong hệ thống nếu đặt theo phòng riêng lẻ
                if ($type == 'phong') {
                    DB::table('phong')
                        ->where('id_phong', $booking_id)
                        ->update(['trang_thai' => 'Đã đặt']); // Đồng bộ trạng thái sơ đồ phòng sang 'Đã đặt'
                }

                DB::commit();

                // Lấy thông tin thực tế trả về hiển thị lên giao diện Phiếu xác nhận đặt phòng
                $item = ($type == 'phong') ? Phong::find($booking_id) : Combo::find($booking_id);
                $selectedDichVus = DichVu::whereIn('id_dichvu', array_keys($sessionDichVus))->get();

                // Xóa bỏ toàn bộ dữ liệu tạm trong session sau khi đã lưu DB thành công
                session()->forget(['booking_type', 'booking_id', 'ngay_nhan', 'ngay_tra', 'so_dem', 'booking_dich_vus', 'tong_tien_dich_vu', 'tong_thanh_toan']);

                return view('user.phieuxacnhan', compact('khachHang', 'item', 'type', 'selectedDichVus', 'tien_coc'));

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('booking.services')->with('error', 'Hệ thống trục trặc khi lưu dữ liệu đặt phòng: ' . $e->getMessage());
            }
        }

        return redirect()->route('booking.services')->with('error', 'Giao dịch thanh toán cọc không thành công!');
    }
}
