<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\TaiKhoan;
use App\Models\NhanVien;
use App\Models\KhachHang;

class TaiKhoanManagementController extends Controller
{
    // 1. Hiển thị Trang Quản lý Tổng hợp
    public function index()
    {
        // Bảng 1: Nội bộ (Phân trang riêng) - có join để lấy thông tin nhân viên liên kết nếu có
        $taiKhoanNoiBo = TaiKhoan::leftJoin('nhanvien as nv', 'nv.tai_khoan_nhanvien_id', '=', 'taikhoan.id_taikhoan')
            ->whereIn('taikhoan.role', ['ADMIN', 'NHANVIEN'])
            ->select('taikhoan.*', 'nv.id_nhanvien as linked_id', 'nv.ho_ten as linked_name', 'nv.so_dien_thoai as linked_phone')
            ->orderBy('taikhoan.id_taikhoan', 'desc')
            ->paginate(10, ['taikhoan.*', 'nv.id_nhanvien', 'nv.ho_ten', 'nv.so_dien_thoai'], 'page_noibo');

        // Bảng 2: Khách hàng (Phân trang riêng) - GIỮ NGUYÊN
        $taiKhoanUser = TaiKhoan::leftJoin('khachhang as kh', 'kh.tai_khoan_khachhang_id', '=', 'taikhoan.id_taikhoan')
            ->where('taikhoan.role', 'USER')
            ->select('taikhoan.*', 'kh.id_khachhang as linked_id', 'kh.ho_ten as linked_name', 'kh.so_dien_thoai as linked_phone')
            ->orderBy('taikhoan.id_taikhoan', 'desc')
            ->paginate(10, ['taikhoan.*', 'kh.id_khachhang', 'kh.ho_ten', 'kh.so_dien_thoai'], 'page_user');

        // THÊM: Lấy danh sách nhân viên chưa được cấp tài khoản để đổ vào dropdown select
        // Điều kiện whereNull đảm bảo nhân viên nào đã có tài khoản rồi sẽ không xuất hiện lại
        // Danh sách nhân viên chưa được gán tài khoản (dùng cho dropdown cả modal tạo và sửa)
        $danhSachNhanVien = NhanVien::whereNull('tai_khoan_nhanvien_id')->get();

        // THÊM: Lấy danh sách Khách hàng chưa được gán tài khoản
        $danhSachKhachHang = KhachHang::whereNull('tai_khoan_khachhang_id')->get();

        // Trỏ thẳng view vào thư mục admin và compact thêm biến danh sách nhân viên
        return view('admin.quanlytaikhoan', compact('taiKhoanNoiBo', 'taiKhoanUser', 'danhSachNhanVien', 'danhSachKhachHang'));
    }

    // 2. Thêm tài khoản mới (Dùng chung) - ĐÃ THÊM LOGIC RÀNG BUỘC NHÂN VIÊN
    public function store(Request $request)
    {
        $request->validate([
            'ten_dang_nhap' => 'required|unique:taikhoan,username',
            'mat_khau' => 'required|min:6',
            'vai_tro' => 'required|in:ADMIN,NHANVIEN,USER',
        ]);

        // GIỮ NGUYÊN mảng insert của mày, chỉ đổi phương thức thành insertGetId để lấy id_taikhoan vừa tạo
        $taiKhoan = TaiKhoan::create([
            'username' => $request->ten_dang_nhap,
            'password' => Hash::make($request->mat_khau),
            'role' => $request->vai_tro,
            'trang_thai' => 'ACTIVE',
            'created_at' => now(),
        ]);

        // THÊM: Kiểm tra nếu vai trò là ADMIN hoặc NHANVIEN thì bắt buộc phải gán kết với ID nhân viên
        if (in_array($request->vai_tro, ['ADMIN', 'NHANVIEN']) && $request->filled('id_nhanvien')) {
            NhanVien::where('id_nhanvien', $request->id_nhanvien)
                ->update(['tai_khoan_nhanvien_id' => $taiKhoan->id_taikhoan]);
        }

        // THÊM: Nếu vai trò là USER và chọn Khách hàng thì gán liên kết
        if ($request->vai_tro === 'USER' && $request->filled('id_khachhang')) {
            $kh = KhachHang::find($request->id_khachhang);
            if ($kh && $kh->tai_khoan_khachhang_id) {
                // Khách hàng đã có tài khoản khác
                return back()->with('error', 'Khách hàng bạn chọn đã được gán tài khoản khác.');
            }
            // Gán khách hàng với tài khoản mới
            KhachHang::where('id_khachhang', $request->id_khachhang)->update(['tai_khoan_khachhang_id' => $taiKhoan->id_taikhoan]);
        }

        return back()->with('success', 'Thêm tài khoản thành công!');
    }

