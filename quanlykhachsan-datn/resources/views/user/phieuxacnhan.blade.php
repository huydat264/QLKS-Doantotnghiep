@extends('layouts.style')

@section('content')
<style>
    .booking-wrapper { background-color: #faf8f5; min-height: 100vh; padding-top: 120px; padding-bottom: 60px; }
    .ticket-box { max-width: 750px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.05); }
    .ticket-header { background-color: #673065; color: white; padding: 40px; text-center: center; }
    .ticket-body { padding: 40px; }
    .dash-line { border-top: 2px dashed #eee; margin: 30px 0; position: relative; }
    .dash-line::before, .dash-line::after { content: ''; width: 20px; height: 20px; background: #faf8f5; border-radius: 50%; position: absolute; top: -10px; }
    .dash-line::before { left: -50px; }
    .dash-line::after { right: -50px; }
    .btn-home { background: transparent; border: 2px solid #673065; color: #673065; font-weight: bold; padding: 10px 30px; border-radius: 20px; text-decoration: none; text-transform: uppercase; font-size: 12px; display: inline-block; transition: 0.3s; }
    .btn-home:hover { background: #673065; color: white; }
</style>

<div class="booking-wrapper">
    <div class="container">
        <div class="ticket-box">

            <div class="ticket-header text-center">
                <img src="https://www.sixsenses.com/media/8254/icon-gem.png" alt="logo" style="width: 50px; filter: brightness(0) invert(1); margin-bottom: 15px; opacity: 0.8;">
                <h2 class="font-family-serif mb-1" style="letter-spacing:1px;">XÁC NHẬN ĐẶT PHÒNG THÀNH CÔNG</h2>
                <p class="small mb-0 opacity-75">Mã giữ chỗ điện tử: <strong>#SS{{ time() }}</strong></p>
            </div>

            <div class="ticket-body">
                <p class="text-center text-muted small mb-4">Cảm ơn ông/bà <strong>{{ $khachHang->ho_ten }}</strong>, Resort đã ghi nhận khoản đặt cọc bảo đảm giữ chỗ 30% cho hành trình lưu trú của bạn.</p>

                <h5 class="font-family-serif text-muted small text-uppercase mb-3" style="letter-spacing:0.5px;">Thông tin hành trình</h5>
                <div class="row mb-2">
                    <div class="col-6 text-muted small">Tên căn hộ / Hạng phòng:</div>
                    <div class="col-6 text-end fw-bold text-dark">{{ $item->ten_phong ?? $item->ten_combo }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6 text-muted small">Thời gian check-in:</div>
                    <div class="col-6 text-end fw-bold text-dark">{{ date('d/m/Y', strtotime(session('ngay_nhan'))) }} (Từ 14:00)</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6 text-muted small">Thời gian check-out:</div>
                    <div class="col-6 text-end fw-bold text-dark">{{ date('d/m/Y', strtotime(session('ngay_tra'))) }} (Trước 12:00)</div>
                </div>

                <div class="dash-line"></div>

                <h5 class="font-family-serif text-muted small text-uppercase mb-3" style="letter-spacing:0.5px;">Dịch vụ đính kèm đã kích hoạt</h5>
                @forelse($selectedDichVus as $dv)
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">• {{ $dv->ten_dich_vu }}</span>
                        <span class="text-dark fw-bold">Đã xác nhận</span>
                    </div>
                @empty
                    <p class="text-muted small">Không đăng ký thêm dịch vụ ngoài.</p>
                @endforelse

                <div class="text-center mt-5">
                    <p class="text-muted small mb-4">Voucher xác nhận chi tiết cùng mã QR kiểm tra thông tin check-in đã được gửi về địa chỉ Email của bạn.</p>
                    <a href="{{ url('/') }}" class="btn-home">Quay lại trang chủ</a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
