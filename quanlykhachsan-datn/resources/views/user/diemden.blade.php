@extends('layouts.style')

@section('content')

<style>
    body {
        font-family: 'Playfair Display', serif;
        color: #666;
    }

    /* ===== HERO SLIDER ===== */
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

    /* ===== CODE CŨ GIỮ NGUYÊN ===== */
    .section {
        padding: 80px 0;
    }

    .heading-main {
        font-size: 32px;
        font-weight: 500;
        text-align: center;
        margin-bottom: 60px;
    }

    .text-content {
        font-size: 16px;
        line-height: 1.6;
    }

    .download-box {
        border-left: 2px solid #673065;
        padding-left: 20px;
    }

    .download-box h6 {
        font-weight: 600;
        letter-spacing: 1px;
    }

    .download-box a {
        color: #673065;
        text-decoration: none;
    }

    .download-box a:hover {
        text-decoration: underline;
    }

    .feature-item {
        display: flex;
        gap: 20px;
        margin-bottom: 50px;
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        flex-shrink: 0;
    }

    .feature-title {
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 10px;
        color: #000;
    }

    .feature-list {
        padding-left: 15px;
    }

    .feature-list li {
        margin-bottom: 6px;
    }
    /* TAB STYLE */
#customTab .nav-link {
    position: relative;
    color: #000;
    transition: 0.3s;
}

/* hover đổi màu */
#customTab .nav-link:hover {
    color: #673065;
}

/* active giữ màu */
#customTab .nav-link.active {
    color: #673065;
    background: none;
}

/* underline (mặc định ẩn) */
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

/* hover hiện underline */
#customTab .nav-link:hover::after {
    width: 100%;
}

/* active luôn có underline */
#customTab .nav-link.active::after {
    width: 100%;
}
/* ===== MAP MARKERS ===== */
.map-marker {
    position: absolute;
    width: 35px;
    height: 35px;
    background-color: #666; /* Màu xám mặc định */
    color: white;
    border: 2px solid white;
    border-radius: 50% 50% 50% 0; /* Tạo hình giọt nước */
    transform: rotate(-45deg);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    padding: 0;
}

/* Xoay lại chữ bên trong cho thẳng */
.map-marker::before {
    content: attr(data-num);
    transform: rotate(45deg);
    font-weight: bold;
    font-size: 14px;
}

/* Ẩn số gốc trong button để dùng số ở ::before */
.map-marker {
    font-size: 0;
}

/* Hiệu ứng hover và khi được chọn (active) chuyển sang màu tím */
.map-marker:hover, .map-marker.active {
    background-color: #673065;
    transform: rotate(-45deg) scale(1.1);
    z-index: 10;
}

</style>

<!-- ===== SLIDER THÊM VÀO ===== -->
<div id="heroCarousel" class="carousel slide hero-slider" data-bs-ride="carousel" data-bs-interval="3000">

    <div class="carousel-inner">

        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e">
        </div>

        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470">
        </div>

        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee">
        </div>

        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1493558103817-58b2924bce98">
        </div>

    </div>

    <!-- overlay -->
    <div class="hero-overlay"></div>

    <!-- text -->
    <div class="hero-content">
        <div class="hero-title">Kimboutique</div>
        <div class="hero-subtitle">Mỗi chuyến bay mở ra một thiên đường của quý khách</div>
    </div>

    <!-- button -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>

</div>

<ul class="nav justify-content-center border-bottom" id="customTab" role="tablist">

    <li class="nav-item" role="presentation">
        <button class="nav-link active fs-6 text-dark fw-normal px-4"
                data-bs-toggle="tab"
                data-bs-target="#diemden"
                type="button">
            ĐIỂM ĐẾN
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link fs-6 text-dark fw-normal px-4"
                data-bs-toggle="tab"
                data-bs-target="#thoidiem"
                type="button">
            THỜI ĐIỂM DU LỊCH
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link fs-6 text-dark fw-normal px-4"
                data-bs-toggle="tab"
                data-bs-target="#huongdan"
                type="button">
            HƯỚNG DẪN DI CHUYỂN
        </button>
    </li>

</ul>

<!-- SECTION 1 -->
<div class="section container">
    <div class="row">

        <!-- LEFT -->
        <div class="col-lg-8 text-content">
            <p>
                Six Senses Con Dao là điểm đến biệt lập đầy quyến rũ, chỉ cách TP. Hồ Chí Minh 45 phút bay hoặc
                130 phút bay từ Hà Nội. Sau lời chào đón nồng hậu, quý khách sẽ bước vào không gian biệt thự
                thanh lịch và khoáng đạt, tọa lạc giữa lòng thiên nhiên xanh ngát.
            </p>

            <p>
                Mỗi biệt thự sở hữu hồ bơi vô cực riêng tư với tầm nhìn đẹp như mơ, kết hợp cùng chuỗi hoạt động
                giải trí dưới nước, liệu trình spa tinh tế và những trải nghiệm ẩm thực đầy cuốn hút, tạo nên
                trải nghiệm nghỉ dưỡng đầy mê hoặc.
            </p>
        </div>

        <!-- RIGHT -->
        <div class="col-lg-4">
            <div class="download-box">
                <h6>TẢI XUỐNG</h6>
                <a href="#">Thông tin tổng quan</a>
            </div>
        </div>

    </div>
</div>

