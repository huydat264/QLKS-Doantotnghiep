@extends('layouts.style')

@section('content')
<style>
    .detail-wrapper { background-color: #faf8f5; min-height: 100vh; padding-top: 140px; padding-bottom: 60px; }
    .card-detail { background: white; border: 1px solid #f1eeea; border-radius: 4px; padding: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .hotel-brand { font-family: 'Playfair Display', serif; color: #673065; font-size: 24px; font-weight: bold; }
    .invoice-title { font-size: 14px; text-transform: uppercase; letter-spacing: 2px; color: #888; font-weight: bold; }
    .info-block-title { font-size: 15px; text-transform: uppercase; color: #673065; font-weight: bold; border-bottom: 1px solid #f1eeea; padding-bottom: 8px; margin-bottom: 15px; }
    .item-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
    .item-label { color: #666; }
    .item-value { font-weight: bold; color: #111; }
    .table-dichvu th { background-color: #fdfaf6; color: #673065; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #f1eeea; }
    .table-dichvu td { font-size: 14px; vertical-align: middle; }
    .money-highlight { font-size: 22px; color: #673065; font-weight: 900; }
</style>

<div class="detail-wrapper">
    <div class="container" style="max-width: 850px;">
        <div class="mb-3">
            <a href="{{ route('booking.history') }}" class="text-decoration-none" style="color: #673065; font-size: 14px; font-weight: bold;">
                <i class="bi bi-chevron-left me-1"></i> Quay lại lịch sử đặt phòng
            </a>
        </div>

        <div class="card-detail">
            <div class="row border-bottom pb-4 mb-4 align-items-center">
                <div class="col-sm-6">
                    <div class="hotel-brand">SIX SENSES RESORT</div>
                    <small class="text-muted">Mã chứng từ hệ thống: #DP{{ $donDat->id_datphong }}</small>
                </div>
                <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                    <div class="invoice-title">Chi Tiết Đơn Đặt Phòng</div>
                    <div class="small text-muted">Ngày lập: {{ date('d/m/Y H:i', strtotime($donDat->ngay_dat)) }}</div>
                    <span class="badge bg-success mt-2" style="padding: 6px 12px; font-size: 11px;">{{ $donDat->trang_thai }}</span>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="info-block-title"><i class="bi bi-door-open me-1"></i> Thông tin dịch vụ lưu trú</div>
                    <div class="item-row">
                        <span class="item-label">Tên/Số phòng:</span>
                        <span class="item-value text-uppercase" style="color: #673065;">{{ $donDat->loai_hinh_dat == 'LẺ' ? $donDat->so_phong : $donDat->ten_combo }}</span>
                    </div>
                    @if($donDat->loai_hinh_dat == 'LẺ')
                    <div class="item-row">
                        <span class="item-label">Hạng phòng nghỉ:</span>
                        <span class="item-value">{{ $donDat->loai_phong }}</span>
                    </div>
                    @endif
                    <div class="item-row">
                        <span class="item-label">Thời gian check-in:</span>
                        <span class="item-value">{{ date('d/m/Y', strtotime($donDat->ngay_nhan)) }} (Từ 14:00)</span>
                    </div>
                    <div class="item-row">
                        <span class="item-label">Thời gian check-out:</span>
                        <span class="item-value">{{ date('d/m/Y', strtotime($donDat->ngay_tra)) }} (Trước 12:00)</span>
                    </div>
                    <div class="item-row">
                        <span class="item-label">Tổng số đêm nghỉ:</span>
                        <span class="item-value">{{ $soDem }} đêm</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="info-block-title"><i class="bi bi-credit-card me-1"></i> Trạng thái quyết toán cọc</div>
                    @if($giaoDich)
                        <div class="item-row">
                            <span class="item-label">Số tiền cọc đã thu (30%):</span>
                            <span class="item-value text-danger">{{ number_format($giaoDich->so_tien, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="item-row">
                            <span class="item-label">Phương thức giao dịch:</span>
                            <span class="item-value">{{ $giaoDich->hinh_thuc }}</span>
                        </div>
                        <div class="item-row">
                            <span class="item-label">Mã giao dịch cổng VNP:</span>
                            <span class="item-value small text-monospace text-muted">{{ $giaoDich->vnp_transaction_no }}</span>
                        </div>
                        <div class="item-row">
                            <span class="item-label">Thời gian thanh toán:</span>
                            <span class="item-value">{{ date('d/m/Y H:i', strtotime($giaoDich->ngay_thanh_toan)) }}</span>
                        </div>
                    @else
                        <p class="text-warning small fw-bold">Đơn hàng này chưa ghi nhận lịch sử thanh toán điện tử.</p>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="info-block-title"><i class="bi bi-tags me-1"></i> Bảng kê dịch vụ bổ sung</div>
                @if(count($dichVuDaDung) == 0)
                    <p class="text-muted small mb-0 p-2 bg-light rounded text-center">Đơn hàng này không đăng ký thêm dịch vụ bổ sung nào ngoài phòng.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-dichvu text-center m-0">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th class="text-start">Tên dịch vụ</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dichVuDaDung as $index => $dv)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-start fw-bold" style="color: #555;">{{ $dv->ten_dich_vu }}</td>
                                        <td>{{ number_format($dv->thanh_tien, 0, ',', '.') }} đ</td>
                                        <td>{{ $dv->so_luong }}</td>
                                        <td class="text-end fw-bold text-dark">{{ number_format($dv->thanh_tien, 0, ',', '.') }} đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="bg-light p-4 rounded border text-sm-end mt-4">
                <div class="mb-2">
                    <span class="text-muted text-uppercase small font-weight-bold">Tổng trị giá toàn bộ hóa đơn:</span>
                    <span class="fw-bold text-dark fs-5 ms-2">{{ number_format($donDat->tong_tien_phai_tra, 0, ',', '.') }} VNĐ</span>
                </div>
                <div class="mb-2">
                    <span class="text-success text-uppercase small font-weight-bold">Số tiền cọc trực tuyến đã thanh toán:</span>
                    <span class="fw-bold text-success fs-5 ms-2">- {{ number_format($giaoDich ? $giaoDich->so_tien : 0, 0, ',', '.') }} VNĐ</span>
                </div>
                <hr style="border-color: #ddd;">
                <div>
                    <span class="text-uppercase small fw-bold" style="color: #673065;">Số tiền mặt cần thanh toán nốt khi Check-out:</span>
                    <div class="money-highlight mt-1">
                        {{ number_format($donDat->tong_tien_phai_tra - ($giaoDich ? $giaoDich->so_tien : 0), 0, ',', '.') }} VNĐ
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
