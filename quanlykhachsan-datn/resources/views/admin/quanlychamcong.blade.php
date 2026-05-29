@extends('admin.layout.master')
@section('title', 'Quản lý chấm công')
@section('page_title', 'Phân hệ quản lý chấm công nhân viên')

@section('content')
<style>
    .card-custom { background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05); }
    .table th { font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #1e40af; background-color: #eff6ff; padding: 15px 10px; border-bottom: 2px solid #dbeafe; white-space: nowrap; }
    .table td { padding: 12px 10px; color: #475569; vertical-align: middle; }
    .btn-rounded { border-radius: 8px !important; font-weight: 600; font-size: 0.85rem; }
    .badge-time { background-color: #e2e8f0; color: #334155; font-weight: bold; border: 1px solid #cbd5e1; }
</style>

<div class="card card-custom p-4">
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-5">
            <form action="{{ route('admin.chamcong.index') }}" method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Tìm tên nhân viên, tháng, năm...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
        <div class="col-md-7 text-md-end">
            <button class="btn btn-success btn-rounded px-4" data-bs-toggle="modal" data-bs-target="#addChamCongModal">
                <i class="bi bi-calendar-check me-2"></i> Chấm công tháng mới
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
                    <th>Mã CC</th>
                    <th>Nhân viên</th>
                    <th class="text-center">Kỳ chấm công</th>
                    <th class="text-center">Ngày đi làm</th>
                    <th class="text-center text-warning">Nghỉ có phép</th>
                    <th class="text-center text-danger">Nghỉ không phép</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @if($danhSachChamCong->isEmpty())
                    <tr><td colspan="7" class="text-center py-4 text-muted">Không có dữ liệu chấm công nào.</td></tr>
                @else
                    @foreach($danhSachChamCong as $cc)
                        <tr>
                            <td class="fw-bold text-primary">#CC-{{ $cc->id_chamcong }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $cc->ho_ten }}</div>
                                <small class="text-muted">{{ $cc->chuc_vu }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-time px-3 py-2 fs-6">Tháng {{ $cc->thang }} / {{ $cc->nam }}</span>
                            </td>
                            <td class="text-center fw-bold text-success fs-5">{{ $cc->so_ngay_di_lam }}</td>
                            <td class="text-center fw-bold text-warning fs-5">{{ $cc->so_ngay_nghi_co_phep }}</td>
                            <td class="text-center fw-bold text-danger fs-5">{{ $cc->so_ngay_nghi_khong_phep }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning btn-rounded btn-edit"
                                        data-id="{{ $cc->id_chamcong }}"
                                        data-hoten="{{ $cc->ho_ten }}"
                                        data-thang="{{ $cc->thang }}"
                                        data-nam="{{ $cc->nam }}"
                                        data-dilam="{{ $cc->so_ngay_di_lam }}"
                                        data-nghicophep="{{ $cc->so_ngay_nghi_co_phep }}"
                                        data-nghikhongphep="{{ $cc->so_ngay_nghi_khong_phep }}">
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
        {{ $danhSachChamCong->appends(['search' => $search])->links('pagination::bootstrap-5') }}
    </div>
</div>

<div class="modal fade" id="addChamCongModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-calendar-check me-2"></i> Khởi tạo chấm công tháng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.chamcong.store') }}" method="POST">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Chọn nhân viên <span class="text-danger">*</span></label>
                        <select name="id_nhanvien" class="form-select" required>
                            <option value="">-- Chọn nhân viên --</option>
                            @foreach($danhSachNhanVien as $nv)
                                <option value="{{ $nv->id_nhanvien }}">{{ $nv->ho_ten }} ({{ $nv->chuc_vu }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tháng <span class="text-danger">*</span></label>
                        <select name="thang" class="form-select" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>Tháng {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Năm <span class="text-danger">*</span></label>
                        <input type="number" name="nam" class="form-control" value="{{ date('Y') }}" required min="2000">
                    </div>

                    <div class="col-md-12 border-top mt-3 pt-3">
                        <label class="form-label fw-bold text-primary mb-3"><i class="bi bi-card-checklist"></i> Bảng tính số ngày công</label>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ngày đi làm</label>
                        <input type="number" step="0.5" name="so_ngay_di_lam" class="form-control fw-bold text-success" required min="0" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nghỉ CÓ phép</label>
                        <input type="number" step="0.5" name="so_ngay_nghi_co_phep" class="form-control fw-bold text-warning" required min="0" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nghỉ KHÔNG phép</label>
                        <input type="number" step="0.5" name="so_ngay_nghi_khong_phep" class="form-control fw-bold text-danger" required min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success px-4">Lưu chấm công</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editChamCongModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Cập nhật chấm công</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditChamCong" method="POST">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Nhân viên đang cập nhật</label>
                        <input type="text" id="edit_ho_ten" class="form-control bg-light" readonly>
                        <small class="text-muted">Không thể thay đổi nhân viên sau khi đã khởi tạo bảng công.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tháng <span class="text-danger">*</span></label>
                        <select name="thang" id="edit_thang" class="form-select" required>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">Tháng {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Năm <span class="text-danger">*</span></label>
                        <input type="number" name="nam" id="edit_nam" class="form-control" required min="2000">
                    </div>

                    <div class="col-md-12 border-top mt-3 pt-3">
                        <label class="form-label fw-bold text-primary mb-3"><i class="bi bi-card-checklist"></i> Điều chỉnh số ngày công</label>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ngày đi làm</label>
                        <input type="number" step="0.5" id="edit_dilam" name="so_ngay_di_lam" class="form-control fw-bold text-success" required min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nghỉ CÓ phép</label>
                        <input type="number" step="0.5" id="edit_nghicophep" name="so_ngay_nghi_co_phep" class="form-control fw-bold text-warning" required min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nghỉ KHÔNG phép</label>
                        <input type="number" step="0.5" id="edit_nghikhongphep" name="so_ngay_nghi_khong_phep" class="form-control fw-bold text-danger" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning px-4 text-dark">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Vanilla JS xử lý truyền dữ liệu vào Modal Cập Nhật
        document.querySelectorAll('.btn-edit').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.dataset.id;

                // Định tuyến action
                document.getElementById('formEditChamCong').action = '/admin/quan-ly-cham-cong/update/' + id;

                // Đổ dữ liệu
                document.getElementById('edit_ho_ten').value = this.dataset.hoten || '';
                document.getElementById('edit_thang').value = this.dataset.thang || '';
                document.getElementById('edit_nam').value = this.dataset.nam || '';
                document.getElementById('edit_dilam').value = this.dataset.dilam || '0';
                document.getElementById('edit_nghicophep').value = this.dataset.nghicophep || '0';
                document.getElementById('edit_nghikhongphep').value = this.dataset.nghikhongphep || '0';

                // Bật Modal
                var editModal = new bootstrap.Modal(document.getElementById('editChamCongModal'));
                editModal.show();
            });
        });
    });
</script>
@endsection
