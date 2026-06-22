@extends('layouts.style')

@section('content')

<style>
    body {
        font-family: 'Playfair Display', serif;
        color: #555;
        background-color: #fdfbf9;
    }

    /* Hero Banner Giới Thiệu */
    .hero-about {
        height: 70vh;
        position: relative;
        overflow: hidden;
    }
    .hero-about img {
        width: 100%;
        height: 70vh;
        object-fit: cover;
        animation: panImage 10s linear infinite alternate;
    }
    @keyframes panImage {
        0% { transform: scale(1.05) translateY(0); }
        100% { transform: scale(1.05) translateY(-2%); }
    }
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(0,0,0,0.6));
        z-index: 1;
    }
    .hero-content {
        position: absolute;
        top: 55%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        z-index: 2;
        width: 90%;
        max-width: 800px;
    }
    .hero-title {
        font-size: 56px;
        font-weight: 700;
        letter-spacing: 2px;
        margin-bottom: 20px;
    }
    .hero-subtitle {
        font-size: 16px;
        font-family: sans-serif;
        text-transform: uppercase;
        letter-spacing: 4px;
        opacity: 0.9;
    }

    /* Khối Câu chuyện thương hiệu */
    .story-section {
        padding: 100px 0;
        background-color: #ffffff;
    }
    .story-tag {
        font-family: sans-serif;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #673065;
        font-weight: 700;
        display: block;
        margin-bottom: 15px;
    }
    .story-title {
        font-size: 40px;
        color: #222;
        font-weight: 600;
        line-height: 1.3;
        margin-bottom: 30px;
    }
    .story-text {
        font-family: sans-serif;
        font-size: 16px;
        line-height: 1.9;
        color: #666;
        text-align: justify;
        margin-bottom: 20px;
    }
    .story-image-wrapper {
        position: relative;
        padding-left: 40px;
        padding-bottom: 40px;
    }
    .story-img-main {
        width: 100%;
        border-radius: 4px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        position: relative;
        z-index: 2;
    }
    .story-img-bg {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 70%;
        height: 70%;
        background-color: #f6f3ee;
        z-index: 1;
        border-radius: 4px;
    }

    /* Khối Giá trị cốt lõi */
    .values-section {
        padding: 100px 0;
        background-color: #faf8f5;
    }
    .value-card {
        background: #fff;
        padding: 40px 30px;
        border-radius: 4px;
        border: 1px solid #f1eeea;
        text-align: center;
        height: 100%;
        transition: 0.4s;
    }
    .value-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(103,48,101,0.06);
        border-color: #673065;
    }
    .value-icon {
        width: 70px;
        height: 70px;
        background-color: #fdfaf6;
        color: #673065;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 25px;
        transition: 0.4s;
    }
    .value-card:hover .value-icon {
        background-color: #673065;
        color: #fff;
    }
    .value-title {
        font-size: 22px;
        font-weight: bold;
        color: #222;
        margin-bottom: 15px;
    }
    .value-desc {
        font-family: sans-serif;
        font-size: 15px;
        line-height: 1.7;
        color: #777;
    }

    /* Khối Con số ấn tượng */
    .stats-section {
        padding: 80px 0;
        background-color: #673065;
        color: white;
    }
    .stat-number {
        font-size: 50px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .stat-label {
        font-family: sans-serif;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 2px;
        opacity: 0.8;
    }

    /* Khối Call to Action */
    .cta-section {
        padding: 120px 0;
        text-align: center;
        background: url('https://images.unsplash.com/photo-1499793983690-e29da59ef1c2?q=80&w=2070') center/cover fixed;
        position: relative;
    }
    .cta-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.9);
    }
    .cta-content {
        position: relative;
        z-index: 2;
        max-width: 700px;
        margin: 0 auto;
    }
    .btn-cta-primary {
        display: inline-block;
        background-color: #673065;
        color: white;
        font-family: sans-serif;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        padding: 16px 40px;
        border-radius: 30px;
        text-decoration: none;
        margin-top: 30px;
        transition: 0.3s;
        border: 2px solid #673065;
    }
    .btn-cta-primary:hover {
        background-color: transparent;
        color: #673065;
    }
</style>

<div class="hero-about">
    <img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=2025" alt="Kimboutique Resort Overview">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-subtitle">Câu chuyện của chúng tôi</span>
        <h1 class="hero-title">Định Nghĩa Lại Sự Sang Trọng</h1>
    </div>
