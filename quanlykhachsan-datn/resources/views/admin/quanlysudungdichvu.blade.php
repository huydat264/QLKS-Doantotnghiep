@extends('admin.layout.master')
@section('title', 'Quản lý sử dụng dịch vụ')
@section('page_title', 'Phân hệ quản lý sử dụng dịch vụ')

@section('content')
<style>
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
        <div class="col-md-5">
            <form action="{{ route('admin.sudungdichvu.index') }}" method="GET" class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo số phòng hoặc dịch vụ..." value="{{ $search }}">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
            </form>
        </div>
        <div class="col-md-7 text-end">
            <button class="btn btn-success btn-rounded" data-bs-toggle="modal" data-bs-target="#addSuDungModal">
                <i class="bi bi-plus-circle"></i> Chỉ định dịch vụ
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Vị trí phòng</th>
                    <th>Dịch vụ cung cấp</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($danhSachSuDung as $item)
                    <tr>
                        <td>#{{ $item->id_sudungdv }}</td>
                        <td><span class="badge bg-primary px-2 py-1" style="border-radius: 6px;">Phòng {{ $item->so_phong }}</span></td>
                        <td><strong>{{ $item->ten_dich_vu }}</strong></td>
                        <td class="fw-bold">{{ $item->so_luong }}</td>
                        <td>{{ number_format($item->gia_dich_vu, 0, ',', '.') }} đ</td>
                        <td class="text-danger fw-bold">{{ number_format($item->so_luong * $item->gia_dich_vu, 0, ',', '.') }} đ</td>
                        <td class="text-center">
                                <button class="btn btn-warning btn-sm btn-rounded btn-edit"
                                    data-id="{{ $item->id_sudungdv }}"
                                    data-datphong="{{ $item->id_datphong }}"
                                    data-dichvu="{{ $item->id_dichvu }}"
                                    data-soluong="{{ $item->so_luong }}">
                                <i class="bi bi-pencil-square"></i> Sửa thông tin
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Hệ thống chưa ghi nhận dữ liệu sử dụng dịch vụ nào phù hợp.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $danhSachSuDung->appends(['search' => $search])->links() }}
    </div>
</div>

<div class="modal fade" id="addSuDungModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.sudungdichvu.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary"><i class="bi bi-plus-circle-fill"></i> Thêm sử dụng dịch vụ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Chọn phòng đang lưu trú</label>
                    <select name="id_datphong" class="form-select" required>
                        <option value="">-- Lựa chọn phòng --</option>
                        @foreach($danhSachDatPhong as $dp)
                            <option value="{{ $dp->id_datphong }}">Phòng {{ $dp->so_phong }} (Đơn đặt #{{ $dp->id_datphong }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Chọn dịch vụ hệ thống</label>
                    <select name="id_dichvu" class="form-select" required>
                        <option value="">-- Lựa chọn dịch vụ --</option>
                        @foreach($danhSachDichVu as $dv)
                            <option value="{{ $dv->id_dichvu }}">{{ $dv->ten_dich_vu }} ({{ number_format($dv->gia, 0, ',', '.') }} đ)</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Số lượng/Người</label>
                    <input type="number" name="so_luong" class="form-control" value="1" min="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-success">Xác nhận cập nhật</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editSuDungModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editSuDungForm" method="POST" class="modal-content" style="border-radius: 12px;">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-warning"><i class="bi bi-pencil-fill"></i> Điều chỉnh dịch vụ sử dụng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Phòng đang sử dụng</label>
                    <select name="id_datphong" id="edit_id_datphong" class="form-select" required>
                        @foreach($danhSachDatPhong as $dp)
                            <option value="{{ $dp->id_datphong }}">Phòng {{ $dp->so_phong }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Loại dịch vụ cung cấp</label>
                    <select name="id_dichvu" id="edit_id_dichvu" class="form-select" required>
                        @foreach($danhSachDichVu as $dv)
                            <option value="{{ $dv->id_dichvu }}">{{ $dv->ten_dich_vu }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Điều chỉnh số lượng</label>
                    <input type="number" name="so_luong" id="edit_so_luong" class="form-control" min="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Đổ dữ liệu cũ vào form chỉnh sửa khi click nút "Sửa thông tin"
        $('.btn-edit').click(function() {
            let id = $(this).data('id');
            let datPhongId = $(this).data('datphong');
            let dichVuId = $(this).data('dichvu');
            let soLuong = $(this).data('soluong');

            $('#edit_id_datphong').val(datPhongId);
            $('#edit_id_dichvu').val(dichVuId);
            $('#edit_so_luong').val(soLuong);

            // Gán lại action động tương ứng với ID cần sửa
            $('#editSuDungForm').attr('action', `/admin/quan-ly-su-dung-dich-vu/update/${id}`);
            $('#editSuDungModal').modal('show');
        });
    });
</script>
@endsection
