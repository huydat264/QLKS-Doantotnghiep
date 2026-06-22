@extends('layouts.style')

@section('content')

<style>
    body {
        font-family: 'Playfair Display', serif;
        color: #555;
        background-color: #fdfbf9;
    }

    /* Hero Banner Liên Hệ */
    .hero-contact {
        height: 60vh;
        position: relative;
        overflow: hidden;
    }
    .hero-contact img {
        width: 100%;
        height: 60vh;
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
        background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.7));
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
        width: 100%;
    }
    .hero-title {
        font-size: 50px;
        font-weight: 700;
        letter-spacing: 2px;
        margin-bottom: 15px;
    }
    .hero-subtitle {
        font-size: 15px;
        font-family: sans-serif;
        text-transform: uppercase;
        letter-spacing: 3px;
        opacity: 0.9;
    }

    /* Khối Thông Tin Liên Hệ (Info Boxes) */
    .contact-info-section {
        padding: 80px 0 40px 0;
        margin-top: -80px;
        position: relative;
        z-index: 10;
    }
    .info-card {
        background: #ffffff;
        padding: 40px 30px;
        border-radius: 4px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        text-align: center;
        height: 100%;
        border-bottom: 3px solid transparent;
        transition: 0.3s;
    }
    .info-card:hover {
        transform: translateY(-5px);
        border-bottom-color: #673065;
        box-shadow: 0 15px 40px rgba(103,48,101,0.08);
    }
    .info-icon {
        font-size: 32px;
        color: #673065;
        margin-bottom: 20px;
    }
    .info-title {
        font-size: 20px;
        font-weight: bold;
        color: #222;
        margin-bottom: 15px;
    }
    .info-detail {
        font-family: sans-serif;
        font-size: 15px;
        line-height: 1.6;
        color: #666;
    }
    .info-detail a {
        color: #673065;
        text-decoration: none;
        font-weight: bold;
    }

    /* Form & Map Section */
    .contact-form-section {
        padding: 60px 0 100px 0;
    }
    .section-title-sm {
        font-family: sans-serif;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #673065;
        font-weight: 700;
        display: block;
        margin-bottom: 15px;
    }
    .section-title-lg {
        font-size: 36px;
        color: #222;
        font-weight: 600;
        margin-bottom: 30px;
    }
    .form-control-custom {
        border: 1px solid #e0e0e0;
        border-radius: 2px;
        padding: 15px;
        font-family: sans-serif;
        font-size: 14px;
        transition: 0.3s;
        box-shadow: none;
    }
    .form-control-custom:focus {
        border-color: #673065;
        box-shadow: 0 0 0 0.2rem rgba(103,48,101,0.1);
    }
    .btn-submit-contact {
        background-color: #673065;
        color: white;
        font-family: sans-serif;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        padding: 16px 40px;
        border-radius: 30px;
        border: none;
        width: 100%;
        transition: 0.3s;
    }
    .btn-submit-contact:hover {
        background-color: #4a2148;
    }
    .map-container {
        height: 100%;
        min-height: 450px;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .map-container iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }
</style>

<div class="hero-contact">
    <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?q=80&w=2080" alt="Kimboutique Reception">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-subtitle">Chúng tôi luôn lắng nghe</span>
        <h1 class="hero-title">Liên Hệ Kimboutique</h1>
    </div>
</div>

<div class="contact-info-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                    <h3 class="info-title">Địa Chỉ Trực Tiếp</h3>
                    <p class="info-detail">
                        Bãi Dài, Xã Gành Dầu<br>
                        Thành phố Phú Quốc, Tỉnh Kiên Giang<br>
                        Việt Nam
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-icon"><i class="bi bi-telephone-fill"></i></div>
                    <h3 class="info-title">Điện Thoại & Email</h3>
                    <p class="info-detail">
                        Hotline Đặt phòng: <a href="tel:+84123456789">+84 123 456 789</a><br>
                        CSKH: <a href="tel:+84987654321">+84 987 654 321</a><br>
                        Email: <a href="mailto:info@kimboutique.com">info@kimboutique.com</a>
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-icon"><i class="bi bi-clock-fill"></i></div>
                    <h3 class="info-title">Thời Gian Phục Vụ</h3>
                    <p class="info-detail">
                        Lễ tân & CSKH: Hỗ trợ 24/7<br>
                        Dịch vụ Spa: 09:00 - 21:00<br>
                        Nhà hàng: 06:00 - 22:30
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="contact-form-section">
    <div class="container">
        <div class="row g-5 align-items-stretch">

            <div class="col-lg-6">
                <span class="section-title-sm">Gửi Thông Điệp</span>
                <h2 class="section-title-lg">Bạn cần chúng tôi hỗ trợ?</h2>
                <p class="mb-4" style="font-family: sans-serif; line-height: 1.7; color: #666;">
                    Vui lòng để lại thông tin và yêu cầu của quý khách theo mẫu dưới đây. Đội ngũ chuyên viên chăm sóc khách hàng của Kimboutique sẽ liên hệ lại trong thời gian sớm nhất.
                </p>

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 20px; border-radius: 4px;">
                        <strong>Thành công!</strong> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 20px; border-radius: 4px;">
                        <strong>Lỗi!</strong> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('contact.send') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="name" class="form-control form-control-custom @error('name') is-invalid @enderror" placeholder="Họ và tên của bạn *" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <input type="email" name="email" class="form-control form-control-custom @error('email') is-invalid @enderror" placeholder="Địa chỉ Email *" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <input type="tel" name="phone" class="form-control form-control-custom @error('phone') is-invalid @enderror" placeholder="Số điện thoại liên hệ *" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <select name="subject" class="form-select form-control-custom text-muted @error('subject') is-invalid @enderror">
                                <option value="" selected disabled>Chủ đề cần hỗ trợ</option>
                                <option value="Đặt phòng / Combo" {{ old('subject') == 'Đặt phòng / Combo' ? 'selected' : '' }}>Đặt phòng / Combo</option>
                                <option value="Tổ chức sự kiện / Tiệc cưới" {{ old('subject') == 'Tổ chức sự kiện / Tiệc cưới' ? 'selected' : '' }}>Tổ chức sự kiện / Tiệc cưới</option>
                                <option value="Góp ý dịch vụ" {{ old('subject') == 'Góp ý dịch vụ' ? 'selected' : '' }}>Góp ý dịch vụ</option>
                                <option value="Vấn đề khác" {{ old('subject') == 'Vấn đề khác' ? 'selected' : '' }}>Vấn đề khác</option>
                            </select>
                            @error('subject')
                                <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <textarea name="message" class="form-control form-control-custom @error('message') is-invalid @enderror" rows="5" placeholder="Nội dung thông điệp chi tiết *" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn-submit-contact shadow-sm">GỬI YÊU CẦU</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-6">
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3942.3169!2d104.00516!3d10.025831!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a797e2ab8f7bdd:0xd94591017aff41a2!2sKim%20Boutique%20Hotel!5e0!3m2!1svi!2svn!4v1686830400000"
                            style="width: 100%; height: 100%; border: 0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

        </div>
    </div>
</div>

@endsection

{{-- Gắn Modal Đăng ký/Đăng nhập để Header không lỗi --}}
@include('user.dangky')
@include('user.dangnhap')
