@extends('layouts.style')

@section('content')

<style>
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

    <div class="balanced-container">
        <a href="{{ url('/combo') }}" class="breadcrumb-custom">< DANH SÁCH COMBO</a>
        <h1 class="detail-title">{{ $combo->ten_combo }}</h1>
    </div>

    <div class="banner-wrapper">
        <img src="{{ $combo->hinh_anh }}" alt="{{ $combo->ten_combo }}">
    </div>

    <div class="balanced-container">
        <div class="row">
            <div class="col-md-7 pe-md-5">
                <div class="desc-highlight">
                    {{ $combo->mo_ta }}
                </div>
                <div class="main-desc">
                    <p>Khám phá trọn vẹn kỳ nghỉ dưỡng với các gói ưu đãi thiết kế riêng biệt. Tận hưởng mọi dịch vụ đẳng cấp nhất và tiết kiệm thời gian chuẩn bị cho chuyến đi của bạn.</p>
                </div>
            </div>

            <div class="col-md-5 border-start ps-md-5">
                <ul class="room-specs-list">
                    <li><strong>Áp dụng:</strong> Phòng hạng {{ $combo->loai_phong_ap_dung }}</li>
                    <li><strong>Thời gian:</strong> {{ $combo->so_dem_luu_tru }} đêm lưu trú</li>
                    <li><strong>Giá phòng định mức:</strong> {{ number_format($combo->gia_phong_dinh_muc, 0, ',', '.') }} VNĐ</li>
                    <li><strong>Giá trọn gói:</strong> <span class="fw-bold" style="color: #673065; font-size: 18px;">{{ number_format($combo->gia_combo, 0, ',', '.') }} VNĐ</span></li>
                </ul>

                <a href="#" class="design-link">Xem quy định áp dụng ></a>
                @auth
    <a href="{{ route('booking.check', ['type' => 'phong', 'id' => $phong->id_phong]) }}"
       class="btn-booking">
       ĐẶT PHÒNG NGAY
    </a>
@else
    <a href="javascript:void(0)"
       class="btn-booking"
       data-bs-toggle="modal"
       data-bs-target="#loginModal"> ĐẶT PHÒNG NGAY
    </a>
@endauth
            </div>
        </div>

        <div class="info-box shadow-sm">
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0 pe-md-5">
                    <h3 class="info-title">Quyền lợi Gói Combo</h3>
                    <ul class="custom-list">
                        @if($combo->quyen_loi)
                            @foreach(explode("\n", $combo->quyen_loi) as $item)
                                @if(trim($item) != '')
                                    <li>{{ trim($item) }}</li>
                                @endif
                            @endforeach
                        @else
                            <li>Đang cập nhật quyền lợi...</li>
                        @endif
                    </ul>
                </div>

                <div class="col-md-6 border-start ps-md-5">
                    <h3 class="info-title">Điều khoản áp dụng</h3>
                    <ul class="custom-list">
                        @if($combo->dieu_khoan)
                            @foreach(explode("\n", $combo->dieu_khoan) as $item)
                                @if(trim($item) != '')
                                    <li>{{ trim($item) }}</li>
                                @endif
                            @endforeach
                        @else
                            <li>Đang cập nhật điều khoản...</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
