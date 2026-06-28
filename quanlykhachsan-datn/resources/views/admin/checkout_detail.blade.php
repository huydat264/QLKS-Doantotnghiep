@extends('admin.layout.master')
@section('title', 'Thanh toán & Trả phòng')
@section('page_title', 'Chi tiết thanh toán')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-wallet2 me-2"></i>Chi tiết các khoản phí</h5>
            </div>
            <div class="card-body p-4">
                <form id="form_thanh_toan" action="{{ route('admin.thanhtoan.process', $datPhong->id_datphong) }}" method="POST">
                    @csrf

                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Hạng mục</th>
                                <th class="text-end">Số tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold">Tổng tiền phòng / Combo gốc</td>
                                <td class="text-end fw-bold" id="display_tong_phong">{{ number_format($tongTienPhong, 0, ',', '.') }} đ</td>
                            </tr>
                            <tr>
                                <td class="text-success"><i class="bi bi-dash-circle me-1"></i> Trừ tiền cọc (Đã thanh toán)</td>
                                <td class="text-end text-success fw-bold" id="display_tien_coc">
                                    @if($tienCoc > 0)
                                        - {{ number_format($tienCoc, 0, ',', '.') }} đ
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-info">
                                <td class="fw-bold">Tiền phòng còn lại cần thu</td>
                                <td class="text-end fw-bold text-primary fs-5" id="display_tien_phong_conlai">{{ number_format($tienPhongConLai, 0, ',', '.') }} đ</td>
                            </tr>
                            <tr>
                                <td class="text-danger"><i class="bi bi-dash-circle me-1"></i> Dịch vụ sử dụng thêm</td>
                                <td class="text-end text-danger fw-bold" id="display_dich_vu_add">+ {{ number_format($tongTienDichVu, 0, ',', '.') }} đ</td>
                            </tr>
                            <tr>
                                <td class="text-warning"><i class="bi bi-exclamation-triangle-fill me-1"></i> Phụ phí / Bồi thường</td>
                                <td class="text-end text-warning fw-bold" id="display_phuphi">+ 0 đ</td>
                            </tr>
                            <tr class="table-warning">
                                <td class="fw-bold"><i class="bi bi-shield-lock-fill me-1"></i> Trừ tiền tạm ứng đã chọn</td>
                                <td class="text-end fw-bold text-warning" id="display_tam_ung_deduct">- 0 đ</td>
                            </tr>
                            <tr class="table-success">
                                <td class="fw-bold fs-5">💰 TỔNG CẦN THANH TOÁN</td>
                                <td class="text-end fw-bold text-success fs-5" id="display_total_invoice">{{ number_format($tienPhongConLai + $tongTienDichVu, 0, ',', '.') }} đ</td>
                            </tr>
                        </tbody>
                    </table>

                    <h6 class="fw-bold mt-4 mb-3">Dịch vụ sử dụng thêm</h6>
                    @if($dichVus->count() > 0)
                        <table class="table table-sm table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Tên dịch vụ</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dichVus as $dv)
                                <tr>
                                    <td>{{ $dv->dichvu->ten_dich_vu ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $dv->so_luong }}</td>
                                    <td class="text-end">{{ number_format($dv->dichvu->gia ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($dv->so_luong * ($dv->dichvu->gia ?? 0), 0, ',', '.') }} đ</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Tổng tiền dịch vụ:</td>
                                    <td class="text-end fw-bold text-danger" id="display_tong_dich_vu">{{ number_format($tongTienDichVu, 0, ',', '.') }} đ</td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <div class="text-muted small italic mb-3">Khách không sử dụng dịch vụ phát sinh.</div>
                    @endif

                    <hr class="my-4">

                    <div class="row g-3 bg-light p-3 rounded-3 border mb-4">
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold text-warning"><i class="bi bi-shield-lock-fill me-1"></i> Khấu trừ Tiền Tạm Ứng</h6>
                            <div class="form-check form-switch mb-2 mt-3">
                                <input class="form-check-input" type="checkbox" id="toggle_tam_ung">
                                <label class="form-check-label fw-semibold" for="toggle_tam_ung">Mở khóa chỉnh sửa tạm ứng</label>
                            </div>
                            <input type="number" id="tien_tam_ung" name="tien_tam_ung" class="form-control font-monospace text-warning fw-bold" value="{{ $tienTamUngCo ?? 0 }}" min="0" readonly>
                            <small class="text-muted d-block mt-1">Khóa giá trị tạm ứng để giữ khi đóng/mở form, vẫn được tính vào hóa đơn nếu quá hạn.</small>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Phụ phí / Bồi thường</h6>
                            <label class="form-label fw-semibold mt-2 mb-1">Số tiền thu thêm (VNĐ)</label>
                            <input type="number" id="tien_boi_thuong" name="tien_boi_thuong" class="form-control font-monospace text-danger fw-bold mb-2" value="0" min="0">
                            <input type="text" name="ghi_chu_boi_thuong" class="form-control" placeholder="Lý do: Đền bù vỡ cốc, hỏng rèm...">
                        </div>
                    </div>

                    <div class="row mt-4 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hình thức thanh toán</label>
                            <select name="hinh_thuc" id="hinh_thuc_select" class="form-select border-primary" required>
                                <option value="Tiền mặt">Tiền mặt</option>
                                <option value="VNPay">Cổng VNPay</option>
                            </select>
                        </div>
                        <div class="col-md-6 text-end">
                            <p class="mb-1 text-muted fw-bold">TỔNG SỐ TIỀN KHÁCH CẦN THANH TOÁN</p>
                            <h2 class="text-success fw-bold mb-0" id="tong_thanh_toan_hienthi">{{ number_format($tienPhongConLai + $tongTienDichVu, 0, ',', '.') }} đ</h2>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="javascript:history.back()" class="btn btn-secondary px-4 me-2">Quay lại</a>
                        <button type="button" class="btn btn-success px-5 fw-bold fs-5 shadow-sm" id="btn_mo_modal">
                            <i class="bi bi-check-circle me-2"></i> XÁC NHẬN THANH TOÁN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 border-bottom border-light pb-2">Thông tin Đặt Phòng</h5>
                <p class="mb-2"><i class="bi bi-person me-2"></i> <strong>Khách hàng:</strong> {{ $datPhong->ho_ten }}</p>
                <p class="mb-2"><i class="bi bi-telephone me-2"></i> <strong>SĐT:</strong> {{ $datPhong->so_dien_thoai }}</p>
                <p class="mb-2"><i class="bi bi-door-open me-2"></i> <strong>Phòng:</strong> {{ $datPhong->ten_phong }}</p>
                <p class="mb-2"><i class="bi bi-calendar-check me-2"></i> <strong>Check-in:</strong> {{ \Carbon\Carbon::parse($datPhong->ngay_nhan)->format('d/m/Y') }}</p>
                <p class="mb-0"><i class="bi bi-calendar-x me-2"></i> <strong>Check-out:</strong> {{ \Carbon\Carbon::parse($datPhong->ngay_tra)->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalThanhToanCash" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-success text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-cash-coin me-2"></i>Xác Nhận Thu Tiền Mặt</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-info-circle text-primary" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold mb-3 text-dark">BẠN ĐÃ NHẬN ĐỦ SỐ TIỀN?</h5>
                <div class="bg-light p-3 rounded-3 d-inline-block border mb-3">
                    <span class="fs-2 fw-bold text-success" id="hien_thi_tien_modal">0 đ</span>
                </div>
                <p class="text-muted small mb-0">Hệ thống sẽ lập tức xuất hóa đơn và chuyển phòng sang trạng thái "Trống".</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="button" class="btn btn-success px-5 fw-bold" onclick="document.getElementById('form_thanh_toan').submit();">
                    Đồng ý, Lập hóa đơn
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tienPhongConLai = {{ $tienPhongConLai }};
        const tongTienDichVu = {{ $tongTienDichVu }};

        const inputBoiThuong = document.getElementById('tien_boi_thuong');
        const inputTamUng = document.getElementById('tien_tam_ung');
        const toggleTamUng = document.getElementById('toggle_tam_ung');
        const displayTotal = document.getElementById('tong_thanh_toan_hienthi');
        let savedTamUng = parseFloat(inputTamUng.value) || 0;

        function updateTotal() {
            let boiThuong = parseFloat(inputBoiThuong.value) || 0;
            let tamUng = parseFloat(inputTamUng.value) || 0;
            let tong = tienPhongConLai + tongTienDichVu + boiThuong - tamUng;

            if (tong < 0) tong = 0; // Tránh âm tiền

            // Cập nhập UI table
            document.getElementById('display_phuphi').innerText = '+ ' + new Intl.NumberFormat('vi-VN').format(boiThuong) + ' đ';
            document.getElementById('display_tam_ung_deduct').innerText = tamUng > 0 ? '- ' + new Intl.NumberFormat('vi-VN').format(tamUng) + ' đ' : '- 0 đ';
            document.getElementById('display_total_invoice').innerText = new Intl.NumberFormat('vi-VN').format(tong) + ' đ';

            // Cập nhập tổng thanh toán ở phải
            displayTotal.innerText = new Intl.NumberFormat('vi-VN').format(tong) + ' đ';
            return tong;
        }

        inputBoiThuong.addEventListener('input', updateTotal);
        inputTamUng.addEventListener('input', function() {
            savedTamUng = parseFloat(inputTamUng.value) || 0;
            updateTotal();
        });

        // Khóa / Mở khóa tiền tạm ứng
        toggleTamUng.addEventListener('change', function() {
            if (this.checked) {
                inputTamUng.removeAttribute('readonly');
                inputTamUng.classList.remove('bg-light');
                inputTamUng.focus();
            } else {
                inputTamUng.setAttribute('readonly', 'readonly');
                inputTamUng.classList.add('bg-light');
                inputTamUng.value = savedTamUng;
                // Lưu giá trị tạm ứng vào DB
                saveTamUngToDB();
            }
            updateTotal();
        });

const saveTamUngUrl = '{{ route('admin.thanhtoan.save_tam_ung', $datPhong->id_datphong) }}';

        function saveTamUngToDB() {
            const tamUngValue = parseFloat(inputTamUng.value) || 0;

            console.log('📝 Lưu tạm ứng:', { saveTamUngUrl, tamUngValue });

            fetch(saveTamUngUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ tien_tam_ung_new: tamUngValue })
            })
            .then(response => {
                console.log('📦 Response status:', response.status);
                const contentType = response.headers.get('content-type') || '';
                console.log('📦 Response content-type:', contentType);
                if (!contentType.includes('application/json')) {
                    return response.text().then(text => { throw new Error('Invalid JSON response: ' + text); });
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Response data:', data);
                if (data.success) {
                    console.log('🎉 Lưu tạm ứng thành công');
                    const toast = document.createElement('div');
                    toast.className = 'alert alert-success position-fixed bottom-0 end-0 m-3';
                    toast.innerHTML = '✓ Đã lưu giá trị tạm ứng thành công';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                } else {
                    console.error('❌ Lỗi lưu tạm ứng:', data.error);
                    alert('Lỗi: ' + data.error);
                }
            })
            .catch(error => {
                console.error('❌ Lỗi AJAX:', error);
                alert('Lỗi kết nối: ' + error.message);
            });
        }

        // Lưu khi thay đổi giá trị (debounce)
        let saveTamUngTimeout;
        inputTamUng.addEventListener('input', function() {
            clearTimeout(saveTamUngTimeout);
            saveTamUngTimeout = setTimeout(() => {
                if (toggleTamUng.checked) {
                    saveTamUngToDB();
                    savedTamUng = parseFloat(inputTamUng.value) || 0;
                }
            }, 1500);
        });

        // Khởi tạo hiển thị ngay khi trang load nếu có tiền tạm ứng trước đó
        updateTotal();

        // Xử lý nút bấm thanh toán (Popup cho tiền mặt, Direct cho VNPay)
        document.getElementById('btn_mo_modal').addEventListener('click', function() {
            let hinhThuc = document.getElementById('hinh_thuc_select').value;
            if(hinhThuc === 'VNPay') {
                // Submit thẳng sang VNPay
                document.getElementById('form_thanh_toan').submit();
            } else {
                // Mở Modal tiền mặt
                document.getElementById('hien_thi_tien_modal').innerText = document.getElementById('tong_thanh_toan_hienthi').innerText;
                var myModal = new bootstrap.Modal(document.getElementById('modalThanhToanCash'));
                myModal.show();
            }
        });
    });
</script>
@endsection
