<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DangkyDangnhapController;

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

use App\Http\Controllers\PhongController;

Route::get('/luu-tru', [PhongController::class, 'indexUser'])->name('phong.user');
Route::get('/luu-tru/{id}', [PhongController::class, 'chitietUser'])->name('phong.chitiet');
