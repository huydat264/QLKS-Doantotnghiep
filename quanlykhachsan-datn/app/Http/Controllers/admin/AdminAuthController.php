<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    // Hiển thị trang đăng nhập Admin toàn màn hình
    public function showLogin()
    {
        // Nếu đã đăng nhập ở guard admin thì đẩy thẳng vào trang dashboard
        if (Auth::guard('admin')->check()) {
            $userRole = strtoupper(trim(Auth::guard('admin')->user()->role));
            if ($userRole === 'ADMIN' || $userRole === 'NHANVIEN') {
                return redirect()->route('admin.dashboard');
            }
        }
        return view('admin.login');
    }

    // Xử lý đăng nhập bằng AJAX
    public function login(Request $request)
    {
        // 1. Validate dữ liệu đầu vào chuẩn chỉ
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Vui lòng điền tên tài khoản quản trị.',
            'password.required' => 'Mật khẩu không được để trống.',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        // 2. Thử thách xác thực tài khoản trong DB qlykhachsan
        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();

            // Chuẩn hóa chuỗi trạng thái và role từ database để tránh lỗi lệch khoảng trắng hoặc hoa/thường
            $userStatus = strtoupper(trim($user->trang_thai));
            $userRole = strtoupper(trim($user->role));

            // Kiểm tra trạng thái tài khoản có bị khóa không
            if ($userStatus === 'BLOCKED') {
                Auth::logout();
                return response()->json([
                    'errors' => ['username' => ['Tài khoản này đã bị khóa khỏi hệ thống!']]
                ], 422);
            }

            // Kiểm tra phân quyền (Tự động chấp nhận cả 'admin', 'Admin', 'ADMIN' nhờ hàm chuẩn hóa)
            if ($userRole === 'ADMIN' || $userRole === 'NHANVIEN') {
                return response()->json([
                    'success' => true,
                    'redirect' => route('admin.dashboard')
                ]);
            }

            // Nếu đúng tài khoản/mật khẩu nhưng role lại là USER thông thường thì đá ra luôn
            Auth::guard('admin')->logout();
            return response()->json([
                'errors' => ['username' => ['Bạn không có quyền truy cập vào khu vực quản trị này.']]
            ], 422);
        }

        // Đăng nhập thất bại (Sai username hoặc sai password)
        return response()->json([
            'errors' => ['username' => ['Tên tài khoản hoặc mật khẩu không chính xác.']]
        ], 422);
    }

    // Đăng xuất hệ thống
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
