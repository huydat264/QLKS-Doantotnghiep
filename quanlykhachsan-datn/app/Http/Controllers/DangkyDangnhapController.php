<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DangkyDangnhapModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DangkyDangnhapController extends Controller
{
    public function showDangky() { return view('user.dangky'); }
    public function showDangnhap() { return view('user.dangnhap'); }

// --- LOGIC ĐĂNG KÝ ---
    public function postDangky(Request $request)
    {
        // Xác thực dữ liệu với điều kiện mật khẩu phức tạp
        $request->validate([
            'username' => 'required|unique:taikhoan,username|max:50',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[@$!%*#?&]/',
            ],
        ], [
            'username.unique' => 'Tên người dùng đã tồn tại.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp.',
            'password.regex' => 'Mật khẩu phải bao gồm ít nhất 1 chữ hoa và 1 ký tự đặc biệt (@$!%*#?&).',
        ]);

        // Tạo bản ghi mới và gán vào biến $user
        $user = DangkyDangnhapModel::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'USER',
            'trang_thai' => 'ACTIVE',
        ]);

        //  TỰ ĐỘNG ĐĂNG NHẬP NGAY SAU KHI TẠO
        Auth::login($user);

        //  Trả về link trang chủ ('/') thay vì trang login
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Đăng ký tài khoản thành công!', 'redirect' => url('/')]);
        }

        return redirect('/')->with('success', 'Đăng ký tài khoản thành công!');
    }

    // --- LOGIC ĐĂNG NHẬP ---
    public function postDangnhap(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Vui lòng nhập tên tài khoản.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Kiểm tra trạng thái tài khoản
            if ($user->trang_thai !== 'ACTIVE') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'username' => ['Tài khoản này đã bị khóa.'],
                ]);
            }

            if ($request->ajax()) {
                return response()->json(['success' => true, 'redirect' => session()->pull('url.intended', url('/'))]);
            }

            return redirect()->intended('/')->with('success', 'Chào mừng bạn quay trở lại!');
        }

        throw ValidationException::withMessages([

            'password' => ['Thông tin đăng nhập không chính xác.'],
        ]);
    }

    // --- ĐĂNG XUẤT ---
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
