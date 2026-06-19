@extends('layouts.style')

@section('content')

<style>
    body {
        font-family: 'Playfair Display', serif;
        color: #666;
    }

    .hero-slider {
        height: 100vh;
        overflow: hidden;
    }

    .hero-slider .carousel-item {
        height: 100vh;
        position: relative;
    }

    .hero-slider .carousel-item img {
        width: 100%;
        height: 100vh;
        object-fit: cover;
        animation: zoomEffect 3s ease-in-out forwards;
    }

    @keyframes zoomEffect {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.3);
        z-index: 1;
    }

    .hero-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        z-index: 2;
    }

    .hero-title {
        font-size: 60px;
        font-family: 'Playfair Display', serif;
        font-weight: bold;
    }

    .hero-subtitle {
        font-size: 18px;
        letter-spacing: 2px;
    }

    #customTab {
        margin-top: -2px;
        background: white;
    }

    #customTab .nav-link {
        position: relative;
        color: #888;
        transition: 0.3s;
        border: none;
        padding: 20px 30px;
        font-weight: 500;
        letter-spacing: 1px;
    }

    #customTab .nav-link:hover {
        color: #673065;
    }

    #customTab .nav-link.active {
        color: #673065;
        background: none;
    }

    #customTab .nav-link::after {
        content: "";
        position: absolute;
        left: 50%;
        bottom: 0;
        width: 0%;
        height: 2px;
        background: #673065;
        transition: 0.3s;
        transform: translateX(-50%);
    }

    #customTab .nav-link.active::after {
        width: 100%;
    }

    /* Tab 2: Thời điểm du lịch - Accordion Styles */
    .weather-accordion .accordion-item {
        border: none;
        border-bottom: 1px solid #f1eeea;
        background: transparent;
    }
    .weather-accordion .accordion-button {
        background: transparent;
        color: #333;
        font-weight: bold;
        padding: 25px 0;
        box-shadow: none;
        border: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 15px;
    }
    .weather-accordion .accordion-button:not(.collapsed) {
        color: #673065;
        background: transparent;
    }
    .weather-accordion .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23673065'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    .weather-content {
        padding-bottom: 30px;
        line-height: 1.8;
        color: #777;
        font-size: 15px;
        font-family: sans-serif;
    }

    /* Tab 3: Hướng dẫn di chuyển - Spec Styles */
    .transit-header-title {
        font-family: 'Playfair Display', serif;
        font-size: 36px;
        color: #222;
        font-weight: 500;
    }
    .transit-section-title {
        font-family: 'Playfair Display', serif;
        font-size: 22px;
        color: #333;
        font-weight: bold;
        position: relative;
        padding-bottom: 12px;
        margin-bottom: 25px;
    }
    .transit-section-title::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        width: 40px;
        height: 1px;
        background-color: #673065;
    }
    .text-description {
        font-family: sans-serif;
        font-size: 15px;
        line-height: 1.8;
        color: #555;
        text-align: justify;
    }
    .route-table {
        width: 100%;
        margin-top: 20px;
        font-family: sans-serif;
    }
    .route-table td {
        padding: 12px 0;
        border-bottom: 1px solid #f1eeea;
        font-size: 14px;
        color: #555;
    }
    .route-table td strong {
        color: #222;
        font-size: 15px;
    }

    /* Transit map zoom */
    .transit-map-wrapper {
        position: relative;
        height: 100%;
        overflow: hidden;
    }
    .transit-map-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.2s ease;
        transform-origin: center center;
        cursor: grab;
    }
    .transit-map-img:active {
        cursor: grabbing;
    }

    /* Map Marker Styling gốc của mày */
    .map-marker {
        position: absolute;
        width: 35px;
        height: 35px;
        background-color: #666;
        color: white;
        border: 2px solid white;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.3s;
    }

    .map-marker::before {
        content: attr(data-num);
        transform: rotate(45deg);
        font-weight: bold;
    }

    .map-marker:hover, .map-marker.active {
        background-color: #673065;
        z-index: 10;
    }
