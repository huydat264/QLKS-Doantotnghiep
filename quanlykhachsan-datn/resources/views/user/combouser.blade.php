@extends('layouts.style')

@section('content')

<style>
    body {
        font-family: 'Arial', sans-serif;
    }

    .reveal {
        opacity: 0;
        transform: translateY(40px);
        transition: all 0.8s ease-out;
    }

    .reveal.active {
        opacity: 1;
        transform: translateY(0);
    }

    .room-card {
        margin-bottom: 60px;
    }

    .room-img-wrapper {
        position: relative;
        overflow: hidden;
        width: 100%;
        height: 320px;
        background-color: #f0f0f0;
    }

    .room-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .room-title {
        font-family: 'Playfair Display', serif;
        font-size: 26px;
        color: #000;
        margin-bottom: 15px;
        font-weight: normal;
    }

    .nav-tabs-custom {
        border-bottom: none;
        margin-bottom: 15px;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        color: #888;
        font-size: 11px;
        font-weight: bold;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 15px 10px 0;
        margin-right: 15px;
        border-bottom: 2px solid transparent;
        background: transparent;
    }

    .nav-tabs-custom .nav-link:hover,
    .nav-tabs-custom .nav-link.active {
        color: #673065;
    }

    .room-desc {
        font-size: 15px;
        color: #555;
        line-height: 1.6;
    }

    .room-specs {
        font-size: 14px;
        color: #555;
        line-height: 1.8;
    }

    .room-specs li::before {
        content: "•";
        margin-right: 8px;
        color: #666;
    }

    .form-check-label {
        font-size: 13px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .booking-bar {
        background-color: #f8f7f5;
        padding: 15px 25px;
        border-radius: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
    }

    .btn-book {
        background-color: #673065;
        color: white;
        border-radius: 25px;
        padding: 10px 30px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        border: none;
        transition: 0.3s;
    }

    .btn-book:hover {
        background-color: #4a2148;
        color: white;
    }

    .filter-dropdown .nav-link {
        font-size: 12px;
        font-weight: bold;
        color: #673065;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        padding: 5px 15px;
        transition: 0.3s;
        border-radius: 4px;
    }

    .filter-dropdown .nav-link:hover {
        background-color: #f0e6ef;
    }

    .dropdown-menu {
        padding: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-radius: 8px;
        min-width: 250px;
    }

    .custom-list-card {
        list-style-type: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .custom-list-card li {
        font-size: 14px;
        color: #555;
        line-height: 2;
    }

    .custom-list-card li::before {
        content: "•";
        margin-right: 8px;
        color: #673065;
        font-weight: bold;
    }

    .read-more-wrapper {
        margin-top: auto;
        padding-top: 20px;
    }

    .read-more {
        color: #673065;
        font-weight: bold;
        font-size: 12px;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .read-more:hover {
        transform: translateX(8px);
        color: #4a2148;
    }

    .accordion-button:not(.collapsed) {
        box-shadow: none;
        background-color: transparent;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,.125);
    }

    .accordion-button::after {
        background-size: 1rem;
    }

    .service-list-left {
        padding-left: 0;
        list-style: none;
    }

    .service-list-left li {
        position: relative;
        padding-left: 20px;
        margin-bottom: 8px;
        font-size: 0.95rem;
        color: #6c757d;
        line-height: 1.8;
    }

    .service-list-left li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 12px;
        width: 10px;
        height: 1px;
        background-color: #999;
    }

    .accordion-button {
        color: #6b3c64 !important;
    }

    .extra-info h2 {
        font-size: 1.5rem;
        letter-spacing: 1px;
    }

    .illustration-wrapper {
        padding: 20px;
        border-left: 1px solid #eee;
    }

    @media (max-width: 991px) {
        .illustration-wrapper { border-left: none; }
    }
</style>

<div class="container mt-5 pt-5">

    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center text-md-start mb-4" style="font-family: 'Playfair Display', serif; font-size: 32px;">
                Khám phá các Gói Combo Ưu Đãi
            </h2>

            <form action="{{ url('/combo') }}" method="GET" class="d-flex flex-wrap gap-3 filter-dropdown">
                <div class="dropdown" data-bs-auto-close="outside">
                    <div class="nav-link dropdown-toggle" data-bs-toggle="dropdown">LOẠI PHÒNG ÁP DỤNG</div>
                    <div class="dropdown-menu">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="loai_phong[]" value="Standard" id="cb-std" {{ (is_array(request('loai_phong')) && in_array('Standard', request('loai_phong'))) ? 'checked' : '' }}>
                            <label class="form-check-label" for="cb-std">Standard</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="loai_phong[]" value="Deluxe" id="cb-dlx" {{ (is_array(request('loai_phong')) && in_array('Deluxe', request('loai_phong'))) ? 'checked' : '' }}>
                            <label class="form-check-label" for="cb-dlx">Deluxe</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="loai_phong[]" value="Suite" id="cb-sui" {{ (is_array(request('loai_phong')) && in_array('Suite', request('loai_phong'))) ? 'checked' : '' }}>
                            <label class="form-check-label" for="cb-sui">Suite</label>
                        </div>
                    </div>
                </div>

                <div class="dropdown" data-bs-auto-close="outside">
                    <div class="nav-link dropdown-toggle" data-bs-toggle="dropdown">MỨC GIÁ COMBO (VND)</div>
                    <div class="dropdown-menu" style="min-width: 300px;">
                        <label class="form-label text-muted small d-flex justify-content-between">
                            <span>Giá tối đa:</span>
                            <span id="price-val" class="fw-bold" style="color:#673065;">
                                {{ number_format(request('gia_max', 10000000), 0, ',', '.') }} VNĐ
                            </span>
                        </label>
                        <input type="range" name="gia_max" class="form-range" min="1000000" max="20000000" step="500000" id="price-slider" value="{{ request('gia_max', 10000000) }}" oninput="document.getElementById('price-val').innerText = parseInt(this.value).toLocaleString('vi-VN') + ' VNĐ'">
                    </div>
                </div>

                <button type="submit" class="btn btn-sm text-white px-4" style="background: #673065; border-radius: 20px;">ÁP DỤNG LỌC</button>
                <a href="{{ url('/combo') }}" class="btn btn-sm btn-light px-3" style="border-radius: 20px;">Xóa lọc</a>
            </form>
        </div>
    </div>

    @foreach($combos as $combo)
    <div class="row room-card reveal">
        <div class="col-lg-5 mb-3 mb-lg-0">
            <div class="room-img-wrapper">
                <img src="{{ $combo->hinh_anh }}" alt="{{ $combo->ten_combo }}" onerror="this.src='https://images.unsplash.com/photo-1582719478250-c89cae4dc85b';">
            </div>
        </div>

        <div class="col-lg-7 d-flex flex-column">
            <div class="row flex-grow-1">
                <div class="col-md-7 pe-md-4 d-flex flex-column">
                    <h3 class="room-title">{{ $combo->ten_combo }}</h3>

                    <ul class="nav nav-tabs-custom" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#desc-{{ $combo->id_combo }}">MÔ TẢ COMBO</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#info-{{ $combo->id_combo }}">CHI TIẾT GÓI</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="desc-{{ $combo->id_combo }}" class="tab-pane active">
                            <p class="room-desc">{{ Str::limit($combo->mo_ta, 150, '...') }}</p>
                        </div>
                        <div id="info-{{ $combo->id_combo }}" class="tab-pane fade">
                            <ul class="custom-list-card">
                                <li>Áp dụng cho hạng phòng: {{ $combo->loai_phong_ap_dung }}</li>
                                <li>Lưu trú trọn gói: {{ $combo->so_dem_luu_tru }} đêm</li>
                                <li>Bao gồm các dịch vụ đi kèm trong gói</li>
                                <li>Giá phòng định mức: {{ number_format($combo->gia_phong_dinh_muc, 0, ',', '.') }} VNĐ</li>
                            </ul>
                        </div>
                    </div>

                    <div class="read-more-wrapper">
                        <a href="{{ url('/combo/' . $combo->id_combo) }}" class="read-more">ĐỌC THÊM ></a>
                    </div>
                </div>

                <div class="col-md-5 mt-4 mt-md-0 border-start ps-md-4">
                    <ul class="list-unstyled room-specs">
                        <li><strong>Loại phòng:</strong> {{ $combo->loai_phong_ap_dung }}</li>
                        <li><strong>Số đêm lưu trú:</strong> {{ $combo->so_dem_luu_tru }} đêm</li>
                        <li>Tiết kiệm hơn so với đặt phòng lẻ</li>
                    </ul>
                </div>
            </div>

            <div class="booking-bar mt-4">
                <span class="text-muted" style="font-size: 14px;">Giá trọn gói: {{ number_format($combo->gia_combo, 0, ',', '.') }} VNĐ</span>
                @auth
    <a href="{{ route('booking.check', ['type' => 'phong', 'id' => $phong->id_phong]) }}"
       class="btn btn-book text-decoration-none">
       ĐẶT PHÒNG
    </a>
@else
    <a href="javascript:void(0)"
       class="btn btn-book text-decoration-none"
       data-bs-toggle="modal"
       data-bs-target="#loginModal"> ĐẶT PHÒNG
    </a>
@endauth
            </div>
        </div>
    </div>
    @endforeach

</div>

<section class="customer-services py-5 mt-5" style="background-color: #faf8f5;">
    <div class="container reveal">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="mb-4 text-uppercase fw-light" style="color: #4a4a4a; letter-spacing: 2px; font-family: 'Playfair Display', serif;">Dịch vụ khách hàng</h2>
                <ul class="service-list-left text-muted">
                    <li>Dịch vụ đưa đón sân bay</li>
                    <li>Trung tâm thể hình đầy đủ tiện nghi</li>
                    <li>Hồ bơi trung tâm rộng rãi với dịch vụ phục vụ tại hồ</li>
                    <li>Six Senses Spa Côn Đảo và Yoga Pavilion</li>
                    <li>Cửa hàng quà lưu niệm Sense of Boutique</li>
                    <li>Câu lạc bộ trẻ em Sense of Laughter, miễn phí cho các vị khách nhí từ 4 đến 12 tuổi</li>
                    <li>Những bất ngờ thú vị! Hãy để đội ngũ Đặt phòng hoặc Quản gia (GEM) giúp quý khách tạo nên những khoảnh khắc bất ngờ.</li>
                    <li>Quý khách có thể tận hưởng đa dạng các hoạt động ngay trong khuôn viên khu nghỉ dưỡng.</li>
                    <li>Xe đạp miễn phí</li>
                    <li>Các môn thể thao dưới nước không dùng động cơ</li>
                    <li>Dịch vụ ẩm thực tại biệt thự</li>
                    <li>Hai quầy bar – Elephant Bar và Splash Bar</li>
                    <li>Hai nhà hàng – By the Beach và Vietnamese by the Market</li>
                    <li>Deli'cious mang đến những lựa chọn ẩm thực phong phú.</li>
                </ul>
            </div>
            <div class="col-lg-4 d-none d-lg-flex justify-content-center">
                <div class="illustration-wrapper">
                    <img src="https://www.sixsenses.com/media/8254/icon-gem.png" alt="icon" style="width: 150px; opacity: 0.6; filter: grayscale(1);">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="extra-info py-5">
    <div class="container reveal">
        <h2 class="mb-4 fw-light border-bottom pb-3" style="color: #4a4a4a;">Xem thêm thông tin</h2>

        <div class="accordion accordion-flush" id="accordionInfo">
            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="flush-headingOne">
                    <button class="accordion-button collapsed text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                        Thông tin quan trọng dành cho khách
                    </button>
                </h2>
                <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionInfo">
                    <div class="accordion-body text-muted" style="font-size: 0.9rem;">
                        <div class="mb-3">
                            <strong class="d-block text-dark">Nguồn điện</strong>
                            Khu nghỉ dưỡng sử dụng dòng điện xoay chiều 220 - 240 V. Mỗi phòng đều được trang bị ổ cắm chuyển đổi.
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong class="d-block text-dark">Giờ nhận phòng</strong>
                                Biệt thự tiêu chuẩn: 14:00<br>Biệt thự nhiều phòng ngủ: 15:00
                            </div>
                            <div class="col-md-6">
                                <strong class="d-block text-dark">Giờ trả phòng</strong>
                                Biệt thự tiêu chuẩn: 12:00 trưa<br>Biệt thự nhiều phòng ngủ: 12:00 trưa
                            </div>
                        </div>
                        <div class="mb-3">
                            <strong class="d-block text-dark">Chính sách hủy phòng</strong>
                            Trong trường hợp trả phòng sớm hơn dự kiến, phí phòng vẫn được áp dụng theo thông tin đặt phòng ban đầu.
                        </div>
                        <div class="mb-3">
                            <strong class="d-block text-dark">Thẻ tín dụng</strong>
                            Visa, MasterCard và American Express.
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="flush-headingTwo">
                    <button class="accordion-button collapsed text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                        Gói dành cho gia đình
                    </button>
                </h2>
                <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionInfo">
                    <div class="accordion-body text-muted">
                        Các thông tin về ưu đãi và dịch vụ dành riêng cho gia đình sẽ được cập nhật tại đây.
                    </div>
                </div>
            </div>

            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="flush-headingThree">
                    <button class="accordion-button collapsed text-uppercase fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                        Nhận phòng sớm và trả phòng muộn
                    </button>
                </h2>
                <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionInfo">
                    <div class="accordion-body text-muted">
                        Tùy vào tình trạng phòng trống, chúng tôi sẽ nỗ lực hỗ trợ quý khách nhận phòng sớm hoặc trả phòng muộn.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function reveal() {
        var reveals = document.querySelectorAll(".reveal");
        for (var i = 0; i < reveals.length; i++) {
            var windowHeight = window.innerHeight;
            var elementTop = reveals[i].getBoundingClientRect().top;
            var elementVisible = 100;

            if (elementTop < windowHeight - elementVisible) {
                reveals[i].classList.add("active");
            }
        }
    }
    window.addEventListener("scroll", reveal);
    window.addEventListener("load", reveal);
</script>

@endsection
@include('user.dangky')
@include('user.dangnhap')