<!-- SECTION 2 -->
<div class="section bg-light">
    <div class="container">

        <h2 class="heading-main">
            Tiện nghi và dịch vụ tại Six Senses Con Dao
        </h2>

        <div class="row">

            <!-- ITEM 1 -->
            <div class="col-lg-6">
                <div class="feature-item">
                    <img src="https://cdn-icons-png.flaticon.com/512/869/869869.png" class="feature-icon">

                    <div>
                        <div class="feature-title">Bãi biển và hồ bơi</div>
                        <ul class="feature-list">
                            <li>Bãi biển Đất Dốc trải dài hơn 1.6km</li>
                            <li>Nước biển xanh ngọc trong vắt</li>
                            <li>Hồ bơi trung tâm với dịch vụ tại chỗ</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ITEM 2 -->
            <div class="col-lg-6">
                <div class="feature-item">
                    <img src="https://cdn-icons-png.flaticon.com/512/1046/1046784.png" class="feature-icon">

                    <div>
                        <div class="feature-title">Tiện ích chung</div>
                        <ul class="feature-list">
                            <li>Dịch vụ đưa đón sân bay</li>
                            <li>Dịch vụ quản gia (GEM)</li>
                            <li>Nhà hàng & quầy bar cao cấp</li>
                            <li>Spa & Yoga Pavilion</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ITEM 3 -->
            <div class="col-lg-6">
                <div class="feature-item">
                    <img src="https://cdn-icons-png.flaticon.com/512/2972/2972185.png" class="feature-icon">

                    <div>
                        <div class="feature-title">Hoạt động đặc sắc</div>
                        <ul class="feature-list">
                            <li>Tour khám phá thiên nhiên</li>
                            <li>Hoạt động ngoài trời</li>
                            <li>Trải nghiệm văn hóa địa phương</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ITEM 4 -->
            <div class="col-lg-6">
                <div class="feature-item">
                    <img src="https://cdn-icons-png.flaticon.com/512/1995/1995574.png" class="feature-icon">

                    <div>
                        <div class="feature-title">Trải nghiệm gia đình</div>
                        <ul class="feature-list">
                            <li>Bữa tối riêng tại biệt thự</li>
                            <li>Câu lạc bộ trẻ em</li>
                            <li>Lớp học nấu ăn</li>
                            <li>Đạp xe khám phá</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
<div class="section container">
    <h2 class="heading-main">Khám phá vị trí</h2>
    <div class="row shadow-sm bg-white rounded overflow-hidden">

        <div class="col-lg-5 p-0 bg-dark text-white d-flex flex-column">
            <div class="position-relative" style="height: 300px; overflow: hidden;">
                <img id="detail-img" src="https://images.unsplash.com/photo-1589710788393-d289945f1b1b"
                     class="w-100 h-100 object-fit-cover" style="transition: 0.5s;">
                <div id="detail-badge" class="position-absolute bottom-0 start-0 bg-purple p-3 fw-bold"
                     style="background: #673065; min-width: 60px; text-align: center;">
                    1
                </div>
            </div>
            <div class="p-4 flex-grow-1">
                <h3 id="detail-title" class="h4 mb-3">VinWonders Phú Quốc</h3>
                <p id="detail-desc" class="text-light opacity-75">
                    Công viên chủ đề lớn nhất Việt Nam với vô vàn trò chơi hấp dẫn.
                </p>
            </div>
        </div>

        <div class="col-lg-7 p-0">
            <div class="map-container position-relative w-100"
                 style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/1/1a/Phu_Quoc_Island_Map.png');
                        background-size: cover;
                        background-position: center;
                        height: 600px;
                        background-color: #f8f9fa;">

                <button class="map-marker active" style="top: 15%; left: 30%;"
                        data-num="1"
                        data-img="https://images.unsplash.com/photo-1589710788393-d289945f1b1b"
                        data-title="VinWonders Phú Quốc"
                        data-desc="Công viên chủ đề lớn nhất Việt Nam với vô vàn trò chơi hấp dẫn.">
                    1
                </button>

                <button class="map-marker" style="top: 45%; left: 35%;"
                        data-num="2"
                        data-img="https://images.unsplash.com/photo-1596422846543-75c6fc18a593"
                        data-title="Thị trấn Dương Đông"
                        data-desc="Trung tâm sầm uất nhất đảo với chợ đêm và nhiều quán ăn ngon.">
                    2
                </button>

                <button class="map-marker" style="top: 80%; left: 60%;"
                        data-num="3"
                        data-img="https://images.unsplash.com/photo-1540202404-a2f290338017"
                        data-title="Bãi Sao"
                        data-desc="Bãi biển đẹp nhất Phú Quốc với bờ cát trắng mịn và làn nước trong xanh.">
                    3
                </button>
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

    markers.forEach(marker => {
        marker.addEventListener('click', function() {
            // Xóa class active ở tất cả marker
            markers.forEach(m => m.classList.remove('active'));
            // Thêm class active cho marker được click
            this.classList.add('active');

            // Lấy data từ marker
            const newImg = this.getAttribute('data-img');
            const newNum = this.getAttribute('data-num');
            const newTitle = this.getAttribute('data-title');
            const newDesc = this.getAttribute('data-desc');

            // Hiệu ứng mờ dần ảnh trước khi đổi
            detailImg.style.opacity = 0;

            setTimeout(() => {
                // Cập nhật nội dung
                detailImg.src = newImg;
                detailBadge.innerText = newNum;
                detailTitle.innerText = newTitle;
                detailDesc.innerText = newDesc;

                // Hiện ảnh lại
                detailImg.style.opacity = 1;
            }, 300); // Đợi 0.3s cho mượt
        });
    });
});
</script>
@endsection
@include('user.dangky')
@include('user.dangnhap')
