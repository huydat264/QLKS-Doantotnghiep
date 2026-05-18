@extends('layouts.style')

@section('content')
<style>
    .mock-wrapper { background-color: #f4f7f6; min-height: 100vh; padding-top: 130px; padding-bottom: 60px; }
    .qr-box { max-width: 500px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; }
    .vn-pay-logo { width: 120px; margin-bottom: 20px; }
    .amount-text { color: #005baa; font-size: 32px; font-weight: 900; margin: 15px 0; }
    .btn-mock-success { background-color: #005baa; color: white; padding: 14px 40px; border-radius: 25px; border: none; font-weight: bold; text-transform: uppercase; width: 100%; display: block; text-decoration: none; margin-top: 25px; }
    .btn-mock-success:hover { background-color: #004282; color: white; }
    .btn-mock-cancel { color: #d9534f; display: inline-block; margin-top: 20px; text-decoration: underline; font-size: 14px; }
</style>

<div class="mock-wrapper">
    <div class="container">
        <div class="qr-box border-top border-4 border-primary">
            <img src="https://vnpay.vn/wp-content/uploads/2020/07/Logo-VNPAYQR-update.png" alt="VNPay Logo" class="vn-pay-logo">

            <h5 class="text-muted mb-1">Môi trường giả lập (Sandbox)</h5>
            <p class="small text-muted mb-4">Mã đơn hàng: <strong>{{ $fake_TxnRef }}</strong></p>

            <h6 class="fw-bold">Số tiền cần thanh toán</h6>
            <div class="amount-text">{{ number_format($tien_coc, 0, ',', '.') }} VNĐ</div>

           <div class="p-3 border rounded d-inline-block bg-light mb-3">
            <img src="https://img.vietqr.io/image/MB-0358414532-qr_only.png?amount={{ $tien_coc }}&addInfo={{ $fake_TxnRef }}&accountName=Kim%20Boutique%20Gialap" alt="VietQR" style="width: 220px; height: 220px;">
           </div>

            <p class="small text-muted px-4">Sử dụng ứng dụng Ngân hàng hoặc Ví điện tử để quét mã QR bên trên.</p>

            <a href="{!! $finalReturnUrl !!}" class="btn-mock-success shadow-sm">Giả lập:Xác nhận Thanh toán thành công</a>

            <a href="{{ route('booking.services') }}" class="btn-mock-cancel">Hủy bỏ giao dịch</a>
        </div>
    </div>
</div>
@endsection
