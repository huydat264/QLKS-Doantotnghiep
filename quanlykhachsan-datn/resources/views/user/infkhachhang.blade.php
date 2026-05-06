@extends('layouts.style')

@section('content')
<style>
    .booking-wrapper { background-color: #faf8f5; min-height: 100vh; padding-top: 120px; padding-bottom: 60px; }
    .form-container { max-width: 800px; margin: 0 auto; background: white; padding: 50px; border-radius: 4px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); }
    .step-title { font-family: 'Playfair Display', serif; font-size: 30px; color: #111; margin-bottom: 10px; }
    .step-subtitle { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; }
    .form-label { font-size: 11px; font-weight: bold; color: #673065; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .form-control { border: none; border-bottom: 1px solid #e0e0e0; border-radius: 0; padding: 10px 0; font-size: 15px; margin-bottom: 30px; background: transparent; }
    .form-control:focus { box-shadow: none; border-bottom: 1px solid #673065; background: transparent; }
    .btn-save { background-color: #673065; color: white; padding: 14px 45px; border-radius: 25px; border: none; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; margin-top: 20px; }
    .btn-save:hover { background-color: #4a2148; transform: translateY(-2px); }
</style>

<div class="booking-wrapper">
    <div class="container">
        <div class="form-container">
            <div class="text-center">
                <p class="step-subtitle">Bước 1: Hồ sơ khách hàng</p>
                <h2 class="step-title">Hoàn thiện thông tin đặt phòng</h2>
                <p class="text-muted small mb-5">Vui lòng điền thông tin chính xác theo căn cước công dân để làm thủ tục nhận phòng tại Resort.</p>
            </div>

            <form action="{{ route('booking.save_customer') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">Họ và tên khách hàng</label>
                        <input type="text" name="ho_ten" class="form-control" placeholder="Nhập đầy đủ họ và tên" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="ngay_sinh" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giới tính</label>
                        <select name="gioi_tinh" class="form-control" required>
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại liên hệ</label>
                        <input type="tel" name="so_dien_thoai" class="form-control" placeholder="Số điện thoại di động" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Địa chỉ định danh Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Địa chỉ email cá nhân" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Số định danh cá nhân (CCCD / Passport)</label>
                        <input type="text" name="cccd" class="form-control" placeholder="Nhập số CCCD gồm 12 số hoặc hộ chiếu" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Địa chỉ cư trú</label>
                        <input type="text" name="dia_chi" class="form-control" placeholder="Tỉnh / Thành phố, Quận / Huyện, Phường / Xã" required>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn-save shadow-sm">Tiếp tục luồng đặt phòng</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
