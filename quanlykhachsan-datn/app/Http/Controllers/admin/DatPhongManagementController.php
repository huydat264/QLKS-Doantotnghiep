<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DatPhong;
use App\Models\Phong;
use App\Models\KhachHang;
use Carbon\Carbon;
use App\Models\ThanhToan;
use App\Services\RoomAvailabilityService;
use Illuminate\Support\Facades\DB;

class DatPhongManagementController extends Controller
{
    // 1. Hiển thị danh sách đơn đặt phòng
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = DatPhong::with(['khachhang', 'phong']);

        if (!empty($search)) {
            $query->whereHas('khachhang', function($q) use ($search) {
                $q->where('ho_ten', 'LIKE', "%{$search}%")
                  ->orWhere('so_dien_thoai', 'LIKE', "%{$search}%");
            })->orWhereHas('phong', function($q) use ($search) {
                $q->where('so_phong', 'LIKE', "%{$search}%");
            });
        }

        $danhSachDatPhong = $query->orderBy('ngay_dat', 'desc')->paginate(20);

        // Lấy dữ liệu bổ trợ cho form Thêm/Sửa
        $danhSachKhachHang = KhachHang::all();
        // Phòng vẫn hiển thị bình thường để quản trị viên đặt song song,
        // nhưng chỉ những phòng bảo trì mới không mở đặt.
        $danhSachPhong = Phong::where('trang_thai', '!=', 'Bảo trì')->get();
        $tatCaPhong = Phong::all(); // Cho form sửa cần hiển thị lại phòng cũ

        return view('admin.quanlydatphong', compact('danhSachDatPhong', 'search', 'danhSachKhachHang', 'danhSachPhong', 'tatCaPhong'));
    }

    // 2. Thêm mới đơn đặt phòng
    public function store(Request $request)
    {
        $request->validate([
            'id_khachhang' => 'required|exists:khachhang,id_khachhang',
            'id_phong' => 'required|exists:phong,id_phong',
            'ngay_nhan' => 'required|date|after_or_equal:today',
            'ngay_tra' => 'required|date|after:ngay_nhan',
        ]);

        // Tính tiền tổng (ủy nhiệm cho model Phong)
        $phong = Phong::findOrFail($request->id_phong);
        $tongTien = $phong->calculatePriceForPeriod($request->ngay_nhan, $request->ngay_tra);

        $booking = DB::transaction(function () use ($request, $tongTien) {
            $roomLock = DB::table('phong')->where('id_phong', $request->id_phong)->lockForUpdate()->first();
            if (!$roomLock || $roomLock->trang_thai === 'Bảo trì') {
                throw new \Exception('Phòng không hợp lệ hoặc đang bảo trì.');
            }

            if (!RoomAvailabilityService::isRoomAvailable($request->id_phong, $request->ngay_nhan, $request->ngay_tra)) {
                throw new \Exception('Phòng đã được đặt trong khoảng thời gian này. Vui lòng chọn ngày khác.');
            }

            $phong = Phong::findOrFail($request->id_phong);
            $tongTien = $phong->calculatePriceForPeriod($request->ngay_nhan, $request->ngay_tra);

            return DatPhong::create([
                'id_khachhang' => $request->id_khachhang,
                'id_phong' => $request->id_phong,
                'ngay_dat' => Carbon::now(),
                'ngay_nhan' => Carbon::parse($request->ngay_nhan)->startOfDay(),
                'ngay_tra' => Carbon::parse($request->ngay_tra)->endOfDay(),
                'tong_tien_phai_tra' => $tongTien,
                'trang_thai' => 'Đã xác nhận'
            ]);
        });

        return redirect()->back()->with('success', 'Tạo đơn đặt phòng thành công!');
    }

    // 3. Cập nhật ngày nhận/trả và trạng thái đơn đặt
    public function update(Request $request, $id)
    {
        $request->validate([
            'ngay_nhan' => 'required|date',
            'ngay_tra' => 'required|date|after:ngay_nhan',
            'trang_thai' => 'required|in:Đã xác nhận,Đã trả phòng,Đã hủy'
        ]);

        $datPhong = DatPhong::findOrFail($id);

        // Tính lại tiền phòng dựa trên cấu hình ngày mới (ủy nhiệm cho model Phong)
        $phong = Phong::findOrFail($datPhong->id_phong);
        $tongTienMoi = $phong->calculatePriceForPeriod($request->ngay_nhan, $request->ngay_tra);

        DB::transaction(function () use ($request, $datPhong, $tongTienMoi) {
            DB::table('phong')->where('id_phong', $datPhong->id_phong)->lockForUpdate()->first();

            if (!RoomAvailabilityService::isRoomAvailable($datPhong->id_phong, $request->ngay_nhan, $request->ngay_tra, $datPhong->id_datphong)) {
                throw new \Exception('Khoảng thời gian mới bị trùng với một booking khác. Vui lòng chọn ngày khác.');
            }

            $datPhong->update([
                'ngay_nhan' => Carbon::parse($request->ngay_nhan)->startOfDay(),
                'ngay_tra' => Carbon::parse($request->ngay_tra)->endOfDay(),
                'tong_tien_phai_tra' => $tongTienMoi,
                'trang_thai' => $request->trang_thai
            ]);
        });

        return redirect()->back()->with('success', 'Cập nhật thông tin gia hạn/đổi ngày thành công!');
    }

    // 4. Hủy/Xóa đơn đặt phòng
    public function destroy($id)
    {
        $datPhong = DatPhong::findOrFail($id);

        // Trả trạng thái phòng về trống trước khi xóa đơn
        Phong::where('id_phong', $datPhong->id_phong)->update(['trang_thai' => 'Trống']);
        $datPhong->delete();

        return redirect()->back()->with('success', 'Đã xóa đơn đặt phòng khỏi hệ thống!');
    }

    // 5. API AJAX tính tiền phòng trực tiếp theo ngày nhận - trả (Hỗ trợ Popup tính tiền)
    public function getLivePrice(Request $request)
    {
        $idPhong = $request->input('id_phong');
        $ngayNhan = $request->input('ngay_nhan');
        $ngayTra = $request->input('ngay_tra');

        if (!$idPhong || !$ngayNhan || !$ngayTra) {
            return response()->json(['success' => false, 'price' => 0]);
        }

        $phong = Phong::findOrFail($idPhong);
        $tongTien = $phong->calculatePriceForPeriod($ngayNhan, $ngayTra);

        return response()->json([
            'success' => true,
            'price' => $tongTien,
            'price_formatted' => number_format($tongTien, 0, ',', '.') . ' đ'
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'id_phong' => 'required|exists:phong,id_phong',
            'ngay_nhan' => 'required|date',
            'ngay_tra' => 'required|date|after:ngay_nhan',
            'exclude_booking_id' => 'nullable|integer',
        ]);

        $idPhong = $request->input('id_phong');
        $ngayNhan = $request->input('ngay_nhan');
        $ngayTra = $request->input('ngay_tra');
        $excludeBookingId = $request->input('exclude_booking_id');

        $phong = Phong::findOrFail($idPhong);

        if ($phong->trang_thai === 'Bảo trì') {
            return response()->json(['available' => false]);
        }

        $available = RoomAvailabilityService::isRoomAvailable($idPhong, $ngayNhan, $ngayTra, $excludeBookingId);

        return response()->json(['available' => $available]);
    }

    // Price calculation moved to Phong model as calculatePriceForPeriod()
}
