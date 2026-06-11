<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class NhanVienManagementController extends Controller
{
    // 1. Hiển thị danh sách
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = DB::table('nhanvien');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('ho_ten', 'LIKE', "%{$search}%")
                  ->orWhere('so_dien_thoai', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('chuc_vu', 'LIKE', "%{$search}%");
            });
        }

        $danhSachNhanVien = $query->orderBy('id_nhanvien', 'desc')->paginate(20);
        return view('admin.quanlynhanvien', compact('danhSachNhanVien', 'search'));
    }

    // 2. Thêm mới nhân viên (Không truyền tai_khoan_nhanvien_id)
    public function store(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:100',
            'chuc_vu' => 'required|string',
            'luong_co_ban' => 'required|numeric|min:0',
            'ngay_vao_lam' => 'required|date',
            'so_dien_thoai' => 'required|string|max:15',
            'email' => 'required|email|max:100',
        ]);

        DB::table('nhanvien')->insert([
            'ho_ten' => $request->ho_ten,
            'chuc_vu' => $request->chuc_vu,
            'luong_co_ban' => $request->luong_co_ban,
            'ngay_vao_lam' => $request->ngay_vao_lam,
            'so_dien_thoai' => $request->so_dien_thoai,
            'email' => $request->email,
            // tai_khoan_nhanvien_id sẽ tự động nhận NULL trong database
        ]);

        return redirect()->back()->with('success', 'Đã thêm mới hồ sơ nhân viên thành công!');
    }

    // 3. Cập nhật thông tin (Chỉ sửa thông tin cá nhân)
    public function update(Request $request, $id)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:100',
            'chuc_vu' => 'required|string',
            'luong_co_ban' => 'required|numeric|min:0',
            'ngay_vao_lam' => 'required|date',
            'so_dien_thoai' => 'required|string|max:15',
            'email' => 'required|email|max:100',
        ]);

        DB::table('nhanvien')->where('id_nhanvien', $id)->update([
            'ho_ten' => $request->ho_ten,
            'chuc_vu' => $request->chuc_vu,
            'luong_co_ban' => $request->luong_co_ban,
            'ngay_vao_lam' => $request->ngay_vao_lam,
            'so_dien_thoai' => $request->so_dien_thoai,
            'email' => $request->email,
        ]);

        return redirect()->back()->with('success', 'Cập nhật hồ sơ nhân viên thành công!');
    }

    // 4. Xóa
    public function destroy($id)
    {
        DB::table('nhanvien')->where('id_nhanvien', $id)->delete();
        return redirect()->back()->with('success', 'Đã xóa nhân viên khỏi hệ thống!');
    }
    public function showProfile()
{
    // Lấy ID tài khoản đang đăng nhập từ guard admin
    $id_taikhoan = Auth::guard('admin')->id();

    // Truy vấn chính xác nhân viên sở hữu tài khoản này thông qua trường liên kết
    $nhanVien = DB::table('nhanvien')
        ->join('taikhoan', 'nhanvien.tai_khoan_nhanvien_id', '=', 'taikhoan.id_taikhoan')
        ->select('nhanvien.*', 'taikhoan.username', 'taikhoan.role', 'taikhoan.trang_thai')
        ->where('taikhoan.id_taikhoan', $id_taikhoan)
        ->first();

    // Nếu không tìm thấy (đề phòng trường hợp tài khoản ADMIN tối cao chưa gắn id_nhanvien)
    if (!$nhanVien) {
        return redirect()->route('admin.dashboard')->with('error', 'Tài khoản của bạn chưa được liên kết với hồ sơ nhân viên trong hệ thống!');
    }

    // Trả về view hiển thị thông tin cá nhân
    return view('admin.hoso', compact('nhanVien'));
 }
}
