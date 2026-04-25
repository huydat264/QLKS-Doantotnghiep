@extends('layouts.style')

@section('content')

@php
$currentDay = now()->day; // 25
$currentMonth = now()->month; // 4
$currentYear = now()->year; // 2026
@endphp

<style>
    /* CSS cho phần Slider Trải Nghiệm */
    .experience-slider {
        position: relative;
    }
    .experience-slider .carousel-item {
        height: 85vh; /* Độ cao của slider */
        min-height: 600px;
        background-size: cover;
        background-position: center;
    }
    .experience-slider .overlay-dark {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.4); /* Làm tối nền để nổi chữ */
        z-index: 1;
    }
    .experience-slider .carousel-caption {
        top: 50%;
        transform: translateY(-50%);
        bottom: auto;
        z-index: 2;
    }
    .experience-slider .caption-category {
        font-family: 'Montserrat', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 20px;
        display: block;
    }
    .experience-slider .caption-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.8rem;
        line-height: 1.4;
        max-width: 800px;
        margin: 0 auto 30px auto;
    }
    .experience-slider .btn-readmore {
        font-family: 'Montserrat', sans-serif;
        color: white;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        border-bottom: 1px solid white;
        padding-bottom: 3px;
        transition: 0.3s;
    }
    .experience-slider .btn-readmore:hover {
        color: #ddd;
        border-color: #ddd;
    }

    /* Mũi tên chuyển slide - Chỉ hiện khi hover */
    .experience-slider .carousel-control-prev,
    .experience-slider .carousel-control-next {
        width: 8%;
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: 3;
    }
    .experience-slider:hover .carousel-control-prev,
    .experience-slider:hover .carousel-control-next {
        opacity: 1;
    }
    .carousel-indicators {
        z-index: 3;
    }
    h1 {
    letter-spacing: 0.5px;
}
</style>