</div>

<div class="story-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 pe-lg-5 mb-5 mb-lg-0">
                <span class="story-tag">Khởi Nguồn</span>
                <h2 class="story-title">Hành trình kiến tạo một hệ sinh thái nghỉ dưỡng giữa đảo Phú Quốc.</h2>
                <p class="story-text">
                    Được thai nghén từ khát vọng mang đến một không gian lưu trú tĩnh lặng, tách biệt phần nào khỏi nhịp sống thị thành ồn ã, <strong>Kimboutique Holtel</strong> ra đời như một nốt trầm xao xuyến giữa bản hòa ca của đại dương và núi rừng nguyên sinh.
                </p>
                <p class="story-text">
                    Chúng tôi không chỉ xây dựng một khách sạn, chúng tôi kiến tạo một hệ sinh thái trải nghiệm. Nơi mọi chi tiết,từng đường nét kiến trúc đậm chất giao thoa Á-Âu, hương vị ẩm thực tươi sống được đánh bắt và chế biến trong ngày chứa giá trị dinh dưỡng thuần khiết, đến nụ cười hiếu khách của đội ngũ nhân viên đều hướng tới một mục đích duy nhất: Đánh thức những giác quan ngủ quên và chữa lành tâm hồn của quý khách.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="story-image-wrapper">
                    <div class="story-img-bg"></div>
                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070" class="story-img-main" alt="Resort Architecture">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="values-section">
    <div class="container">
        <div class="text-center mb-5 pb-3">
            <span class="story-tag">Giá Trị Cốt Lõi</span>
            <h2 class="story-title">Lời Cam Kết Từ Kimboutique</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="value-card">
                    <div class="value-icon"><i class="bi bi-gem"></i></div>
                    <h3 class="value-title">Sang Trọng Tinh Tế</h3>
                    <p class="value-desc">Sự sang trọng không nằm ở những chi tiết phô trương, mà ẩn chứa trong sự chỉn chu, tỉ mỉ của thiết kế không gian và chất lượng nội thất thủ công độc bản.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card">
                    <div class="value-icon"><i class="bi bi-person-heart"></i></div>
                    <h3 class="value-title">Dịch Vụ Cá Nhân Hóa</h3>
                    <p class="value-desc">Mỗi vị khách là một cá thể duy nhất. Đội ngũ quản gia riêng của chúng tôi luôn sẵn sàng thấu hiểu và đáp ứng mọi nhu cầu dù là nhỏ nhất của bạn.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card">
                    <div class="value-icon"><i class="bi bi-tree"></i></div>
                    <h3 class="value-title">Tôn Trọng Và Bảo Tồn Tự Nhiên</h3>
                    <p class="value-desc">Phát triển bền vững là kim chỉ nam. Chúng tôi giảm thiểu rác thải nhựa, sử dụng năng lượng tái tạo và bảo tồn nguyên vẹn cảnh quan môi trường xung quanh.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="stats-section">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-number">02</div>
                <div class="stat-label">Bể bơi vô cực</div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-number">5</div>
                <div class="stat-label">Giải thưởng quốc tế về dịch vụ khách sạn</div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-number">03</div>
                <div class="stat-label">Nhà hàng & Bar</div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-number">98%</div>
                <div class="stat-label">Khách hàng hài lòng (Trustmebro)</div>
            </div>
        </div>
    </div>
</div>

<div class="cta-section">
    <div class="cta-overlay"></div>
    <div class="cta-content container">
        <h2 class="story-title" style="font-size: 46px; margin-bottom: 20px;">Sẵn sàng cho một kỳ nghỉ trong mơ?</h2>
        <p class="story-text text-center" style="font-size: 18px;">Hãy để Kimboutique viết tiếp câu chuyện trải nghiệm tuyệt vời của bạn. Khám phá các hạng phòng sang trọng và những ưu đãi đặc quyền ngay hôm nay.</p>

        <a href="{{ route('phong.user') }}" class="btn-cta-primary shadow">BẮT ĐẦU HÀNH TRÌNH</a>
    </div>
</div>

@endsection

{{-- Gắn Modal Đăng ký/Đăng nhập để Header không lỗi --}}
@include('user.dangky')
@include('user.dangnhap')
