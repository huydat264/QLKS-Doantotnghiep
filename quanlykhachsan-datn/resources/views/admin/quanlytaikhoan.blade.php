@extends('admin.layout.master')
@section('title', 'Quản lý Tài Khoản')
@section('page_title', 'Hệ thống Quản lý Tài khoản (Nội bộ & Khách hàng)')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Quản lý Tài khoản Toàn hệ thống</h4>
            <p class="text-muted mb-0">Phân loại và điều khiển trạng thái đăng nhập.</p>
        </div>
        <button class="btn btn-primary fw-bold px-4 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddAccount">
            <i class="bi bi-person-plus-fill me-1"></i> Tạo Tài Khoản
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</div>
    @endif

    <ul class="nav nav-tabs nav-fill border-bottom-0 mb-4" id="accountTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold border-0 bg-light shadow-sm me-2 rounded-3 text-primary" id="noibo-tab" data-bs-toggle="tab" data-bs-target="#noibo" type="button" role="tab">
                <i class="bi bi-shield-lock-fill me-1"></i> Tài khoản Nội bộ (ADMIN & NHÂN VIÊN)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold border-0 bg-light shadow-sm rounded-3 text-secondary" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button" role="tab">
                <i class="bi bi-people-fill me-1"></i> Tài khoản Khách hàng (USER)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="accountTabsContent">

        <div class="tab-pane fade show active" id="noibo" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tên đăng nhập</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taiKhoanNoiBo as $tk)
                        <tr>
                            <td class="fw-bold text-dark">
                                {{ $tk->username }}
                                @if(!empty($tk->linked_phone))
                                    <div class="small text-muted">SĐT: {{ $tk->linked_phone }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $tk->role == 'ADMIN' ? 'bg-danger' : 'bg-primary' }}">{{ $tk->role }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $tk->trang_thai == 'ACTIVE' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $tk->trang_thai == 'ACTIVE' ? 'Hoạt động' : 'Bị khóa' }}</span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.taikhoan.toggle', $tk->id_taikhoan) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $tk->trang_thai == 'ACTIVE' ? 'btn-outline-danger' : 'btn-outline-success' }}" title="Khóa/Mở khóa">
                                        <i class="bi {{ $tk->trang_thai == 'ACTIVE' ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-id="{{ $tk->id_taikhoan }}" data-vaitro="{{ $tk->role }}" data-idnhanvien="{{ $tk->linked_id ?? '' }}" data-nvname="{{ $tk->linked_name ?? '' }}" data-nvphone="{{ $tk->linked_phone ?? '' }}" data-idkhachhang="{{ $tk->linked_id ?? '' }}" data-khname="{{ $tk->linked_name ?? '' }}" data-khphone="{{ $tk->linked_phone ?? '' }}" data-bs-toggle="modal" data-bs-target="#modalEditAccount">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Không có tài khoản nội bộ nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-2">{{ $taiKhoanNoiBo->links() }}</div>
        </div>

        <div class="tab-pane fade" id="user" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tên đăng nhập</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taiKhoanUser as $tk)
                        <tr>
                            <td class="fw-bold text-dark">
                                {{ $tk->username }}
                                @if(!empty($tk->linked_phone))
                                    <div class="small text-muted">SĐT: {{ $tk->linked_phone }}</div>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $tk->role }}</span></td>
                            <td>
                                <span class="badge {{ $tk->trang_thai == 'ACTIVE' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $tk->trang_thai == 'ACTIVE' ? 'Hoạt động' : 'Bị khóa' }}</span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.taikhoan.toggle', $tk->id_taikhoan) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $tk->trang_thai == 'ACTIVE' ? 'btn-outline-danger' : 'btn-outline-success' }}" title="Khóa/Mở khóa">
                                        <i class="bi {{ $tk->trang_thai == 'ACTIVE' ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-id="{{ $tk->id_taikhoan }}" data-vaitro="{{ $tk->role }}" data-idnhanvien="{{ $tk->linked_id ?? '' }}" data-nvname="{{ $tk->linked_name ?? '' }}" data-nvphone="{{ $tk->linked_phone ?? '' }}" data-idkhachhang="{{ $tk->linked_id ?? '' }}" data-khname="{{ $tk->linked_name ?? '' }}" data-khphone="{{ $tk->linked_phone ?? '' }}" data-bs-toggle="modal" data-bs-target="#modalEditAccount">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Không có tài khoản khách hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-2">{{ $taiKhoanUser->links() }}</div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalAddAccount" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.taikhoan.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Tạo Tài Khoản Hệ Thống</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên đăng nhập (Username)</label>
                    <input type="text" name="ten_dang_nhap" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Mật khẩu</label>
                    <input type="password" name="mat_khau" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Phân quyền Vai trò</label>
                    <select name="vai_tro" class="form-select border-primary fw-bold text-primary">
                        <option value="USER">Khách hàng (USER)</option>
                        <option value="NHANVIEN">Nhân viên (NHANVIEN)</option>
                        <option value="ADMIN">Quản trị viên (ADMIN)</option>
                    </select>
                    <div class="mb-3 d-none" id="group_chon_nhan_vien">
    <label class="form-label fw-bold text-danger"><i class="bi bi-person-bounding-box"></i> Chọn Nhân Viên Nhận Tài Khoản</label>
    <select name="id_nhanvien" id="id_nhanvien" class="form-select border-danger">
        <option value="">-- Click để chọn nhân viên chưa có tài khoản --</option>
        @foreach($danhSachNhanVien as $nv)
            <option value="{{ $nv->id_nhanvien }}">
                NV-{{ $nv->id_nhanvien }} | {{ $nv->ho_ten }} (Phòng ban/SĐT: {{ $nv->so_dien_thoai ?? 'Chưa cập nhật' }})
            </option>
        @endforeach
    </select>
    <small class="text-muted d-block mt-1">* Hệ thống tự động lọc những nhân viên chưa có bất kỳ tài khoản nào.</small>
