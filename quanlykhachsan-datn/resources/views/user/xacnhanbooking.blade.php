@extends('layouts.style')

@section('content')
<style>
    .booking-wrapper { background-color: #faf8f5; min-height: 100vh; padding-top: 120px; padding-bottom: 60px; }
    .confirm-card { background: white; padding: 40px; border-radius: 4px; border: 1px solid #f1eeea; margin-bottom: 30px; }
    .section-title { font-family: 'Playfair Display', serif; font-size: 20px; color: #673065; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
    .info-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px; }
    .info-label { color: #777; }
    .info-value { font-weight: bold; color: #222; }
    .btn-pay { background-color: #673065; color: white; padding: 14px 50px; border-radius: 25px; border: none; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; text-decoration: none; display: inline-block; transition: 0.3s; text-align: center; }
    .btn-pay:hover { background-color: #4a2148; color: white; transform: translateY(-2px); }
    .price-breakdown { font-size: 14px; color: #666; font-style: italic; }
    .badge-qty { background-color: #f8f9fa; color: #673065; border: 1px solid #e9ecef; padding: 2px 8px; border-radius: 12px; font-weight: normal; font-size: 12px; }

    /* Highlight cho phần tiền cọc */
    .deposit-box { background-color: #e8f5e9; border: 2px solid #2e7d32; padding: 20px; border-radius: 8px; margin-top: 20px; }
    .deposit-amount { color: #2e7d32; font-size: 28px; font-weight: 900; }
</style>

<div class="booking-wrapper">
    <div class="container" style="max-width: 850px;">
        <div class="text-center mb-5">
            <span class="text-uppercase small text-muted fw-bold" style="letter-spacing: 2px;">Bước 3/3</span>
            <h2 class="font-family-serif">Xác nhận đơn đặt phòng</h2>
            <p class="text-muted">Vui lòng kiểm tra kỹ hồ sơ và thanh toán số tiền đặt cọc 30%</p>
        </div>

        <div class="confirm-card shadow-sm">
            <h4 class="section-title">Hồ sơ người lưu trú</h4>
            <div class="info-row">
                <span class="info-label">Họ và tên:</span>
                <span class="info-value">{{ $khachHang->ho_ten }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Số điện thoại:</span>
                <span class="info-value">{{ $khachHang->so_dien_thoai }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $khachHang->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Số CCCD/Passport:</span>
                <span class="info-value">{{ $khachHang->cccd }}</span>
            </div>
        </div>

        <div class="confirm-card shadow-sm">
            <h4 class="section-title">Chi tiết đặt phòng</h4>
            <div class="info-row">
                <span class="info-label">Lựa chọn:</span>
                <span class="info-value">{{ $item->ten_phong ?? $item->ten_combo }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Hạng phòng:</span>
                <span class="info-value">{{ $item->loai_phong ?? $item->loai_phong_ap_dung }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Đơn giá:</span>
                <span class="info-value">{{ number_format($item->gia_hien_tai ?? $item->gia_combo, 0, ',', '.') }} VNĐ / đêm</span>
            </div>
            <div class="info-row border-top pt-3 mt-2">
                <span class="info-label">Ngày nhận phòng:</span>
                <span class="info-value">{{ date('d/m/Y', strtotime($ngay_nhan)) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ngày trả phòng:</span>
                <span class="info-value">{{ date('d/m/Y', strtotime($ngay_tra)) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Thời gian lưu trú:</span>
                <span class="info-value text-primary">{{ $so_dem }} đêm</span>
            </div>
            <div class="info-row border-top pt-2 mt-2">
                <span class="info-label fw-bold">Tổng tiền phòng:</span>
                <span class="info-value text-dark">{{ number_format($roomTotal, 0, ',', '.') }} VNĐ</span>
            </div>
        </div>

        <div class="confirm-card shadow-sm">
            <h4 class="section-title">Dịch vụ bổ sung đã chọn</h4>

            @if(count($bookingServices) > 0)
                @foreach($bookingServices as $dv)
                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-check2-circle me-1"></i> {{ $dv['ten_dich_vu'] }}
                            <span class="ms-2 badge-qty">Số lượng: {{ $dv['so_luong'] }}</span>
                        </div>
                        <span class="info-value">{{ number_format($dv['thanh_tien'], 0, ',', '.') }} VNĐ</span>
                    </div>
                @endforeach
                <div class="info-row border-top pt-2 mt-2">
                    <span class="info-label fw-bold">Tổng tiền dịch vụ:</span>
                    <span class="info-value">{{ number_format($serviceTotal, 0, ',', '.') }} VNĐ</span>
                </div>
            @else
                <p class="text-muted small mb-0 italic text-center">Quý khách không sử dụng thêm dịch vụ bổ sung.</p>
            @endif
        </div>

        <div class="confirm-card shadow-sm bg-light text-center py-5" style="border: 2px dashed #e9ecef;">
            <h3 class="font-family-serif mb-2" style="font-size: 22px; color: #444;">Tổng chi phí dự kiến</h3>

            <div class="price-breakdown mb-3">
                (Tiền lưu trú: {{ number_format($roomTotal, 0, ',', '.') }}đ + Dịch vụ: {{ number_format($serviceTotal, 0, ',', '.') }}đ)
            </div>

            <div class="fw-bold mb-2" style="font-size: 18px; color: #555;">
                Tổng giá trị đơn hàng: <span class="text-dark">{{ number_format($totalAmount, 0, ',', '.') }} VNĐ</span>
            </div>

            <div class="deposit-box shadow-sm">
                <div class="text-uppercase fw-bold mb-1" style="letter-spacing: 1px; font-size: 14px; color: #2e7d32;">
                    Số tiền cần thanh toán ngay (Đặt cọc 30%)
                </div>
                <div class="deposit-amount">
                    {{ number_format($depositAmount, 0, ',', '.') }} <span style="font-size: 18px;">VNĐ</span>
                </div>
                <div class="small text-muted mt-2">
                    * 70% còn lại sẽ được thanh toán trực tiếp khi quý khách trả phòng.<br>
                    * Lưu ý sau khi xác nhận số tiền cọc sẽ không được hoàn lại nếu quý khách HUỶ ĐẶT PHÒNG hoặc KHÔNG NHẬN PHÒNG sau quá 1h kể từ thời điểm check-in 2.pm hàng ngày.
                </div>
            </div>

            <div class="d-flex justify-content-center gap-3 mt-4">
                <a href="{{ route('booking.services') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 25px; font-size: 13px;">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
                <a href="{{ route('booking.payment') }}" class="btn btn-pay">
                    Thanh toán đặt cọc <i class="bi bi-credit-card-2-front ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
