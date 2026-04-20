@extends('layouts.style')

@section('content')

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
                <div class="calendar-grid">
                    <div>
                        <h6 class="fw-bold text-center mb-3">THÁNG TƯ 2026</h6>
                        <div class="d-grid text-center" style="grid-template-columns: repeat(7, 1fr); font-size: 0.7rem; color: #999; margin-bottom: 10px;">
                            <div>T2</div><div>T3</div><div>T4</div><div>T5</div><div>T6</div><div>T7</div><div>CN</div>
                        </div>
                        <div class="d-grid" style="grid-template-columns: repeat(7, 1fr);">
                            <div class="date-cell muted">30</div><div class="date-cell muted">31</div>
                            @for($i=1; $i<=19; $i++) <div class="date-cell">{{$i}}</div> @endfor
                            @for($i=20; $i<=30; $i++) <div class="date-cell available">{{$i}}</div> @endfor
                        </div>
                    </div>
                    <div>
                        <h6 class="fw-bold text-center mb-3">THÁNG NĂM 2026</h6>
                        <div class="d-grid text-center" style="grid-template-columns: repeat(7, 1fr); font-size: 0.7rem; color: #999; margin-bottom: 10px;">
                            <div>T2</div><div>T3</div><div>T4</div><div>T5</div><div>T6</div><div>T7</div><div>CN</div>
                        </div>
                        <div class="d-grid" style="grid-template-columns: repeat(7, 1fr);">
                            @for($i=1; $i<=31; $i++) <div class="date-cell available">{{$i}}</div> @endfor
                        </div>
                    </div>
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
                <div class="col-md-3 border-end px-4 cursor-pointer" onclick="openCalendar()">
                    <label class="d-block small text-muted fw-bold">Nhận phòng → Trả phòng</label>
                    <div class="small w-100 text-truncate text-start text-dark" id="calendarInputDisplay">Chọn ngày...</div>
                </div>
                <div class="col-md-3 border-end px-4 cursor-pointer" onclick="openGuests()">
                    <label class="d-block small text-muted fw-bold">Khách</label>
                    <div class="small w-100 text-truncate text-start text-dark" id="guestInputDisplay">2 Người lớn, 0 Trẻ em</div>
                </div>
                <div class="col-md-4 px-4">
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
            <p class="text-muted mb-4">Chúng tôi tự hào là đơn vị tiên phong trong việc bảo vệ môi trường biển tại Côn Đảo, giúp hàng nghìn chú rùa con trở về đại dương mỗi năm.</p>
            <a href="#" class="text-dark fw-bold text-decoration-none border-bottom border-dark pb-1">ĐỌC THÊM →</a>
        </div>
        <div class="col-md-6" data-aos="zoom-in">
            <img src="https://images.unsplash.com/photo-1518467166778-b88f373ffec7?auto=format&fit=crop&w=1000&q=80" class="img-fluid rounded shadow-lg" alt="">
        </div>
    </div>

    <div class="text-center py-5" data-aos="fade-up">
        <h2 class="display-4 mb-3">Nhật ký Six Senses</h2>
        <p class="text-muted mx-auto" style="max-width: 700px;">Khám phá những câu chuyện thú vị và những khoảnh khắc đáng nhớ tại khu nghỉ dưỡng của chúng tôi qua lăng kính của các vị khách.</p>
        <a href="#" class="text-uppercase small fw-bold text-dark letter-spacing-2 text-decoration-none mt-3 d-inline-block">XEM TẤT CẢ CÂU CHUYỆN <i class="bi bi-arrow-right ms-2"></i></a>
    </div>
</div>

@endsection
