@extends('layouts.style')

@section('content')

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show m-3" role="alert" style="margin-top: 100px;">
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<style>
    body {
        font-family: 'Playfair Display', serif;
        color: #555;
        background-color: #fdfbf9;
    }

    /* Hero Banner Section */
    .hero-dining {
        height: 85vh;
        position: relative;
        overflow: hidden;
    }

    .hero-dining img {
        width: 100%;
        height: 85vh;
        object-fit: cover;
        animation: zoomEffect 4s ease-in-out forwards;
    }

    @keyframes zoomEffect {
        0% { transform: scale(1); }
        100% { transform: scale(1.08); }
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.35);
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
    }

    .hero-title {
        font-size: 56px;
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 15px;
    }

    .hero-subtitle {
        font-size: 16px;
        font-family: sans-serif;
        text-transform: uppercase;
        letter-spacing: 3px;
        opacity: 0.9;
    }

    /* Philosophy Section */
    .philosophy-section {
        padding: 90px 0;
        background-color: #ffffff;
    }

    .section-tag {
        font-family: sans-serif;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #673065;
        font-weight: 600;
        display: block;
        margin-bottom: 15px;
    }

    .section-title {
        font-size: 36px;
        color: #222;
        font-weight: 600;
        margin-bottom: 30px;
    }

    .philosophy-text {
        font-family: sans-serif;
        font-size: 16px;
        line-height: 1.9;
        color: #666;
        max-width: 800px;
        margin: 0 auto;
    }

    /* Restaurant Post Card Style (Alternating Grid) */
    .restaurant-row {
        margin-bottom: 100px;
        align-items: center;
    }

    .restaurant-img-wrapper {
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.04);
        border-radius: 4px;
        height: 480px;
    }

    .restaurant-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .restaurant-row:hover .restaurant-img-wrapper img {
        transform: scale(1.04);
    }

    .restaurant-info-box {
        padding: 40px;
    }

    .restaurant-name {
        font-size: 28px;
        color: #222;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .restaurant-meta {
        font-family: sans-serif;
        font-size: 14px;
        color: #888;
        margin-bottom: 25px;
        border-bottom: 1px solid #f1eeea;
        padding-bottom: 15px;
    }

    .restaurant-meta span {
        margin-right: 20px;
    }

    .restaurant-meta i {
        color: #673065;
        margin-right: 6px;
    }

    .restaurant-desc {
        font-family: sans-serif;
        font-size: 15px;
        line-height: 1.8;
        color: #666;
        margin-bottom: 30px;
        text-align: justify;
    }

    .btn-dining-outline {
        font-family: sans-serif;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 2px;
        color: #673065;
        border: 1px solid #673065;
        padding: 12px 30px;
        background: transparent;
        transition: all 0.3s ease;
        text-transform: uppercase;
        border-radius: 25px;
    }

    .btn-dining-outline:hover {
        background-color: #673065;
        color: white;
    }

    /* Exclusive Experiences Banner Grid */
    .experience-card {
        background: white;
        border: 1px solid #f1eeea;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.01);
        transition: transform 0.3s ease;
    }

    .experience-card:hover {
        transform: translateY(-5px);
    }

    .experience-img {
        height: 260px;
        width: 100%;
        object-fit: cover;
    }

    .experience-body {
        padding: 25px;
    }

    .experience-title {
        font-size: 20px;
        font-weight: bold;
        color: #333;
        margin-bottom: 12px;
    }

    .experience-desc {
        font-family: sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #777;
    }
</style>

<div class="hero-dining">
    <img src="https://images.unsplash.com/photo-1544025162-d76694265947?q=80&w=2069" alt="Culinary Hero Image">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-subtitle">Tinh Hoa Ẩm Thực Tại Kimboutique</span>
        <h1 class="hero-title">Trải Nghiệm Ẩm Thực</h1>
    </div>
</div>

