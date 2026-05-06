@extends('layouts.style')

@section('content')
<style>
    .booking-wrapper { background-color: #faf8f5; min-height: 100vh; padding-top: 120px; padding-bottom: 60px; }
    .confirm-card { background: white; padding: 40px; border-radius: 4px; border: 1px solid #f1eeea; margin-bottom: 30px; }
    .section-title { font-family: 'Playfair Display', serif; font-size: 20px; color: #673065; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
    .info-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px; }
    .info-label { color: #777; }
    .info-value { font-weight: bold; color: #222; }
    .btn-pay { background-color: #673065; color: white; padding: 14px 50px; border-radius: 25px; border: none; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; text-decoration: none; display: inline-block; transition: 0.3s; }
    .btn-pay:hover { background-color: #4a2148; color: white; }
</style>

<div class="booking-wrapper">
    <div class="container" style="max-width: 900px;">
        <div class="text-center mb-5">
            <p class="text-uppercase text-muted small letter-spacing-2 mb-2">Bước 3: Xác nhận</p>
            <h2 class="font-family-serif" style="font-size: 32px;">Kiểm tra thông tin hành trình</h2>
        </div>

        <div class="confirm-card shadow-sm">
            <h4 class="section-title">Hồ sơ người lưu trú</h4>
            <div class="info-row"><span class="info-label">Họ và tên:</span><span class="info-value">{{ $khachHang->ho_ten }}</span></div>
            <div class="info-row"><span class="info-label">Số điện thoại:</span><span class="info-value">{{ $khachHang->so_dien_thoai }}</span></div>
            <div class="info-row"><span class="info-label">Email:</span><span class="info-value">{{ $khachHang->email }}</span></div>
            <div class="info-row"><span class="info-label">CCCD / Hộ chiếu:</span><span class="info-value">{{ $khachHang->cccd }}</span></div>
        </div>

        <div class="confirm-card shadow-sm">
            <h4 class="section-title">Chi tiết dịch vụ lưu trú</h4>
            <div class="info-row"><span class="info-label">Tên gói / Hạng phòng:</span><span class="info-value">{{ $item->ten_phong ?? $item->ten_combo }}</span></div>
            <div class="info-row"><span class="info-label">Thời gian đến:</span><span class="info-value">{{ date('d/m/Y', strtotime(session('ngay_nhan'))) }}</span></div>
            <div class="info-row"><span class="info-label">Thời gian đi:</span><span class="info-value">{{ date('d/m/Y', strtotime(session('ngay_tra'))) }}</span></div>
            <div class="info-row"><span class="info-label">Tổng số đêm lưu trú:</span><span class="info-value">{{ session('so_dem') }} đêm</span></div>
        </div>

        <div class="confirm-card shadow-sm">
            <h4 class="section-title">Dịch vụ bổ sung đã chọn</h4>
            @forelse($selectedDichVus as $dv)
                <div class="info-row">
                    <span class="info-label">• {{ $dv->ten_dich_vu }}</span>
                    <span class="info-value">{{ number_format($dv->gia_dich_vu, 0, ',', '.') }} VNĐ</span>
                </div>
            @empty
                <p class="text-muted small mb-0">Không đăng ký dịch vụ bổ sung thêm.</p>
            @endforelse
        </div>

        <div class="confirm-card shadow-sm bg-light text-center py-5">
            <h3 class="font-family-serif mb-2" style="font-size: 24px;">Tổng giá trị hóa đơn</h3>
            <h2 class="fw-bold mb-4" style="color: #673065; font-size: 36px;">{{ number_format($tong_thanh_toan, 0, ',', '.') }} VNĐ</h2>

            <a href="{{ route('booking.payment') }}" class="btn-pay shadow-sm">Tiến hành đặt cọc giữ phòng</a>
        </div>
    </div>
</div>
@endsection
