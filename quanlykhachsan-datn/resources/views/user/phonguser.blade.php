@extends('layouts.style')

@section('content')

<style>
    /* ===== STYLE GIAO DIỆN PHÒNG LƯU TRÚ ===== */
    body {
        font-family: 'Arial', sans-serif; /* Đổi font tùy ý mày */
    }

    .room-card {
        margin-bottom: 60px;
    }

    /* Khung ảnh */
    .room-img-wrapper {
        position: relative;
        overflow: hidden;
        height: 100%;
        min-height: 250px;
    }

    .room-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Tiêu đề phòng */
    .room-title {
        font-family: 'Playfair Display', serif;
        font-size: 26px;
        color: #000;
        margin-bottom: 15px;
        font-weight: normal;
    }

    /* Style cho Tab Bootstrap (Mô tả, Thông tin...) */
    .nav-tabs-custom {
        border-bottom: none;
        margin-bottom: 15px;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        color: #888;
        font-size: 11px;
        font-weight: bold;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 15px 10px 0;
        margin-right: 15px;
        border-bottom: 2px solid transparent;
        background: transparent;
    }

    .nav-tabs-custom .nav-link:hover,
    .nav-tabs-custom .nav-link.active {
        color: #673065;
    }

    .room-desc {
        font-size: 15px;
        color: #555;
        line-height: 1.6;
    }

    .read-more {
        color: #673065;
        font-weight: bold;
        font-size: 12px;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Thông số phòng bên phải */
    .room-specs {
        font-size: 14px;
        color: #555;
        line-height: 1.8;
    }

    /* Dấu chấm đằng trước thông số */
    .room-specs li::before {
        content: "•";
        margin-right: 8px;
        color: #666;
    }

    .form-check-label {
        font-size: 13px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Thanh bar đặt phòng bên dưới */
    .booking-bar {
        background-color: #f8f7f5;
        padding: 15px 25px;
        border-radius: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
    }

    .btn-book {
        background-color: #673065;
        color: white;
        border-radius: 25px;
        padding: 10px 30px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        border: none;
        transition: 0.3s;
    }

    .btn-book:hover {
        background-color: #4a2148;
        color: white;
    }
</style>

<div class="container mt-5 pt-5">

    <div class="row mb-5">
        <div class="col-12 text-center text-md-start">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 32px; color: #000;">
                Kiểm tra tình trạng phòng trống và đặt trực tiếp
            </h2>
            <div class="d-flex gap-4 mt-3 text-uppercase" style="font-size: 12px; font-weight: bold; color: #673065;">
                <span style="cursor: pointer;">V BỘ LỌC</span>
                <span style="cursor: pointer;">V SẮP XẾP THEO</span>
                <span style="cursor: pointer;">V VND</span>
            </div>
        </div>
    </div>

    @foreach($phongs as $phong)
    <div class="row room-card">

        <div class="col-lg-5 mb-3 mb-lg-0">
            <div class="room-img-wrapper">
                <img src="{{ asset('storage/' . $phong->anh) }}"
                     alt="{{ $phong->loai_phong }}"
                     onerror="this.src='https://images.unsplash.com/photo-1582719478250-c89cae4dc85b';">
            </div>
        </div>

        <div class="col-lg-7 d-flex flex-column">

            <div class="row flex-grow-1">

                <div class="col-md-7 pe-md-4">
                    <h3 class="room-title">
                        {{ $phong->loai_phong }} - Phòng {{ $phong->so_phong }}
                    </h3>

                    <ul class="nav nav-tabs-custom" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#desc-{{ $phong->id_phong }}">MÔ TẢ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#info-{{ $phong->id_phong }}">THÔNG TIN QUAN TRỌNG</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#amenities-{{ $phong->id_phong }}">TIỆN NGHI</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div id="desc-{{ $phong->id_phong }}" class="tab-pane active">
                            <p class="room-desc">
                                {{ Str::limit($phong->mo_ta, 180, '...') }}
                            </p>
                            <a href="#" class="read-more">ĐỌC THÊM ></a>
                        </div>

                        <div id="info-{{ $phong->id_phong }}" class="tab-pane fade">
                            <p class="room-desc">Giờ nhận phòng: 14:00. Giờ trả phòng: 12:00.</p>
                        </div>

                        <div id="amenities-{{ $phong->id_phong }}" class="tab-pane fade">
                            <p class="room-desc">Hồ bơi riêng, Wifi miễn phí, Tivi màn hình phẳng...</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-5 mt-4 mt-md-0 border-start ps-md-4">
                    <ul class="list-unstyled room-specs">
                        <li>2,090 foot vuông / 194 mét vuông</li>
                        <li>Số lượng khách tối đa {{ $phong->so_luong_nguoi }}</li>
                        <li>Hướng phòng: Hướng biển</li>
                        <li>1 Phòng ngủ</li>
                    </ul>

                    <a href="#" class="read-more d-block mt-4 mb-3">SƠ ĐỒ THIẾT KẾ ></a>

                    <div class="form-check mt-3">
                        <input class="form-check-input shadow-none" type="checkbox" id="compare-{{ $phong->id_phong }}">
                        <label class="form-check-label text-muted" for="compare-{{ $phong->id_phong }}">
                            THÊM ĐỂ SO SÁNH
                        </label>
                    </div>
                </div>

            </div>

            <div class="booking-bar mt-auto">
                <span class="text-muted" style="font-size: 14px;">
                    Nhập ngày để xem mức giá và đặt phòng (Giá tham khảo: {{ number_format($phong->gia_phong, 0, ',', '.') }} VNĐ)
                </span>
                <a href="#" class="btn btn-book text-decoration-none">NHẬP NGÀY</a>
            </div>

        </div>

    </div>
    @endforeach

</div>

@endsection
