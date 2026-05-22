<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuDungDichVuManagementController extends Controller
{
    // 1. Hiển thị danh sách và tìm kiếm sử dụng dịch vụ
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Truy vấn danh sách kết hợp bảng dịch vụ, đặt phòng và phòng để lấy thông tin chi tiết
        $query = DB::table('sudungdichvu')
            ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->join('datphong', 'sudungdichvu.id_datphong', '=', 'datphong.id_datphong')
            ->join('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->select(
                'sudungdichvu.*',
                'dichvu.ten_dich_vu',
                'dichvu.gia as gia_dich_vu',
                'phong.so_phong'
            );

        // Hỗ trợ tìm kiếm theo Số phòng hoặc Tên dịch vụ
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('phong.so_phong', 'LIKE', "%{$search}%")
                  ->orWhere('dichvu.ten_dich_vu', 'LIKE', "%{$search}%");
            });
        }

        // Phân trang cứng 20 dòng trên 1 trang
        // Note: table primary key is `id_sudungdv` in this database
        $danhSachSuDung = $query->orderBy('sudungdichvu.id_sudungdv', 'desc')->paginate(20);

        // Lấy dữ liệu bổ trợ cho các Form Modal Thêm/Sửa
        $danhSachDichVu = DB::table('dichvu')->select('id_dichvu', 'ten_dich_vu', 'gia')->get();

        // Lấy danh sách các đơn đặt phòng hiện tại kèm số phòng tương ứng để gán dịch vụ
        $danhSachDatPhong = DB::table('datphong')
            ->join('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->select('datphong.id_datphong', 'phong.so_phong')
            ->get();

        return view('admin.quanlysudungdichvu', compact('danhSachSuDung', 'search', 'danhSachDichVu', 'danhSachDatPhong'));
    }

    // 2. Thêm mới bản ghi sử dụng dịch vụ
    public function store(Request $request)
    {
        $request->validate([
            'id_datphong' => 'required|integer',
            'id_dichvu' => 'required|integer',
            'so_luong' => 'required|integer|min:1',
        ], [
            'id_datphong.required' => 'Vui lòng chọn phòng đang sử dụng dịch vụ.',
            'id_dichvu.required' => 'Vui lòng chọn loại dịch vụ sử dụng.',
            'so_luong.required' => 'Số lượng không được để trống.',
            'so_luong.min' => 'Số lượng sử dụng tối thiểu phải từ 1.',
        ]);

        // Lấy giá dịch vụ để tính thành tiền (bảng 'dichvu' dùng cột 'gia')
        $gia = DB::table('dichvu')->where('id_dichvu', $request->id_dichvu)->value('gia');
        $thanhTien = ($gia ? $gia : 0) * $request->so_luong;

        DB::table('sudungdichvu')->insert([
            'id_datphong' => $request->id_datphong,
            'id_dichvu' => $request->id_dichvu,
            'so_luong' => $request->so_luong,
            'ngay_su_dung' => Carbon::now(),
            'thanh_tien' => $thanhTien,
        ]);

        return redirect()->back()->with('success', 'Đã thêm dịch vụ sử dụng cho phòng thành công!');
    }

    // 3. Cập nhật thông tin số lượng hoặc loại dịch vụ đã dùng
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_datphong' => 'required|integer',
            'id_dichvu' => 'required|integer',
            'so_luong' => 'required|integer|min:1',
        ], [
            'id_datphong.required' => 'Vui lòng chọn phòng đang sử dụng dịch vụ.',
            'id_dichvu.required' => 'Vui lòng chọn loại dịch vụ sử dụng.',
            'so_luong.required' => 'Số lượng không được để trống.',
            'so_luong.min' => 'Số lượng sử dụng tối thiểu phải từ 1.',
        ]);

        // Tính lại thành tiền khi sửa (lấy giá hiện tại của dịch vụ)
        $gia = DB::table('dichvu')->where('id_dichvu', $request->id_dichvu)->value('gia');
        $thanhTien = ($gia ? $gia : 0) * $request->so_luong;

        DB::table('sudungdichvu')->where('id_sudungdv', $id)->update([
            'id_datphong' => $request->id_datphong,
            'id_dichvu' => $request->id_dichvu,
            'so_luong' => $request->so_luong,
            'thanh_tien' => $thanhTien,
        ]);

        return redirect()->back()->with('success', 'Cập nhật thông tin sử dụng dịch vụ thành công!');
    }
}
