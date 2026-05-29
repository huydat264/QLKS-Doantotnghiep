<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChamCongManagementController extends Controller
{
    // 1. Hiển thị danh sách bảng chấm công
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Join bảng chamcong với nhanvien để lấy tên nhân viên hiển thị
        $query = DB::table('chamcong')
            ->join('nhanvien', 'chamcong.id_nhanvien', '=', 'nhanvien.id_nhanvien')
            ->select('chamcong.*', 'nhanvien.ho_ten', 'nhanvien.chuc_vu');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nhanvien.ho_ten', 'LIKE', "%{$search}%")
                  ->orWhere('chamcong.thang', 'LIKE', "%{$search}%")
                  ->orWhere('chamcong.nam', 'LIKE', "%{$search}%");
            });
        }

        $danhSachChamCong = $query->orderBy('chamcong.nam', 'desc')
                                  ->orderBy('chamcong.thang', 'desc')
                                  ->orderBy('chamcong.id_chamcong', 'desc')
                                  ->paginate(20);

        // Lấy danh sách nhân viên đang làm việc để đổ vào Select Box lúc Thêm mới
        $danhSachNhanVien = DB::table('nhanvien')->select('id_nhanvien', 'ho_ten', 'chuc_vu')->get();

        return view('admin.quanlychamcong', compact('danhSachChamCong', 'search', 'danhSachNhanVien'));
    }

    // 2. Thêm mới bản ghi chấm công
    public function store(Request $request)
    {
        $request->validate([
            'id_nhanvien' => 'required|integer',
            'thang' => 'required|integer|min:1|max:12',
            'nam' => 'required|integer|min:2000',
            'so_ngay_di_lam' => 'required|numeric|min:0',
            'so_ngay_nghi_khong_phep' => 'required|numeric|min:0',
            'so_ngay_nghi_co_phep' => 'required|numeric|min:0',
        ]);

        // Logic chặn chấm công trùng lặp: Kiểm tra xem nhân viên này đã có điểm danh trong tháng/năm này chưa
        $isExist = DB::table('chamcong')
            ->where('id_nhanvien', $request->id_nhanvien)
            ->where('thang', $request->thang)
            ->where('nam', $request->nam)
            ->exists();

        if ($isExist) {
            return redirect()->back()->with('error', 'Nhân viên này đã được chấm công trong tháng ' . $request->thang . '/' . $request->nam . ' rồi!');
        }

        DB::table('chamcong')->insert([
            'id_nhanvien' => $request->id_nhanvien,
            'thang' => $request->thang,
            'nam' => $request->nam,
            'so_ngay_di_lam' => $request->so_ngay_di_lam,
            'so_ngay_nghi_khong_phep' => $request->so_ngay_nghi_khong_phep,
            'so_ngay_nghi_co_phep' => $request->so_ngay_nghi_co_phep,
        ]);

        return redirect()->back()->with('success', 'Đã lưu bản ghi chấm công mới thành công!');
    }

    // 3. Cập nhật sửa đổi điểm danh
    public function update(Request $request, $id)
    {
        $request->validate([
            'thang' => 'required|integer|min:1|max:12',
            'nam' => 'required|integer|min:2000',
            'so_ngay_di_lam' => 'required|numeric|min:0',
            'so_ngay_nghi_khong_phep' => 'required|numeric|min:0',
            'so_ngay_nghi_co_phep' => 'required|numeric|min:0',
        ]);

        // Kiểm tra trùng tháng/năm với bản ghi khác của cùng nhân viên đó (Trừ chính nó ra)
        $chamCongHienTai = DB::table('chamcong')->where('id_chamcong', $id)->first();

        $isExist = DB::table('chamcong')
            ->where('id_nhanvien', $chamCongHienTai->id_nhanvien)
            ->where('thang', $request->thang)
            ->where('nam', $request->nam)
            ->where('id_chamcong', '!=', $id)
            ->exists();

        if ($isExist) {
            return redirect()->back()->with('error', 'Lỗi: Bị trùng với một bản ghi chấm công khác của tháng ' . $request->thang . '/' . $request->nam);
        }

        DB::table('chamcong')->where('id_chamcong', $id)->update([
            'thang' => $request->thang,
            'nam' => $request->nam,
            'so_ngay_di_lam' => $request->so_ngay_di_lam,
            'so_ngay_nghi_khong_phep' => $request->so_ngay_nghi_khong_phep,
            'so_ngay_nghi_co_phep' => $request->so_ngay_nghi_co_phep,
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật lại thông tin chấm công!');
    }
}
