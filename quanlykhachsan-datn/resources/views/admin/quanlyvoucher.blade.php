@extends('admin.layout.master')
@section('title', 'Quản lý Voucher')
@section('page_title', 'Phân hệ quản lý Mã Giảm Giá')

@section('content')
<style>
    .card-custom { background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05); }
    .table th { font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #1e40af; background-color: #eff6ff; padding: 15px 10px; border-bottom: 2px solid #dbeafe; }
    .btn-rounded { border-radius: 8px !important; font-weight: 600; font-size: 0.85rem; }
    .voucher-code { font-family: monospace; font-size: 1.1rem; letter-spacing: 2px; font-weight: bold; color: #b91c1c; background: #fee2e2; padding: 4px 10px; border-radius: 6px; border: 1px dashed #ef4444; }
</style>

<div class="card card-custom p-4">
    <div class="row mb-4">
        <div class="col-md-6 text-end offset-md-6">
            <button class="btn btn-primary btn-rounded px-4" data-bs-toggle="modal" data-bs-target="#createVoucherModal">
                <i class="bi bi-ticket-perforated me-2"></i> Tạo Voucher Mới
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
                    <th>Mã Code</th>
                    <th>Áp dụng cho</th>
                    <th>Mức giảm</th>
                    <th>Hạn sử dụng</th>
                    <th>Phân loại phát hành</th>
                    <th class="text-center">Kích hoạt</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vouchers as $vc)
                <tr>
                    <td><span class="voucher-code">{{ $vc->ma_code }}</span></td>
                    <td>
                        @if($vc->loai_voucher == 'ALL') <span class="badge bg-dark">Toàn hóa đơn</span>
                        @elseif($vc->loai_voucher == 'PHONG') <span class="badge bg-info text-dark">Chỉ Tiền Phòng</span>
                        @else <span class="badge bg-warning text-dark">Chỉ Dịch Vụ</span>
                        @endif
                    </td>
                    <td class="fw-bold text-success">
                        {{ $vc->is_percent ? $vc->muc_giam . '%' : number_format($vc->muc_giam, 0, ',', '.') . ' đ' }}
                    </td>
                    <td>
                        @php
                            $isExpired = \Carbon\Carbon::parse($vc->ngay_het_han)->isPast();
                        @endphp
                        <span class="{{ $isExpired ? 'text-danger fw-bold' : 'text-dark' }}">
                            {{ \Carbon\Carbon::parse($vc->ngay_het_han)->format('d/m/Y') }}
                        </span>
                        @if($isExpired) <br><small class="text-danger">(Đã hết hạn)</small> @endif
                    </td>
                    <td>
                        @if($vc->id_khachhang)
                            <span class="badge bg-primary"><i class="bi bi-person-fill"></i> Cá nhân</span><br>
                            <small class="text-muted">{{ $vc->ten_nguoi_so_huu }}</small>
                        @else
                            <span class="badge bg-success"><i class="bi bi-globe"></i> Công khai</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.voucher.toggle', $vc->id_voucher) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $vc->trang_thai ? 'btn-success' : 'btn-secondary' }} btn-rounded">
                                {{ $vc->trang_thai ? 'Đang bật' : 'Đang tắt' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-warning btn-rounded btn-edit"
                            data-id="{{ $vc->id_voucher }}"
                            data-code="{{ $vc->ma_code }}"
                            data-type="{{ $vc->loai_voucher }}"
                            data-discount="{{ $vc->muc_giam }}"
                            data-ispercent="{{ $vc->is_percent }}"
                            data-expiry="{{ \Carbon\Carbon::parse($vc->ngay_het_han)->format('Y-m-d') }}"
                            data-customer="{{ $vc->id_khachhang }}">
                            Sửa
                        </button>
                        <form action="{{ route('admin.voucher.destroy', $vc->id_voucher) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger btn-rounded" onclick="return confirm('Xóa voucher này?');">Xoá</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $vouchers->links('pagination::bootstrap-5') }}
    </div>
</div>

<div class="modal fade" id="createVoucherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.voucher.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Khởi tạo Voucher</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Mã Code (Tự nhập hoặc Random)</label>
                    <div class="input-group">
                        <input type="text" name="ma_code" id="create_ma_code" class="form-control text-uppercase fw-bold" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="generateCode('create_ma_code')">Tạo ngẫu nhiên</button>
                    </div>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold">Loại mục áp dụng giảm</label>
                    <select name="loai_voucher" class="form-select border-primary" required>
                        <option value="ALL">Giảm trên tổng thanh toán (Cả phòng + Dịch vụ)</option>
                        <option value="PHONG">Chỉ giảm trên tiền Phòng</option>
                        <option value="DICH_VU">Chỉ giảm trên tiền Dịch vụ đi kèm</option>
                    </select>
                </div>

                <div class="col-md-7">
                    <label class="form-label fw-bold">Mức giảm</label>
                    <input type="number" name="muc_giam" class="form-control" min="1" step="0.1" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold">Loại chiết khấu</label>
                    <select name="is_percent" class="form-select" required>
                        <option value="1">Theo phần trăm (%)</option>
                        <option value="0">Tiền mặt (VNĐ)</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold">Ngày hết hạn</label>
                    <input type="date" name="ngay_het_han" class="form-control" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold text-success">Cấp phát riêng cho khách (Tùy chọn)</label>
                    <input type="text" id="search_id_khachhang_create" class="form-control mb-2" placeholder="Tìm khách theo tên hoặc SĐT">
                    <select name="id_khachhang" id="create_id_khachhang" class="form-select">
                        <option value="">-- [Voucher công khai] Áp dụng cho mọi người --</option>
                        @foreach($khachHangs as $kh)
                            <option value="{{ $kh->id_khachhang }}">{{ $kh->ho_ten }} - {{ $kh->so_dien_thoai }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Nếu chọn 1 khách hàng, chỉ người đó đăng nhập mới dùng được mã này.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Tạo Voucher</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editVoucherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editVoucherForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Sửa thông tin Voucher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Mã Code</label>
                    <input type="text" id="edit_ma_code" name="ma_code" class="form-control text-uppercase fw-bold" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold">Loại mục áp dụng giảm</label>
                    <select id="edit_loai_voucher" name="loai_voucher" class="form-select border-warning" required>
                        <option value="ALL">Giảm trên tổng thanh toán</option>
                        <option value="PHONG">Chỉ giảm trên tiền Phòng</option>
                        <option value="DICH_VU">Chỉ giảm trên tiền Dịch vụ đi kèm</option>
                    </select>
                </div>

                <div class="col-md-7">
                    <label class="form-label fw-bold">Mức giảm</label>
                    <input type="number" id="edit_muc_giam" name="muc_giam" class="form-control" min="1" step="0.1" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold">Loại chiết khấu</label>
                    <select id="edit_is_percent" name="is_percent" class="form-select" required>
                        <option value="1">Theo phần trăm (%)</option>
                        <option value="0">Tiền mặt (VNĐ)</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold">Ngày hết hạn</label>
                    <input type="date" id="edit_ngay_het_han" name="ngay_het_han" class="form-control" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold text-success">Cấp phát riêng cho khách</label>
                    <input type="text" id="search_id_khachhang_edit" class="form-control mb-2" placeholder="Tìm khách theo tên hoặc SĐT">
                    <select id="edit_id_khachhang" name="id_khachhang" class="form-select">
                        <option value="">-- [Voucher công khai] Áp dụng cho mọi người --</option>
                        @foreach($khachHangs as $kh)
                            <option value="{{ $kh->id_khachhang }}">{{ $kh->ho_ten }} - {{ $kh->so_dien_thoai }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-warning fw-bold text-dark">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Hàm tạo ngẫu nhiên mã code 8 ký tự
    function generateCode(inputId) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let result = '';
        for (let i = 0; i < 8; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById(inputId).value = 'KM' + result;
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('editVoucherForm').action = '/admin/quan-ly-voucher/update/' + this.dataset.id;
                document.getElementById('edit_ma_code').value = this.dataset.code;
                document.getElementById('edit_loai_voucher').value = this.dataset.type;
                document.getElementById('edit_muc_giam').value = this.dataset.discount;
                document.getElementById('edit_is_percent').value = this.dataset.ispercent;
                document.getElementById('edit_ngay_het_han').value = this.dataset.expiry;

                // Set the specific customer if exists
                let idKhachHang = this.dataset.customer;
                let khachHangSelect = document.getElementById('edit_id_khachhang');
                khachHangSelect.value = idKhachHang ? idKhachHang : "";

                new bootstrap.Modal(document.getElementById('editVoucherModal')).show();
            });

            const filterCustomerOptions = (inputId, selectId) => {
                const search = document.getElementById(inputId).value.toLowerCase();
                const select = document.getElementById(selectId);

                Array.from(select.options).forEach(option => {
                    if (option.value === '') {
                        option.hidden = false;
                        return;
                    }

                    const text = option.text.toLowerCase();
                    option.hidden = !text.includes(search);
                });
            };

            document.getElementById('search_id_khachhang_create').addEventListener('input', function () {
                filterCustomerOptions('search_id_khachhang_create', 'create_id_khachhang');
            });

            document.getElementById('search_id_khachhang_edit').addEventListener('input', function () {
                filterCustomerOptions('search_id_khachhang_edit', 'edit_id_khachhang');
            });
        });
    });
</script>
@endsection
