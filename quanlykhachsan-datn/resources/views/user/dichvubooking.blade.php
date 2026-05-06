@extends('layouts.style')

@section('content')
<style>
    .booking-wrapper { background-color: #faf8f5; min-height: 100vh; padding-top: 120px; padding-bottom: 60px; }
    .step-header { font-family: 'Playfair Display', serif; color: #222; font-size: 28px; }
    .date-box { background: white; padding: 25px; border-radius: 4px; border: 1px solid #f1eeea; margin-bottom: 30px; }
    .accordion-button:not(.collapsed) { background-color: transparent; color: #673065; box-shadow: none; }
    .accordion-button { font-family: 'Playfair Display', serif; font-size: 18px; color: #444; padding: 20px 0; }
    .accordion-button:focus { box-shadow: none; }
    .service-item { border-bottom: 1px solid #f5f5f5; padding: 15px 0; }
    .summary-sticky { background: white; padding: 30px; border-radius: 4px; border: 1px solid #f1eeea; position: sticky; top: 110px; }
    .btn-next { background-color: #673065; color: white; border-radius: 25px; padding: 12px 35px; border: none; text-transform: uppercase; font-size: 13px; font-weight: bold; width: 100%; transition: 0.3s; }
    .btn-next:hover { background-color: #4a2148; }
    .form-check-input:checked { background-color: #673065; border-color: #673065; }
</style>
<div class="booking-wrapper">
    <div class="container">
        @if($errors->any())
            <div class="alert alert-danger mb-4">{{ $errors->first() }}</div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <p class="text-uppercase text-muted small letter-spacing-2 mb-2">Bước 2: Lựa chọn kỳ nghỉ</p>
                <h2 class="step-header mb-4">Chọn ngày lưu trú và dịch vụ bổ trợ</h2>

                <form action="{{ route('booking.save_services') }}" method="POST">
                    @csrf
                    <div class="date-box shadow-sm">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">NGÀY ĐẾN (CHECK-IN)</label>
                                <input type="date" name="ngay_nhan" class="form-control border-0 mt-1" required min="{{ date('Y-m-to') }}">
                            </div>
                            <div class="col-md-6 border-start">
                                <label class="form-label fw-bold text-muted small">NGÀY ĐI (CHECK-OUT)</label>
                                <input type="date" name="ngay_tra" class="form-control border-0 mt-1" required>
                            </div>
                        </div>
                    </div>

                    <h4 class="font-family-serif mt-5 mb-3 text-uppercase small text-muted" style="letter-spacing: 1px;">Nâng tầm trải nghiệm của bạn</h4>
                    <div class="accordion accordion-flush bg-white px-4 shadow-sm rounded" id="servicesAccordion">

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#expCollapse">
                                    TRẢI NGHIỆM ĐỊA PHƯƠNG & KHÁM PHÁ
                                </button>
                            </h2>
                            <div id="expCollapse" class="accordion-collapse collapse" data-bs-parent="#servicesAccordion">
                                <div class="accordion-body">
                                    @foreach($dichVus->where('loai_dich_vu', 'Trải nghiệm') as $dv)
                                    <div class="d-flex justify-content-between align-items-center service-item">
                                        <div>
                                            <h6 class="mb-1 text-dark">{{ $dv->ten_dich_vu }}</h6>
                                            <small class="text-muted">+ {{ number_format($dv->gia_dich_vu, 0, ',', '.') }} VNĐ / lượt</small>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="dich_vu[]" value="{{ $dv->id_dichvu }}">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#diningCollapse">
                                    TINH HOA ẨM THỰC ĐẶC SẮC
                                </button>
                            </h2>
                            <div id="diningCollapse" class="accordion-collapse collapse" data-bs-parent="#servicesAccordion">
                                <div class="accordion-body">
                                    @foreach($dichVus->where('loai_dich_vu', 'Ẩm thực') as $dv)
                                    <div class="d-flex justify-content-between align-items-center service-item">
                                        <div>
                                            <h6 class="mb-1 text-dark">{{ $dv->ten_dich_vu }}</h6>
                                            <small class="text-muted">+ {{ number_format($dv->gia_dich_vu, 0, ',', '.') }} VNĐ</small>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="dich_vu[]" value="{{ $dv->id_dichvu }}">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#spaCollapse">
                                    WELLNESS & TRỊ LIỆU SPA CAO CẤP
                                </button>
                            </h2>
                            <div id="spaCollapse" class="accordion-collapse collapse" data-bs-parent="#servicesAccordion">
                                <div class="accordion-body">
                                    @foreach($dichVus->where('loai_dich_vu', 'Spa') as $dv)
                                    <div class="d-flex justify-content-between align-items-center service-item">
                                        <div>
                                            <h6 class="mb-1 text-dark">{{ $dv->ten_dich_vu }}</h6>
                                            <small class="text-muted">+ {{ number_format($dv->gia_dich_vu, 0, ',', '.') }} VNĐ</small>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="dich_vu[]" value="{{ $dv->id_dichvu }}">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="mt-5 text-end">
                        <button type="submit" class="btn-next shadow-sm" style="max-width: 250px;">Tiếp tục xác nhận</button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="summary-sticky shadow-sm">
                    <h5 class="font-family-serif border-bottom pb-3 mb-3 text-uppercase fs-6 text-muted" style="letter-spacing: 1px;">Thông tin đặt phòng</h5>
                    <h3 class="font-family-serif mb-4" style="color:#673065;">{{ $item->ten_phong ?? $item->ten_combo }}</h3>

                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Hạng lựa chọn:</span>
                        <span class="text-dark fw-bold">{{ $item->loai_phong ?? $item->loai_phong_ap_dung }}</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-4">
                        <span>Giá cơ sở:</span>
                        <span class="text-dark fw-bold">{{ number_format($item->gia_phong ?? $item->gia_combo, 0, ',', '.') }} VNĐ</span>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-uppercase small fw-bold">Giá gói tạm tính:</span>
                            <span class="fs-5 fw-bold" style="color: #673065;">{{ number_format($item->gia_phong ?? $item->gia_combo, 0, ',', '.') }} VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
