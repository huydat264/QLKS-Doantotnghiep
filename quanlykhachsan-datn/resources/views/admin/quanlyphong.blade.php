@extends('admin.layout.master')
@section('title', 'Quản lý phòng khách sạn')
@section('page_title', 'Phân hệ quản lý phòng')

@section('content')
<style>
    .card-custom { background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05), 0 8px 15px -6px rgba(0,0,0,0.03); }
    .table th { font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #1e40af; background-color: #eff6ff; padding: 15px 10px; border-bottom: 2px solid #dbeafe; white-space: nowrap; }
    .table td { padding: 12px 10px; color: #475569; vertical-align: middle; }
    .text-truncate-custom { max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; font-size: 0.85rem; }
    .img-thumbnail-custom { width: 50px; height: 50px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .btn-rounded { border-radius: 8px !important; font-weight: 600; font-size: 0.8rem; transition: all 0.2s ease-in-out; }
    .btn-action-custom { min-width: 85px; padding: 6px 10px; }
    .btn-blue-primary { background-color: #2563eb; color: #ffffff; border: none; }
    .btn-blue-primary:hover { background-color: #1d4ed8; color: #ffffff; transform: translateY(-1px); }
    .btn-reset-filter {
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #cbd5e1;
        font-weight: 600;
        font-size: 0.8rem;
        min-width: 95px;
        padding: 0.5rem 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-reset-filter:hover { background-color: #e2e8f0; color: #475569; }
    .btn-outline-blue { border: 1px solid #2563eb; color: #2563eb; background-color: transparent; }
    .btn-outline-blue:hover { background-color: #2563eb; color: #ffffff; }
    .text-price-green { color: #16a34a !important; font-weight: 700; font-size: 1rem; }
    .badge-sale-red { background-color: #fef2f2 !important; color: #dc2626 !important; border: 1px solid #fca5a5; border-radius: 6px; padding: 4px 8px; font-weight: 700; }
    .form-control-custom, .form-select-custom { border-radius: 10px; border: 1px solid #cbd5e1; padding: 8px 16px; background-color: #f8fafc; }
    .form-control-custom:focus, .form-select-custom:focus { border-color: #2563eb; background-color: #ffffff; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
    .input-readonly { background-color: #e2e8f0; cursor: not-allowed; color: #64748b; font-weight: bold; }
    .modal-content-custom { border-radius: 18px; border: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    .modal-header-blue { background-color: #2563eb; color: white; border-top-left-radius: 18px; border-top-right-radius: 18px; }
    .modal-header-red { background-color: #dc2626; color: white; border-top-left-radius: 18px; border-top-right-radius: 18px; }
</style>

<div class="card card-custom p-4">
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-5">
            <form action="{{ route('admin.phong.index') }}" method="GET" class="input-group">
                <input type="text" name="search" class="form-control form-control-custom me-2" placeholder="Tìm số phòng hoặc hạng..." value="{{ $search ?? '' }}">
                <button class="btn btn-blue-primary btn-rounded" type="submit"><i class="bi bi-search"></i> Tìm</button>
                <a href="{{ route('admin.phong.index') }}" class="btn btn-reset-filter btn-rounded ms-2">Xóa lọc</a>
            </form>
        </div>
        <div class="col-md-7 text-end">
            <button type="button" class="btn btn-danger btn-rounded fw-bold px-4" id="btnBulkSale">
                <i class="bi bi-tags-fill"></i> Áp dụng Sale hàng loạt (<span id="selectedCount">0</span>)
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="40" class="text-center"><input type="checkbox" id="selectAllRooms" class="form-check-input" style="width:18px; height:18px;"></th>
                    <th width="60">Ảnh</th>
                    <th>Phòng</th>
                    <th>Hạng</th>
                    <th>Sức chứa</th>
                    <th>Mô tả</th>
                    <th>Tiện nghi</th>
                    <th>Lưu ý</th>
                    <th>Giá gốc</th>
                    <th>Giá bán</th>
                    <th>Trạng thái</th>
                    <th>Khách</th>
                    <th>Sale</th>
                    <th class="text-center" width="280">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($danhSachPhong as $phong)
                @php
                    $now = \Carbon\Carbon::now();
                    $isSaleActive = $phong->giam_gia_percent > 0 && $phong->sale_tu_ngay && $phong->sale_den_ngay && $now->between($phong->sale_tu_ngay, $phong->sale_den_ngay);
                    $giaHienTai = $isSaleActive ? $phong->gia_phong * (1 - $phong->giam_gia_percent / 100) : $phong->gia_phong;
                @endphp
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="sub-chk form-check-input" data-id="{{ $phong->id_phong }}" style="width:18px; height:18px;">
                    </td>
                    <td>
                        @if($phong->anh)
                            @if(filter_var($phong->anh, FILTER_VALIDATE_URL))
                                <img src="{{ $phong->anh }}" class="img-thumbnail-custom" alt="Ảnh P.{{ $phong->so_phong }}">
                            @else
                                <img src="{{ asset('storage/' . $phong->anh) }}" class="img-thumbnail-custom" alt="Ảnh P.{{ $phong->so_phong }}">
                            @endif
                        @else
                            <div class="img-thumbnail-custom bg-light d-flex align-items-center justify-content-center text-muted border">
                                <i class="bi bi-image" style="font-size: 1.2rem;"></i>
                            </div>
                        @endif
                    </td>
                    <td class="fw-bold text-dark fs-6">P.{{ $phong->so_phong }}</td>
                    <td><span class="badge bg-light text-secondary border px-2 py-1">{{ $phong->loai_phong }}</span></td>
                    <td class="text-center fw-bold text-primary">{{ $phong->so_luong_nguoi ?? 2 }} <i class="bi bi-person-fill"></i></td>

                    <td><span class="text-truncate-custom" title="{{ $phong->mo_ta }}">{{ $phong->mo_ta ?? '---' }}</span></td>
                    <td><span class="text-truncate-custom" title="{{ $phong->tien_nghi }}">{{ $phong->tien_nghi ?? '---' }}</span></td>
                    <td><span class="text-truncate-custom" title="{{ $phong->thong_tin_quan_trong }}">{{ $phong->thong_tin_quan_trong ?? '---' }}</span></td>

                    <td><del class="text-muted small">{{ number_format($phong->gia_phong, 0, ',', '.') }}đ</del></td>
                    <td class="text-price-green">{{ number_format($giaHienTai, 0, ',', '.') }}đ</td>

                    <td>
                        @if($phong->trang_thai == 'Trống')
                            <span class="badge bg-success-subtle text-success px-2 py-1">Trống</span>
                        @elseif($phong->trang_thai == 'Đã đặt')
                            <span class="badge bg-danger-subtle text-danger px-2 py-1">Đã đặt</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning px-2 py-1">Bảo trì</span>
                        @endif
                    </td>

                    <td>
                        @if($phong->datPhongHienTai && $phong->datPhongHienTai->khachhang)
                            <div class="fw-bold text-dark small"><i class="bi bi-person-fill text-primary"></i> {{ $phong->datPhongHienTai->khachhang->ho_ten }}</div>
                        @else
                            <span class="text-muted small">---</span>
                        @endif
                    </td>
                    <td>
                        @if($isSaleActive)
                            <span class="badge badge-sale-red"><i class="bi bi-lightning-charge-fill"></i> -{{ $phong->giam_gia_percent }}%</span>
                        @else
                            <span class="text-muted small">---</span>
                        @endif
                    </td>

                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-outline-blue btn-rounded btn-action-custom btn-edit-room"
                                    data-id="{{ $phong->id_phong }}"
                                    data-so="{{ $phong->so_phong }}"
                                    data-loai="{{ $phong->loai_phong }}"
                                    data-gia="{{ $phong->gia_phong }}"
                                    data-trangthai="{{ $phong->trang_thai }}"
                                    data-nguoi="{{ $phong->so_luong_nguoi }}"
                                    data-mota="{{ $phong->mo_ta }}"
                                    data-tiennghi="{{ $phong->tien_nghi }}"
                                    data-thongtin="{{ $phong->thong_tin_quan_trong }}"
                                    data-anh="{{ $phong->anh }}">
                                <i class="bi bi-pencil-square me-1"></i> Sửa
                            </button>

                            @if($phong->datPhongHienTai)
                                <button type="button" class="btn btn-outline-danger btn-rounded btn-action-custom btn-clear-customer"
                                        data-url="{{ route('admin.phong.giaiphong', $phong->id_phong) }}" data-room="P.{{ $phong->so_phong }}">
                                    <i class="bi bi-person-x-fill me-1"></i> Xóa
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-rounded btn-action-custom" disabled>
                                    <i class="bi bi-person-x-fill me-1"></i> Xóa
                                </button>
                            @endif

                            <button type="button" class="btn btn-danger btn-rounded btn-action-custom btn-single-sale"
                                    data-id="{{ $phong->id_phong }}" data-room="P.{{ $phong->so_phong }}">
                                <i class="bi bi-tag-fill me-1"></i> Sale
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="14" class="text-center text-muted py-5">Không tìm thấy phòng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="editRoomModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formEditRoom" method="POST" enctype="multipart/form-data" class="modal-content modal-content-custom">
            @csrf
            <div class="modal-header modal-header-blue py-3">
                <h5 class="modal-title fw-bold fs-6"><i class="bi bi-pencil-square"></i> Cập nhật chi tiết thông tin phòng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="row g-4">
                    <div class="col-md-6 border-end pe-4">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Thông tin cơ bản</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Số phòng</label>
                            <input type="text" name="so_phong" id="edit_so_phong" class="form-control form-control-custom input-readonly" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Hạng phòng</label>
                            <select name="loai_phong" id="edit_loai_phong" class="form-select form-select-custom" required>
                                <option value="Standard">Standard</option>
                                <option value="Deluxe">Deluxe</option>
                                <option value="Suite">Suite</option>
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-7">
                                <label class="form-label fw-bold small text-secondary">Giá gốc (VNĐ)</label>
                                <input type="number" name="gia_phong" id="edit_gia_phong" class="form-control form-control-custom" min="0" required>
                            </div>
                            <div class="col-5">
                                <label class="form-label fw-bold small text-secondary">Sức chứa</label>
                                <div class="input-group">
                                    <input type="number" name="so_luong_nguoi" id="edit_so_luong_nguoi" class="form-control form-control-custom" min="1" required>
                                    <span class="input-group-text bg-light text-muted" style="border-radius: 0 10px 10px 0;"><i class="bi bi-people-fill"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Trạng thái phòng</label>
                            <select name="trang_thai" id="edit_trang_thai" class="form-select form-select-custom" required>
                                <option value="Trống">Trống (Sẵn sàng đón khách)</option>
                                <option value="Đã đặt">Đã đặt (Đang có khách)</option>
                                <option value="Bảo trì">Bảo trì (Tạm khóa sửa chữa)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 ps-md-3">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Chi tiết & Hình ảnh</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Ảnh phòng (Chọn 1 trong 2)</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light"><i class="bi bi-cloud-arrow-up-fill text-primary"></i></span>
                                <input type="file" name="anh" class="form-control form-control-custom" accept="image/*" title="Upload từ máy tính">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-link-45deg text-success"></i></span>
                                <input type="url" name="anh_link" id="edit_anh_link" class="form-control form-control-custom" placeholder="Hoặc dán link ảnh web (https://...)">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Mô tả tổng quan</label>
                            <textarea name="mo_ta" id="edit_mo_ta" class="form-control form-control-custom" rows="2" placeholder="Ví dụ: Phòng view biển, có ban công..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Tiện nghi</label>
                            <textarea name="tien_nghi" id="edit_tien_nghi" class="form-control form-control-custom" rows="2" placeholder="Tivi, Điều hòa, Bồn tắm..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Thông tin quan trọng / Lưu ý</label>
                            <textarea name="thong_tin_quan_trong" id="edit_thong_tin_quan_trong" class="form-control form-control-custom" rows="1" placeholder="Ví dụ: Không hút thuốc..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light" style="border-bottom-left-radius: 18px; border-bottom-right-radius: 18px;">
                <button type="button" class="btn btn-outline-secondary btn-rounded px-4" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-blue-primary btn-rounded px-4" id="btnSubmitEdit">Lưu thông tin</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="saleRoomModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.phong.applySale') }}" method="POST" class="modal-content modal-content-custom">
            @csrf
            <input type="hidden" name="scope" id="sale_scope" value="selected">
            <div id="container_selected_ids"></div>
            <div class="modal-header modal-header-red py-3">
                <h5 class="modal-title fw-bold fs-6"><i class="bi bi-lightning-charge-fill"></i> Thiết lập giảm giá</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert border-0 small mb-3 d-none" id="alertSaleContext" style="border-radius: 10px;"></div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Mức giảm giá (% )</label>
                    <div class="input-group">
                        <input type="number" name="giam_gia_percent" class="form-control form-control-custom text-sale-red fw-bold" min="0" max="100" required>
                        <span class="input-group-text fw-bold text-danger bg-danger-subtle">% SALE</span>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-bold small text-secondary">Từ ngày</label>
                        <input id="sale_tu_ngay" type="date" name="sale_tu_ngay" class="form-control form-control-custom" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small text-secondary">Đến ngày</label>
                        <input id="sale_den_ngay" type="date" name="sale_den_ngay" class="form-control form-control-custom" value="{{ date('Y-m-d', strtotime('+7 days')) }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light" style="border-bottom-left-radius: 18px; border-bottom-right-radius: 18px;">
                <button type="button" class="btn btn-outline-secondary btn-rounded px-4" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger btn-rounded px-4" id="btnSubmitSale">Kích hoạt Sale</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="confirmActionModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-4">
                <div class="text-warning mb-3" style="font-size: 3.5rem;"><i class="bi bi-exclamation-circle-fill"></i></div>
                <h6 class="fw-bold text-dark mb-2" id="confirmTitle">Xác nhận</h6>
                <p class="text-secondary small mb-4" id="confirmMessage">Bạn chắc chắn chứ?</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light btn-rounded px-3 w-50 fw-bold" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-dark btn-rounded px-3 w-50 fw-bold" id="btnConfirmExecute">Đồng ý</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let activeFormToSubmit = null;

        function triggerConfirmation(title, message, callbackAction) {
            $('#confirmTitle').text(title);
            $('#confirmMessage').text(message);
            $('#confirmActionModal').modal('show');
            activeFormToSubmit = callbackAction;
        }

        $('#btnConfirmExecute').click(function() {
            $('#confirmActionModal').modal('hide');
            if(typeof activeFormToSubmit === 'function') activeFormToSubmit();
            else if(activeFormToSubmit) activeFormToSubmit.submit();
        });

        // Bật popup Sửa
        $('.btn-edit-room').click(function() {
            $('#formEditRoom').attr('action', `/admin/quan-ly-phong/update/${$(this).data('id')}`);
            $('#edit_so_phong').val($(this).data('so'));
            $('#edit_loai_phong').val($(this).data('loai'));
            $('#edit_gia_phong').val($(this).data('gia'));
            $('#edit_trang_thai').val($(this).data('trangthai'));
            $('#edit_so_luong_nguoi').val($(this).data('nguoi'));
            $('#edit_mo_ta').val($(this).data('mota'));
            $('#edit_tien_nghi').val($(this).data('tiennghi'));
            $('#edit_thong_tin_quan_trong').val($(this).data('thongtin'));

            // Đổ link ảnh (nếu là link web) vào input text
            let currentAnh = $(this).data('anh');
            if(currentAnh && currentAnh.startsWith('http')) {
                $('#edit_anh_link').val(currentAnh);
            } else {
                $('#edit_anh_link').val('');
            }

            $('#editRoomModal').modal('show');
        });

        $('#btnSubmitEdit').click(function() {
            $('#editRoomModal').modal('hide');
            triggerConfirmation('Cập nhật phòng?', 'Dữ liệu mới sẽ được ghi đè.', function() { $('#formEditRoom').submit(); });
        });

        $('#selectAllRooms').change(function() {
            $('.sub-chk').prop('checked', $(this).prop('checked'));
            $('#selectedCount').text($('.sub-chk:checked').length);
        });

        $(document).on('change', '.sub-chk', function() {
            $('#selectAllRooms').prop('checked', $('.sub-chk:checked').length === $('.sub-chk').length);
            $('#selectedCount').text($('.sub-chk:checked').length);
        });

        $('#btnBulkSale').click(function() {
            let checkedIds = [];
            $('.sub-chk:checked').each(function() { checkedIds.push($(this).data('id')); });
            $('#container_selected_ids').empty();
            $('#alertSaleContext').removeClass('d-none');

            if(checkedIds.length === 0) {
                $('#sale_scope').val('all');
                $('#alertSaleContext').removeClass('alert-info').addClass('alert-danger bg-danger-subtle text-danger').html('<b>Cảnh báo:</b> Áp dụng Sale cho TẤT CẢ phòng!');
            } else {
                $('#sale_scope').val('selected');
                $('#alertSaleContext').removeClass('alert-danger bg-danger-subtle text-danger').addClass('alert-info bg-info-subtle text-info').html(`Sale <b>${checkedIds.length}</b> phòng.`);
                checkedIds.forEach(id => { $('#container_selected_ids').append(`<input type="hidden" name="room_ids[]" value="${id}">`); });
            }
            $('#saleRoomModal').modal('show');
        });

        $('.btn-single-sale').click(function() {
            $('#container_selected_ids').empty().append(`<input type="hidden" name="room_ids[]" value="${$(this).data('id')}">`);
            $('#sale_scope').val('single');
            $('#alertSaleContext').removeClass('d-none alert-danger bg-danger-subtle text-danger').addClass('alert-info bg-info-subtle text-info').html(`Sale phòng <b>${$(this).data('room')}</b>.`);
            $('#saleRoomModal').modal('show');
        });

        $('#btnSubmitSale').click(function() {
            let form = $(this).closest('form');
            $('#saleRoomModal').modal('hide');
            triggerConfirmation('Bật Sale?', 'Giá sẽ giảm ngay lập tức.', function() { form.submit(); });
        });

        $('#sale_tu_ngay').on('change', function() {
            let startDate = $(this).val();
            if (startDate) {
                $('#sale_den_ngay').attr('min', startDate);
                if ($('#sale_den_ngay').val() < startDate) {
                    $('#sale_den_ngay').val(startDate);
                }
            }
        });

        $('.btn-clear-customer').click(function() {
            let tempForm = $('<form>', { 'action': $(this).data('url'), 'method': 'POST' }).append($('<input>', { 'type': 'hidden', 'name': '_token', 'value': '{{ csrf_token() }}' }));
            $('body').append(tempForm);
            triggerConfirmation(`Xóa khách ở ${$(this).data('room')}?`, 'Chuyển về trạng thái Trống.', function() { tempForm.submit(); });
        });
    });
</script>
@endsection
