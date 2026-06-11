<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class TaiKhoanManagementController extends Controller
{
    // 1. Hiển thị Trang Quản lý Tổng hợp
    public function index()
    {
        // Bảng 1: Nội bộ (Phân trang riêng) - có join để lấy thông tin nhân viên liên kết nếu có
        $taiKhoanNoiBo = DB::table('taikhoan as t')
            ->leftJoin('nhanvien as nv', 'nv.tai_khoan_nhanvien_id', '=', 't.id_taikhoan')
            ->whereIn('t.role', ['ADMIN', 'NHANVIEN'])
            ->select('t.*', 'nv.id_nhanvien as linked_id', 'nv.ho_ten as linked_name')
            ->orderBy('t.id_taikhoan', 'desc')
            ->paginate(10, ['t.*', 'nv.id_nhanvien', 'nv.ho_ten'], 'page_noibo');

        // Bảng 2: Khách hàng (Phân trang riêng) - GIỮ NGUYÊN
        $taiKhoanUser = DB::table('taikhoan as t')
            ->leftJoin('nhanvien as nv', 'nv.tai_khoan_nhanvien_id', '=', 't.id_taikhoan')
            ->where('t.role', 'USER')
            ->select('t.*', 'nv.id_nhanvien as linked_id', 'nv.ho_ten as linked_name')
            ->orderBy('t.id_taikhoan', 'desc')
            ->paginate(10, ['t.*', 'nv.id_nhanvien', 'nv.ho_ten'], 'page_user');

        // THÊM: Lấy danh sách nhân viên chưa được cấp tài khoản để đổ vào dropdown select
        // Điều kiện whereNull đảm bảo nhân viên nào đã có tài khoản rồi sẽ không xuất hiện lại
        // Danh sách nhân viên chưa được gán tài khoản (dùng cho dropdown cả modal tạo và sửa)
        $danhSachNhanVien = DB::table('nhanvien')
            ->whereNull('tai_khoan_nhanvien_id')
            ->get();

        // Trỏ thẳng view vào thư mục admin và compact thêm biến danh sách nhân viên
        return view('admin.quanlytaikhoan', compact('taiKhoanNoiBo', 'taiKhoanUser', 'danhSachNhanVien'));
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
        $id_taikhoan = DB::table('taikhoan')->insertGetId([
            'username' => $request->ten_dang_nhap,
            'password' => Hash::make($request->mat_khau),
            'role' => $request->vai_tro,
            'trang_thai' => 'ACTIVE',
            'created_at' => now(),
        ]);

        // THÊM: Kiểm tra nếu vai trò là ADMIN hoặc NHANVIEN thì bắt buộc phải gắn kết với ID nhân viên
        if (in_array($request->vai_tro, ['ADMIN', 'NHANVIEN']) && $request->filled('id_nhanvien')) {
            DB::table('nhanvien')
                ->where('id_nhanvien', $request->id_nhanvien) // Giả định khóa chính bảng nhanvien của mày là id_nhanvien
                ->update([
                    'tai_khoan_nhanvien_id' => $id_taikhoan // Gán mối quan hệ nối bảng như mày yêu cầu
                ]);
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
        ]);

        $updateData = [
            'role' => $request->vai_tro,
        ];

        // Nếu nhập mật khẩu mới thì mới update pass
        if ($request->filled('mat_khau')) {
            $updateData['password'] = Hash::make($request->mat_khau);
        }

        DB::table('taikhoan')->where('id_taikhoan', $id)->update($updateData);

        // Xử lý ràng buộc nhân viên ↔ tài khoản
        // Nếu role là ADMIN hoặc NHANVIEN thì phải gán id_nhanvien (nếu có)
        if (in_array($request->vai_tro, ['ADMIN', 'NHANVIEN'])) {
            $selectedNv = $request->input('id_nhanvien');
            if ($selectedNv) {
                // Kiểm tra nhân viên có đang bị gán cho tài khoản khác không
                $existing = DB::table('nhanvien')->where('id_nhanvien', $selectedNv)->first();
                if ($existing && $existing->tai_khoan_nhanvien_id && $existing->tai_khoan_nhanvien_id != $id) {
                    return back()->with('error', 'Nhân viên này đã có tài khoản khác. Vui lòng chọn nhân viên khác.');
                }

                // Hủy gán tất cả nhân viên đang gán với tài khoản này (nếu có)
                DB::table('nhanvien')->where('tai_khoan_nhanvien_id', $id)->update(['tai_khoan_nhanvien_id' => null]);

                // Gán nhân viên được chọn với tài khoản hiện tại
                DB::table('nhanvien')->where('id_nhanvien', $selectedNv)->update(['tai_khoan_nhanvien_id' => $id]);
            }
        } else {
            // Nếu chuyển về USER thì hủy gán nhân viên (nếu có)
            DB::table('nhanvien')->where('tai_khoan_nhanvien_id', $id)->update(['tai_khoan_nhanvien_id' => null]);
        }

        return back()->with('success', 'Cập nhật tài khoản thành công!');
    }

    // 4. Bật/Tắt trạng thái tài khoản - GIỮ NGUYÊN NGUYÊN BẢN
    public function toggleStatus($id)
    {
        $tk = DB::table('taikhoan')->where('id_taikhoan', $id)->first();
        if ($tk) {
            $newStatus = ($tk->trang_thai == 'ACTIVE') ? 'BLOCKED' : 'ACTIVE';
            DB::table('taikhoan')->where('id_taikhoan', $id)->update(['trang_thai' => $newStatus]);
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
