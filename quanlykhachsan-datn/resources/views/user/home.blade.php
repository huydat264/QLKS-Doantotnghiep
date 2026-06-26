@extends('layouts.style')

@section('content')

@php
$currentDay = now()->day;
$currentMonth = now()->month;
$currentYear = now()->year;
@endphp

<style>
    .experience-slider{position:relative}
    .experience-slider .carousel-item{height:85vh;min-height:600px;background-size:cover;background-position:center}
    .experience-slider .overlay-dark{position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.4);z-index:1}
    .experience-slider .carousel-caption{top:50%;transform:translateY(-50%);bottom:auto;z-index:2;left:0;right:0;text-align:center}
    .experience-slider .caption-category{font-family:'Montserrat',sans-serif;font-size:.85rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:20px;display:block}
    .experience-slider .caption-title{font-family:'Playfair Display',serif;font-size:2.8rem;line-height:1.4;max-width:800px;margin:0 auto 30px auto}
    .experience-slider .btn-readmore{font-family:'Montserrat',sans-serif;color:white;text-decoration:none;font-size:.85rem;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;border-bottom:1px solid white;padding-bottom:3px;transition:.3s}
    .experience-slider .btn-readmore:hover{color:#ddd;border-color:#ddd}
    .experience-slider .carousel-control-prev,.experience-slider .carousel-control-next{width:8%;opacity:0;transition:opacity .4s ease;z-index:3}
    .experience-slider:hover .carousel-control-prev,.experience-slider:hover .carousel-control-next{opacity:1}
    .carousel-indicators{z-index:3}
    .search-box { align-items: center; }
    .search-box form { align-items: stretch; }
    .search-box .btn-book-submit { line-height: 1; margin-bottom: 6px; }
    .search-box .col-auto { display: flex; align-items: flex-end; justify-content: center; }
    h1{letter-spacing:.5px}
</style>

<section class="hero d-flex flex-column justify-content-center align-items-center text-white text-center position-relative" style="height: 100vh; overflow: hidden;">

    <video autoplay muted loop playsinline id="heroVideo" style="position:absolute;top:50%;left:50%;min-width:100%;min-height:100%;width:auto;height:auto;z-index:-2;transform:translate(-50%,-50%);object-fit:cover">
        <source src="https://vjs.zencdn.net/v/oceans.mp4" type="video/mp4">
        Trình duyệt của bạn không hỗ trợ video.
    </video>
    <div class="overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.3);z-index:-1"></div>

    <div data-aos="zoom-in" style="z-index: 1;">
        <h1 class="display-1 fw-bold mb-0">Kim Boutique Hotel</h1>
        <p class="lead mb-4">Việt Nam</p>
        <div data-aos="fade-up" data-aos-delay="400">

        </div>
    </div>

    <div class="search-container-relative position-absolute w-100" style="bottom: 40px; left: 0; z-index: 10;">

            <div id="calendarPopover" class="booking-popover" style="width: 400px;">
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

            <div id="guestPopover" class="booking-popover" style="width: 300px;">
                <div class="guest-type-row d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Số khách</span>
                    <div class="d-flex align-items-center gap-2">
                        <button class="counter-btn btn btn-sm btn-outline-secondary">-</button>
                        <input type="number" id="guestNumberInput" class="form-control form-control-sm text-center" style="width: 60px;" min="1" value="2">
                        <button class="counter-btn btn btn-sm btn-outline-secondary">+</button>
                    </div>
                </div>
            </div>

            <div class="search-box bg-white rounded-pill p-2 d-flex align-items-center justify-content-center shadow-lg mx-auto" data-aos="fade-up" style="max-width: 860px; width: calc(100% - 40px); min-height: 74px;">
                <form action="{{ route('phong.user') }}" method="GET" class="d-flex align-items-stretch w-100 row mx-0">
                    <div class="col-md-3 border-end px-2 cursor-pointer d-flex flex-column justify-content-center py-2 search-field" onclick="openCalendar('checkin', event)">
                        <label class="d-block small text-muted fw-bold">Nhận phòng</label>
                        <input type="hidden" name="checkin" id="checkinInput" value="{{ request('checkin') }}">
                        <div class="small w-100 text-truncate text-start text-dark" id="checkinDisplay">{{ request('checkin') ? request('checkin') : 'Chọn ngày...' }}</div>
                    </div>
                    <div class="col-md-3 border-end px-2 cursor-pointer d-flex flex-column justify-content-center py-2 search-field" onclick="openCalendar('checkout', event)">
                        <label class="d-block small text-muted fw-bold">Trả phòng</label>
                        <input type="hidden" name="checkout" id="checkoutInput" value="{{ request('checkout') }}">
                        <div class="small w-100 text-truncate text-start text-dark" id="checkoutDisplay">{{ request('checkout') ? request('checkout') : 'Chọn ngày...' }}</div>
                    </div>
                    <div class="col-md-3 border-end px-2 cursor-pointer d-flex flex-column justify-content-center py-2 search-field" onclick="openGuests(event)">
                        <label class="d-block small text-muted fw-bold">Khách</label>
                        <input type="hidden" name="tong_khach" id="tongKhachInput" value="{{ request('tong_khach', 2) }}">
                        <div class="small w-100 text-truncate text-start text-dark" id="guestInputDisplay">{{ request('tong_khach', 2) }} Khách</div>
                    </div>
                    <div class="col-auto p-0 d-flex align-items-center justify-content-center h-100 search-field">
                        <button type="submit" class="btn rounded-pill py-2 px-4 fw-bold text-white btn-book-submit" style="background: var(--primary-color); min-width: 140px;">TÌM KIẾM <i class="bi bi-search ms-1"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row">

        <div class="col-lg-7">
            <h1 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif; line-height: 1.4;">
                Khách sạn nghỉ dưỡng ven biển tại Việt Nam, nơi tôn vinh và lưu giữ trọn vẹn nét mộc mạc và bản sắc của làng chài truyền thống.
            </h1>

            <p class="text-muted" style="line-height: 1.8;">
                Ẩn mình giữa Vườn quốc gia và khu bảo tồn biển được bảo vệ, khách sạn mang đến không gian yên bình,
                nơi mỗi khoảnh khắc đều gợi lên cảm giác an yên và thư thái. Là khách sạn nghỉ dưỡng ven biển sang trọng
                hàng đầu tại Phú Quốc, nơi đây kết hợp giữa vẻ đẹp đương đại và nét mộc mạc làng chài xưa.
            </p>

            <p class="text-muted" style="line-height: 1.8;">
                Chỉ cách TP.HCM 45 phút bay, nơi đây mở ra một thế giới hoàn toàn khác biệt, tách khỏi nhịp sống thường nhật,
                với bãi cát vàng và làn nước xanh ngọc bích.
            </p>
        </div>

        <div class="col-lg-4 offset-lg-1">
            <h6 class="text-uppercase fw-bold text-muted mb-3">Liên hệ</h6>
            <p class="mb-2">Bãi biển</p><p class="mb-2">Đặc khu Phú Quốc</p><p class="mb-2">Tp.An Giang-Việt Nam</p>
            <p class="mt-3 mb-1 text-primary">reservations-phuquoc@kimboutique.com</p><p class="fw-bold">+84 358414532</p>
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
                <span class="text-uppercase">Cách đến với Kim Boutique Hotel</span>
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
            <h2 class="display-5 mb-4">Khách sạn nghỉ dưỡng tôn vinh nét đẹp truyền thống</h2>
            <p class="text-muted mb-4 lead">Nơi lưu giữ trọn vẹn bản sắc truyền thống Việt Nam, kết hợp cùng dịch vụ chăm sóc chuyên nghiệp giữa thiên nhiên hoang sơ.</p>
            <a href="{{ url('/kientruc') }}" class="btn btn-outline-dark rounded-pill px-4">KHÁM PHÁ CHI TIẾT</a>
        </div>
        <div class="col-md-6" data-aos="zoom-in">
            <img src="https://images.trvl-media.com/lodging/94000000/93240000/93235700/93235601/4961892d.jpg?impolicy=resizecrop&rw=575&rh=575&ra=fill" class="img-fluid rounded shadow-lg" alt="">
        </div>
    </div>

    <div class="row align-items-center flex-row-reverse py-5 my-5">
        <div class="col-md-6 px-lg-5" data-aos="fade-left">
            <span class="text-uppercase small fw-bold text-muted letter-spacing-2">Phát triển bền vững</span>
            <h2 class="display-5 my-4">Chương trình bảo tồn san hô</h2>
            <p class="text-muted mb-4">Chúng tôi tự hào là đơn vị tiên phong trong việc bảo vệ môi trường biển tại Nam Đảo Phú Quốc, giúp quần thể san hô được bảo tồn mỗi năm.</p>
            <a href="{{ url('/baoton') }}" class="text-dark fw-bold text-decoration-none border-bottom border-dark pb-1">ĐỌC THÊM →</a>
        </div>
        <div class="col-md-6" data-aos="zoom-in">
            <img src="https://vj-prod-website-cms.s3.ap-southeast-1.amazonaws.com/depositphotos54387583xl-1719191773890.jpg" class="img-fluid rounded shadow-lg" alt="">
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

            </div>
        </div>

        <div class="carousel-item" style="background-image: url('https://eholiday.vn/wp-content/uploads/2021/07/ve-dep-van-hoa-va-con-nguoi-phu-quoc-6.jpg');">
            <div class="overlay-dark"></div>
            <div class="carousel-caption">
                <span class="caption-category">VĂN HÓA</span>
                <h3 class="caption-title">Hòa mình vào nhịp sống thường nhật dung dị và đượm tình người dân chài qua những hành trình khám phá Làng chài Hàm Ninh độc đáo</h3>

            </div>
        </div>

        <div class="carousel-item" style="background-image: url('https://images.unsplash.com/photo-1544551763-46a013bb70d5?q=80&w=2070&auto=format&fit=crop');">
            <div class="overlay-dark"></div>
            <div class="carousel-caption">
                <span class="caption-category">HOẠT ĐỘNG ĐẶC SẮC</span>
                <h3 class="caption-title">Đắm mình vào thế giới đại dương đầy sắc màu qua các trải nghiệm lặn biển và ngắm san hô tại một trong những rạn san hô đẹp nhất Phú Quốc</h3>

            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#experienceCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true" style="width:3rem;height:3rem"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#experienceCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true" style="width:3rem;height:3rem"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<div class="container py-5">
    <div class="text-center py-5" data-aos="fade-up">
        <h2 class="display-4 mb-3">Nhật ký Kim Boutique Hotel</h2>
        <p class="text-muted mx-auto" style="max-width: 700px;">Khám phá những câu chuyện thú vị và những khoảnh khắc đáng nhớ tại khu nghỉ dưỡng của chúng tôi qua lăng kính của các vị khách.</p>
        <a href="{{ url('/nhatky') }}" class="text-uppercase small fw-bold text-dark letter-spacing-2 text-decoration-none mt-3 d-inline-block">XEM TẤT CẢ CÂU CHUYỆN <i class="bi bi-arrow-right ms-2"></i></a>
    </div>
</div>

<script>
function updateLocalTime(){
    const n=new Date(),h=n.getHours().toString().padStart(2,'0'),m=n.getMinutes().toString().padStart(2,'0'),a=h>=12?'PM':'AM';
    document.getElementById('localTime').textContent=`Giờ địa phương ${h}:${m} ${a}`;
}
updateLocalTime();setInterval(updateLocalTime,1000);

// Script cho guest counter
$(document).ready(function(){
    let initialGuestValue = parseInt($('#tongKhachInput').val());
    let tongKhach = initialGuestValue && initialGuestValue > 0 ? initialGuestValue : 2;
    function updateGuestDisplay(){
        $('#guestInputDisplay').text(`${tongKhach} Khách`);
        $('#tongKhachInput').val(tongKhach);
        $('#guestNumberInput').val(tongKhach);
    }
    $('#guestPopover .guest-type-row .counter-btn').first().click(function(){
        if(tongKhach>1){tongKhach--;updateGuestDisplay()}
    });
    $('#guestPopover .guest-type-row .counter-btn').last().click(function(){
        tongKhach++;updateGuestDisplay()
    });
    $('#guestNumberInput').on('input change',function(){
        let v=parseInt($(this).val());
        if(v>=1){tongKhach=v;updateGuestDisplay()}else{$(this).val(tongKhach)}
    });
    updateGuestDisplay();
});
</script>

@endsection
@include('user.dangky')
@include('user.dangnhap')
