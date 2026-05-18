@extends('layouts.style')

@section('content')
<style>
    .history-wrapper { background-color: #faf8f5; min-height: 100vh; padding-top: 140px; padding-bottom: 60px; }
    .history-title { font-family: 'Playfair Display', serif; color: #673065; font-size: 28px; margin-bottom: 30px; }
    .booking-card { background: white; border: 1px solid #f1eeea; padding: 25px; border-radius: 4px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); transition: 0.3s; position: relative; }
    .booking-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(103,48,101,0.06); border-color: #673065; }
    .badge-status { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: bold; }
    .status-xacnhan { background-color: #e8f5e9; color: #2e7d32; }
    .status-cho { background-color: #fff3e0; color: #ef6c00; }
    .meta-label { color: #888; font-size: 13px; }
    .meta-value { font-weight: bold; color: #333; font-size: 15px; }
    .btn-detail { background-color: transparent; border: 1px solid #673065; color: #673065; border-radius: 20px; padding: 5px 15px; font-size: 12px; font-weight: bold; text-decoration: none; transition: 0.3s; }
    .btn-detail:hover { background-color: #673065; color: white; }
</style>

<div class="history-wrapper">
    <div class="container">
        <h2 class="history-title text-center text-uppercase letter-spacing-1">Danh sách phòng đã đặt</h2>

        @if(count($danhSachDat) == 0)
            <div class="text-center py-5 bg-white border rounded">
                <p class="text-muted mb-4">Bạn chưa thực hiện bất kỳ giao dịch đặt phòng nào trên hệ thống.</p>
                <a href="{{ route('phong.user') }}" class="btn btn-primary px-4 py-2" style="background-color: #673065; border: none; border-radius: 25px;">Đặt phòng ngay</a>
            </div>
        @else
            @foreach($danhSachDat as $item)
                <div class="booking-card">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <div>
                            <span class="text-muted small">Mã đơn đặt: <strong class="text-dark">#DP{{ $item->id_datphong }}</strong></span>
                            <span class="mx-2 text-muted">|</span>
                            <span class="text-muted small">Ngày đặt: {{ date('d/m/Y', strtotime($item->ngay_dat)) }}</span>
                        </div>
                        <div>
                            <span class="badge-status {{ $item->trang_thai == 'Đã xác nhận' ? 'status-xacnhan' : 'status-cho' }}">
                                {{ $item->trang_thai }}
                            </span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h5 class="fw-bold mb-1" style="color: #673065;">
                                {{ $item->loai_hinh_dat == 'LẺ' ? $item->so_phong : $item->ten_combo }}
                            </h5>
                            <small class="text-muted">Hình thức: Đặt {{ strtolower($item->loai_hinh_dat) }}</small>
                        </div>
                        <div class="col-md-4 d-flex gap-4">
                            <div>
                                <div class="meta-label">Ngày nhận phòng</div>
                                <div class="meta-value">{{ date('d/m/Y', strtotime($item->ngay_nhan)) }}</div>
                            </div>
                            <div>
                                <div class="meta-label">Ngày trả phòng</div>
                                <div class="meta-value">{{ date('d/m/Y', strtotime($item->ngay_tra)) }}</div>
                            </div>
                        </div>
                        <div class="col-md-2 mt-2 mt-md-0">
                            <div class="meta-label">Tổng chi phí</div>
                            <div class="fw-bold text-dark">{{ number_format($item->tong_tien_phai_tra, 0, ',', '.') }} đ</div>
                        </div>
                        <div class="col-md-2 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('booking.detail', $item->id_datphong) }}" class="btn-detail d-inline-block">
                                Xem chi tiết <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