<div class="philosophy-section text-center">
    <div class="container">
        <span class="section-tag">Triết Lý Của Chúng Tôi</span>
        <h2 class="section-title">Từ Khu Vườn Hữu Cơ Đến Bàn Ăn Thượng Hạng</h2>
        <p class="philosophy-text">
            Tại Kimboutique, mỗi bữa ăn là một hành trình tôn vinh văn hóa bản địa kết hợp với nghệ thuật ẩm thực đương đại thế giới. Chúng tôi ưu tiên lựa chọn những nguyên liệu hải sản đánh bắt trong ngày tươi ngon nhất và các loại rau củ hữu cơ được thu hoạch trực tiếp từ khu vườn của Resort, mang đến cho quý khách những hương vị thuần khiết, trọn vẹn và tốt cho sức khỏe.
        </p>
    </div>
</div>

<div class="container" style="padding: 60px 0 30px 0;">

    <div class="row restaurant-row">
        <div class="col-lg-6">
            <div class="restaurant-img-wrapper">
                <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070" alt="Nhà hàng By The Beach">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="restaurant-info-box">
                <span class="section-tag">Nhà Hàng Chính</span>
                <h3 class="restaurant-name">Nhà Hàng By The Beach</h3>
                <div class="restaurant-meta">
                    <span><i class="bi bi-clock"></i> 06:00 - 22:00</span>
                    <span><i class="bi bi-egg-fried"></i> Ẩm thực Quốc tế & Hải sản</span>
                </div>
                <p class="restaurant-desc">
                    Sở hữu tầm nhìn panorama trọn vẹn ra đại dương xanh ngát và đón những làn gió biển mát rượi, By The Beach là không gian lý tưởng để bắt đầu ngày mới với buffet sáng phong phú hoặc tận hưởng bữa tối lãng mạn dưới ánh nến. Thực đơn tập trung vào hải sản địa phương tươi sống được chế biến tinh tế.
                </p>
                <button class="btn btn-dining-outline" data-bs-toggle="modal" data-bs-target="#orderTableModal">Đặt Bàn Ngay</button>
            </div>
        </div>
    </div>

    <div class="row restaurant-row flex-css-direct">
        <div class="col-lg-6 order-lg-2">
            <div class="restaurant-img-wrapper">
                <img src="https://images.unsplash.com/photo-1552566626-52f8b828add9?q=80&w=2070" alt="Nhà hàng Dining Room">
            </div>
        </div>
        <div class="col-lg-6 order-lg-1">
            <div class="restaurant-info-box">
                <span class="section-tag">Không Gian Ấm Cúng</span>
                <h3 class="restaurant-name">The Dining Room</h3>
                <div class="restaurant-meta">
                    <span><i class="bi bi-clock"></i> 11:30 - 21:30</span>
                    <span><i class="bi bi-translate"></i> Ẩm thực Việt truyền thống</span>
                </div>
                <p class="restaurant-desc">
                    Với thiết kế bếp mở hiện đại độc đáo, The Dining Room dẫn dắt thực khách bước vào hành trình khám phá chiều sâu của ẩm thực Việt Nam ba miền. Các món ăn truyền thống được tái hiện lại một cách đầy sáng tạo qua bàn tay tài hoa của các đầu bếp ngôi sao, mang lại trải nghiệm vừa quen thuộc vừa bất ngờ.
                </p>
                <button class="btn btn-dining-outline" data-bs-toggle="modal" data-bs-target="#orderTableModal">Đặt Bàn Ngay</button>
            </div>
        </div>
    </div>

    <div class="row restaurant-row">
        <div class="col-lg-6">
            <div class="restaurant-img-wrapper">
                <img src="https://images.unsplash.com/photo-1470337458703-46ad1756a187?q=80&w=2069" alt="Elephant Bar">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="restaurant-info-box">
                <span class="section-tag">Lounge & Bar</span>
                <h3 class="restaurant-name">Elephant Bar</h3>
                <div class="restaurant-meta">
                    <span><i class="bi bi-clock"></i> 16:00 - Midnight</span>
                    <span><i class="bi bi-cup-straw"></i> Cocktails & Rượu vang chọn lọc</span>
                </div>
                <p class="restaurant-desc">
                    Mang đậm phong cách kiến trúc thuộc địa cổ điển sang trọng pha lẫn nét nhiệt đới phóng khoáng, Elephant Bar là nơi tuyệt vời để thả mình trên những chiếc ghế sofa êm ái, thưởng thức những ly cocktail thủ công đặc trưng và lắng nghe giai điệu nhạc Jazz du dương khi hoàng hôn dần buông xuống.
                </p>
                <button class="btn btn-dining-outline" data-bs-toggle="modal" data-bs-target="#orderTableModal">Đặt Bàn Ngay</button>
            </div>
        </div>
    </div>