</div>

                    <!-- Chọn Khách hàng (dành cho role USER) -->
                    <div class="mb-3 d-none" id="group_chon_khach_hang">
                        <label class="form-label fw-bold text-success"><i class="bi bi-person"></i> Gán cho Khách hàng</label>
                        <select name="id_khachhang" id="id_khachhang" class="form-select border-success">
                            <option value="">-- Chọn Khách hàng chưa có tài khoản --</option>
                            @foreach($danhSachKhachHang as $kh)
                                <option value="{{ $kh->id_khachhang }}">KH-{{ $kh->id_khachhang }} | {{ $kh->ho_ten }} ({{ $kh->so_dien_thoai ?? 'Chưa cập nhật' }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Gán tài khoản USER cho hồ sơ khách hàng.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary fw-bold">Khởi tạo</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditAccount" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editAccountForm" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">Điều Chỉnh Thông Tin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Phân quyền Vai trò</label>
                    <select name="vai_tro" id="edit_vaitro" class="form-select border-warning fw-bold">
                        <option value="USER">Khách hàng (USER)</option>
                        <option value="NHANVIEN">Nhân viên (NHANVIEN)</option>
                        <option value="ADMIN">Quản trị viên (ADMIN)</option>
                    </select>
                </div>
                    <div class="mb-3 d-none" id="group_chon_nhan_vien_edit">
                        <label class="form-label fw-bold text-danger"><i class="bi bi-person-bounding-box"></i> Chọn Nhân Viên Nhận Tài Khoản</label>
                        <select name="id_nhanvien" id="edit_id_nhanvien" class="form-select border-danger">
                            <option value="">-- Click để chọn nhân viên chưa có tài khoản --</option>
                            @foreach($danhSachNhanVien as $nv)
                                <option value="{{ $nv->id_nhanvien }}">NV-{{ $nv->id_nhanvien }} | {{ $nv->ho_ten }} ({{ $nv->so_dien_thoai ?? 'Chưa cập nhật' }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">* Hệ thống tự động lọc những nhân viên chưa có bất kỳ tài khoản nào.</small>
                    </div>
                    <div class="mb-3 d-none" id="group_chon_khach_hang_edit">
                        <label class="form-label fw-bold text-success"><i class="bi bi-person"></i> Chọn Khách hàng</label>
                        <select name="id_khachhang" id="edit_id_khachhang" class="form-select border-success">
                            <option value="">-- Chọn Khách hàng chưa có tài khoản --</option>
                            @foreach($danhSachKhachHang as $kh)
                                <option value="{{ $kh->id_khachhang }}">KH-{{ $kh->id_khachhang }} | {{ $kh->ho_ten }} ({{ $kh->so_dien_thoai ?? 'Chưa cập nhật' }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Chọn khách hàng để gán tài khoản USER.</small>
                    </div>
                <div class="mb-3 p-3 bg-light rounded-3 border border-danger">
                    <label class="form-label fw-bold text-danger mb-1"><i class="bi bi-key"></i> Mật khẩu mới</label>
                    <p class="small text-muted mb-2">Chỉ nhập nếu muốn đổi mật khẩu. Để trống để giữ nguyên.</p>
                    <input type="password" name="mat_khau" class="form-control border-danger">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-warning fw-bold text-dark">Lưu Thay Đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
    // JS Bắt sự kiện chuyển Tab cho đẹp
    document.querySelectorAll('#accountTabs button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', event => {
            document.querySelectorAll('#accountTabs button').forEach(b => {
                b.classList.remove('text-primary');
                b.classList.add('text-secondary');
            });
            event.target.classList.add('text-primary');
            event.target.classList.remove('text-secondary');
        });
    });

    // JS Load dữ liệu vào Form Sửa
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const vaitro = this.getAttribute('data-vaitro');
                const idNhanVien = this.getAttribute('data-idnhanvien');
                const nvName = this.getAttribute('data-nvname');

                document.getElementById('edit_vaitro').value = vaitro;
                document.getElementById('editAccountForm').action = `/admin/quan-ly-tai-khoan/update/${id}`;

                const groupEdit = document.getElementById('group_chon_nhan_vien_edit');
                const selectEditNv = document.getElementById('edit_id_nhanvien');

                // Reset select to default
                selectEditNv.value = "";

                // If there is a linked employee for this account, ensure option exists and select it
                if (idNhanVien) {
                    // If option doesn't exist in the list, add it at top
                    if (!selectEditNv.querySelector(`option[value="${idNhanVien}"]`)) {
                        const opt = document.createElement('option');
                        opt.value = idNhanVien;
                        const nvPhone = this.getAttribute('data-nvphone') || '';
                        opt.text = nvPhone ? `NV-${idNhanVien} | ${nvName} (${nvPhone})` : `NV-${idNhanVien} | ${nvName}`;
                        selectEditNv.insertBefore(opt, selectEditNv.children[1] || null);
                    }
                    selectEditNv.value = idNhanVien;
                }

                // Show/hide group depending on selected role
                if (vaitro === 'ADMIN' || vaitro === 'NHANVIEN') {
                    groupEdit.classList.remove('d-none');
                    selectEditNv.setAttribute('required', 'required');
                } else {
                    groupEdit.classList.add('d-none');
                    selectEditNv.removeAttribute('required');
                }
                // Handle customer selection for USER role
                const groupEditKh = document.getElementById('group_chon_khach_hang_edit');
                const selectEditKh = document.getElementById('edit_id_khachhang');
                const idKh = this.getAttribute('data-idkhachhang') || this.getAttribute('data-idkh') || '';
                // Reset customer select
                if (selectEditKh) selectEditKh.value = '';
                if (vaitro === 'USER') {
                    if (groupEditKh) {
                        groupEditKh.classList.remove('d-none');
                        selectEditKh.setAttribute('required', 'required');
                    }
                    // If there is a linked customer for this account, ensure option exists and select it
                    const idKhach = this.getAttribute('data-idkhachhang');
                    const khName = this.getAttribute('data-khname');
                    const khPhone = this.getAttribute('data-khphone') || '';
                    if (idKhach) {
                        if (!selectEditKh.querySelector(`option[value="${idKhach}"]`)) {
                            const opt = document.createElement('option');
                            opt.value = idKhach;
                            opt.text = khPhone ? `KH-${idKhach} | ${khName} (${khPhone})` : `KH-${idKhach} | ${khName}`;
                            selectEditKh.insertBefore(opt, selectEditKh.children[1] || null);
                        }
                        selectEditKh.value = idKhach;
                    }
                } else {
                    if (groupEditKh) {
                        groupEditKh.classList.add('d-none');
                        selectEditKh.removeAttribute('required');
                        if (selectEditKh) selectEditKh.value = '';
                    }
                }
            });
        });
    });
    // BỔ SUNG: Xử lý ẩn hiện ô chọn nhân viên dựa trên vai trò được chọn
