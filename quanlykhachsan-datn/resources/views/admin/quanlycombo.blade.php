@extends('admin.layout.master')
@section('title', 'Quản lý Combo')
@section('page_title', 'Phân hệ quản lý Combo khách sạn')

@section('content')
<style>
    .card-custom { background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05); }
    .table th { font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #1e40af; background-color: #eff6ff; padding: 15px 10px; border-bottom: 2px solid #dbeafe; }
    .btn-rounded { border-radius: 8px !important; font-weight: 600; font-size: 0.85rem; }

    /* Style cho Danh sách dịch vụ dạng nút (+ / -) */
    .dv-item { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 15px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; transition: 0.3s; }
    .dv-item.active { border-color: #10b981; background: #ecfdf5; }
    .dv-price { font-size: 0.85rem; color: #ef4444; font-weight: bold; }
    .btn-dv-toggle { border-radius: 50%; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; font-weight: bold; }
    .combo-img { width: 55px; height: 55px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
</style>

<div class="card card-custom p-4">
    <div class="row mb-4">
        <div class="col-md-6 text-end offset-md-6">
            <button class="btn btn-primary btn-rounded px-4" data-bs-toggle="modal" data-bs-target="#createComboModal">
                <i class="bi bi-plus-circle me-2"></i> Tạo Combo Mới
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Mã Combo</th>
                    <th>Ảnh</th>
                    <th>Tên Combo</th>
                    <th>Hạng phòng</th>
                    <th class="text-center">Đêm ở</th>
                    <th>Quyền lợi</th>
                    <th>Điều khoản</th>
                    <th>Dịch vụ đi kèm</th>
                    <th class="text-end">Tổng Giá (VNĐ)</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($combos as $cb)
                <tr>
                    <td class="fw-bold text-primary">CB-{{ $cb->id_combo }}</td>
                    <td>
                        @if($cb->hinh_anh)
                            <img src="{{ asset($cb->hinh_anh) }}" class="combo-img" alt="Combo image">
                        @else
                            <span class="badge bg-light text-muted border">Không ảnh</span>
                        @endif
                    </td>
                    <td class="fw-bold text-dark">
                        {{ $cb->ten_combo }}
                        @if($cb->mo_ta)
                            <br><small class="text-muted fw-normal" style="font-size: 0.75rem;">{{ \Illuminate\Support\Str::limit($cb->mo_ta, 30) }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ $cb->loai_phong_ap_dung }}</span>
                    </td>
                    <td class="text-center fw-bold">{{ $cb->so_dem_luu_tru }}</td>
                    <td><small class="text-muted d-block text-truncate" style="max-width: 120px;" title="{{ $cb->quyen_loi }}">{{ $cb->quyen_loi ?? '---' }}</small></td>
                    <td><small class="text-muted d-block text-truncate" style="max-width: 120px;" title="{{ $cb->dieu_khoan }}">{{ $cb->dieu_khoan ?? '---' }}</small></td>
                    <td>
                        @if($cb->dich_vu->count() > 0)
                            <ul class="mb-0 ps-3" style="font-size: 0.85rem;">
                                @foreach($cb->dich_vu as $dv)
                                    <li>{{ $dv->ten_dich_vu }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted small">Không có</span>
                        @endif
                    </td>
                    <td class="text-end fw-bold text-success">{{ number_format($cb->gia_combo, 0, ',', '.') }} VNĐ</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-warning btn-rounded btn-edit"
                            data-id="{{ $cb->id_combo }}"
                            data-ten="{{ $cb->ten_combo }}"
                            data-loaiphong="{{ $cb->loai_phong_ap_dung }}"
                            data-gia="{{ $cb->gia_combo }}"
                            data-sodem="{{ $cb->so_dem_luu_tru }}"
                            data-giadinhmuc="{{ $cb->gia_phong_dinh_muc }}"
                            data-mota="{{ $cb->mo_ta }}"
                            data-quyenloi="{{ $cb->quyen_loi }}"
                            data-dieukhoan="{{ $cb->dieu_khoan }}"
                            data-hinhanh="{{ $cb->hinh_anh ? asset($cb->hinh_anh) : '' }}"
                            data-dichvu='@json($cb->dichvu_ids)'>
                            Sửa
                        </button>
                        <form action="{{ route('admin.combo.destroy', $cb->id_combo) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger btn-rounded" onclick="return confirm('Xóa combo này?');">Xoá</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $combos->links('pagination::bootstrap-5') }}
    </div>
</div>

<div class="modal fade" id="createComboModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.combo.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Khởi tạo Combo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Tên Combo</label>
                    <input type="text" name="ten_combo" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-success">Giá trọn gói (Tự tính)</label>
                    <input type="number" name="tong_gia" class="form-control fw-bold text-success bg-light" readonly required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Tải lên hình ảnh Combo</label>
                    <input type="file" name="hinh_anh" class="form-control" accept="image/*">
                </div>
                <div class="col-md-12">
                    <label class="form-label text-primary fw-bold">Chọn Hạng Phòng Áp Dụng</label>
                    <select name="loai_phong_ap_dung" class="form-select border-primary" required>
                        <option value="">-- Chọn hạng phòng --</option>
                        @foreach($hangPhongs as $hp)
                            <option value="{{ $hp }}">{{ $hp }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Số đêm lưu trú</label>
                    <input type="number" name="so_dem_luu_tru" class="form-control" value="1" min="1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Giá phòng định mức/đêm (VNĐ)</label>
                    <input type="number" name="gia_phong_dinh_muc" class="form-control" value="0" min="0" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Mô tả ngắn</label>
                    <textarea name="mo_ta" class="form-control" rows="2" placeholder="Nhập một vài thông tin mô tả cơ bản..."></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-primary">Quyền lợi Combo</label>
                    <textarea name="quyen_loi" class="form-control" rows="2" placeholder="Ví dụ: Miễn phí Buffet ăn sáng, Free nước uống lúc tiếp đón..."></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-danger">Điều khoản áp dụng</label>
                    <textarea name="dieu_khoan" class="form-control" rows="2" placeholder="Ví dụ: Không áp dụng hoàn huỷ phòng, không sử dụng đồng thời khuyến mãi khác..."></textarea>
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-label fw-bold">Thiết lập Dịch Vụ Đi Kèm</label>
                    <div class="row g-2" id="create_dichvu_list">
                        @foreach($dichVus as $dv)
                        <div class="col-md-6">
                            <div class="dv-item js-dv-item">
                                <input type="checkbox" name="dichvu_ids[]" value="{{ $dv->id_dichvu }}" class="d-none js-dv-checkbox" data-gia="{{ $dv->gia }}">
                                <div>
                                    <span class="fw-semibold">{{ $dv->ten_dich_vu }}</span><br>
                                    <span class="dv-price">+{{ number_format($dv->gia, 0, ',', '.') }} VNĐ</span>
                                </div>
                                <button type="button" class="btn btn-outline-success btn-dv-toggle js-dv-toggle">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Tạo Combo</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editComboModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editComboForm" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Chỉnh sửa Combo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Tên Combo</label>
                    <input type="text" id="edit_ten_combo" name="ten_combo" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-success">Giá trọn gói (Tự tính)</label>
                    <input type="number" id="edit_tong_gia" name="tong_gia" class="form-control fw-bold text-success bg-light" readonly required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Hình ảnh đại diện (Để trống nếu giữ nguyên)</label>
                    <input type="file" name="hinh_anh" class="form-control" accept="image/*">
                    <div id="img_preview_area" class="mt-2" style="display:none;">
                        <span class="d-block small text-muted mb-1">Ảnh hiện tại:</span>
                        <img id="edit_img_src" src="" alt="Combo Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px;">
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="form-label text-warning fw-bold">Hạng phòng áp dụng</label>
                    <select name="loai_phong_ap_dung" id="edit_loai_phong_ap_dung" class="form-select border-warning" required>
                        <option value="">-- Chọn hạng phòng --</option>
                        @foreach($hangPhongs as $hp)
                            <option value="{{ $hp }}">{{ $hp }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Số đêm lưu trú</label>
                    <input type="number" id="edit_so_dem_luu_tru" name="so_dem_luu_tru" class="form-control" min="1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Giá phòng định mức/đêm (VNĐ)</label>
                    <input type="number" id="edit_gia_phong_dinh_muc" name="gia_phong_dinh_muc" class="form-control" min="0" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Mô tả ngắn</label>
                    <textarea name="mo_ta" id="edit_mo_ta" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-primary">Quyền lợi Combo</label>
                    <textarea name="quyen_loi" id="edit_quyen_loi" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-danger">Điều khoản áp dụng</label>
                    <textarea name="dieu_khoan" id="edit_dieu_khoan" class="form-control" rows="2"></textarea>
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-label fw-bold">Tuỳ chỉnh Dịch Vụ Đi Kèm</label>
                    <div class="row g-2" id="edit_dichvu_list">
                        @foreach($dichVus as $dv)
                        <div class="col-md-6">
                            <div class="dv-item js-dv-item">
                                <input type="checkbox" name="dichvu_ids[]" value="{{ $dv->id_dichvu }}" class="d-none js-dv-checkbox edit-checkbox" data-gia="{{ $dv->gia }}">
                                <div>
                                    <span class="fw-semibold">{{ $dv->ten_dich_vu }}</span><br>
                                    <span class="dv-price">+{{ number_format($dv->gia, 0, ',', '.') }} VNĐ</span>
                                </div>
                                <button type="button" class="btn btn-outline-success btn-dv-toggle js-dv-toggle">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-warning text-dark fw-bold">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- Hàm tính toán Tổng Giá Combo tự động ---
        function updateComboTotal(modalSelector) {
            let modal = document.querySelector(modalSelector);
            if (!modal) return;

            let giaDinhMuc = parseFloat(modal.querySelector('[name="gia_phong_dinh_muc"]').value) || 0;
            let soDem = parseInt(modal.querySelector('[name="so_dem_luu_tru"]').value) || 0;

            let tongDichVu = 0;
            modal.querySelectorAll('.js-dv-checkbox:checked').forEach(cb => {
                tongDichVu += parseFloat(cb.dataset.gia) || 0;
            });

            // Công thức: (Giá định mức * Số đêm) + Tổng giá dịch vụ đính kèm
            let tongGiaCombo = (giaDinhMuc * soDem) + tongDichVu;
            modal.querySelector('[name="tong_gia"]').value = tongGiaCombo;
        }

        // Lắng nghe thay đổi trên ô Nhập số đêm và Giá định mức để cập nhật tiền liên tục
        document.querySelectorAll('#createComboModal [name="gia_phong_dinh_muc"], #createComboModal [name="so_dem_luu_tru"]').forEach(el => {
            el.addEventListener('input', () => updateComboTotal('#createComboModal'));
        });
        document.querySelectorAll('#editComboModal [name="gia_phong_dinh_muc"], #editComboModal [name="so_dem_luu_tru"]').forEach(el => {
            el.addEventListener('input', () => updateComboTotal('#editComboModal'));
        });

        // --- 1. Logic xử lý đổi Trạng thái nút (+) hoặc (-) của Dịch vụ ---
        function attachServiceToggle(containerSelector) {
            document.querySelectorAll(containerSelector + ' .js-dv-toggle').forEach(btn => {
                btn.addEventListener('click', function() {
                    let parentItem = this.closest('.js-dv-item');
                    let checkbox = parentItem.querySelector('.js-dv-checkbox');
                    let icon = this.querySelector('i');

                    if (!checkbox.checked) {
                        checkbox.checked = true;
                        parentItem.classList.add('active');
                        this.classList.replace('btn-outline-success', 'btn-danger');
                        icon.classList.replace('bi-plus-lg', 'bi-dash-lg');
                    } else {
                        checkbox.checked = false;
                        parentItem.classList.remove('active');
                        this.classList.replace('btn-danger', 'btn-outline-success');
                        icon.classList.replace('bi-dash-lg', 'bi-plus-lg');
                    }
                    // Tính lại tổng tiền sau khi bớt hoặc thêm dịch vụ
                    updateComboTotal(containerSelector);
                });
            });
        }

        attachServiceToggle('#createComboModal');
        attachServiceToggle('#editComboModal');

        // --- 2. Xử lý logic Đổ dữ liệu vào Modal Sửa ---
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('editComboForm').action = '/admin/quan-ly-combo/update/' + this.dataset.id;
                document.getElementById('edit_ten_combo').value = this.dataset.ten;
                document.getElementById('edit_so_dem_luu_tru').value = this.dataset.sodem || 1;
                document.getElementById('edit_gia_phong_dinh_muc').value = this.dataset.giadinhmuc || 0;
                document.getElementById('edit_loai_phong_ap_dung').value = this.dataset.loaiphong;
                document.getElementById('edit_mo_ta').value = this.dataset.mota || '';
                document.getElementById('edit_quyen_loi').value = this.dataset.quyenloi || '';
                document.getElementById('edit_dieu_khoan').value = this.dataset.dieukhoan || '';

                // Hiển thị preview hình ảnh hiện tại
                let previewArea = document.getElementById('img_preview_area');
                if(this.dataset.hinhanh) {
                    document.getElementById('edit_img_src').src = this.dataset.hinhanh;
                    previewArea.style.display = 'block';
                } else {
                    previewArea.style.display = 'none';
                }

                // Đồng bộ và tải danh sách dịch vụ đã chọn từ trước
                let selectedServices = JSON.parse(this.dataset.dichvu);

                document.querySelectorAll('#edit_dichvu_list .js-dv-item').forEach(item => {
                    let checkbox = item.querySelector('.js-dv-checkbox');
                    let toggleBtn = item.querySelector('.js-dv-toggle');
                    let icon = toggleBtn.querySelector('i');

                    checkbox.checked = false;
                    item.classList.remove('active');
                    toggleBtn.classList.replace('btn-danger', 'btn-outline-success');
                    if (icon.classList.contains('bi-dash-lg')) {
                        icon.classList.replace('bi-dash-lg', 'bi-plus-lg');
                    }

                    if (selectedServices.includes(parseInt(checkbox.value))) {
                        checkbox.checked = true;
                        item.classList.add('active');
                        toggleBtn.classList.replace('btn-outline-success', 'btn-danger');
                        if (icon.classList.contains('bi-plus-lg')) {
                            icon.classList.replace('bi-plus-lg', 'bi-dash-lg');
                        }
                    }
                });

                // Chạy tính toán tổng tiền ngay sau khi vừa mở modal sửa
                updateComboTotal('#editComboModal');

                new bootstrap.Modal(document.getElementById('editComboModal')).show();
            });
        });
    });
</script>
@endsection
