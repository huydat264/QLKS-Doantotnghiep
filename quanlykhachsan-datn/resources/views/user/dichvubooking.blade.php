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
    /* Tùy chỉnh giao diện Flatpickr để hợp với tông màu tím của bạn */
    .flatpickr-day.disabled { color: #ccc !important; text-decoration: line-through; }
    .flatpickr-day.selected { background: #673065 !important; border-color: #673065 !important; }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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
                                <input type="text" name="ngay_nhan" id="ngay_den" class="form-control border-0 p-0 fs-5 fw-bold bg-white"
                                       value="{{ $defaultCheckin }}" min="{{ $defaultCheckin }}">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-uppercase fw-bold text-muted mb-2 d-block">Ngày đi</label>
                                <input type="text" name="ngay_tra" id="ngay_di" class="form-control border-0 p-0 fs-5 fw-bold bg-white"
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
                    <h4 class="category-title" style="margin-top: 0;">Ưu đãi & Voucher</h4>
                    <div class="card border-0 shadow-sm p-3 mb-4 bg-white rounded-3">
                        @if(isset($vouchers) && $vouchers->isNotEmpty())
                            <div class="d-flex flex-column gap-2">
                                <div class="p-2 border rounded service-item bg-light">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input voucher-radio" type="radio" name="id_voucher" id="voucher_none" value="" checked data-type="NONE" data-discount="0" data-percent="0" data-code="">
                                        <label class="form-check-label fw-bold text-secondary mb-0 ms-1" for="voucher_none">
                                            Không sử dụng mã giảm giá
                                        </label>
                                    </div>
                                </div>

                                @foreach($vouchers as $vc)
                                    @php
                                        $isCombo = session('booking_type') == 'combo';
                                        $isDisabledPhong = ($vc->loai_voucher == 'PHONG' && $isCombo);
                                    @endphp
                                    <div class="p-2 border rounded service-item voucher-wrapper {{ $isDisabledPhong ? 'opacity-50 text-muted' : '' }}" id="wrapper_vc_{{ $vc->id_voucher }}" style="background: #fff;">
                                        <div class="form-check mb-0 d-flex align-items-start justify-content-between">
                                            <div class="d-flex align-items-start">
                                                <input class="form-check-input voucher-radio" type="radio" name="id_voucher" id="vc_{{ $vc->id_voucher }}" value="{{ $vc->id_voucher }}"
                                                    {{ $isDisabledPhong ? 'disabled' : '' }}
                                                    data-type="{{ $vc->loai_voucher }}"
                                                    data-discount="{{ $vc->muc_giam }}"
                                                    data-percent="{{ $vc->is_percent }}"
                                                    data-code="{{ $vc->ma_code }}">

                                                <label class="form-check-label mb-0 ms-2" for="vc_{{ $vc->id_voucher }}">
                                                    <span class="badge bg-danger-subtle text-danger fw-bold border border-danger border-dashed px-2 py-1 mb-1">{{ $vc->ma_code }}</span>
                                                    <div class="small text-muted mb-1">
                                                        <strong class="text-dark">
                                                            @if($vc->loai_voucher == 'PHONG') Voucher giảm giá tiền phòng
                                                            @elseif($vc->loai_voucher == 'DICH_VU') Voucher giảm giá tiền dịch vụ
                                                            @else Voucher giảm giá tổng hóa đơn
                                                            @endif
                                                        </strong>
                                                    </div>
                                                    @if($isDisabledPhong)
                                                        <div class="small text-danger fw-semibold mt-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Không áp dụng cho Combo</div>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="text-end text-danger fw-bold pt-1 small">
                                                -{{ number_format($vc->muc_giam, 0, ',', '.') }}{{ $vc->is_percent ? '%' : ' đ' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Chưa có mã giảm giá nào.</p>
                        @endif
                    </div>

                    <div class="summary-sticky shadow-sm">
                        <h5 class="font-family-serif border-bottom pb-3 mb-3 text-uppercase fs-6 text-muted">Thông tin đặt phòng</h5>
                        <h3 class="font-family-serif mb-4" style="color:#673065;">{{ $item->ten_phong ?? $item->ten_combo }}</h3>

                        <div class="mb-3 small">
                            <p class="mb-1"><strong>Ngày đến:</strong> <span id="display-checkin">{{ date('d/m/Y', strtotime($defaultCheckin)) }}</span></p>
                            <p class="mb-1"><strong>Ngày đi:</strong> <span id="display-checkout">{{ date('d/m/Y', strtotime($defaultCheckout)) }}</span></p>
                            <p class="mb-1"><strong>Số đêm:</strong> <span id="display-nights">1</span> đêm</p>
                         </div>
                        <div class="d-flex justify-content-between mb-2 text-danger fw-semibold" id="row_discount_display" style="display: none !important;">
                         <span>Mã giảm giá (<span id="txt_applied_code"></span>):</span>
                         <span>-<span id="val_discount_display">0</span> VNĐ</span>
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

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkinInput = document.getElementById('ngay_den');
    const checkoutInput = document.getElementById('ngay_di');
    const displayCheckin = document.getElementById('display-checkin');
    const displayCheckout = document.getElementById('display-checkout');
    const displayNights = document.getElementById('display-nights');

    const checkboxes = document.querySelectorAll('.service-checkbox');
    const serviceList = document.getElementById('list-selected-services'); // Đảm bảo ID này tồn tại
    const totalDisplay = document.getElementById('total-price-display');
    const basePrice = parseInt("{{ $item->gia_hien_tai ?? $item->gia_combo }}");

    // Danh sách ngày bị khóa từ Server
    const disabledDates = @json($disabledDates);
    const isCombo = "{{ $type === 'combo' ? 'true' : 'false' }}" === "true";
    const comboNights = parseInt("{{ $item->so_dem_luu_tru ?? 0 }}");

    // Khởi tạo Flatpickr cho Ngày đến
    const fpCheckin = flatpickr(checkinInput, {
        locale: "vn",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        minDate: "today",
        disable: disabledDates,
        onChange: function(selectedDates, dateStr) {
            if (isCombo && selectedDates.length > 0) {
                // Tự động tính ngày trả dựa trên số đêm của combo
                const checkinDate = selectedDates[0];
                const checkoutDate = new Date(checkinDate);
                checkoutDate.setDate(checkinDate.getDate() + comboNights);
                fpCheckout.setDate(checkoutDate);
            } else {
                fpCheckout.set("minDate", dateStr); // Ngày đi không được trước ngày đến
            }
            updateSummary();
        }
    });

    // Khởi tạo Flatpickr cho Ngày đi
    const fpCheckout = flatpickr(checkoutInput, {
        locale: "vn",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        minDate: "{{ $defaultCheckout }}",
        disable: disabledDates,
        clickOpens: !isCombo, // Khóa không cho mở lịch nếu là combo
        onChange: function() {
            updateSummary();
        }
    });

    // Nếu là combo, làm mờ ô ngày đi để khách biết là cố định
    if (isCombo) {
        checkoutInput.parentElement.style.opacity = '0.7';
        checkoutInput.style.cursor = 'not-allowed';
    }

    function updateSummary() {
        if (!checkinInput.value || !checkoutInput.value) return;

        const checkin = new Date(checkinInput.value);
        const checkout = new Date(checkoutInput.value);

        const diffTime = Math.abs(checkout - checkin);
        const nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (isNaN(nights) || nights <= 0) return;

        displayCheckin.innerText = checkin.toLocaleDateString('vi-VN');
        displayCheckout.innerText = checkout.toLocaleDateString('vi-VN');
        displayNights.innerText = nights;

        // --- LOGIC TÍNH DỊCH VỤ ---
        let extraPrice = 0;
        let html = '';

        checkboxes.forEach(box => {
            const row = box.closest('.service-item');
            const qtyWrapper = row.querySelector('.qty-input-wrapper');
            const qtyInput = row.querySelector('.qty-input');

            if(box.checked) {
                qtyWrapper.style.display = 'block';
                const name = box.getAttribute('data-name');
                const price = parseInt(box.getAttribute('data-price'));
                const qty = parseInt(qtyInput.value) || 1;

                const subTotal = price * qty;
                extraPrice += subTotal;
                html += `<div class="d-flex justify-content-between mb-1">
                            <span>+ ${name} (x${qty})</span>
                            <span>${subTotal.toLocaleString('vi-VN')} VNĐ</span>
                         </div>`;
            } else {
                if(qtyWrapper) qtyWrapper.style.display = 'none';
            }
        });
        if(serviceList) serviceList.innerHTML = html;

        // --- BỔ SUNG LOGIC VOUCHER ---
        // Nếu là combo thì lấy giá trọn gói (basePrice), nếu là phòng thì nhân với số đêm
        let roomPriceTotal = isCombo ? basePrice : (basePrice * nights);
        let servicePriceTotal = extraPrice;
        let totalBeforeDiscount = roomPriceTotal + servicePriceTotal;
        let discountAmount = 0;

        // 1. Kiểm tra trạng thái Voucher DICH_VU
        const voucherRadios = document.querySelectorAll('.voucher-radio');
        voucherRadios.forEach(radio => {
            if (radio.getAttribute('data-type') === 'DICH_VU') {
                const wrapper = document.getElementById('wrapper_vc_' + radio.value);
                if (servicePriceTotal <= 0) {
                    radio.disabled = true;
                    if (wrapper) {
                        wrapper.classList.add('opacity-50', 'text-muted');
                        wrapper.style.backgroundColor = '#f8f9fa';
                    }
                    if (radio.checked) {
                        document.getElementById('voucher_none').checked = true;
                    }
                } else {
                    radio.disabled = false;
                    if (wrapper) {
                        wrapper.classList.remove('opacity-50', 'text-muted');
                        wrapper.style.backgroundColor = '#fff';
                    }
                }
            }
        });

        // 2. Tính toán giá trị giảm trừ
        const activeVoucher = document.querySelector('.voucher-radio:checked');
        const rowDiscount = document.getElementById('row_discount_display');
        const txtCode = document.getElementById('txt_applied_code');
        const valDiscount = document.getElementById('val_discount_display');

        if (activeVoucher && activeVoucher.value !== '') {
            const vcType = activeVoucher.getAttribute('data-type');
            const vcDiscount = parseFloat(activeVoucher.getAttribute('data-discount')) || 0;
            const vcIsPercent = parseInt(activeVoucher.getAttribute('data-percent')) || 0;
            const vcCode = activeVoucher.getAttribute('data-code');

            if (vcType === 'PHONG') {
                discountAmount = (vcIsPercent === 1) ? roomPriceTotal * (vcDiscount / 100) : vcDiscount;
                if (discountAmount > roomPriceTotal) discountAmount = roomPriceTotal;
            }
            else if (vcType === 'DICH_VU') {
                if (servicePriceTotal > 0) {
                    discountAmount = (vcIsPercent === 1) ? servicePriceTotal * (vcDiscount / 100) : vcDiscount;
                    if (discountAmount > servicePriceTotal) discountAmount = servicePriceTotal;
                }
            }
            else if (vcType === 'ALL') {
                discountAmount = (vcIsPercent === 1) ? totalBeforeDiscount * (vcDiscount / 100) : vcDiscount;
                if (discountAmount > totalBeforeDiscount) discountAmount = totalBeforeDiscount;
            }

            if (discountAmount > 0) {
                if (rowDiscount) rowDiscount.style.setProperty('display', 'flex', 'important');
                if (txtCode) txtCode.innerText = vcCode;
                if (valDiscount) valDiscount.innerText = discountAmount.toLocaleString('vi-VN');
            } else {
                if (rowDiscount) rowDiscount.style.setProperty('display', 'none', 'important');
            }
        } else {
            if (rowDiscount) rowDiscount.style.setProperty('display', 'none', 'important');
        }

        // 3. Kết xuất tổng tiền cuối cùng
        const finalTotal = totalBeforeDiscount - discountAmount;
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

    // Gắn sự kiện radio voucher
    document.querySelectorAll('.voucher-radio').forEach(radio => {
        radio.addEventListener('change', updateSummary);
    });

    updateSummary(); // Chạy lần đầu
});
</script>
@endsection
