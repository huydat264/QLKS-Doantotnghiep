@extends('admin.layout.master')
@section('title', 'Quản lý khách hàng')
@section('page_title', 'Phân hệ quản lý khách hàng')

@section('content')
<style>
    /* Đồng bộ giao diện card mềm mại, bo tròn */
    .card-custom {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 8px 15px -6px rgba(0, 0, 0, 0.03);
    }
    .table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #1e40af;
        background-color: #eff6ff;
        padding: 15px 10px;
        border-bottom: 2px solid #dbeafe;
        white-space: nowrap;
    }
    .table td {
        padding: 12px 10px;
        color: #475569;
        vertical-align: middle;
    }
    .btn-rounded {
        border-radius: 8px !important;
        font-weight: 600;
        font-size: 0.85rem;
    }
</style>

<div class="card card-custom p-4">
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-6">
            <form action="{{ route('admin.khachhang.index') }}" method="GET" class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, sđt, cccd, email..." value="{{ $search }}">
                <button class="btn btn-dark" type="submit"><i class="bi bi-search"></i> Tìm kiếm</button>
                @if($search)
                    <a href="{{ route('admin.khachhang.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                @endif
            </form>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-success btn-rounded" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                <i class="bi bi-plus-circle"></i> Thêm khách hàng
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên khách hàng</th>
                    <th>Căn cước</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Địa chỉ</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($danhSachKhachHang as $key => $kh)
                <tr>
                    <td>{{ $danhSachKhachHang->firstItem() + $key }}</td>
                    <td class="fw-bold text-dark">{{ $kh->ho_ten }}</td>
                    <td>{{ $kh->cccd }}</td>
                    <td>{{ $kh->ngay_sinh ? \Carbon\Carbon::parse($kh->ngay_sinh)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $kh->gioi_tinh ?? 'N/A' }}</td>
                    <td>{{ $kh->so_dien_thoai }}</td>
                    <td>{{ $kh->email ?? 'N/A' }}</td>
                    <td>{{ $kh->dia_chi ?? 'N/A' }}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary btn-rounded btn-edit"
                            data-id="{{ $kh->id_khachhang }}"
                            data-hoten="{{ $kh->ho_ten }}"
                            data-cccd="{{ $kh->cccd }}"
                            data-ngaysinh="{{ $kh->ngay_sinh }}"
                            data-gioitinh="{{ $kh->gioi_tinh }}"
                            data-sdt="{{ $kh->so_dien_thoai }}"
                            data-email="{{ $kh->email }}"
                            data-diachi="{{ $kh->dia_chi }}">
                            <i class="bi bi-pencil-square"></i> Sửa
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i> Không tìm thấy dữ liệu khách hàng nào!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
        {{ $danhSachKhachHang->links() }}
    </div>
</div>

<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0" style="border-radius: 12px;">
            <div class="modal-header bg-success text-white" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus"></i> Thêm Khách Hàng Mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.khachhang.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_source" value="add_customer">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                            <input type="text" name="ho_ten" value="{{ old('ho_ten') }}" class="form-control @error('ho_ten') is-invalid @enderror" required placeholder="Nhập họ tên...">
                            @error('ho_ten')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số CCCD / CMND <span class="text-danger">*</span></label>
                            <input type="text" name="cccd" value="{{ old('cccd') }}" class="form-control @error('cccd') is-invalid @enderror" required placeholder="Nhập CCCD...">
                            @error('cccd')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="so_dien_thoai" value="{{ old('so_dien_thoai') }}" class="form-control @error('so_dien_thoai') is-invalid @enderror" required placeholder="Nhập số điện thoại...">
                            @error('so_dien_thoai')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Nhập địa chỉ email...">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày sinh</label>
                            <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giới tính</label>
                            <select name="gioi_tinh" class="form-select">
                                <option value="" {{ old('gioi_tinh') == '' ? 'selected' : '' }}>-- Chọn giới tính --</option>
                                <option value="Nam" {{ old('gioi_tinh') == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('gioi_tinh') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ old('gioi_tinh') == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Địa chỉ</label>
                            <input type="text" name="dia_chi" value="{{ old('dia_chi') }}" class="form-control" placeholder="Nhập địa chỉ...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-success btn-rounded">Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0" style="border-radius: 12px;">
            <div class="modal-header bg-primary text-white" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Cập Nhật Thông Tin Khách Hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditCustomer" method="POST" action="{{ old('form_source') === 'edit_customer' && old('edit_customer_id') ? route('admin.khachhang.update', old('edit_customer_id')) : '' }}">
                @csrf
                <input type="hidden" name="form_source" value="edit_customer">
                <input type="hidden" id="edit_customer_id" name="edit_customer_id" value="{{ old('edit_customer_id') }}">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                            <input type="text" name="ho_ten" id="edit_ho_ten" value="{{ old('ho_ten') }}" class="form-control @error('ho_ten') is-invalid @enderror" required>
                            @error('ho_ten')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số CCCD / CMND <span class="text-danger">*</span></label>
                            <input type="text" name="cccd" id="edit_cccd" value="{{ old('cccd') }}" class="form-control @error('cccd') is-invalid @enderror" required>
                            @error('cccd')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="so_dien_thoai" id="edit_sdt" value="{{ old('so_dien_thoai') }}" class="form-control @error('so_dien_thoai') is-invalid @enderror" required>
                            @error('so_dien_thoai')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" id="edit_email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày sinh</label>
                            <input type="date" name="ngay_sinh" id="edit_ngay_sinh" value="{{ old('ngay_sinh') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giới tính</label>
                            <select name="gioi_tinh" id="edit_gioi_tinh" class="form-select">
                                <option value="" {{ old('gioi_tinh') == '' ? 'selected' : '' }}>-- Chọn giới tính --</option>
                                <option value="Nam" {{ old('gioi_tinh') == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('gioi_tinh') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ old('gioi_tinh') == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Địa chỉ</label>
                            <input type="text" name="dia_chi" id="edit_dia_chi" value="{{ old('dia_chi') }}" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary btn-rounded">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bắt sự kiện click nút Sửa để đổ dữ liệu vào Modal
        document.querySelectorAll('.btn-edit').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.dataset.id;
                var url = '/admin/khach-hang/update/' + id;

                // Gán action cho form
                document.getElementById('formEditCustomer').action = url;

                // Đổ dữ liệu vào các input
                document.getElementById('edit_ho_ten').value = this.dataset.hoten || '';
                document.getElementById('edit_cccd').value = this.dataset.cccd || '';
                document.getElementById('edit_sdt').value = this.dataset.sdt || '';
                document.getElementById('edit_email').value = this.dataset.email || '';
                document.getElementById('edit_ngay_sinh').value = this.dataset.ngaysinh || '';
                document.getElementById('edit_gioi_tinh').value = this.dataset.gioitinh || '';
                document.getElementById('edit_dia_chi').value = this.dataset.diachi || '';

                // Mở modal bằng Bootstrap 5 API
                var editModalEl = document.getElementById('editCustomerModal');
                var editModal = new bootstrap.Modal(editModalEl);
                editModal.show();
            });

            @if(old('form_source') === 'add_customer' && $errors->any())
                var addModalEl = document.getElementById('addCustomerModal');
                var addModal = new bootstrap.Modal(addModalEl);
                addModal.show();
            @endif

            @if(old('form_source') === 'edit_customer' && old('edit_customer_id'))
                var editModalEl = document.getElementById('editCustomerModal');
                var editModal = new bootstrap.Modal(editModalEl);
                editModal.show();
            @endif
        });
    });
</script>
@endsection
