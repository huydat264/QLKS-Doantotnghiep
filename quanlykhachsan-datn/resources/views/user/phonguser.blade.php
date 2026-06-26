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

    .btn-book.disabled {
        background-color: #ccc;
        color: #666;
        cursor: not-allowed;
        pointer-events: none;
        text-decoration: none;
    }

    .btn-book.disabled:hover {
        background-color: #ccc;
        color: #666;
        transform: none;
        box-shadow: none;
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

    .filter-panel {
        background: #fff;
        padding: 24px;
        border-radius: 24px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.08);
        width: 100%;
    }

    .filter-panel .filter-toggle {
        min-width: 180px;
        max-width: 220px;
    }

    .filter-panel .search-input {
        min-width: 320px;
        max-width: 520px;
    }

    /* make filter items align center vertically and keep consistent spacing */
    .filter-panel .filter-toggle .nav-link,
    .filter-panel .search-input input {
        height: 40px;
        display: flex;
        align-items: center;
        padding: 0 12px;
    }

    .filter-panel .search-input {
        min-width: 220px;
        max-width: 520px;
    }

    .filter-panel .filter-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-actions .btn {
        min-width: 120px;
    }

    .badge-hot {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: #fff4d8;
        color: #b05a1a;
        border: 1px solid #f0c692;
        border-radius: 999px;
        padding: 0.4rem 0.85rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .filter-toggle-label {
        font-size: 0.75rem;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: #6b5e78;
    }

    .filter-panel .form-check-label {
        font-size: 0.9rem;
        color: #4f3f5c;
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
                Đặt phòng trực tiếp
            </h2>
            <p class="text-center text-md-start text-muted mb-4" style="max-width: 780px; font-size: 0.95rem; line-height: 1.8;">
                Duyệt qua các phòng đang trống, so sánh giá và tiện ích nhanh chóng. Chúng tôi giúp bạn tìm phòng phù hợp nhất với nhu cầu và trải nghiệm nghỉ dưỡng đẳng cấp.
            </p>

            <form action="{{ url('/luu-tru') }}" method="GET" class="filter-panel d-flex flex-column flex-lg-row flex-wrap gap-3 align-items-start align-items-lg-center">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <div class="search-input me-2">
                        <input type="text" name="keyword" class="form-control form-control-sm shadow-none" placeholder="Tìm kiếm (tên, mô tả, tiện nghi...)" value="{{ request('keyword') }}">
                    </div>

                    <div class="dropdown filter-toggle" data-bs-auto-close="outside">
                        <div class="nav-link dropdown-toggle" data-bs-toggle="dropdown">BỘ LỌC</div>
                        <div class="dropdown-menu">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Hướng phòng</label>
                                <select name="huong_phong" class="form-select form-select-sm shadow-none">
                                    <option value="">Tất cả</option>
                                    <option value="biển" {{ request('huong_phong') == 'biển' ? 'selected' : '' }}>Hướng biển</option>
                                    <option value="vườn" {{ request('huong_phong') == 'vườn' ? 'selected' : '' }}>Hướng vườn</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Số người tối thiểu</label>
                                <input type="number" name="so_luong_nguoi" class="form-control form-control-sm shadow-none" value="{{ request('so_luong_nguoi') }}" min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Số phòng ngủ</label>
                                <input type="number" name="so_phong_ngu" class="form-control form-control-sm shadow-none" value="{{ request('so_phong_ngu') }}" min="1">
                            </div>
                        </div>
                    </div>



                    <div class="dropdown filter-toggle" data-bs-auto-close="outside">
                        <div class="nav-link dropdown-toggle" data-bs-toggle="dropdown">LOẠI PHÒNG</div>
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

                    <div class="dropdown filter-toggle" data-bs-auto-close="outside">
                        <div class="nav-link dropdown-toggle" data-bs-toggle="dropdown">VND</div>
                        <div class="dropdown-menu" style="min-width: 300px;">
                            <label class="form-label text-muted small d-flex justify-content-between">
                                <span>Mức giá tối đa:</span>
                                <span id="price-val" class="fw-bold" style="color:#673065;">
                                    {{ number_format(request('gia_max', 5000000), 0, ',', '.') }} VNĐ
                                </span>
                            </label>
                            <input type="range" name="gia_max" class="form-range" min="500000" max="5000000" step="100000" id="price-slider" value="{{ request('gia_max', 5000000) }}" oninput="document.getElementById('price-val').innerText = parseInt(this.value).toLocaleString('vi-VN') + ' VNĐ'">
                        </div>
                    </div>

                    <div class="dropdown filter-toggle" data-bs-auto-close="outside">
                        <div class="nav-link dropdown-toggle" data-bs-toggle="dropdown">PHÒNG HOT</div>
                        <div class="dropdown-menu" style="min-width: 320px;">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="hot" value="1" id="cb-hot" {{ request('hot') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="cb-hot">Chỉ phòng hot</label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Thời gian hot</label>
                                <select name="hot_scope" class="form-select form-select-sm shadow-none">
                                    <option value="month" {{ request('hot_scope', 'month') == 'month' ? 'selected' : '' }}>Trong tháng</option>
                                    <option value="year" {{ request('hot_scope') == 'year' ? 'selected' : '' }}>Trong năm</option>
                                </select>
                            </div>
                            <p class="text-muted small mb-0">Hiển thị những phòng được đặt nhiều nhất trong phạm vi thời gian đã chọn.</p>
                        </div>
                    </div>
                </div>

                <div class="filter-actions ms-auto">
                    <button type="submit" class="btn btn-sm text-white px-4" style="background: #673065; border-radius: 20px;">ÁP DỤNG LỌC</button>
                    <a href="{{ url('/luu-tru') }}" class="btn btn-sm btn-light px-3" style="border-radius: 20px;">Xóa lọc</a>
                </div>
            </form>


    @foreach($phongs as $phong)
    <div class="row room-card reveal">
        <div class="col-lg-5 mb-3 mb-lg-0">
            <div class="room-img-wrapper">
                <img src="{{ $phong->anh }}" alt="{{ $phong->loai_phong }}" onerror="this.src='https://images.unsplash.com/photo-1582719478250-c89cae4dc85b';">
            </div>
        </div>

        <div class="col-lg-7 d-flex flex-column">
            <div class="row flex-grow-1">
                <div class="col-md-7 pe-md-4 d-flex flex-column">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <h3 class="room-title mb-0">{{ $phong->loai_phong }} - Phòng {{ $phong->so_phong }}</h3>
                        @if($phong->booking_count > 0)
                            <span class="badge-hot">HOT {{ $phong->booking_count }} lượt đặt</span>
                        @endif
                    </div>

                    @if(request()->filled('keyword') && isset($phong->match_meta))
                        <div class="mb-2">
                            @if(!empty($phong->match_meta['matched']))
                                <small class="text-success">Khớp bởi: {{ implode(', ', $phong->match_meta['matched']) }}</small>
                            @endif
                            @if(!empty($phong->match_meta['unmatched']))
                                <br><small class="text-muted">Không khớp: {{ implode(', ', $phong->match_meta['unmatched']) }}</small>
                            @endif
                        </div>
                    @endif

                    <ul class="nav nav-tabs-custom" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#desc-{{ $phong->id_phong }}">MÔ TẢ</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#info-{{ $phong->id_phong }}">THÔNG TIN QUAN TRỌNG</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#amenities-{{ $phong->id_phong }}">TIỆN NGHI</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="desc-{{ $phong->id_phong }}" class="tab-pane active">
                            <p class="room-desc">{{ Str::limit($phong->mo_ta, 150, '...') }}</p>
                        </div>
                        <div id="info-{{ $phong->id_phong }}" class="tab-pane fade">
                            <ul class="custom-list-card">
                                @foreach(array_slice(explode("\n", $phong->thong_tin_quan_trong), 0, 4) as $item)
                                    @if(trim($item) != '') <li>{{ trim($item) }}</li> @endif
                                @endforeach
                            </ul>
                        </div>
                        <div id="amenities-{{ $phong->id_phong }}" class="tab-pane fade">
                            <ul class="custom-list-card">
                                @foreach(array_slice(explode("\n", $phong->tien_nghi), 0, 4) as $item)
                                    @if(trim($item) != '') <li>{{ trim($item) }}</li> @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="read-more-wrapper">
                        <a href="{{ route('phong.chitiet', $phong->id_phong) }}" class="read-more">ĐỌC THÊM ></a>
                    </div>
                </div>

                <div class="col-md-5 mt-4 mt-md-0 border-start ps-md-4">
                    <ul class="list-unstyled room-specs">
                        <li>{{ $phong->dien_tich }}</li>
                        <li>Số lượng khách tối đa {{ $phong->so_luong_nguoi }}</li>
                        <li>Hướng phòng: {{ $phong->huong_phong }}</li>
                        <li>{{ $phong->so_phong_ngu }} Phòng ngủ</li>
                    </ul>
                    <a href="#" class="read-more d-block mt-4 mb-3">SƠ ĐỒ THIẾT KẾ ></a>
                </div>
            </div>

            @php
                $isSaleActive = $phong->is_sale_active;
                $giaHienTai = $phong->gia_hien_tai;
            @endphp
            <div class="booking-bar mt-4">
                    @if($isSaleActive)
                    <div>
                        <span class="text-muted small">Giá gốc: </span><span class="text-muted" style="font-size: 14px;"><del>{{ number_format($phong->gia_phong, 0, ',', '.') }} VNĐ</del></span>
                        <div class="text-price-green fw-bold mt-1">Giá sale: {{ number_format($giaHienTai, 0, ',', '.') }} VNĐ/Đêm</div>
                    </div>
                @else
                    <span class="text-muted" style="font-size: 14px;">Giá tham khảo: {{ number_format($phong->gia_phong, 0, ',', '.') }} VNĐ/Đêm</span>
                @endif

                @if($phong->trang_thai === 'Trống')
                    @auth
                        <a href="javascript:void(0)"
                            class="btn btn-book text-decoration-none"
                            onclick="viewBookingCalendar({{ $phong->id_phong }}, '{{ $phong->so_phong }}', '{{ $phong->loai_phong }}')">
                            ĐẶT PHÒNG
                        </a>
                    @else
                        <a href="javascript:void(0)"
                            class="btn btn-book text-decoration-none"
                            data-bs-toggle="modal"
                            data-bs-target="#loginModal"> ĐẶT PHÒNG
                        </a>
                    @endauth
                @else
                    <a href="javascript:void(0)"
                        class="btn btn-book text-decoration-none disabled"
                        title="Phòng này đã được đặt hoặc không khả dụng">
                        HẾT PHÒNG
                    </a>
                @endif
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
                    <img src="https://www.sixsenses.com/images/icons/guest-services.svg" alt="icon" style="width: 250px; opacity: 0.8; filter: grayscale(1);">
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

<!-- SCRIPT HIỆU ỨNG CUỘN -->
<script src="{{ asset('js/room-availability.js') }}"></script>
<script>
    const checker = new RoomAvailabilityChecker({ apiBaseUrl: '/api' });

    // Modal hiển thị lịch đã book
    async function viewBookingCalendar(roomId, roomNumber, roomType) {
        try {
            // Lấy lịch đã book
            const availability = await checker.fetchRoomAvailability(roomId);

            // Hiển thị modal
            const modalHTML = `
                <div class="modal fade" id="bookingCalendarModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Lịch đặt phòng - ${roomType} (Phòng ${roomNumber})</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <strong>Lịch phòng trống:</strong> Bạn có thể đặt những khoảng thời gian <span class="text-success"><strong>không có</strong></span> trong danh sách dưới.
                                </div>
                                <h6 class="mb-3">Lịch đã được đặt:</h6>
                                ${availability.bookedDates.length > 0 ? checker.getBookingCalendarHTML() : '<p class="text-muted">Phòng hiện trống, chưa có lịch đặt.</p>'}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <a href="{{ route('booking.check', ['type' => 'phong', 'id' => 'ROOM_ID']) }}"
                                   class="btn btn-primary"
                                   id="bookNowBtn"
                                   onclick="this.href = this.href.replace('ROOM_ID', ${roomId})">
                                    Đặt phòng
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Xóa modal cũ nếu tồn tại
            const oldModal = document.getElementById('bookingCalendarModal');
            if (oldModal) {
                oldModal.remove();
            }

            // Thêm modal mới
            document.body.insertAdjacentHTML('beforeend', modalHTML);

            // Hiển thị modal
            const modal = new bootstrap.Modal(document.getElementById('bookingCalendarModal'));
            modal.show();
        } catch (error) {
            console.error('Error:', error);
            alert('Lỗi khi tải lịch phòng. Vui lòng thử lại!');
        }
    }

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