<section class="hero d-flex flex-column justify-content-center align-items-center text-white text-center position-relative" style="height: 100vh; overflow: hidden;">

    <video autoplay muted loop playsinline id="heroVideo"
           style="position: absolute; top: 50%; left: 50%; min-width: 100%; min-height: 100%; width: auto; height: auto; z-index: -2; transform: translate(-50%, -50%); object-fit: cover;">
        <source src="https://vjs.zencdn.net/v/oceans.mp4" type="video/mp4">
        Trình duyệt của bạn không hỗ trợ video.
    </video>

    <div class="overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); z-index: -1;"></div>

    <div data-aos="zoom-in" style="z-index: 1;">
        <h1 class="display-1 fw-bold mb-0">Kim Boutique Hotel</h1>
        <p class="lead mb-4">Việt Nam</p>
        <div data-aos="fade-up" data-aos-delay="400">
            <a href="#" class="text-white text-decoration-none small letter-spacing-2">XEM VIDEO ĐẦY ĐỦ <i class="bi bi-play-circle ms-2"></i></a>
        </div>
    </div>

    <div class="container" style="position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%); z-index: 10;">
        <div class="search-container-relative">

            <div id="calendarPopover" class="booking-popover">
                <div class="calendar-header d-flex justify-content-between align-items-center mb-3">
                    <button id="prevMonth" class="btn btn-sm btn-outline-secondary">&laquo;</button>
                    <div id="headerContent">
                        <span id="monthDisplay" class="cursor-pointer fw-bold">Tháng 4</span>
                        <span id="yearDisplay" class="cursor-pointer fw-bold ms-2">2026</span>
                    </div>
                    <button id="nextMonth" class="btn btn-sm btn-outline-secondary">&raquo;</button>
                </div>
                <div class="calendar-days">
                    <div class="d-grid text-center" style="grid-template-columns: repeat(7, 1fr); font-size: 0.7rem; color: #999; margin-bottom: 10px;">
                        <div>CN</div><div>T2</div><div>T3</div><div>T4</div><div>T5</div><div>T6</div><div>T7</div>
                    </div>
                    <div id="calendarGrid" class="d-grid" style="grid-template-columns: repeat(7, 1fr);"></div>
                </div>
            </div>

            <div id="guestPopover" class="booking-popover">
                <div class="guest-type-row">
                    <span class="fw-bold">Người lớn</span>
                    <div class="d-flex align-items-center gap-3">
                        <button class="counter-btn">-</button><b class="fs-5">2</b><button class="counter-btn">+</button>
                    </div>
                </div>
                <div class="guest-type-row">
                    <span class="fw-bold">Trẻ em</span>
                    <div class="d-flex align-items-center gap-3">
                        <button class="counter-btn">-</button><b class="fs-5">0</b><button class="counter-btn">+</button>
                    </div>
                </div>
            </div>

            <div class="search-box bg-white rounded-pill p-3 d-flex align-items-center shadow-lg row mx-0" data-aos="fade-up">
                <div class="col-md-2 border-end px-4 cursor-pointer" onclick="openCalendar('checkin', event)">
                    <label class="d-block small text-muted fw-bold">Nhận phòng</label>
                    <div class="small w-100 text-truncate text-start text-dark" id="checkinDisplay">Chọn ngày...</div>
                </div>
                <div class="col-md-2 border-end px-4 cursor-pointer" onclick="openCalendar('checkout', event)">
                    <label class="d-block small text-muted fw-bold">Trả phòng</label>
                    <div class="small w-100 text-truncate text-start text-dark" id="checkoutDisplay">Chọn ngày...</div>
                </div>
                <div class="col-md-3 border-end px-4 cursor-pointer" onclick="openGuests(event)">
                    <label class="d-block small text-muted fw-bold">Khách</label>
                    <div class="small w-100 text-truncate text-start text-dark" id="guestInputDisplay">2 Người lớn, 0 Trẻ em</div>
                </div>
                <div class="col-md-3 px-4">
                    <label class="d-block small text-muted fw-bold">Mã đặc biệt</label>
                    <input type="text" class="border-0 w-100 small text-dark" placeholder="Nhập mã...">
                </div>
                <div class="col-md-2 p-0">
                    <button class="btn w-100 rounded-pill py-3 fw-bold text-white btn-book-submit" style="background: var(--primary-color);">TÌM KIẾM <i class="bi bi-search ms-2"></i></button>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row">

        <!-- LEFT: MÔ TẢ -->
        <div class="col-lg-7">
            <h1 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif; line-height: 1.4;">
                Khu nghỉ dưỡng ven biển tại Việt Nam, nơi tôn vinh và lưu giữ trọn vẹn nét mộc mạc và bản sắc của làng chài truyền thống.
            </h1>

            <p class="text-muted" style="line-height: 1.8;">
                Ẩn mình giữa Vườn quốc gia và khu bảo tồn biển được bảo vệ, khu nghỉ dưỡng mang đến không gian yên bình,
                nơi mỗi khoảnh khắc đều gợi lên cảm giác an yên và thư thái. Là khu nghỉ dưỡng ven biển sang trọng
                hàng đầu tại Côn Đảo, nơi đây kết hợp giữa vẻ đẹp đương đại và nét mộc mạc làng chài xưa.
            </p>

            <p class="text-muted" style="line-height: 1.8;">
                Chỉ cách TP.HCM 45 phút bay, nơi đây mở ra một thế giới hoàn toàn khác biệt, tách khỏi nhịp sống thường nhật,
                với bãi cát vàng và làn nước xanh ngọc bích.
            </p>
        </div>

        <!-- RIGHT: LIÊN HỆ -->
        <div class="col-lg-4 offset-lg-1">
            <h6 class="text-uppercase fw-bold text-muted mb-3">Liên hệ</h6>

            <p class="mb-2">Bãi biển </p>
            <p class="mb-2">Đặc khu Phú Quốc</p>
            <p class="mb-2">Tp.An Giang-Việt Nam</p>
            <p class="mt-3 mb-1 text-primary">reservations-phuquoc@kimboutique.com</p>
            <p class="fw-bold">+84 358414532</p>
        </div>

    </div>
</div>
<div class="container mt-0 mb-1">
    <div class="row align-items-center text-secondary" style="font-size: 0.9rem; font-weight: 600;">

        <div class="col-md-auto me-4">
            <a href="#" class="d-flex align-items-center text-decoration-none text-secondary">
                <i class="bi bi-clock me-2" style="font-size: 1.2rem;"></i>
                <span id="localTime" class="text-uppercase">Giờ địa phương --:-- --</span>
            </a>
        </div>

        <div class="col-md-auto me-4">
            <a href="#" class="d-flex align-items-center text-decoration-none text-secondary">
                <i class="bi bi-map me-2" style="font-size: 1.2rem;"></i>
                <span class="text-uppercase">Cách đến với Six Senses</span>
            </a>
        </div>

        <div class="col-md-auto">
            <a href="#" class="d-flex align-items-center text-decoration-none text-secondary">
                <i class="bi bi-flag me-2" style="font-size: 1.2rem;"></i>
                <span class="text-uppercase">11 Trải nghiệm</span>
            </a>
        </div>

    </div>
</div>

