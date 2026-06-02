@extends('admin.layout.master')
@section('title', 'Quản lý đặt phòng khách sạn')
@section('page_title', 'Phân hệ quản lý đặt phòng')

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
            <form action="{{ route('admin.datphong.index') }}" method="GET" class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm tên khách, số điện thoại hoặc số phòng..." value="{{ $search }}">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Tìm kiếm</button>
                @if($search)
                    <a href="{{ route('admin.datphong.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                @endif
            </form>
        </div>
        <div class="col-md-7 text-end">
            <button class="btn btn-success btn-rounded" data-bs-toggle="modal" data-bs-target="#addBookingModal">
                <i class="bi bi-calendar-plus"></i> Tạo Đơn Đặt Phòng Mới
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Mã Đơn</th>
                    <th>Khách Hàng</th>
                    <th>Số Phòng</th>
                    <th>Ngày Nhận Phòng</th>
                    <th>Ngày Trả Phòng</th>
                    <th>Loại Hình Đặt</th>
                    <th>Tổng Tiền</th>
                    <th>Trạng Thái</th>
                    <th class="text-center">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($danhSachDatPhong as $dp)
                <tr>
                    <td class="fw-bold text-dark">#DP-{{ $dp->id_datphong }}</td>
                    <td>
                        <div class="fw-bold text-slate-800">{{ $dp->khachhang->ho_ten ?? 'N/A' }}</div>
                        <small class="text-muted">{{ $dp->khachhang->so_dien_thoai ?? '' }}</small>
                    </td>
                    <td><span class="badge bg-primary px-2 py-1 fs-6">Phòng {{ $dp->phong->so_phong ?? 'N/A' }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($dp->ngay_nhan)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($dp->ngay_tra)->format('d/m/Y') }}</td>
                    <td><span class="badge bg-info px-2 py-1">{{ $dp->loai_hinh_dat == 'LẺ' ? 'Lẻ' : 'Combo' }}</span></td>
                    <td class="fw-bold text-danger">{{ number_format($dp->tong_tien_phai_tra, 0, ',', '.') }} đ</td>
                    <td>
                        @if($dp->trang_thai === 'Đã xác nhận')
                            <span class="badge bg-success-subtle text-success px-2 py-1">Đã xác nhận</span>
                        @elseif($dp->trang_thai === 'Đã trả phòng')
                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Đã trả phòng</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger px-2 py-1">{{ $dp->trang_thai }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary btn-rounded btn-edit"
                                data-id="{{ $dp->id_datphong }}"
                                data-nhan="{{ \Carbon\Carbon::parse($dp->ngay_nhan)->format('Y-m-d') }}"
                                data-tra="{{ \Carbon\Carbon::parse($dp->ngay_tra)->format('Y-m-d') }}"
                                data-status="{{ $dp->trang_thai }}"
                                data-phong="{{ $dp->id_phong }}">
                            <i class="bi bi-pencil-square"></i> Gia hạn/Sửa
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Không tìm thấy dữ liệu đặt phòng nào phù hợp.</td>
                </tr>
                @endempty
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $danhSachDatPhong->appends(['search' => $search])->links() }}
    </div>
</div>