</div>

<div class="container-fluid" style="background-color: #f6f3ee; padding: 90px 0;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag">Trải Nghiệm Đặc Quyền</span>
            <h2 class="section-title">Khoảnh Khắc Ẩm Thực Đáng Nhớ</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="experience-card">
                    <img src="https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?q=80&w=2070" class="experience-img" alt="Beach Dinner">
                    <div class="experience-body">
                        <h4 class="experience-title">Bữa Tối Lãng Mạn Bên Bờ Biển</h4>
                        <p class="experience-desc">Một chiếc bàn ăn riêng tư được set-up tinh tế ngay sát mép sóng, bao bọc bởi ánh nến lung linh và ngàn ánh sao, dành riêng cho khoảnh khắc thăng hoa của tình yêu.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="experience-card">
                    <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=1974" class="experience-img" alt="In-villa BBQ">
                    <div class="experience-body">
                        <h4 class="experience-title">Tiệc BBQ Tư Gia Tại Biệt Thự</h4>
                        <p class="experience-desc">Đầu bếp riêng và nhân viên phục vụ của resort sẽ trực tiếp chuẩn bị và nướng các món hải sản cao cấp ngay tại sân vườn hoặc hồ bơi biệt thự riêng của bạn.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="experience-card">
                    <img src="https://images.unsplash.com/photo-1556910103-1c02745aae4d?q=80&w=2070" class="experience-img" alt="Cooking Class">
                    <div class="experience-body">
                        <h4 class="experience-title">Lớp Học Nấu Ăn Truyền Thống</h4>
                        <p class="experience-desc">Đồng hành cùng Bếp trưởng ghé thăm khu vườn hữu cơ, tự tay hái nguyên liệu và học công thức bí truyền chế biến các món ăn đặc sản đậm chất bản địa.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="orderTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 4px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold" style="color: #673065; font-family: 'Playfair Display', serif; font-size: 24px;">Yêu Cầu Đặt Bàn</h5>
                <button type="button" class="btn-close" data-bs-shadow="none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4" style="font-family: sans-serif;">
                <form action="{{ route('amthuc.datban') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold text-uppercase">Họ và tên</label>
                        <input type="text" name="name" class="form-control" style="border-radius: 2px; padding: 10px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold text-uppercase">Số điện thoại</label>
                        <input type="tel" name="phone" class="form-control" style="border-radius: 2px; padding: 10px;" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">Ngày đặt</label>
                            <input type="date" name="date" class="form-control" style="border-radius: 2px; padding: 10px;" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">Số khách</label>
                            <input type="number" name="guests" class="form-control" min="1" value="2" style="border-radius: 2px; padding: 10px;" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold text-uppercase">Ghi chú đặc biệt</label>
                        <textarea name="notes" class="form-control" rows="2" style="border-radius: 2px;" placeholder="Ví dụ: Dị ứng thực phẩm, Ghế trẻ em, Bàn sát biển..."></textarea>
                    </div>
                    <button type="submit" class="btn w-100 text-white fw-bold py-2" style="background-color: #673065; border-radius: 25px; letter-spacing: 1px; font-size: 14px;">GỬI YÊU CẦU ĐẶT BÀN</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- GẮN ĐẦY ĐỦ CÁC MODAL LOGIN/REGISTER ĐỂ KHÔNG BỊ LỖI HEADER --}}
@include('user.dangky')
@include('user.dangnhap')
