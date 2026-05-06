@extends('layouts.style')

@section('content')
<style>
    .booking-wrapper { background-color: #faf8f5; min-height: 100vh; padding-top: 120px; padding-bottom: 60px; }
    .pay-container { max-width: 650px; margin: 0 auto; background: white; padding: 40px; border-radius: 4px; border: 1px solid #f1eeea; }
    .price-badge { background: #fdfaf6; padding: 20px; border-radius: 4px; margin-bottom: 35px; border-left: 4px solid #673065; }
    .method-card { border: 1px solid #e8e8e8; padding: 20px; border-radius: 4px; display: flex; align-items: center; cursor: pointer; transition: 0.3s; margin-bottom: 15px; }
    .method-card:hover { border-color: #673065; background-color: #fcf9fc; }
    .btn-submit-pay { background-color: #673065; color: white; width: 100%; border: none; padding: 15px; text-transform: uppercase; font-weight: bold; font-size: 14px; border-radius: 25px; letter-spacing: 1px; margin-top: 25px; }
</style>

<div class="booking-wrapper">
    <div class="container">
        <div class="pay-container shadow-sm">
            <div class="text-center mb-4">
                <p class="text-uppercase text-muted small letter-spacing-2 mb-1">Bước 4: Thanh toán đặt cọc</p>
                <h3 class="font-family-serif">Ký quỹ bảo đảm giữ chỗ</h3>
            </div>

            <div class="price-badge">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tổng tiền phòng & dịch vụ:</span>
                    <span class="fw-bold">{{ number_format($tong_thanh_toan, 0, ',', '.') }} VNĐ</span>
                </div>
                <div class="d-flex justify-content-between border-top pt-2">
                    <span class="fw-bold text-dark">Tiền cọc cần thanh toán (30%):</span>
                    <span class="fw-bold text-danger fs-5">{{ number_format($tien_coc, 0, ',', '.') }} VNĐ</span>
                </div>
            </div>

            <h5 class="small fw-bold mb-3 text-uppercase text-muted">Chọn cổng thanh toán điện tử</h5>

            <form action="{{ route('booking.vnpay_process') }}" method="POST">
                @csrf
                <div class="method-card">
                    <input class="form-check-input me-3" type="radio" name="payment_gateway" value="vnpay" checked id="r_vnpay">
                    <label class="d-flex align-items-center w-100" for="r_vnpay" style="cursor:pointer;">
                        <img src="https://sandbox.vnpayment.vn/paymentv2/Images/brands/logo-vnpay.svg" alt="VNPay" height="30" class="me-3">
                        <div>
                            <strong class="d-block text-dark small">Cổng thanh toán điện tử VNPay</strong>
                            <small class="text-muted" style="font-size:12px;">Hỗ trợ thẻ ATM nội địa, QR Code ngân hàng, thẻ quốc tế Visa/MasterCard</small>
                        </div>
                    </label>
                </div>

                <button type="submit" class="btn-submit-pay shadow-sm">Kết nối đến cổng VNPay</button>
            </form>
        </div>
    </div>
</div>
@endsection