</style>

<div id="heroCarousel" class="carousel slide hero-slider" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e" alt="Slider 1">
        </div>
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470" alt="Slider 2">
        </div>
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-title">Kimboutique</div>
        <div class="hero-subtitle">Mỗi chuyến bay mở ra một thiên đường của quý khách</div>
    </div>
</div>

<ul class="nav justify-content-center sticky-top shadow-sm" id="customTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-diemden" data-bs-toggle="tab" data-bs-target="#content-diemden" type="button" role="tab" aria-controls="content-diemden" aria-selected="true">ĐIỂM ĐẾN</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-thoidiem" data-bs-toggle="tab" data-bs-target="#content-thoidiem" type="button" role="tab" aria-controls="content-thoidiem" aria-selected="false">THỜI ĐIỂM DU LỊCH</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-huongdan" data-bs-toggle="tab" data-bs-target="#content-huongdan" type="button" role="tab" aria-controls="content-huongdan" aria-selected="false">HƯỚNG DẪN DI CHUYỂN</button>
    </li>
</ul>

<div class="tab-content" id="myTabContent">

    <div class="tab-pane fade show active" id="content-diemden" role="tabpanel" aria-labelledby="tab-diemden">
        <div class="container" style="padding: 80px 0;">
            <div class="row">
                <div class="col-lg-8">
                    <p class="mb-4" style="font-size: 18px; line-height: 1.8;">Kim Boutique Hotel là điểm đến biệt lập đầy quyến rũ, chỉ cách TP. Hồ Chí Minh 60 phút bay hoặc 130 phút bay từ Hà Nội. Sau lời chào đón nồng hậu, quý khách sẽ bước vào không gian khách sạn thanh lịch và khoáng đạt.</p>
                    <p style="line-height: 1.8;">Khách sạn sở hữu hồ bơi vô cực riêng tư với tầm nhìn đẹp như mơ ra biển cùng không gian thư giãn, kết hợp cùng chuỗi hoạt động giải trí trên cạn cũng như và liệu trình spa tinh tế mang lại cho quý khách một trải nghiệm khó quên trong kì nghỉ của mình.</p>
                </div>
                <div class="col-lg-4 border-start border-2 ps-4" style="border-color: #673065 !important;">
                    <h6 class="fw-bold letter-spacing-1" style="color: #333;">TẢI XUỐNG</h6>
                    <a href="#" class="text-decoration-none" style="color: #673065; font-weight: bold;">Thông tin tổng quan</a>
                </div>
            </div>
        </div>

        <div class="container" style="padding: 80px 0;">
            <h2 style="font-size: 32px; font-weight: 500; text-align: center; margin-bottom: 60px; color: #333;">Tiện nghi và dịch vụ tại Kim Boutique Hotel</h2>
            <div class="row g-5">
                <div class="col-md-6">
                    <div class="d-flex gap-4">
                        <div style="flex-shrink: 0;">
                            <div style="font-size: 48px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">🏖️</div>
                        </div>
                        <div>
                            <h4 style="font-size: 18px; font-weight: bold; color: #333; font-family: 'Playfair Display', serif; margin-bottom: 12px;">Bãi biển và hồ bơi</h4>
                            <ul style="list-style: none; padding: 0; margin: 0; font-family: sans-serif; font-size: 14px; line-height: 1.8; color: #666;">
                                <li>• Bãi Xếp trái dài hơn 4km, nối bắt với làn nước xanh ngọc trong vắt như phà lê,tấp nập các hoạt động cho du khách như lặn biển cano nước.</li>
                                <li>• Hồ bơi lộn tọa lạc tại vi trung tâm, với dịch vụ phục vụ tại chỗ, mang đến các môn ăn nhẹ và thức uống bổ dưỡng.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex gap-4">
                        <div style="flex-shrink: 0;">
                            <div style="font-size: 48px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">✨</div>
                        </div>
                        <div>
                            <h4 style="font-size: 18px; font-weight: bold; color: #333; font-family: 'Playfair Display', serif; margin-bottom: 12px;">Tiện ích chung</h4>
                            <ul style="list-style: none; padding: 0; margin: 0; font-family: sans-serif; font-size: 14px; line-height: 1.8; color: #666;">
                                <li>• Dịch vụ đưa đón sân bay</li>
                                <li>• Dịch vụ tiệc tùng</li>
                                <li>• Nhà hàng buffet/thực đơn chuẩn 4 sao</li>
                                <li>• Hai quầy bar 2 vibe khác nhau cho du khách thoả sức lựa chọn</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex gap-4">
                        <div style="flex-shrink: 0;">
                            <div style="font-size: 48px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">🚴</div>
                        </div>
                        <div>
                            <h4 style="font-size: 18px; font-weight: bold; color: #333; font-family: 'Playfair Display', serif; margin-bottom: 12px;">Các hoạt động đặc sắc tại khách sạn nghỉ dưỡng</h4>
                            <ul style="list-style: none; padding: 0; margin: 0; font-family: sans-serif; font-size: 14px; line-height: 1.8; color: #666;">
                                <li>• Chúng tôi thiết kế và tổ chức hoạt động thường niên trong khuôn viên khu nghỉ dưỡng, các chuyên tham quan địa phương cùng những hành trình khám phá thiên nhiên kỳ thú nếu du khách có nhu cầu.</li>
                                <li>• Câu lạc bộ lướt sóng và lăn biển Kim Ocean</li>
                                <li>• Thả rùa con về biển,cùng các hoạt động sinh thái quanh đảo Phú Quốc</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex gap-4">
                        <div style="flex-shrink: 0;">
                            <div style="font-size: 48px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">👨‍👩‍👧</div>
                        </div>
                        <div>
                            <h4 style="font-size: 18px; font-weight: bold; color: #333; font-family: 'Playfair Display', serif; margin-bottom: 12px;">Trải nghiệm dành cho gia đình</h4>
                            <ul style="list-style: none; padding: 0; margin: 0; font-family: sans-serif; font-size: 14px; line-height: 1.8; color: #666;">
                                <li>• Bữa tối riêng từ tại phòng Family cho những khoảnh khắc thư giãn trọn vẹn</li>
                                <li>• Miễn phí dùng bữa cho các vị khách nhí từ 4 đến 10 tuổi</li>
                                <li>• Kimboutique hướng đến đa dạng các món ăn Việt Nam đầm đà bán sắc,cũng như thực đơn Á/Âu đặt trước</li>
                                <li>• Đạp xe,Thuê xe máy</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid bg-white border-top" style="padding: 80px 0;">
            <div class="container">
                <h2 style="font-size: 32px; font-weight: 500; text-align: center; margin-bottom: 60px; color: #333;">Khám phá vị trí đáng trải nghiệm của đảo Phú Quốc</h2>
                <div class="row shadow-sm rounded overflow-hidden bg-white">
                    <div class="col-lg-5 p-0 bg-white d-flex flex-column justify-content-center align-items-center" style="padding: 40px !important; text-align: center;">
                        <div style="height: 300px; overflow: hidden; border-radius: 8px; margin-bottom: 30px; width: 100%;">
                            <img id="detail-img" src="https://images.unsplash.com/photo-1549194380-b1f45cda28a9" class="w-100 h-100 object-fit-cover" style="transition: 0.5s; opacity: 1;">
                        </div>
                        <span id="detail-badge" class="badge bg-secondary" style="position: relative; bottom: 20px; width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background-color: #673065 !important; font-size: 16px; margin-bottom: 10px;">1</span>
                        <h3 id="detail-title" style="font-size: 24px; font-weight: 500; color: #333; font-family: 'Playfair Display', serif; margin-bottom: 15px; margin-top: 0;">Vinpearl Safari Phú Quốc</h3>
                        <p id="detail-desc" style="font-family: sans-serif; font-size: 14px; line-height: 1.6; color: #666; margin: 0;">Khu bảo tồn động vật hoang dã và trải nghiệm sinh thái hàng đầu đảo Phú Quốc.</p>
                    </div>
                    <div class="col-lg-7 p-0 position-relative" style="height: 600px; background: #eee;">
                        <div class="w-100 h-100" style="background: url('{{ asset('minimalist-phuquoc.png') }}') center/cover;"></div>

                        <button class="map-marker active" style="top: 17%; left: 36%;" data-num="1" data-img="https://mia.vn/media/uploads/blog-du-lich/vinpearl-phu-quoc-1-1713695424.jpg" data-title="Vinpearl Safari Phú Quốc" data-desc="Khu bảo tồn động vật hoang dã và trải nghiệm sinh thái hàng đầu đảo Phú Quốc."></button>
                        <button class="map-marker" style="top: 78%; left: 53%;" data-num="2" data-img="https://cf.bstatic.com/xdata/images/hotel/max1024x768/832801688.jpg?k=b720003326d74c50b9d21c4ff099f50db2e2f2ccc05975406a0d870c9990e6e5&o=" data-title="Kim Boutique Hotel" data-desc="Khách sạn boutique sang trọng nằm gần bờ biển, tiện đường khám phá đảo."></button>
                        <button class="map-marker" style="top: 76%; left: 52%;" data-num="3" data-img="https://vinhtour.vn/wp-content/uploads/2024/06/VT_Cau-Hon-Phu-Quoc-Kiss-Bridge-1.jpg" data-title="Cầu Hôn Phú Quốc" data-desc="Điểm ngắm hoàng hôn lãng mạn và cảnh đẹp biển xanh của đảo ngọc."></button>
                        <button class="map-marker" style="top: 40%; left: 56%;" data-num="4" data-img="https://ongvove.com/uploads/0000/1/2023/05/19/suoi-da-ban-phu-quoc.jpg" data-title="Khu du lịch sinh thái Suối Đá Bàn" data-desc="Thiên đường sinh thái với hồ nước trong xanh và đường mòn rừng nguyên sinh."></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="content-thoidiem" role="tabpanel" aria-labelledby="tab-thoidiem">
        <div class="container" style="padding: 80px 0;">
            <div class="row align-items-center mb-5">
                <div class="col-lg-7">
                    <span class="text-muted text-uppercase letter-spacing-2 small fw-bold">Kim Boutique Hotel</span>
                    <h1 class="display-4 fw-bold text-dark mt-2 mb-4" style="font-family: 'Playfair Display', serif;">Thời điểm lý tưởng để ghé thăm Phú Quốc</h1>
                    <p class="text-muted fs-5" style="font-family: sans-serif; line-height: 1.6;">Phú Quốc luôn sẵn sàng chào đón bạn vào bất kỳ mùa nào trong năm với đặc trưng khí hậu nắng ấm quanh năm. Từ những hoạt động lặn sâu khám phá lòng đại dương kỳ vĩ đến hành trình trekking xuyên rừng nhiệt đới, mỗi thời điểm mang một vẻ đẹp riêng biệt trọn vẹn.</p>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <img src="https://www.sixsenses.com/images/icons/when-to-visit.svg" alt="Illustration Weather" class="img-fluid" style="max-width: 280px;">
                </div>
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-lg-12">
                    <div class="accordion weather-accordion" id="weatherYearMenu">

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#m1-2">
                                    Từ tháng 1 đến tháng 2
                                </button>
                            </h2>
                            <div id="m1-2" class="accordion-collapse collapse show" data-bs-parent="#weatherYearMenu">
                                <div class="accordion-body weather-content">
                                    Khoảng thời gian đầu năm thời tiết vô cùng dịu mát, ngập tràn ánh nắng ấm áp với độ ẩm thấp dễ chịu. Đây là thời điểm vàng để thực hiện các chuyến hành trình mạo hiểm leo núi, đi bộ xuyên rừng quốc gia, khám phá hệ sinh thái thực vật kỳ thú hoặc viếng thăm các khu di tích lịch sử linh thiêng trên đảo.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#m3-5">
                                    Từ tháng 3 đến tháng 5
                                </button>
                            </h2>
                            <div id="m3-5" class="accordion-collapse collapse" data-bs-parent="#weatherYearMenu">
                                <div class="accordion-body weather-content">
                                    Mùa biển lặng và êm đềm nhất trong năm, mặt biển trong vắt như một tấm gương khổng lồ. Ánh nắng rực rỡ chiếu rọi trực tiếp xuống mặt nước, tạo điều kiện hoàn hảo cho các hoạt động thể thao biển đỉnh cao như chèo thuyền kayak, lặn ngắm rạn san hô tự nhiên đa sắc màu hay đi cano khám phá các cụm đảo nhỏ hoang sơ.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#m6-9">
                                    Từ tháng 6 đến tháng 9
                                </button>
                            </h2>
                            <div id="m6-9" class="accordion-collapse collapse" data-bs-parent="#weatherYearMenu">
                                <div class="accordion-body weather-content">
                                    Mặc dù xuất hiện những cơn mưa nhiệt đới bất chợt, đây lại là mùa trải nghiệm độc nhất vô nhị: Mùa rùa biển đẻ trứng. Khi nghỉ dưỡng tại Kim Boutique Hotel Phú Quốc, quý khách sẽ được đặc quyền tham gia vào hành trình đỡ đẻ cho rùa và tận tay thả những chú rùa con về với đại dương bao la đầy tính nhân văn.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#m10-12">
                                    Từ tháng 10 đến tháng 12
                                </button>
                            </h2>
                            <div id="m10-12" class="accordion-collapse collapse" data-bs-parent="#weatherYearMenu">
                                <div class="accordion-body weather-content">
                                    Những làn gió nhiệt đới bắt đầu thổi mạnh vào mang theo không khí mát mẻ sảng khoái vào sáng sớm. Thời tiết này cực kỳ lý tưởng để tham gia trải nghiệm lướan diều mạo hiểm, hoặc thả lỏng toàn bộ cơ thể trong không gian yên tĩnh với những liệu trình trị liệu chuyên sâu tại khu Spa ấm áp.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="content-huongdan" role="tabpanel" aria-labelledby="tab-huongdan">
        <div class="container" style="padding: 80px 0;">

            <div class="text-center mb-5">
                <span class="text-muted text-uppercase letter-spacing-2 small fw-bold">Kim Boutique Hotel</span>
                <h1 class="transit-header-title mt-2">Hướng dẫn di chuyển đến khu nghỉ dưỡng khách sạn</h1>
            </div>

            <div class="row g-5 mt-2">
                <div class="col-md-6">
                    <h3 class="transit-section-title">BẰNG ĐƯỜNG HÀNG KHÔNG</h3>
                    <p class="text-description">
                        Phú Quốc nằm cách Thành phố Hồ Chí Minh khoảng 60 phút bay và cách Hà Nội khoảng 2 tiếng 15 phút bay. Các chuyến bay thương mại nội địa được khai thác liên tục hàng ngày, đưa hành khách đáp xuống sân bay Phú Quốc, cách khách sạn nghỉ dưỡng chỉ 25 phút di chuyển bằng ô tô.
                    </p>

                    <table class="route-table">
                        <tr>
                            <td>Từ <strong>Thành phố Hồ Chí Minh (SGN)</strong></td>
                            <td class="text-end">60 phút</td>
                        </tr>
                        <tr>
                            <td>Từ <strong>Hà Nội (HAN)</strong></td>
                            <td class="text-end">2 tiếng 15 phút</td>
                        </tr>
                        <tr>
                            <td>Từ <strong>Cần Thơ (VCA)</strong></td>
                            <td class="text-end">50 phút</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h3 class="transit-section-title">BẰNG ĐƯỜNG BIỂN</h3>
                    <p class="text-description">
                        Đối với những du khách muốn trải nghiệm hành trình trên biển khơi ngắm nhìn đại dương, hệ thống tàu cao tốc chất lượng cao là lựa chọn vô cùng phù hợp. Tàu xuất phát từ cảng Cầu Đá (Vũng Tàu) hoặc cảng Trần Đề (Sóc Trăng) di chuyển trực tiếp đến cảng An Thới (Phú Quốc).
                    </p>

                    <table class="route-table">
                        <tr>
                            <td>Tàu cao tốc từ <strong>Vũng Tàu</strong></td>
                            <td class="text-end">4 tiếng 15 phút</td>
                        </tr>
                        <tr>
                            <td>Tàu cao tốc từ <strong>Sóc Trăng (Trần Đề)</strong></td>
                            <td class="text-end">3 tiếng 30 phút</td>
                        </tr>
                        <tr>
                            <td>Tàu cao tốc từ <strong>Cần Thơ</strong></td>
                            <td class="text-end">2 tiếng 5 phút</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-5 pt-4">
                <div class="col-12 text-center shadow-sm rounded overflow-hidden" style="height: 380px; background: #eee;">
                    <div class="transit-map-wrapper">
                        <img id="transit-map-img" src="{{ asset('minimalist-phuquoc.png') }}" class="transit-map-img" alt="Minimalist Map Transit">
                    </div>
                </div>
            </div>
            <div class="row mt-4 text-center">
                <div class="col-12">
                    <p class="text-muted small italic" style="font-family: sans-serif;">
                        * Khu nghỉ dưỡng cung cấp dịch vụ đưa đón sân bay bằng ô tô riêng theo lịch trình đặt trước của quý khách. Vui lòng liên hệ bộ phận lễ tân để được hỗ trợ tốt nhất.
                    </p>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const markers = document.querySelectorAll('.map-marker');
    const detailImg = document.getElementById('detail-img');
    const detailBadge = document.getElementById('detail-badge');
    const detailTitle = document.getElementById('detail-title');
    const detailDesc = document.getElementById('detail-desc');

    // Hàm cập nhật detail
    function updateDetail(marker) {
        const newImg = marker.getAttribute('data-img');
        const newNum = marker.getAttribute('data-num');
        const newTitle = marker.getAttribute('data-title');
        const newDesc = marker.getAttribute('data-desc');

        detailImg.style.opacity = 0;

        setTimeout(() => {
            detailImg.src = newImg;
            detailBadge.innerText = newNum;
            detailTitle.innerText = newTitle;
            detailDesc.innerText = newDesc;

            // Hiện ảnh lại
            detailImg.style.opacity = 1;
        }, 300);
    }

    markers.forEach(marker => {
        marker.addEventListener('click', function() {
            markers.forEach(m => m.classList.remove('active'));

            this.classList.add('active');
            updateDetail(this);
        });
    });

    const transitMapImg = document.getElementById('transit-map-img');
    let transitScale = 1;
    const minTransitScale = 1;
    const maxTransitScale = 1.8;
    const transitStep = 0.08;

    function setTransitScale(scale) {
        transitScale = Math.min(maxTransitScale, Math.max(minTransitScale, scale));
        transitMapImg.style.transform = `scale(${transitScale})`;
    }

    transitMapImg.addEventListener('wheel', function(event) {
        event.preventDefault();
        const delta = event.deltaY > 0 ? -transitStep : transitStep;
        setTransitScale(transitScale + delta);
    }, { passive: false });

    // Mặc định load marker 1 khi trang load lần đầu
    const firstMarker = document.querySelector('.map-marker.active');
    if (firstMarker) {
        updateDetail(firstMarker);
    }
});
</script>
@endsection
@include('user.dangky')
@include('user.dangnhap')
