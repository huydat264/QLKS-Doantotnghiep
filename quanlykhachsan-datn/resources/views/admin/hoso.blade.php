@extends('admin.layout.master')
@section('title', 'Hồ sơ cá nhân')
@section('page_title', 'Thông tin tài khoản nội bộ')

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 bg-white">
                <div class="mb-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($nhanVien->ho_ten) }}&background=0D6EFD&color=fff&size=128"
                         class="rounded-circle shadow-sm border border-3 border-primary-subtle" alt="Avatar">
                </div>
                <h5 class="fw-bold mb-1">{{ $nhanVien->ho_ten }}</h5>
                <p class="text-muted small mb-2">Mã NV: #NV-{{ $nhanVien->id_nhanvien }}</p>

                <div class="d-flex justify-content-center gap-2 mt-2">
                    <span class="badge bg-primary px-3 py-2">{{ $nhanVien->role }}</span>
                    <span class="badge bg-success px-3 py-2">{{ $nhanVien->trang_thai }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Thông tin chi tiết lý lịch</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">Tên tài khoản hệ thống</label>
                        <input type="text" class="form-control bg-light" value="{{ $nhanVien->username }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">Quyền hạn</label>
                        <input type="text" class="form-control bg-light" value="{{ $nhanVien->role == 'ADMIN' ? 'Quản trị viên (Admin)' : 'Nhân viên tác nghiệp' }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">Họ và tên</label>
                        <input type="text" class="form-control bg-light" value="{{ $nhanVien->ho_ten }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">Số điện thoại liên hệ</label>
                        <input type="text" class="form-control bg-light" value="{{ $nhanVien->so_dien_thoai ?? 'Chưa cập nhật' }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">Địa chỉ Email</label>
                        <input type="text" class="form-control bg-light" value="{{ $nhanVien->email ?? 'Chưa cập nhật' }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-secondary">Chức vụ công tác</label>
                        <input type="text" class="form-control bg-light" value="{{ $nhanVien->chuc_vu ?? 'Nhân viên nội bộ' }}" readonly>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Hồ sơ này được quản lý và lưu hành nội bộ. Mọi yêu cầu thay đổi thông tin vui lòng liên hệ bộ phận quản lý nhân sự.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
