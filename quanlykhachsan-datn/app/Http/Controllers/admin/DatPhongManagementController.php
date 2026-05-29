<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DatPhong;
use App\Models\Phong;
use App\Models\KhachHang;
use Carbon\Carbon;
use App\Models\ThanhToan;

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
        $danhSachPhong = Phong::where('trang_thai', 'Trống')->get(); // Chỉ lấy phòng trống để đặt mới
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

        // Tính tiền tổng
        $tongTien = $this->calculateRoomPrice($request->id_phong, $request->ngay_nhan, $request->ngay_tra);

        DatPhong::create([
            'id_khachhang' => $request->id_khachhang,
            'id_phong' => $request->id_phong,
            'ngay_dat' => Carbon::now(),
            'ngay_nhan' => Carbon::parse($request->ngay_nhan)->startOfDay(),
            'ngay_tra' => Carbon::parse($request->ngay_tra)->endOfDay(),
            'tong_tien_phai_tra' => $tongTien,
            'trang_thai' => 'Đã xác nhận'
        ]);

        // Cập nhật trạng thái phòng sang 'Đã đặt'
        Phong::where('id_phong', $request->id_phong)->update(['trang_thai' => 'Đã đặt']);

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

        // Tính lại tiền phòng dựa trên cấu hình ngày mới
        $tongTienMoi = $this->calculateRoomPrice($datPhong->id_phong, $request->ngay_nhan, $request->ngay_tra);

        $datPhong->update([
            'ngay_nhan' => Carbon::parse($request->ngay_nhan)->startOfDay(),
            'ngay_tra' => Carbon::parse($request->ngay_tra)->endOfDay(),
            'tong_tien_phai_tra' => $tongTienMoi,
            'trang_thai' => $request->trang_thai
        ]);

        // Nếu hủy phòng thì trả trạng thái về Trống
        if ($request->trang_thai === 'Đã hủy' || $request->trang_thai === 'Đã trả phòng') {
            Phong::where('id_phong', $datPhong->id_phong)->update(['trang_thai' => 'Trống']);
        } else {
            Phong::where('id_phong', $datPhong->id_phong)->update(['trang_thai' => 'Đã đặt']);
        }

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

        $tongTien = $this->calculateRoomPrice($idPhong, $ngayNhan, $ngayTra);

        return response()->json([
            'success' => true,
            'price' => $tongTien,
            'price_formatted' => number_format($tongTien, 0, ',', '.') . ' đ'
        ]);
    }

    // Hàm lõi tính toán chi phí phòng (Thuật toán quét cấu hình Sale theo từng ngày)
    private function calculateRoomPrice($idPhong, $ngayNhan, $ngayTra)
    {
        $phong = Phong::findOrFail($idPhong);
        $start = Carbon::parse($ngayNhan)->startOfDay();
        $end = Carbon::parse($ngayTra)->startOfDay();

        $soDem = $start->diffInDays($end);
        if ($soDem <= 0) $soDem = 1; // Tính tối thiểu 1 đêm

        $tongTien = 0;
        $giaGoc = $phong->gia_phong;

        // Quét từng đêm xem đêm nào được sale, đêm nào giữ giá gốc
        for ($i = 0; $i < $soDem; $i++) {
            $currentDay = $start->copy()->addDays($i);

            // Kiểm tra xem ngày hiện tại có nằm trong chu kỳ sale không
            $isSale = false;
            if ($phong->giam_gia_percent > 0 && $phong->sale_tu_ngay && $phong->sale_den_ngay) {
                $saleTu = Carbon::parse($phong->sale_tu_ngay)->startOfDay();
                $saleDen = Carbon::parse($phong->sale_den_ngay)->endOfDay();
                if ($currentDay->between($saleTu, $saleDen)) {
                    $isSale = true;
                }
            }

            if ($isSale) {
                $tongTien += $giaGoc * (1 - ($phong->giam_gia_percent / 100));
            } else {
                $tongTien += $giaGoc;
            }
        }

        return $tongTien;
    }
}
