<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DangkyDangnhapController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\ComboUserController;
use App\Http\Controllers\DatPhongController;

Route::get('/', function () {
    return view('user.home');
})->name('home');;

// Routes Đăng ký
Route::get('/dang-ky', [DangkyDangnhapController::class, 'showDangky'])->name('register');
Route::post('/dang-ky', [DangkyDangnhapController::class, 'postDangky']);

// Routes Đăng nhập
Route::get('/dang-nhap', [DangkyDangnhapController::class, 'showDangnhap'])->name('login');
Route::post('/dang-nhap', [DangkyDangnhapController::class, 'postDangnhap']);

Route::post('/dang-xuat', [DangkyDangnhapController::class, 'logout'])->name('logout');

Route::get('/diem-den', function () {
    return view('user.diemden');
});
Route::get('/luu-tru', [PhongController::class, 'indexUser'])->name('phong.user');
Route::get('/luu-tru/{id}', [PhongController::class, 'chitietUser'])->name('phong.chitiet');

// Hiển thị danh sách combo
Route::get('/combo', [ComboUserController::class, 'index'])->name('combo.index');

// Hiển thị chi tiết 1 combo
Route::get('/combo/{id}', [ComboUserController::class, 'show'])->name('combo.show');

Route::group(['middleware' => 'auth'], function () {
    // Trạm kiểm tra ban đầu khi nhấn nút Đặt phòng hoặc Đặt Combo
    Route::get('/dat-phong/bat-dau/{type}/{id}', [DatPhongController::class, 'checkCustomer'])->name('booking.check');

    // Bước 1: Form thông tin khách hàng (chỉ hiển thị nếu là lần đầu tiên)
    Route::get('/dat-phong/thong-tin-khach-hang', [DatPhongController::class, 'showCustomerForm'])->name('booking.customer');
    Route::post('/dat-phong/luu-khach-hang', [DatPhongController::class, 'saveCustomer'])->name('booking.save_customer');

    // Bước 2: Chọn ngày lưu trú và dịch vụ bổ sung
    Route::get('/dat-phong/dich-vu', [DatPhongController::class, 'showServiceForm'])->name('booking.services');
    Route::post('/dat-phong/luu-dich-vu', [DatPhongController::class, 'saveServices'])->name('booking.save_services');

    // Bước 3: Xác nhận toàn bộ thông tin đơn đặt phòng
    Route::get('/dat-phong/xac-nhan', [DatPhongController::class, 'showConfirmation'])->name('booking.confirm');

    // Bước 4: Giao diện chọn phương thức & Giả lập VNPay cọc 30%
    Route::get('/dat-phong/thanh-toan', [DatPhongController::class, 'showPayment'])->name('booking.payment');
    Route::post('/dat-phong/vnpay-process', [DatPhongController::class, 'vnpayPayment'])->name('booking.vnpay_process');
    Route::get('/dat-phong/vnpay-return', [DatPhongController::class, 'vnpayReturn'])->name('booking.vnpay_return');
});
