@extends('admin.layout.master')
@section('title', 'Quản lý bảng lương')
@section('page_title', 'Phân hệ tính & quản lý lương nhân viên')

@section('content')
<style>
    .card-custom { background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05); }
    .table th { font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #1e40af; background-color: #eff6ff; padding: 15px 10px; border-bottom: 2px solid #dbeafe; white-space: nowrap; }
    .table td { padding: 12px 10px; color: #475569; vertical-align: middle; }
    .btn-rounded { border-radius: 8px !important; font-weight: 600; font-size: 0.85rem; }
    .badge-time { background-color: #f1f5f9; color: #334155; font-weight: bold; border: 1px solid #cbd5e1; }
</style>

<div class="card card-custom p-4">
    <div class="row g-3 mb-4 align-items-end">
        <div class="col-md-7">
            <form action="{{ route('admin.bangluong.index') }}" method="GET" class="row g-2">
                <div class="col-auto">
                    <label class="form-label small fw-bold text-muted mb-1">Chọn Tháng</label>
                    <select name="thangLoc" class="form-select border-primary" style="min-width: 120px;">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $thangLoc == $i ? 'selected' : '' }}>Tháng {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small fw-bold text-muted mb-1">Nhập Năm</label>
                    <input type="number" name="namLoc" value="{{ $namLoc }}" class="form-control border-primary" style="width: 100px;">
                </div>
                <div class="col-auto d-flex align-items-end gap-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-funnel-fill"></i> Lọc dữ liệu</button>
                    <a href="{{ route('admin.bangluong.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i> Xoá lọc</a>
                </div>
            </form>
        </div>
        <div class="col-md-5 text-md-end">
            <button class="btn btn-success btn-rounded px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#calcLuongModal">
                <i class="bi bi-calculator me-2"></i> Tính lương tháng mới
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nhân viên</th>
                    <th class="text-center">Kỳ lương</th>
                    <th class="text-center">Ngày công</th>
                    <th class="text-end">Lương CB</th>
                    <th class="text-end text-primary">Thưởng / Phạt</th>
                    <th class="text-end" style="color: #ea580c;">Thuế TNCN <small>(Tạm tính)</small></th>
                    <th class="text-end">THỰC NHẬN</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @if($danhSachBangLuong->isEmpty())
                    <tr><td colspan="8" class="text-center py-5 text-muted">Chưa có dữ liệu bảng lương cho kỳ <b>Tháng {{ $thangLoc }}/{{ $namLoc }}</b>.</td></tr>
                @else
                    @foreach($danhSachBangLuong as $bl)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $bl->ho_ten }}</div>
                                <small class="text-muted">{{ $bl->chuc_vu }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-time px-2 py-1">T{{ $bl->thang }}/{{ $bl->nam }}</span>
                            </td>
                            <td class="text-center fw-bold">{{ $bl->so_ngay_cong }}</td>
                            <td class="text-end">{{ number_format($bl->luong_co_ban, 0, ',', '.') }} đ</td>
                            <td class="text-end">
                                <div class="text-primary fw-semibold">+{{ number_format($bl->thuong, 0, ',', '.') }}</div>
                                <div class="text-danger fw-semibold">-{{ number_format($bl->phat, 0, ',', '.') }}</div>
                            </td>
                            <td class="text-end fw-bold" style="color: #ea580c;">
                                {{ number_format($bl->thue_tncn, 0, ',', '.') }} đ
                            </td>
                            <td class="text-end fw-bold text-success fs-6">{{ number_format($bl->tong_luong, 0, ',', '.') }} đ</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning btn-rounded btn-edit"
                                        data-id="{{ $bl->id_bangluong }}"
                                        data-hoten="{{ $bl->ho_ten }}"
                                        data-thang="{{ $bl->thang }}"
                                        data-nam="{{ $bl->nam }}"
                                        data-ngaycong="{{ $bl->so_ngay_cong }}"
                                        data-luongcb="{{ $bl->luong_co_ban }}"
                                        data-thuong="{{ $bl->thuong }}"
                                        data-phat="{{ $bl->phat }}">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $danhSachBangLuong->appends(['thangLoc' => $thangLoc, 'namLoc' => $namLoc])->links('pagination::bootstrap-5') }}
    </div>
</div>