<div class="modal fade" id="addBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-header bg-success text-white" style="border-top-left-radius: 14px; border-top-right-radius: 14px;">
                <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus-fill"></i> Tạo Đơn Đặt Phòng Mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.datphong.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold">Khách hàng</label>
                            <div class="input-group">
                                <select name="id_khachhang" class="form-select" required>
                                    <option value="">-- Chọn khách hàng đã có --</option>
                                    @foreach($danhSachKhachHang as $kh)
                                        <option value="{{ $kh->id_khachhang }}">{{ $kh->ho_ten }} ({{ $kh->so_dien_thoai }})</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-dark" id="btn_new_customer"><i class="bi bi-person-plus"></i> Khách hàng mới</button>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Chọn phòng trống</label>
                            <select name="id_phong" id="add_id_phong" class="form-select" required>
                                <option value="">-- Chọn phòng --</option>
                                @foreach($danhSachPhong as $p)
                                    <option value="{{ $p->id_phong }}">Phòng {{ $p->so_phong }} - {{ $p->loai_phong }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày nhận phòng</label>
                            <input type="date" name="ngay_nhan" id="add_ngay_nhan" class="form-control" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày trả phòng</label>
                            <input type="date" name="ngay_tra" id="add_ngay_tra" class="form-control" min="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 d-none" id="add_price_box">
                        <i class="bi bi-wallet2"></i> Dự kiến tổng chi phí phòng tạm tính: <strong id="add_calculated_price" class="text-danger">0 đ</strong>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success btn-rounded">Xác nhận đặt phòng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 14px; border-top-right-radius: 14px;">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Gia Hạn & Cập Nhật Ngày Đặt</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditBooking" method="POST">
                @csrf
                <input type="hidden" id="edit_id_phong">
                <div class="modal-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <strong>Lỗi:</strong>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Ngày nhận phòng</label>
                            <input type="date" name="ngay_nhan" id="edit_ngay_nhan" class="form-control" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Ngày trả phòng (Gia hạn)</label>
                            <input type="date" name="ngay_tra" id="edit_ngay_tra" class="form-control" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Trạng thái xử lý</label>
                            <select name="trang_thai" id="edit_trang_thai" class="form-select" required>
                                <option value="Đã xác nhận">Đã xác nhận</option>

                                <option value="Đã hủy">Đã hủy</option>
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3 d-none" id="edit_price_box">
                        <i class="bi bi-exclamation-triangle-fill"></i> Hệ thống tự động tính lại tiền phòng dựa trên giá gốc/giá sale: <strong id="edit_calculated_price" class="text-danger">0 đ</strong>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary btn-rounded" id="btnSubmitEdit">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Ràng buộc logic chặn ngày quá khứ đồng bộ khi thay đổi ngày nhận
        $('#add_ngay_nhan').on('change', function() {
            let val = $(this).val();
            $('#add_ngay_tra').attr('min', val);
            calculateLivePrice('add');
        });
        $('#add_ngay_tra, #add_id_phong').on('change', function() {
            calculateLivePrice('add');
        });

        $('#edit_ngay_nhan').on('change', function() {
            let val = $(this).val();
            $('#edit_ngay_tra').attr('min', val);
            calculateLivePrice('edit');
        });
        $('#edit_ngay_tra').on('change', function() {
            calculateLivePrice('edit');
        });

        // Hàm gọi AJAX tính tiền trực tiếp thời gian thực
        function calculateLivePrice(type) {
            let idPhong = (type === 'add') ? $('#add_id_phong').val() : $('#edit_id_phong').val();
            let ngayNhan = (type === 'add') ? $('#add_ngay_nhan').val() : $('#edit_ngay_nhan').val();
            let ngayTra = (type === 'add') ? $('#add_ngay_tra').val() : $('#edit_ngay_tra').val();

            if(idPhong && ngayNhan && ngayTra) {
                $.ajax({
                    url: "{{ route('admin.datphong.getprice') }}",
                    method: "GET",
                    data: { id_phong: idPhong, ngay_nhan: ngayNhan, ngay_tra: ngayTra },
                    success: function(res) {
                        if(res.success) {
                            $(`#${type}_price_box`).removeClass('d-none');
                            $(`#${type}_calculated_price`).text(res.price_formatted);
                        }
                    }
                });
            }
        }

        // Bấm nút Khách hàng mới (Tạm thời thông báo thiết lập sau)
        $('#btn_new_customer').click(function() {
            alert('Chức năng chuyển hướng sang form thông tin khách hàng mới sẽ được thiết lập sau theo yêu cầu của mày!');
        });

        // Click Mở Modal Sửa Đơn Đặt Phòng
        $('.btn-edit').click(function() {
            let id = $(this).data('id');
            let idPhong = $(this).data('phong');
            let ngayNhan = $(this).data('nhan');
            let ngayTra = $(this).data('tra');
            let status = $(this).data('status');

            $('#formEditBooking').attr('action', `/admin/dat-phong/update/${id}`);
            $('#edit_id_phong').val(idPhong);
            $('#edit_ngay_nhan').val(ngayNhan);
            $('#edit_ngay_tra').val(ngayTra).attr('min', ngayNhan);
            $('#edit_trang_thai').val(status);

            calculateLivePrice('edit');
            $('#editBookingModal').modal('show');
        });

        // Xác nhận thay đổi/gia hạn phòng bằng popup xác nhận
        $('#btnSubmitEdit').click(function() {
            let form = $('#formEditBooking');
            $('#editBookingModal').modal('hide');

            triggerConfirmation(
                'Xác nhận gia hạn/Thay đổi lịch đặt phòng?',
                'Hệ thống sẽ cập nhật thời gian lưu trú và tự động điều chỉnh tổng hóa đơn chi phí dựa theo bảng giá phòng thực tế.',
                function() {
                    form.submit();
                }
            );
        });

        function triggerConfirmation(title, message, callbackAction) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        callbackAction();
                    }
                });
            } else {
                if (confirm(`${title}\n\n${message}`)) {
                    callbackAction();
                }
            }
        }
    });
</script>
@endsection
