@extends('admin.layout.master')
@section('title', 'Quản lý dịch vụ')
@section('page_title', 'Phân hệ quản lý dịch vụ')

@section('content')
<style>
    .card-custom { background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05); }
    .table th { font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #1e40af; background-color: #eff6ff; padding: 15px 10px; border-bottom: 2px solid #dbeafe; white-space: nowrap; }
    .table td { padding: 12px 10px; color: #475569; vertical-align: middle; }
    .btn-rounded { border-radius: 8px !important; font-weight: 600; font-size: 0.85rem; }
    .img-thumbnail-custom { width: 50px; height: 50px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .text-truncate-custom { max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; }
</style>

<div class="card card-custom p-4">
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-6">
            <form action="{{ route('admin.dichvu.index') }}" method="GET" class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm tên dịch vụ..." value="{{ $search }}">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Tìm</button>
                @if($search)
                    <a href="{{ route('admin.dichvu.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                @endif
            </form>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-dark btn-rounded" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                <i class="bi bi-plus-circle-fill"></i> Thêm dịch vụ
            </button>
        </div>
    </div>

    <div class="table-responsive rounded-3 border">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th width="5%">STT</th>
                    <th width="60">Ảnh</th>
                    <th>Tên dịch vụ</th>
                    <th>Mô tả</th>
                    <th>Giá vốn</th>
                    <th>Đơn giá</th>
                    <th class="text-center" width="15%">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($danhSachDichVu as $key => $dv)
                <tr>
                    <td class="text-center fw-bold text-muted">{{ $danhSachDichVu->firstItem() + $key }}</td>
                    <td>
                        @if($dv->hinh_anh)
                            @if(filter_var($dv->hinh_anh, FILTER_VALIDATE_URL))
                                <img src="{{ $dv->hinh_anh }}" class="img-thumbnail-custom" alt="{{ $dv->ten_dich_vu }}">
                            @else
                                <img src="{{ asset('storage/' . $dv->hinh_anh) }}" class="img-thumbnail-custom" alt="{{ $dv->ten_dich_vu }}">
                            @endif
                        @else
                            <div class="img-thumbnail-custom bg-light d-flex align-items-center justify-content-center text-muted border">
                                <i class="bi bi-image" style="font-size: 1.2rem;"></i>
                            </div>
                        @endif
                    </td>
                    <td class="fw-bold text-dark">{{ $dv->ten_dich_vu }}</td>
                    <td><span class="text-truncate-custom" title="{{ $dv->mo_ta }}">{{ $dv->mo_ta ?? '---' }}</span></td>
                    <td class="text-warning fw-bold">{{ number_format($dv->gia_von, 0, ',', '.') }} đ</td>
                    <td class="text-danger fw-bold">{{ number_format($dv->gia, 0, ',', '.') }} đ</td>
                    <td class="text-center">
                        <div class="btn-group gap-2">
                            <button class="btn btn-sm btn-outline-primary btn-rounded btn-edit"
                                    data-id="{{ $dv->id_dichvu }}"
                                    data-name="{{ $dv->ten_dich_vu }}"
                                    data-giavon="{{ $dv->gia_von }}"
                                    data-price="{{ $dv->gia }}"
                                    data-mota="{{ $dv->mo_ta }}"
                                    data-hinhanh="{{ $dv->hinh_anh }}">
                                <i class="bi bi-pencil-square"></i> Sửa
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-rounded btn-delete"
                                    data-url="{{ route('admin.dichvu.destroy', $dv->id_dichvu) }}"
                                    data-name="{{ $dv->ten_dich_vu }}">
                                <i class="bi bi-trash-fill"></i> Xóa
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center p-4 text-muted">Không tìm thấy dữ liệu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $danhSachDichVu->links() }}</div>
</div>

<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('admin.dichvu.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle"></i> Thêm dịch vụ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tên dịch vụ <span class="text-danger">*</span></label>
                    <input type="text" name="ten_dich_vu" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Giá vốn (đ) <span class="text-danger">*</span></label>
                    <input type="number" name="gia_von" class="form-control text-warning fw-bold" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Đơn giá bán (đ) <span class="text-danger">*</span></label>
                    <input type="number" name="gia" class="form-control text-danger fw-bold" min="0" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Ảnh dịch vụ (Chọn file hoặc dán link)</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-light"><i class="bi bi-cloud-arrow-up-fill text-primary"></i></span>
                        <input type="file" name="hinh_anh" class="form-control" accept="image/*">
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-link-45deg text-success"></i></span>
                        <input type="url" name="hinh_anh_link" class="form-control" placeholder="Hoặc dán link ảnh web (https://...)">
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Mô tả dịch vụ</label>
                    <textarea name="mo_ta" class="form-control" rows="3" placeholder="Nhập mô tả chi tiết..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary px-4">Lưu dịch vụ</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="editServiceForm" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary"><i class="bi bi-pencil-square"></i> Cập nhật dịch vụ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tên dịch vụ <span class="text-danger">*</span></label>
                    <input type="text" name="ten_dich_vu" id="edit_ten_dich_vu" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Giá vốn (đ) <span class="text-danger">*</span></label>
                    <input type="number" name="gia_von" id="edit_gia_von" class="form-control text-warning fw-bold" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Đơn giá bán (đ) <span class="text-danger">*</span></label>
                    <input type="number" name="gia" id="edit_gia" class="form-control text-danger fw-bold" min="0" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Cập nhật ảnh</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-light"><i class="bi bi-cloud-arrow-up-fill text-primary"></i></span>
                        <input type="file" name="hinh_anh" class="form-control" accept="image/*">
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-link-45deg text-success"></i></span>
                        <input type="url" name="hinh_anh_link" id="edit_hinh_anh_link" class="form-control" placeholder="Hoặc dán link ảnh web (https://...)">
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Mô tả dịch vụ</label>
                    <textarea name="mo_ta" id="edit_mo_ta" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary px-4">Xác nhận</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function triggerConfirmation(title, message, callbackAction) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ title: title, text: message, icon: 'warning', showCancelButton: true, confirmButtonText: 'Đồng ý', cancelButtonText: 'Hủy' }).then((result) => {
                    if (result.isConfirmed) { callbackAction(); }
                });
            } else {
                if (confirm(`${title}\n\n${message}`)) { callbackAction(); }
            }
        }

        // Bật Modal Sửa
        $('.btn-edit').click(function() {
            let id = $(this).data('id');
            $('#edit_ten_dich_vu').val($(this).data('name'));
            $('#edit_gia_von').val($(this).data('giavon'));
            $('#edit_gia').val($(this).data('price'));
            $('#edit_mo_ta').val($(this).data('mota'));

            // Xử lý điền link web nếu đang dùng link ngoài
            let currentAnh = $(this).data('hinhanh');
            if(currentAnh && currentAnh.startsWith('http')) {
                $('#edit_hinh_anh_link').val(currentAnh);
            } else {
                $('#edit_hinh_anh_link').val('');
            }

            $('#editServiceForm').attr('action', `/admin/quan-ly-dich-vu/update/${id}`);
            $('#editServiceModal').modal('show');
        });

        // Xóa dịch vụ
        $('.btn-delete').click(function() {
            let url = $(this).data('url');
            let tempForm = $('<form>', { 'action': url, 'method': 'POST' })
                .append($('<input>', { 'type': 'hidden', 'name': '_token', 'value': '{{ csrf_token() }}' }))
                .append($('<input>', { 'type': 'hidden', 'name': '_method', 'value': 'DELETE' }));

            $('body').append(tempForm);
            triggerConfirmation(`Xóa bỏ ${$(this).data('name')}?`, 'Hành động này sẽ gỡ bỏ dịch vụ.', function() { tempForm.submit(); });
        });
    });
</script>
@endsection