<div class="modal fade" id="calcLuongModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-calculator me-2"></i> Tính lương tự động</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.bangluong.calculate') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Hệ thống sẽ lấy <b>Số ngày công</b> và <b>Lương cơ bản</b> để tính Thực nhận. Cột <b>Thuế TNCN</b> sẽ được hệ thống tính nháp theo lũy tiến và lưu lại chờ quyết toán cuối năm.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chọn Tháng <span class="text-danger">*</span></label>
                            <select name="thang" class="form-select" required>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>Tháng {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nhập Năm <span class="text-danger">*</span></label>
                            <input type="number" name="nam" class="form-control" value="{{ date('Y') }}" required min="2000">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Tiến hành chạy lương</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editLuongModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Điều chỉnh Bảng Lương</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditLuong" method="POST">
                @csrf
                <div class="modal-body row g-3 p-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Nhân viên</label>
                        <input type="text" id="edit_ho_ten" class="form-control bg-light border-0" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted">Kỳ lương</label>
                        <input type="text" id="edit_ky_luong" class="form-control bg-light border-0 text-center fw-bold" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted">Ngày công</label>
                        <input type="number" id="edit_ngay_cong" class="form-control bg-light border-0 text-center fw-bold" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Lương cơ bản (VNĐ)</label>
                        <input type="number" id="edit_luong_cb" name="luong_co_ban" class="form-control fw-bold form-calc" required min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary">Thưởng (VNĐ)</label>
                        <input type="number" id="edit_thuong" name="thuong" class="form-control fw-bold form-calc" required min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-danger">Phạt (VNĐ)</label>
                        <input type="number" id="edit_phat" name="phat" class="form-control fw-bold form-calc" required min="0">
                    </div>

                    <div class="col-md-12 mt-4 pt-3 border-top d-flex justify-content-between align-items-end">
                        <div>
                            <span class="d-block small text-muted mb-1">Thuế TNCN tạm tính (Không trừ vào lương):</span>
                            <span class="fs-5 fw-bold" style="color: #ea580c;" id="preview_thue_tncn">0 đ</span>
                        </div>
                        <div class="text-end">
                            <span class="fs-6 fw-bold text-muted me-2">THỰC NHẬN:</span>
                            <span class="fs-3 fw-bold text-success" id="preview_tong_luong">0 đ</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-warning px-4 text-dark fw-bold">Lưu điều chỉnh</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formatCurrency = (val) => new Intl.NumberFormat('vi-VN').format(Math.round(val)) + ' đ';

        // Thuật toán tính thuế TNCN y hệt Controller để Preview chuẩn
        function getThueTNCN(thuNhap) {
            let thuNhapTinhThue = thuNhap - 11000000;
            if (thuNhapTinhThue <= 0) return 0;

            if (thuNhapTinhThue <= 5000000) return thuNhapTinhThue * 0.05;
            if (thuNhapTinhThue <= 10000000) return (thuNhapTinhThue * 0.10) - 250000;
            if (thuNhapTinhThue <= 18000000) return (thuNhapTinhThue * 0.15) - 750000;
            if (thuNhapTinhThue <= 32000000) return (thuNhapTinhThue * 0.20) - 1650000;
            if (thuNhapTinhThue <= 52000000) return (thuNhapTinhThue * 0.25) - 3250000;
            if (thuNhapTinhThue <= 80000000) return (thuNhapTinhThue * 0.30) - 5850000;
            return (thuNhapTinhThue * 0.35) - 9850000;
        }

        function calcPreview() {
            let base = parseFloat(document.getElementById('edit_luong_cb').value) || 0;
            let days = parseFloat(document.getElementById('edit_ngay_cong').value) || 0;
            let thuong = parseFloat(document.getElementById('edit_thuong').value) || 0;
            let phat = parseFloat(document.getElementById('edit_phat').value) || 0;

            let luongChinh = (base / 26) * days;
            let thueTNCN = getThueTNCN(luongChinh + thuong);
            let total = luongChinh + thuong - phat; // Thực nhận không trừ thuế

            document.getElementById('preview_thue_tncn').innerText = formatCurrency(thueTNCN);
            document.getElementById('preview_tong_luong').innerText = formatCurrency(total);
        }

        document.querySelectorAll('.form-calc').forEach(el => {
            el.addEventListener('input', calcPreview);
        });

        document.querySelectorAll('.btn-edit').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.dataset.id;
                document.getElementById('formEditLuong').action = '/admin/quan-ly-bang-luong/update/' + id;

                document.getElementById('edit_ho_ten').value = this.dataset.hoten;
                document.getElementById('edit_ky_luong').value = 'T' + this.dataset.thang + '/' + this.dataset.nam;
                document.getElementById('edit_ngay_cong').value = this.dataset.ngaycong;

                document.getElementById('edit_luong_cb').value = this.dataset.luongcb;
                document.getElementById('edit_thuong').value = this.dataset.thuong;
                document.getElementById('edit_phat').value = this.dataset.phat;

                calcPreview(); // Cập nhật số tiền ngay lập tức

                var editModal = new bootstrap.Modal(document.getElementById('editLuongModal'));
                editModal.show();
            });
        });
    });
</script>
@endsection