document.addEventListener('DOMContentLoaded', function() {
    const selectVaiTro = document.querySelector('#modalAddAccount select[name="vai_tro"]');
    const groupChonNhanVien = document.getElementById('group_chon_nhan_vien');
    const inputNhanVien = document.getElementById('id_nhanvien');
    const groupChonKhachHang = document.getElementById('group_chon_khach_hang');
    const inputKhachHang = document.getElementById('id_khachhang');

    if (selectVaiTro) {
        selectVaiTro.addEventListener('change', function() {
            // Nếu chọn ADMIN hoặc NHANVIEN thì bắt buộc phải chọn nhân viên tương ứng
            if (this.value === 'ADMIN' || this.value === 'NHANVIEN') {
                groupChonNhanVien.classList.remove('d-none');
                inputNhanVien.setAttribute('required', 'required'); // Bắt buộc chọn
                // Hide customer group
                if (groupChonKhachHang) {
                    groupChonKhachHang.classList.add('d-none');
                    inputKhachHang.removeAttribute('required');
                    inputKhachHang.value = '';
                }
            } else {
                // Nếu chọn USER thì ẩn đi và loại bỏ thuộc tính required
                groupChonNhanVien.classList.add('d-none');
                inputNhanVien.removeAttribute('required');
                inputNhanVien.value = ""; // Reset giá trị về rỗng
                // Show customer group for USER
                if (groupChonKhachHang) {
                    groupChonKhachHang.classList.remove('d-none');
                    inputKhachHang.setAttribute('required', 'required');
                }
            }
        });
        // Trigger initial state
        selectVaiTro.dispatchEvent(new Event('change'));
    }
});
// Edit modal: toggle groups when role changed by user
document.addEventListener('DOMContentLoaded', function() {
    const editVaiTro = document.getElementById('edit_vaitro');
    const groupChonNhanVienEdit = document.getElementById('group_chon_nhan_vien_edit');
    const selectEditNv = document.getElementById('edit_id_nhanvien');
    const groupChonKhachHangEdit = document.getElementById('group_chon_khach_hang_edit');
    const selectEditKh = document.getElementById('edit_id_khachhang');

    if (editVaiTro) {
        editVaiTro.addEventListener('change', function() {
            if (this.value === 'ADMIN' || this.value === 'NHANVIEN') {
                if (groupChonNhanVienEdit) groupChonNhanVienEdit.classList.remove('d-none');
                if (selectEditNv) selectEditNv.setAttribute('required', 'required');
                if (groupChonKhachHangEdit) groupChonKhachHangEdit.classList.add('d-none');
                if (selectEditKh) { selectEditKh.removeAttribute('required'); selectEditKh.value = ''; }
            } else {
                if (groupChonNhanVienEdit) groupChonNhanVienEdit.classList.add('d-none');
                if (selectEditNv) { selectEditNv.removeAttribute('required'); selectEditNv.value = ''; }
                if (groupChonKhachHangEdit) groupChonKhachHangEdit.classList.remove('d-none');
                if (selectEditKh) selectEditKh.setAttribute('required', 'required');
            }
        });
    }
});
</script>
@endsection
