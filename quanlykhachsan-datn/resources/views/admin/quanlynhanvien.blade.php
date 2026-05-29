@extends('admin.layout.master')
@section('title', 'Quản lý nhân viên')
@section('page_title', 'Phân hệ quản lý nhân viên')

@section('content')
<style>
    .card-custom { background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05); }
    .table th { font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #1e40af; background-color: #eff6ff; padding: 15px 10px; border-bottom: 2px solid #dbeafe; white-space: nowrap; }
    .table td { padding: 12px 10px; color: #475569; vertical-align: middle; }
    .btn-rounded { border-radius: 8px !important; font-weight: 600; font-size: 0.85rem; }
</style>

<div class="card card-custom p-4">
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-5">
            <form action="{{ route('admin.nhanvien.index') }}" method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Tìm theo tên, SĐT, email, chức vụ...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
        <div class="col-md-7 text-md-end">
            <button class="btn btn-success btn-rounded px-4" data-bs-toggle="modal" data-bs-target="#addNhanVienModal">
                <i class="bi bi-plus-circle me-2"></i> Thêm hồ sơ nhân viên
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Mã NV</th>
                    <th>Tài khoản</th>
                    <th>Họ và Tên</th>
                    <th>Chức vụ</th>
                    <th>Lương cơ bản</th>
                    <th>Ngày vào làm</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @if($danhSachNhanVien->isEmpty())
                    <tr><td colspan="9" class="text-center py-4 text-muted">Không có dữ liệu nhân viên nào.</td></tr>
                @else
                    @foreach($danhSachNhanVien as $nv)
                        <tr>
                            <td class="fw-bold text-primary">#NV-{{ $nv->id_nhanvien }}</td>
                            <td>
                                @if($nv->tai_khoan_nhanvien_id)
                                    <span class="badge bg-info">ID: {{ $nv->tai_khoan_nhanvien_id }}</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Chưa cấp TK</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $nv->ho_ten }}</td>
                            <td><span class="badge bg-secondary">{{ $nv->chuc_vu }}</span></td>
                            <td class="fw-bold text-success">{{ number_format($nv->luong_co_ban, 0, ',', '.') }} đ</td>
                            <td>{{ \Carbon\Carbon::parse($nv->ngay_vao_lam)->format('d/m/Y') }}</td>
                            <td>{{ $nv->so_dien_thoai }}</td>
                            <td>{{ $nv->email }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning btn-rounded me-1 btn-edit"
                                        data-id="{{ $nv->id_nhanvien }}"
                                        data-hoten="{{ $nv->ho_ten }}"
                                        data-chucvu="{{ $nv->chuc_vu }}"
                                        data-luong="{{ $nv->luong_co_ban }}"
                                        data-ngayvaolam="{{ $nv->ngay_vao_lam }}"
                                        data-sdt="{{ $nv->so_dien_thoai }}"
                                        data-email="{{ $nv->email }}">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-rounded btn-delete"
                                        data-url="{{ route('admin.nhanvien.destroy', $nv->id_nhanvien) }}"
                                        data-hoten="{{ $nv->ho_ten }}">
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $danhSachNhanVien->appends(['search' => $search])->links('pagination::bootstrap-5') }}
    </div>
</div>

<div class="modal fade" id="addNhanVienModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i> Thêm mới hồ sơ nhân viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.nhanvien.store') }}" method="POST">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="ho_ten" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Chức vụ <span class="text-danger">*</span></label>
                        <input type="text" name="chuc_vu" class="form-control" required placeholder="VD: Quản lý, Lễ tân...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Lương cơ bản (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="luong_co_ban" class="form-control" required min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ngày vào làm <span class="text-danger">*</span></label>
                        <input type="date" name="ngay_vao_lam" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="so_dien_thoai" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success px-4">Lưu hồ sơ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editNhanVienModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Cập nhật hồ sơ nhân viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditNhanVien" method="POST">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" id="edit_ho_ten" name="ho_ten" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Chức vụ <span class="text-danger">*</span></label>
                        <input type="text" id="edit_chuc_vu" name="chuc_vu" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Lương cơ bản (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" id="edit_luong" name="luong_co_ban" class="form-control" required min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ngày vào làm <span class="text-danger">*</span></label>
                        <input type="date" id="edit_ngay_vao_lam" name="ngay_vao_lam" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" id="edit_sdt" name="so_dien_thoai" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" id="edit_email" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Quay lại</button>
                    <button type="submit" class="btn btn-warning px-4 text-dark">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-edit').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.dataset.id;
                document.getElementById('formEditNhanVien').action = '/admin/quan-ly-nhan-vien/update/' + id;
                document.getElementById('edit_ho_ten').value = this.dataset.hoten || '';
                document.getElementById('edit_chuc_vu').value = this.dataset.chucvu || '';
                document.getElementById('edit_luong').value = this.dataset.luong || '';
                document.getElementById('edit_ngay_vao_lam').value = this.dataset.ngayvaolam || '';
                document.getElementById('edit_sdt').value = this.dataset.sdt || '';
                document.getElementById('edit_email').value = this.dataset.email || '';

                var editModal = new bootstrap.Modal(document.getElementById('editNhanVienModal'));
                editModal.show();
            });
        });

        document.querySelectorAll('.btn-delete').forEach(function(button) {
            button.addEventListener('click', function() {
                var url = this.dataset.url;
                if (confirm('Bạn chắc chắn muốn xóa hồ sơ nhân viên [' + this.dataset.hoten + ']?')) {
                    var tempForm = document.createElement('form');
                    tempForm.action = url;
                    tempForm.method = 'POST';
                    tempForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
                    document.body.appendChild(tempForm);
                    tempForm.submit();
                }
            });
        });
    });
</script>
@endsection