<div class="container py-5 mt-5">
    <div class="row align-items-center py-5">
        <div class="col-md-6 px-lg-5" data-aos="fade-right">
            <h2 class="display-5 mb-4">Khu nghỉ dưỡng tôn vinh nét mộc mạc</h2>
            <p class="text-muted mb-4 lead">Nơi lưu giữ trọn vẹn bản sắc của làng chài truyền thống Việt Nam, kết hợp cùng dịch vụ đẳng cấp thế giới giữa thiên nhiên hoang sơ.</p>
            <a href="#" class="btn btn-outline-dark rounded-pill px-4">KHÁM PHÁ CHI TIẾT</a>
        </div>
        <div class="col-md-6" data-aos="zoom-in">
            <img src="https://images.unsplash.com/photo-1544124499-58912cbddaad?auto=format&fit=crop&w=1000&q=80" class="img-fluid rounded shadow-lg" alt="">
        </div>
    </div>

    <div class="row align-items-center flex-row-reverse py-5 my-5">
        <div class="col-md-6 px-lg-5" data-aos="fade-left">
            <span class="text-uppercase small fw-bold text-muted letter-spacing-2">Phát triển bền vững</span>
            <h2 class="display-5 my-4">Chương trình bảo tồn rùa biển</h2>
            <p class="text-muted mb-4">Chúng tôi tự hào là đơn vị tiên phong trong việc bảo vệ môi trường biển tại Nam Đảo Phú Quốc, giúp hàng nghìn chú rùa con trở về đại dương mỗi năm.</p>
            <a href="#" class="text-dark fw-bold text-decoration-none border-bottom border-dark pb-1">ĐỌC THÊM →</a>
        </div>
        <div class="col-md-6" data-aos="zoom-in">
            <img src="https://images.unsplash.com/photo-1518467166778-b88f373ffec7?auto=format&fit=crop&w=1000&q=80" class="img-fluid rounded shadow-lg" alt="">
        </div>
    </div>
</div>

<div id="experienceCarousel" class="carousel slide carousel-fade experience-slider" data-bs-ride="carousel" data-bs-interval="3000">

    <div class="carousel-indicators mb-4">
        <button type="button" data-bs-target="#experienceCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
        <button type="button" data-bs-target="#experienceCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#experienceCarousel" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">
        <div class="carousel-item active" style="background-image: url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=2070&auto=format&fit=crop');">
            <div class="overlay-dark"></div>
            <div class="carousel-caption">
                <span class="caption-category">LƯU TRÚ</span>
                <h3 class="caption-title">Tận hưởng kỳ nghỉ trọn vẹn với đa dạng lựa chọn biệt thự đẳng cấp, cùng chuỗi tiện ích phong phú giữa lòng Đảo Ngọc Phú Quốc</h3>
                <a href="#" class="btn-readmore">ĐỌC THÊM <i class="bi bi-chevron-right ms-1" style="font-size: 0.75rem;"></i></a>
            </div>
        </div>

        <div class="carousel-item" style="background-image: url('https://images.unsplash.com/photo-1559592413-7ce4f0a048a7?q=80&w=2069&auto=format&fit=crop');">
            <div class="overlay-dark"></div>
            <div class="carousel-caption">
                <span class="caption-category">VĂN HÓA</span>
                <h3 class="caption-title">Hòa mình vào nhịp sống thường nhật dung dị và đượm tình người dân chài qua những hành trình khám phá Làng chài Hàm Ninh độc đáo</h3>
                <a href="#" class="btn-readmore">ĐỌC THÊM <i class="bi bi-chevron-right ms-1" style="font-size: 0.75rem;"></i></a>
            </div>
        </div>

        <div class="carousel-item" style="background-image: url('https://images.unsplash.com/photo-1544551763-46a013bb70d5?q=80&w=2070&auto=format&fit=crop');">
            <div class="overlay-dark"></div>
            <div class="carousel-caption">
                <span class="caption-category">HOẠT ĐỘNG ĐẶC SẮC</span>
                <h3 class="caption-title">Đắm mình vào thế giới đại dương đầy sắc màu qua các trải nghiệm lặn biển và ngắm san hô tại một trong những rạn san hô đẹp nhất Phú Quốc</h3>
                <a href="#" class="btn-readmore">ĐỌC THÊM <i class="bi bi-chevron-right ms-1" style="font-size: 0.75rem;"></i></a>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#experienceCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true" style="width: 3rem; height: 3rem;"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#experienceCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true" style="width: 3rem; height: 3rem;"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<div class="container py-5">
    <div class="text-center py-5" data-aos="fade-up">
        <h2 class="display-4 mb-3">Nhật ký Six Senses</h2>
        <p class="text-muted mx-auto" style="max-width: 700px;">Khám phá những câu chuyện thú vị và những khoảnh khắc đáng nhớ tại khu nghỉ dưỡng của chúng tôi qua lăng kính của các vị khách.</p>
        <a href="#" class="text-uppercase small fw-bold text-dark letter-spacing-2 text-decoration-none mt-3 d-inline-block">XEM TẤT CẢ CÂU CHUYỆN <i class="bi bi-arrow-right ms-2"></i></a>
    </div>
</div>

<script>
function updateLocalTime() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const timeString = `${hours}:${minutes} ${ampm}`;
    document.getElementById('localTime').textContent = `Giờ địa phương ${timeString}`;
}
updateLocalTime();
setInterval(updateLocalTime, 1000);
</script>

@endsection
@include('user.dangky')
@include('user.dangnhap')
