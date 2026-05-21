@extends('layouts.style')

@section('content')
<style>
    .booking-wrapper { background-color: #faf8f5; min-height: 100vh; padding-top: 120px; padding-bottom: 60px; }
    .date-box { background: white; padding: 25px; border-radius: 4px; border: 1px solid #f1eeea; margin-bottom: 30px; }
    .summary-sticky { background: white; padding: 30px; border-radius: 4px; border: 1px solid #f1eeea; position: sticky; top: 110px; }
    .btn-next { background-color: #673065; color: white; border-radius: 25px; padding: 12px 35px; border: none; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; }
    .category-title { font-family: 'Playfair Display', serif; color: #673065; border-left: 4px solid #673065; padding-left: 15px; margin: 40px 0 20px 0; font-size: 1.25rem; text-transform: uppercase; letter-spacing: 1px; }
    .qty-input-wrapper { display: none; width: 100px; }
    .service-item { transition: 0.3s; border-radius: 4px; }
    .service-item:hover { background-color: #fff; }
</style>

<div class="booking-wrapper">
    <div class="container">
        <form action="{{ route('booking.save_services') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-5">
                        <span class="text-uppercase small text-muted fw-bold" style="letter-spacing: 2px;">Bước 2/3</span>
                        <h2 class="font-family-serif mb-4">Chọn ngày lưu trú và dịch vụ bổ trợ</h2>
                    </div>

                    <div class="date-box shadow-sm">
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Ngày đến</label>
                                <input type="date" name="ngay_nhan" id="ngay_den" class="form-control border-0 p-0 fs-5 fw-bold"
                                       value="{{ $defaultCheckin }}" min="{{ $defaultCheckin }}">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Ngày đi</label>
                                <input type="date" name="ngay_tra" id="ngay_di" class="form-control border-0 p-0 fs-5 fw-bold"
                                       value="{{ $defaultCheckout }}" min="{{ $defaultCheckout }}">
                            </div>
                        </div>
                    </div>

                    <h4 class="category-title">Dịch vụ lưu trú</h4>
                    <div class="accordion accordion-flush mb-4">
                        @foreach($dvLuuTru as $dv)
                        <div class="accordion-item border-bottom py-2" style="background: transparent;">
                            <div class="d-flex align-items-center justify-content-between w-100 py-3 service-item">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <input class="form-check-input service-checkbox me-3" type="checkbox"
                                           name="dich_vu[]" value="{{ $dv->id_dichvu }}"
                                           data-name="{{ $dv->ten_dich_vu }}" data-price="{{ $dv->gia }}"
                                           style="width: 22px; height: 22px; cursor: pointer;">
                                    <div>
                                        <span class="fw-bold text-dark text-uppercase small d-block">{{ $dv->ten_dich_vu }}</span>
                                        <span class="text-muted small">{{ number_format($dv->gia, 0, ',', '.') }} VND</span>
                                    </div>
                                </div>
                                <div class="qty-input-wrapper">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0">SL:</span>
                                        <input type="number" name="so_luong[{{ $dv->id_dichvu }}]"
                                               class="form-control qty-input border-start-0"
                                               value="1" min="1" style="max-width: 60px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <h4 class="category-title">Dịch vụ ngoại lệ & Tiện ích</h4>
                    <div class="accordion accordion-flush">
                        @foreach($dvNgoaiLe as $dv)
                        <div class="accordion-item border-bottom py-2" style="background: transparent;">
                            <div class="d-flex align-items-center justify-content-between w-100 py-3 service-item">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <input class="form-check-input service-checkbox me-3" type="checkbox"
                                           name="dich_vu[]" value="{{ $dv->id_dichvu }}"
                                           data-name="{{ $dv->ten_dich_vu }}" data-price="{{ $dv->gia }}"
                                           style="width: 22px; height: 22px; cursor: pointer;">
                                    <div>
                                        <span class="fw-bold text-dark text-uppercase small d-block">{{ $dv->ten_dich_vu }}</span>
                                        <span class="text-muted small">{{ number_format($dv->gia, 0, ',', '.') }} VND</span>
                                    </div>
                                </div>
                                <div class="qty-input-wrapper">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0">SL:</span>
                                        <input type="number" name="so_luong[{{ $dv->id_dichvu }}]"
                                               class="form-control qty-input border-start-0"
                                               value="1" min="1" style="max-width: 60px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="summary-sticky shadow-sm">
                        <h5 class="font-family-serif border-bottom pb-3 mb-3 text-uppercase fs-6 text-muted">Thông tin đặt phòng</h5>
                        <h3 class="font-family-serif mb-4" style="color:#673065;">{{ $item->ten_phong ?? $item->ten_combo }}</h3>

                        <div class="mb-3 small">
                            <p class="mb-1"><strong>Ngày đến:</strong> <span id="display-checkin">{{ date('d/m/Y', strtotime($defaultCheckin)) }}</span></p>
                            <p class="mb-1"><strong>Ngày đi:</strong> <span id="display-checkout">{{ date('d/m/Y', strtotime($defaultCheckout)) }}</span></p>
                            <p class="mb-1"><strong>Số đêm:</strong> <span id="display-nights">1</span> đêm</p>
                        </div>

                        <div id="list-selected-services" class="border-top pt-2 mt-2 small text-muted">
                            </div>

                        <div class="border-top pt-3 mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-uppercase small fw-bold">Tổng tiền tạm tính:</span>
                                <span class="fs-5 fw-bold" style="color: #673065;">
                                    <span id="total-price-display">{{ number_format($item->gia_hien_tai ?? $item->gia_combo, 0, ',', '.') }}</span> VNĐ
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-next w-100 mt-4">Tiếp tục thanh toán</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkinInput = document.getElementById('ngay_den');
    const checkoutInput = document.getElementById('ngay_di');
    const displayCheckin = document.getElementById('display-checkin');
    const displayCheckout = document.getElementById('display-checkout');
    const displayNights = document.getElementById('display-nights');

    const checkboxes = document.querySelectorAll('.service-checkbox');
    const serviceList = document.getElementById('list-selected-services');
    const totalDisplay = document.getElementById('total-price-display');
    const basePrice = parseInt("{{ $item->gia_hien_tai ?? $item->gia_combo }}");

    function updateSummary() {
        const checkin = new Date(checkinInput.value);
        let checkout = new Date(checkoutInput.value);

        // Chặn ngày đi trước ngày đến
        if (checkout <= checkin) {
            const nextDay = new Date(checkin);
            nextDay.setDate(nextDay.getDate() + 1);
            checkoutInput.value = nextDay.toISOString().split('T')[0];
            checkout = nextDay;
        }

        // Cập nhật min cho ngày đi để không chọn được ngày cũ
        const minCheckout = new Date(checkin);
        minCheckout.setDate(minCheckout.getDate() + 1);
        checkoutInput.min = minCheckout.toISOString().split('T')[0];

        const diffTime = Math.abs(checkout - checkin);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        displayCheckin.innerText = checkin.toLocaleDateString('vi-VN');
        displayCheckout.innerText = checkout.toLocaleDateString('vi-VN');
        displayNights.innerText = diffDays;

        calculateTotal(diffDays);
    }

    function calculateTotal(nights) {
        let extraPrice = 0;
        let html = '';

        checkboxes.forEach(item => {
            const row = item.closest('.service-item');
            const qtyWrapper = row.querySelector('.qty-input-wrapper');
            const qtyInput = row.querySelector('.qty-input');

            if(item.checked) {
                qtyWrapper.style.display = 'block';
                const name = item.getAttribute('data-name');
                const price = parseInt(item.getAttribute('data-price'));
                const qty = parseInt(qtyInput.value) || 1;

                const subTotal = price * qty;
                extraPrice += subTotal;
                html += `<div class="d-flex justify-content-between mb-1">
                            <span>+ ${name} (x${qty})</span>
                            <span>${subTotal.toLocaleString('vi-VN')} VNĐ</span>
                         </div>`;
            } else {
                qtyWrapper.style.display = 'none';
            }
        });

        serviceList.innerHTML = html;
        const finalTotal = (basePrice * nights) + extraPrice;
        totalDisplay.innerText = finalTotal.toLocaleString('vi-VN');
    }

    // Gắn sự kiện
    checkinInput.addEventListener('change', updateSummary);
    checkoutInput.addEventListener('change', updateSummary);

    checkboxes.forEach(box => {
        box.addEventListener('change', updateSummary);
        const row = box.closest('.service-item');
        const qtyInput = row.querySelector('.qty-input');
        qtyInput.addEventListener('input', updateSummary);
    });

    updateSummary(); // Chạy lần đầu
});
</script>
@endsection