    // 3. Cập nhật thông tin (Dùng chung) - GIỮ NGUYÊN NGUYÊN BẢN
    public function update(Request $request, $id)
    {
        $request->validate([
            'vai_tro' => 'required|in:ADMIN,NHANVIEN,USER',
            'mat_khau' => 'nullable|min:6',
            'id_nhanvien' => 'nullable|integer',
            'id_khachhang' => 'nullable|integer',
        ]);

        $updateData = [
            'role' => $request->vai_tro,
        ];

        // Nếu nhập mật khẩu mới thì mới update pass
        if ($request->filled('mat_khau')) {
            $updateData['password'] = Hash::make($request->mat_khau);
        }

        TaiKhoan::where('id_taikhoan', $id)->update($updateData);

        // Xử lý ràng buộc nhân viên ↔ tài khoản
        // Nếu role là ADMIN hoặc NHANVIEN thì phải gán id_nhanvien (nếu có)
        if (in_array($request->vai_tro, ['ADMIN', 'NHANVIEN'])) {
            $selectedNv = $request->input('id_nhanvien');
            if ($selectedNv) {
                // Kiểm tra nhân viên có đang bị gán cho tài khoản khác không
                $existing = NhanVien::where('id_nhanvien', $selectedNv)->first();
                if ($existing && $existing->tai_khoan_nhanvien_id && $existing->tai_khoan_nhanvien_id != $id) {
                    return back()->with('error', 'Nhân viên này đã có tài khoản khác. Vui lòng chọn nhân viên khác.');
                }

                // Hủy gán tất cả nhân viên đang gán với tài khoản này (nếu có)
                NhanVien::where('tai_khoan_nhanvien_id', $id)->update(['tai_khoan_nhanvien_id' => null]);

                // Gán nhân viên được chọn với tài khoản hiện tại
                NhanVien::where('id_nhanvien', $selectedNv)->update(['tai_khoan_nhanvien_id' => $id]);
            }
            // Nếu chuyển role sang ADMIN/NHANVIEN thì chắc chắn không còn liên kết khách hàng
            KhachHang::where('tai_khoan_khachhang_id', $id)->update(['tai_khoan_khachhang_id' => null]);
        } else {
            // Nếu chuyển về USER thì hủy gán nhân viên (nếu có)
            NhanVien::where('tai_khoan_nhanvien_id', $id)->update(['tai_khoan_nhanvien_id' => null]);
            // Xử lý gán Khách hàng ↔ Tài khoản
            $selectedKh = $request->input('id_khachhang');
            if ($selectedKh) {
                // Kiểm tra khách hàng có đang bị gán cho tài khoản khác không
                $existingKh = KhachHang::where('id_khachhang', $selectedKh)->first();
                if ($existingKh && $existingKh->tai_khoan_khachhang_id && $existingKh->tai_khoan_khachhang_id != $id) {
                    return back()->with('error', 'Khách hàng này đã có tài khoản khác. Vui lòng chọn khách hàng khác.');
                }

                // Hủy gán trước đó tất cả khách hàng đang gán với tài khoản này
                KhachHang::where('tai_khoan_khachhang_id', $id)->update(['tai_khoan_khachhang_id' => null]);

                // Gán khách hàng được chọn với tài khoản hiện tại
                KhachHang::where('id_khachhang', $selectedKh)->update(['tai_khoan_khachhang_id' => $id]);
            } else {
                // Nếu không chọn khách hàng thì đảm bảo tài khoản này không còn liên kết nào
                KhachHang::where('tai_khoan_khachhang_id', $id)->update(['tai_khoan_khachhang_id' => null]);
            }
        }

        return back()->with('success', 'Cập nhật tài khoản thành công!');
    }

    // 4. Bật/Tắt trạng thái tài khoản - GIỮ NGUYÊN NGUYÊN BẢN
    public function toggleStatus($id)
    {
        $tk = TaiKhoan::find($id);
        if ($tk) {
            $newStatus = ($tk->trang_thai == 'ACTIVE') ? 'BLOCKED' : 'ACTIVE';
            $tk->update(['trang_thai' => $newStatus]);
            // Nếu tài khoản vừa bị khóa mà hiện đang là người dùng đang đăng nhập, đăng xuất ngay
            if ($newStatus === 'BLOCKED') {
                $current = Auth::guard('admin')->user();
                if ($current && $current->id_taikhoan == $id) {
                    Auth::guard('admin')->logout();
                    // Đảm bảo invalidate session
                    request()->session()->invalidate();
                    request()->session()->regenerateToken();
                    return redirect()->route('admin.login')->with('error', 'Bạn vừa khóa tài khoản đang đăng nhập; vui lòng đăng nhập lại bằng tài khoản khác.');
                }
            }

            return back()->with('success', 'Đã thay đổi trạng thái tài khoản!');
        }
        return back()->with('error', 'Không tìm thấy tài khoản!');
    }
}
