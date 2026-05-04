@extends('layouts.style')

@section('content')

<style>
    /* ===== STYLE TRANG CHI TIẾT PHÒNG ===== */

    /* Căn giữa toàn bộ nội dung dưới banner */
    .balanced-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .breadcrumb-custom {
        font-size: 11px;
        font-weight: bold;
        color: #673065;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .breadcrumb-custom:hover {
        color: #000;
        transform: translateX(-5px);
    }

    .detail-title {
        font-family: 'Playfair Display', serif;
        font-size: 42px;
        color: #000;
        margin-bottom: 30px;
    }

    /* Banner ảnh full */
    .banner-wrapper {
        width: 100%;
        height: 60vh;
        overflow: hidden;
        margin-bottom: 60px;
    }

    .banner-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Phần mô tả chính */
    .desc-highlight {
        font-family: 'Playfair Display', serif;
        font-size: 26px;
        color: #000;
        margin-bottom: 25px;
        line-height: 1.4;
    }

    .main-desc {
        font-size: 16px;
        color: #555;
        line-height: 1.8;
    }

    /* Cột thông số bên phải */
    .room-specs-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 20px;
    }

    .room-specs-list li {
        font-size: 14px;
        color: #555;
        margin-bottom: 12px;
        position: relative;
        padding-left: 15px;
    }

    .room-specs-list li::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #555;
    }

    .design-link {
        color: #673065;
        text-decoration: none;
        font-size: 13px;
        font-weight: bold;
        display: inline-block;
        margin-bottom: 30px;
    }

    /* Nút Đặt phòng & Hiệu ứng */
    .btn-booking {
        display: block;
        width: 100%;
        background-color: #673065;
        color: white;
        text-align: center;
        padding: 16px;
        text-decoration: none;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 14px;
        transition: all 0.4s ease;
        border: 1px solid #673065;
    }

    .btn-booking:hover {
        background-color: #fff;
        color: #673065;
        box-shadow: 0 8px 20px rgba(103, 48, 101, 0.15);
        transform: translateY(-3px);
    }

    /* Box 2 cột (Tiện nghi & Thông tin) */
    .info-box {
        background-color: #f9f8f6;
        padding: 60px 40px;
        margin-top: 80px;
        margin-bottom: 80px;
    }

    .info-title {
        font-size: 20px;
        color: #673065;
        margin-bottom: 25px;
        font-family: 'Playfair Display', serif;
    }

    .custom-list {
        list-style-type: none;
        padding-left: 0;
        font-size: 14px;
        color: #555;
        line-height: 2.2;
    }

    .custom-list li::before {
        content: "•";
        margin-right: 10px;
        color: #555;
    }
</style>

<div class="container-fluid px-0 mt-5 pt-5">

    <!-- Phần tiêu đề: Căn giữa -->
    <div class="balanced-container">
        <a href="{{ url('/luu-tru') }}" class="breadcrumb-custom">< KHU NGHỈ DƯỠNG</a>
        <h1 class="detail-title">{{ $phong->loai_phong }}</h1>
    </div>

    <!-- Banner (Vẫn Full màn hình cho đẹp) -->
    <div class="banner-wrapper">
        <img src="{{ $phong->anh }}" alt="{{ $phong->loai_phong }}">
    </div>

    <!-- Nội dung chính: Đã được căn giữa và thu hẹp chiều ngang để cân đối -->
    <div class="balanced-container">
        <div class="row">
            <!-- Cột trái: Mô tả (7 phần) -->
            <div class="col-md-7 pe-md-5">
                <div class="desc-highlight">
                    {{ $phong->mo_ta }}
                </div>
                <div class="main-desc">
                    <p>Trải nghiệm không gian đẳng cấp với thiết kế tỉ mỉ, mang lại sự riêng tư tuyệt đối cho kỳ nghỉ của bạn. Tận hưởng vẻ đẹp trọn vẹn của thiên nhiên ngay trong tầm mắt với những tiện ích chuẩn 5 sao.</p>
                </div>
            </div>

            <!-- Cột phải: Thông số & Đặt phòng (5 phần) -->
            <div class="col-md-5 border-start ps-md-5">
                <ul class="room-specs-list">
                    <li>{{ $phong->dien_tich }}</li>
                    <li>Số lượng khách tối đa {{ $phong->so_luong_nguoi }}</li>
                    <li>Hướng phòng: {{ $phong->huong_phong }}</li>
                    <li>{{ $phong->so_phong_ngu }} Phòng ngủ</li>
                </ul>

                <a href="#" class="design-link">Sơ đồ thiết kế ></a>

                <!-- Nút Đặt phòng với hiệu ứng chọn -->
                <a href="#" class="btn-booking">ĐẶT PHÒNG NGAY</a>
            </div>
        </div>

        <!-- Box Tiện nghi & Thông tin quan trọng -->
        <div class="info-box shadow-sm">
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0 pe-md-5">
                    <h3 class="info-title">Tiện nghi</h3>
                    <ul class="custom-list">
                        @if($phong->tien_nghi)
                            @foreach(explode("\n", $phong->tien_nghi) as $item)
                                @if(trim($item) != '')
                                    <li>{{ trim($item) }}</li>
                                @endif
                            @endforeach
                        @else
                            <li>Đang cập nhật...</li>
                        @endif
                    </ul>
                </div>

                <div class="col-md-6 border-start ps-md-5">
                    <h3 class="info-title">Thông tin quan trọng</h3>
                    <ul class="custom-list">
                        @if($phong->thong_tin_quan_trong)
                            @foreach(explode("\n", $phong->thong_tin_quan_trong) as $item)
                                @if(trim($item) != '')
                                    <li>{{ trim($item) }}</li>
                                @endif
                            @endforeach
                        @else
                            <li>Đang cập nhật...</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
