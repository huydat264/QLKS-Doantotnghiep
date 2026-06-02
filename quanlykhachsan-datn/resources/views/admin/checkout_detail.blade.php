@extends('admin.layout.master')
@section('title', 'Thanh toán & Trả phòng')
@section('page_title', 'Chi tiết thanh toán')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-wallet2 me-2"></i>Chi tiết các khoản phí</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.thanhtoan.process', $datPhong->id_datphong) }}" method="POST">
                    @csrf

                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Hạng mục</th>
                                <th class="text-end">Số tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold">Tổng tiền phòng / Combo gốc</td>
                                <td class="text-end fw-bold">{{ number_format($tongTienPhong, 0, ',', '.') }} đ</td>
                            </tr>
                            <tr>
                                <td class="text-success"><i class="bi bi-dash-circle me-1"></i> Trừ tiền cọc (Đã thanh toán)</td>
                                <td class="text-end text-success fw-bold">
                                    @if($tienCoc > 0)
                                        - {{ number_format($tienCoc, 0, ',', '.') }} đ
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-info">
                                <td class="fw-bold">Tiền phòng còn lại cần thu</td>
                                <td class="text-end fw-bold text-primary fs-5">{{ number_format($tienPhongConLai, 0, ',', '.') }} đ</td>
                            </tr>
                        </tbody>
                    </table>

                    <h6 class="fw-bold mt-4 mb-3">Dịch vụ sử dụng thêm</h6>
                    @if($dichVus->count() > 0)
                        <table class="table table-sm table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Tên dịch vụ</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dichVus as $dv)
                                <tr>
                                    <td>{{ $dv->ten_dich_vu }}</td>
                                    <td class="text-center">{{ $dv->so_luong }}</td>
                                    <td class="text-end">{{ number_format($dv->don_gia, 0, ',', '.') }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($dv->so_luong * $dv->don_gia, 0, ',', '.') }} đ</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Tổng tiền dịch vụ:</td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($tongTienDichVu, 0, ',', '.') }} đ</td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <div class="text-muted small italic mb-3">Khách không sử dụng dịch vụ phát sinh.</div>
                    @endif

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3 text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Ghi nhận Bồi thường / Phụ phí (Nếu có)</h6>
                    <div class="row g-3 bg-light p-3 rounded-3 border">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Số tiền thu thêm (VNĐ)</label>
                            <input type="number" id="tien_boi_thuong" name="tien_boi_thuong" class="form-control font-monospace text-danger fw-bold" value="" min="0">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Lý do / Ghi chú</label>
                            <input type="text" name="ghi_chu_boi_thuong" class="form-control" placeholder="Ví dụ: Đền bù vỡ cốc, làm hỏng rèm...">
                        </div>
                    </div>

                    <div class="row mt-4 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hình thức thanh toán</label>
                            <select name="hinh_thuc" class="form-select border-primary" required>
                                <option value="Tiền mặt">Tiền mặt</option>
                                <option value="VNPay">Cổng VNPay</option>
                            </select>
                        </div>
                        <div class="col-md-6 text-end">
                            <p class="mb-1 text-muted fw-bold">TỔNG SỐ TIỀN KHÁCH CẦN THANH TOÁN</p>
                            <h2 class="text-success fw-bold mb-0" id="tong_thanh_toan_hienthi">{{ number_format($tienPhongConLai + $tongTienDichVu, 0, ',', '.') }} đ</h2>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="javascript:history.back()" class="btn btn-secondary px-4 me-2">Quay lại</a>
                        <button type="submit" class="btn btn-success px-5 fw-bold fs-5 shadow-sm" onclick="return confirm('Xác nhận thu tiền và trả phòng?')">
                            <i class="bi bi-check-circle me-2"></i> HOÀN TẤT THANH TOÁN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 border-bottom border-light pb-2">Thông tin Đặt Phòng</h5>
                <p class="mb-2"><i class="bi bi-person me-2"></i> <strong>Khách hàng:</strong> {{ $datPhong->ho_ten }}</p>
                <p class="mb-2"><i class="bi bi-telephone me-2"></i> <strong>SĐT:</strong> {{ $datPhong->so_dien_thoai }}</p>
                <p class="mb-2"><i class="bi bi-door-open me-2"></i> <strong>Phòng:</strong> {{ $datPhong->ten_phong }}</p>
                <p class="mb-2"><i class="bi bi-calendar-check me-2"></i> <strong>Check-in:</strong> {{ \Carbon\Carbon::parse($datPhong->ngay_nhan)->format('d/m/Y') }}</p>
                <p class="mb-0"><i class="bi bi-calendar-x me-2"></i> <strong>Check-out:</strong> {{ \Carbon\Carbon::parse($datPhong->ngay_tra)->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>
</div>

<script>
    // JS Tính tổng tiền thời gian thực
    document.addEventListener('DOMContentLoaded', function() {
        const tienPhongConLai = {{ $tienPhongConLai }};
        const tongTienDichVu = {{ $tongTienDichVu }};
        const inputBoiThuong = document.getElementById('tien_boi_thuong');
        const displayTotal = document.getElementById('tong_thanh_toan_hienthi');

        inputBoiThuong.addEventListener('input', function() {
            let boiThuong = parseFloat(this.value) || 0;
            let tong = tienPhongConLai + tongTienDichVu + boiThuong;
            displayTotal.innerText = new Intl.NumberFormat('vi-VN').format(tong) + ' đ';
        });
    });
</script>
@endsection
