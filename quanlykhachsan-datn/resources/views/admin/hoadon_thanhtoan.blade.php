@extends('admin.layout.master')
@section('title', 'Thanh toán & Trả phòng')
@section('page_title', 'Hoá đơn thanh toán')

@section('content')
<style>
    .invoice-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .invoice-header { border-bottom: 2px dashed #e2e8f0; padding-bottom: 20px; margin-bottom: 20px; }
    .amount-highlight { color: #dc2626; font-weight: bold; font-size: 1.2rem; }
</style>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card invoice-card p-4 p-md-5">
            <div class="invoice-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-primary mb-1">HOÁ ĐƠN THANH TOÁN</h4>
                    <p class="text-muted mb-0">Mã đặt phòng: <strong>#{{ $datPhong->id_datphong }}</strong></p>
                </div>
                <div class="text-end">
                    <p class="mb-1">Ngày lập: <strong>{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</strong></p>
                    <span class="badge bg-info-subtle text-info fs-6">Phòng: {{ $datPhong->phong->so_phong }}</span>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="fw-bold text-secondary text-uppercase mb-3">Thông tin khách hàng</h6>
                    <p class="mb-1"><strong>Họ tên:</strong> {{ $datPhong->khachhang->ho_ten ?? 'Khách lẻ' }}</p>
                    <p class="mb-1"><strong>SĐT:</strong> {{ $datPhong->khachhang->so_dien_thoai ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h6 class="fw-bold text-secondary text-uppercase mb-3">Thông tin lưu trú</h6>
                    <p class="mb-1"><strong>Nhận phòng:</strong> {{ \Carbon\Carbon::parse($datPhong->ngay_nhan)->format('d/m/Y H:i') }}</p>
                    <p class="mb-1"><strong>Trả phòng:</strong> {{ \Carbon\Carbon::parse($datPhong->ngay_tra)->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <h6 class="fw-bold text-secondary text-uppercase mb-3">Chi tiết phí dịch vụ phát sinh</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tên dịch vụ</th>
                            <th class="text-center">Đơn giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dichVuDaDung as $dv)
                        <tr>
                            <td>{{ $dv->ten_dich_vu }}</td>
                            <td class="text-center">{{ number_format($dv->don_gia) }} đ</td>
                            <td class="text-center">{{ $dv->so_luong }}</td>
                            <td class="text-end fw-semibold">{{ number_format($dv->so_luong * $dv->don_gia) }} đ</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Không phát sinh dịch vụ nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <form action="{{ route('admin.datphong.process_checkout', $datPhong->id_datphong) }}" method="POST">
                @csrf
                <div class="row bg-light rounded p-3 mb-4">
                    <div class="col-md-6 border-end pe-md-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-exclamation-triangle text-warning"></i> Cấu hình phát sinh & Bồi thường</h6>

                        <div class="mb-3">
                            <label class="form-label">Phí bồi thường (Nếu có)</label>
                            <div class="input-group">
                                <input type="number" name="tien_boi_thuong" id="inputBoiThuong" class="form-control" value="0" min="0" step="1000">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                            <small class="text-muted">Khoản này cộng trực tiếp vào tổng bill mà không cần lưu vào DB bồi thường riêng.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lý do bồi thường / Ghi chú</label>
                            <input type="text" name="ly_do_boi_thuong" class="form-control" placeholder="VD: Khách làm hỏng máy sấy tóc...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                            <select name="phuong_thuc_thanh_toan" class="form-select" required>
                                <option value="Tiền mặt">Tiền mặt</option>
                                <option value="Chuyển khoản">Chuyển khoản / QR Code</option>
                                <option value="Thẻ">Quẹt thẻ (POS)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 ps-md-4 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng tiền phòng gốc:</span>
                            <strong>{{ number_format($datPhong->tong_tien_phai_tra) }} đ</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng tiền dịch vụ:</span>
                            <strong>{{ number_format($tongTienDichVu) }} đ</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Tiền cọc đã thu:</span>
                            <strong>- {{ number_format($tienDatCoc) }} đ</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-warning">
                            <span>Tiền bồi thường:</span>
                            <strong id="textBoiThuong">+ 0 đ</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold">KHÁCH CẦN TRẢ:</span>
                            <span class="amount-highlight" id="textTongThanhToan">{{ number_format($tongThanhToanDuKien) }} đ</span>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.datphong.index') }}" class="btn btn-secondary px-4 me-2">Quay lại</a>
                    <button type="button" class="btn btn-info px-4 me-2" onclick="window.print()">
                        <i class="bi bi-printer"></i> In Phiếu
                    </button>
                    <button type="submit" class="btn btn-danger px-4" onclick="return confirm('Xác nhận hoàn tất thanh toán và trả phòng?')">
                        <i class="bi bi-check-circle"></i> Hoàn tất trả phòng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // JS Tính tổng tiền auto khi nhập tiền bồi thường
    document.addEventListener("DOMContentLoaded", function() {
        const inputBoiThuong = document.getElementById('inputBoiThuong');
        const textBoiThuong = document.getElementById('textBoiThuong');
        const textTongThanhToan = document.getElementById('textTongThanhToan');

        const tongDuKien = {{ $tongThanhToanDuKien }};

        inputBoiThuong.addEventListener('input', function() {
            let boiThuong = parseFloat(this.value) || 0;
            let tongCuoiCung = tongDuKien + boiThuong;

            // Format lại dạng tiền tệ
            textBoiThuong.innerText = '+ ' + new Intl.NumberFormat('vi-VN').format(boiThuong) + ' đ';
            textTongThanhToan.innerText = new Intl.NumberFormat('vi-VN').format(tongCuoiCung) + ' đ';
        });
    });
</script>
@endsection
