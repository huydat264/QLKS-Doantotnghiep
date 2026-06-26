@extends('admin.layout.master')
@section('title', 'Báo cáo thống kê tổng lực')
@section('page_title', 'Trung Tâm Điều Hành Doanh Số & Hành Vi Khách Hàng')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div id="baocaoPrintable">
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
    <div class="row align-items-center g-3">
        <div class="col-md-7">
            <form action="{{ route('admin.baocao.index') }}" method="GET" class="row g-2 align-items-center mb-0">
                <div class="col-auto">
                    <span class="fw-bold text-secondary"><i class="bi bi-funnel-fill text-primary"></i> Lọc thống kê:</span>
                </div>
                <div class="col-auto">
                    <select name="kieu_loc" id="kieu_loc" class="form-select border-2 shadow-sm fw-semibold" onchange="doiLoaiInput()">
                        <option value="nam" {{ $kieuLoc == 'nam' ? 'selected' : '' }}>Theo Năm</option>
                        <option value="thang" {{ $kieuLoc == 'thang' ? 'selected' : '' }}>Theo Tháng</option>
                        <option value="quy" {{ $kieuLoc == 'quy' ? 'selected' : '' }}>Theo Quý</option>
                        <option value="ngay" {{ $kieuLoc == 'ngay' ? 'selected' : '' }}>Theo Ngày</option>
                    </select>
                </div>
                <div class="col-auto" id="vung_input_loc">
                    <input type="text" name="gia_tri_loc" id="gia_tri_loc" value="{{ $giaTriLoc }}" class="form-control border-2 shadow-sm">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary fw-bold px-3 shadow-sm"><i class="bi bi-search"></i> Tải Thống Kê</button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.baocao.index') }}" class="btn btn-outline-secondary fw-bold px-3 shadow-sm"><i class="bi bi-arrow-clockwise"></i> Đặt lại</a>
                </div>
            </form>
        </div>
        <div class="col-md-5 text-md-end">
            <div class="btn-group shadow-sm me-2" role="group">
                <button type="button" class="btn btn-outline-success fw-bold" title="Xuất Excel" onclick="xuatExcel()">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Excel
                </button>
                <button type="button" class="btn btn-outline-secondary fw-bold" title="In báo cáo" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> In
                </button>
            </div>
            <button class="btn btn-outline-dark fw-bold shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#modalSoSanh">
                <i class="bi bi-arrow-left-right text-danger me-1"></i> So Sánh
            </button>
            <button class="btn btn-warning fw-bold text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDuBao">
                <i class="bi bi-stars me-1"></i> Dự Báo
            </button>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm border-start border-primary border-4 rounded-3 h-100">
            <h6 class="text-muted mb-2 fw-bold text-uppercase small">Doanh thu kỳ lọc</h6>
            <h4 class="fw-bold mb-0 text-dark">{{ number_format($tongDoanhThuKy, 0, ',', '.') }} đ</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm border-start border-success border-4 rounded-3 h-100">
            <h6 class="text-muted mb-2 fw-bold text-uppercase small">Lượt đặt phòng kỳ lọc</h6>
            <h4 class="fw-bold mb-0 text-dark">{{ $tongDonKy }} đơn</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm border-start border-warning border-4 rounded-3 h-100">
            <h6 class="text-muted mb-2 fw-bold text-uppercase small">Phòng đang thuê (Realtime)</h6>
            <h4 class="fw-bold mb-0 text-dark">{{ $phongDangThue }} / {{ $tongPhong }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-0 shadow-sm border-start border-info border-4 rounded-3 h-100">
            <h6 class="text-muted mb-2 fw-bold text-uppercase small">Nhân sự (Chức vụ)</h6>
            <h4 class="fw-bold mb-0 text-dark">{{ $tongNhanVien }} / {{ $soChucVu }}</h4>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold text-dark mb-1">Biến Động Dòng Tiền Doanh Thu & Lợi Nhuận Ròng</h5>
            <small class="text-muted mb-4 d-block">Phân tích chuyên sâu tự động chia lưới theo kỳ lọc: {{ $giaTriLoc }}</small>
            <div style="height: 380px; position: relative;">
                <canvas id="chartDoanhThu"></canvas>
            </div>
            <div class="mt-3 small text-muted fst-italic">
                <span class="me-2"><strong>Lợi nhuận ròng</strong> = Doanh thu thô - Lương nhân viên - Giá vốn dịch vụ - Chi phí vận hành (10% doanh thu)</span>
                <span class="badge bg-secondary">Di chuột lên cột lợi nhuận để xem chi tiết</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold text-dark mb-4 text-center">Tỉ Lệ Lấp Đầy Phòng (Trong Kỳ)</h5>
            <div style="height: 300px;"><canvas id="chartLapDay"></canvas></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold text-dark mb-4 text-center">Phân Khúc Tiêu Dùng Khách Hàng</h5>
            <div style="height: 300px;"><canvas id="chartPhanKhucKhach"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold text-dark mb-4 text-center">Hành Vi Khách Quay Lại</h5>
            <div style="height: 250px;"><canvas id="chartKhachQuayLai"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold text-dark mb-4 text-center">Top Phòng Đắt Khách</h5>
            <div style="height: 250px;"><canvas id="chartTanSuatPhong"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold text-dark mb-4 text-center">Doanh Thu Theo Dịch Vụ</h5>
            <div style="height: 250px;"><canvas id="chartDoanhThuDichVu"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold text-dark mb-4 text-center">Tần Suất Sử Dụng Dịch Vụ</h5>
            <div style="height: 250px;"><canvas id="chartTanSuatDichVu"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold text-dark mb-4 text-center">Thống Kê Khách Hàng</h5>
            <small class="text-center text-muted d-block mb-3">(Theo {{ $kieuLoc }}: {{ $giaTriLoc }})</small>
            <div style="height: 250px;"><canvas id="chartKhachHangTrend"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold text-dark mb-4 text-center">Chi Phí Lương Nhân Viên</h5>
            <small class="text-center text-muted d-block mb-3">(Theo {{ $kieuLoc }}: {{ $giaTriLoc }})</small>
            <div style="height: 250px;"><canvas id="chartLuongTrend"></canvas></div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalSoSanh" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-left-right me-2 text-danger"></i>Cổng Phân Tích & So Sánh Đối Chiếu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formExecuteSoSanh" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Hạng mục so sánh</label>
                        <select id="comp_loai" class="form-select border-2" required>
                            <option value="doanh_thu">Doanh thu kinh doanh tổng hợp (đ)</option>
                            <option value="phong">Tần suất sử dụng phòng (Lượt Check-in)</option>
                            <option value="dich_vu">Tần suất tiêu dùng dịch vụ (Lượt Order)</option>
                            <option value="khach_hang">Lượng khách hàng giao dịch (Người)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Cấp độ thời gian</label>
                        <select id="comp_kieu" class="form-select border-2" required>
                            <option value="ngay">So sánh Ngày cụ thể</option>
                            <option value="thang">So sánh các Tháng</option>
                            <option value="quy">So sánh theo Quý tài chính</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-primary fw-semibold" id="lbl_moc1">Mốc thời gian số 1</label>
                        <div id="vung_nhap_1"><input type="date" id="val1_date" class="form-control" required value="{{ date('Y-m-d') }}"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-danger fw-semibold" id="lbl_moc2">Mốc thời gian số 2</label>
                        <div id="vung_nhap_2"><input type="date" id="val2_date" class="form-control" required value="{{ date('Y-m-d') }}"></div>
                    </div>
                    <div class="col-12 text-end mt-3">
                        <button type="submit" class="btn btn-success fw-bold px-4"><i class="bi bi-play-fill"></i> Tiến Hành</button>
                    </div>
                </form>

                <div id="ketQuaSoSanhVung" class="mt-4 pt-3 border-top d-none">
                    <div class="row text-center g-2 mb-3">
                        <div class="col-4 bg-light p-2 rounded"><span class="small text-muted" id="res_label1">Mốc 1</span><h5 class="fw-bold text-primary mt-1" id="res_val1">0</h5></div>
                        <div class="col-4 bg-light p-2 rounded"><span class="small text-muted" id="res_label2">Mốc 2</span><h5 class="fw-bold text-danger mt-1" id="res_val2">0</h5></div>
                        <div class="col-4 p-2 rounded" id="res_color_box"><span class="small text-dark">Biến động</span><h5 class="fw-bold mt-1" id="res_val_diff">0</h5></div>
                    </div>
                    <div style="height: 180px;"><canvas id="chartKetQuaSoSanhLive"></canvas></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDuBao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg bg-dark text-white">
            <div class="modal-header border-0 py-3">
                <h5 class="modal-title fw-bold text-warning"><i class="bi bi-stars me-2"></i>Mô Hình Dự Báo Tương Lai</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-black bg-opacity-25 rounded-bottom-4">
                <label class="form-label fw-bold text-white-50">Chọn tháng/năm bạn muốn dự báo (Tương lai):</label>
                <div class="input-group mb-4">
                    <input type="month" id="target_dubao" class="form-control fw-bold border-0 fs-5 text-center" min="{{ date('Y-m') }}" required>
                    <button class="btn btn-warning fw-bold text-dark px-4" type="button" onclick="chayDuBao()">
                        <i class="bi bi-lightning-fill"></i> Phân Tích
                    </button>
                </div>
                <div id="vung_ket_qua_dubao" class="text-center p-3 border border-secondary rounded-3 d-none bg-dark">
                    <span class="text-white-50 small d-block mb-1" id="res_dubao_label"></span>
                    <h3 class="text-success fw-bold mb-2" id="res_dubao_value"></h3>
                    <div id="vung_du_bao_chi_tiet" class="row gx-2 gy-1 justify-content-center mb-2 d-none">
                        <div class="col-6 small text-white-50"><strong>Occupancy Rate-Tỷ lệ lấp đầy:</strong> <span id="res_dubao_occu"></span></div>
                        <div class="col-6 small text-white-50"><strong>ADR-Giá trung bình/đêm:</strong> <span id="res_dubao_adr"></span></div>
                        <div class="col-6 small text-white-50"><strong>RevPAR-Doanh thu trên mỗi phòng có sẵn:</strong> <span id="res_dubao_revpar"></span></div>
                        <div class="col-6 small text-white-50"><strong>Doanh thu phòng:</strong> <span id="res_dubao_revenue"></span></div>
                    </div>
                    <small class="text-white-50 fst-italic" id="res_dubao_note"></small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const danhSachPhongCoKhach = {!! json_encode($phongCoKhachList) !!};
    const danhSachPhongTrong = {!! json_encode($phongTrongList) !!};

    function doiLoaiInput() {
        let kieu = document.getElementById('kieu_loc').value;
        let vung = document.getElementById('vung_input_loc');
        let currentVal = '{{ $giaTriLoc }}';

        if (kieu === 'nam') {
            vung.innerHTML = `<input id="gia_tri_loc" type="number" name="gia_tri_loc" class="form-control border-2 shadow-sm" value="${currentVal}">`;
        } else if (kieu === 'thang') {
            vung.innerHTML = `<input id="gia_tri_loc" type="month" name="gia_tri_loc" class="form-control border-2 shadow-sm" value="${currentVal}">`;
        } else if (kieu === 'ngay') {
            vung.innerHTML = `<input id="gia_tri_loc" type="date" name="gia_tri_loc" class="form-control border-2 shadow-sm" value="${currentVal}">`;
        } else {
            vung.innerHTML = `<select id="gia_tri_loc" name="gia_tri_loc" class="form-select border-2 shadow-sm">
                                <option value="2026-Q1" ${currentVal === '2026-Q1' ? 'selected' : ''}>2026 - Quý 1</option>
                                <option value="2026-Q2" ${currentVal === '2026-Q2' ? 'selected' : ''}>2026 - Quý 2</option>
                                <option value="2026-Q3" ${currentVal === '2026-Q3' ? 'selected' : ''}>2026 - Quý 3</option>
                                <option value="2026-Q4" ${currentVal === '2026-Q4' ? 'selected' : ''}>2026 - Quý 4</option>
                              </select>`;
        }
    }
    document.addEventListener('DOMContentLoaded', doiLoaiInput);

    document.addEventListener('DOMContentLoaded', function () {

        Chart.defaults.scale.grid.display = false; // Tắt toàn bộ lưới kẻ

        // 1. Dòng Tiền & Khấu Hao
        const d_doanhThuGoc = {!! json_encode($doanhThuData->pluck('doanh_thu_goc')) !!};
        const d_doanhThuRong = {!! json_encode($doanhThuData->pluck('doanh_thu_rong')) !!};
        const d_chiPhiLuong = {!! json_encode($doanhThuData->pluck('chi_phi_luong')) !!};
        const d_chiPhiDichVu = {!! json_encode($doanhThuData->pluck('chi_phi_dich_vu')) !!};
        const d_chiPhiVanHanh = {!! json_encode($doanhThuData->pluck('chi_phi_van_hanh')) !!};

        const bgColorsRong = d_doanhThuRong.map(value => value < 0 ? '#ef4444' : '#10b981');
        const safeNumber = value => {
            if (value === null || value === undefined || value === '') return 0;
            const num = Number(value);
            return Number.isNaN(num) ? 0 : num;
        };
        const d_tongChiPhi = d_chiPhiLuong.map((value, index) => safeNumber(value) + safeNumber(d_chiPhiDichVu[index]) + safeNumber(d_chiPhiVanHanh[index]));
        const formatMoney = value => safeNumber(value).toLocaleString('vi-VN');
        const percentLabelPlugin = {
            id: 'percentLabelPlugin',
            afterDatasetDraw(chart) {
                if (chart.config.type !== 'doughnut' && chart.config.type !== 'pie') return;
                const dataset = chart.data.datasets[0];
                const meta = chart.getDatasetMeta(0);
                const total = dataset.data.reduce((sum, item) => sum + safeNumber(item), 0);
                const ctx = chart.ctx;
                ctx.save();
                ctx.font = '700 12px Inter, system-ui';
                ctx.fillStyle = '#ffffff';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';

                if (!total) {
                    const x = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                    const y = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                    ctx.fillText('Không có dữ liệu', x, y);
                    ctx.restore();
                    return;
                }

                meta.data.forEach((element, index) => {
                    const value = safeNumber(dataset.data[index]);
                    if (!value) return;
                    const percent = Math.round((value / total) * 100);
                    const center = element.getCenterPoint ? element.getCenterPoint() : { x: element.x, y: element.y };
                    ctx.fillText(`${percent}%`, center.x, center.y);
                });

                ctx.restore();
            }
        };

        new Chart(document.getElementById('chartDoanhThu'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($doanhThuData->pluck('thoi_gian')) !!},
                datasets: [
                    {
                        label: 'Doanh Thu Thô',
                        data: d_doanhThuGoc,
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        maxBarThickness: 45
                    },
                    {
                        label: 'Lợi Nhuận Ròng (Thực lãi/Lỗ)',
                        data: d_doanhThuRong,
                        backgroundColor: bgColorsRong,
                        borderRadius: 6,
                        maxBarThickness: 45
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed && context.parsed.y !== undefined ? context.parsed.y : context.raw;
                                if (context.dataset.label === 'Lợi Nhuận Ròng (Thực lãi/Lỗ)') {
                                    const idx = context.dataIndex;
                                    const totalCost = Number(d_tongChiPhi[idx] || 0);
                                    return [
                                        `Lợi nhuận: ${formatMoney(value)} đ`,
                                        `- Lương NV: ${formatMoney(d_chiPhiLuong[idx] || 0)} đ`,
                                        `- Giá vốn DV: ${formatMoney(d_chiPhiDichVu[idx] || 0)} đ`,
                                        `- Vận hành: ${formatMoney(d_chiPhiVanHanh[idx] || 0)} đ`,
                                        `Tổng chi phí khấu hao: ${formatMoney(totalCost)} đ`
                                    ];
                                }
                                return `${context.dataset.label}: ${formatMoney(value)} đ`;
                            },
                            footer: function(context) {
                                if (context[0] && context[0].dataset.label === 'Lợi Nhuận Ròng (Thực lãi/Lỗ)') {
                                    return 'Tổng chi phí khấu hao = Lương NV + Giá vốn DV + Chi phí vận hành';
                                }
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: { callback: value => value.toLocaleString('vi-VN') + ' đ' }
                    }
                }
            }
        });

        // 2. Lấp Đầy
        new Chart(document.getElementById('chartLapDay'), {
            type: 'doughnut',
            data: {
                labels: ['Đang sử dụng', 'Phòng trống'],
                datasets: [{
                    data: [{{ $phongLopDay }}, {{ $phongTrongTheoKy }}],
                    backgroundColor: ['#10b981', '#cbd5e1'],
                    borderWidth: 2
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '50%' },
            plugins: [percentLabelPlugin]
        });

        // 3. Phân Khúc Khách Hàng
        new Chart(document.getElementById('chartPhanKhucKhach'), {
            type: 'doughnut',
            data: {
                labels: ['Khách VIP', 'Khách Thường', 'Khách Ít Quay Lại'],
                datasets: [{
                    data: [{{ $khachVIP }}, {{ $khachThuong }}, {{ $khachItQuayLai }}],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#ef4444'],
                    borderWidth: 2
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '50%' },
            plugins: [percentLabelPlugin]
        });

        // 4. Khách Quay Lại
        new Chart(document.getElementById('chartKhachQuayLai'), {
            type: 'pie',
            data: {
                labels: ['Quay Lại', 'Vãng Lai'],
                datasets: [{
                    data: [{{ $khachQuayLai }}, {{ $khachMotLan }}],
                    backgroundColor: ['#8b5cf6', '#fcd34d'],
                    borderWidth: 2
                }]
            },
            options: { responsive: true, maintainAspectRatio: false },
            plugins: [percentLabelPlugin]
        });

        // 5. Tần Suất Phòng
        new Chart(document.getElementById('chartTanSuatPhong'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($tanSuatPhong->pluck('so_phong')) !!},
                datasets: [{
                    label: 'Lượt khách',
                    data: {!! json_encode($tanSuatPhong->pluck('so_lan_dat')) !!},
                    backgroundColor: '#14b8a6',
                    maxBarThickness: 30, borderRadius: 4
                }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });

        // 6. Tần Suất Dịch Vụ
        new Chart(document.getElementById('chartTanSuatDichVu'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($tanSuatDichVu->pluck('ten_dich_vu')) !!},
                datasets: [{
                    label: 'Số lượng sử dụng',
                    data: {!! json_encode($tanSuatDichVu->pluck('tong_so_luong')) !!},
                    backgroundColor: '#f43f5e',
                    maxBarThickness: 30, borderRadius: 4
                }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });

        // 6.1 Doanh Thu Theo Dịch Vụ
        new Chart(document.getElementById('chartDoanhThuDichVu'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($doanhThuDichVu->pluck('ten_dich_vu')) !!},
                datasets: [{
                    data: {!! json_encode($doanhThuDichVu->pluck('doanh_thu')) !!},
                    backgroundColor: ['#f97316', '#22c55e', '#0ea5e9', '#e11d48', '#8b5cf6', '#facc15'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed || 0;
                                return `${context.label}: ${value.toLocaleString('vi-VN')} đ`;
                            }
                        }
                    }
                }
            },
            plugins: [percentLabelPlugin]
        });

        // ================= KHU VỰC THÊM MỚI SCRIPT 2 BIỂU ĐỒ =================

        // 7. Biểu đồ thống kê biến động khách hàng
        const d_tongKhach = {!! json_encode($doanhThuData->pluck('tong_khach')) !!};
        const d_khachMoi = {!! json_encode($doanhThuData->pluck('khach_moi')) !!};
        new Chart(document.getElementById('chartKhachHangTrend'), {
            type: 'line',
            data: {
                labels: {!! json_encode($doanhThuData->pluck('thoi_gian')) !!},
                datasets: [
                    {
                        label: 'Tổng số khách',
                        data: d_tongKhach,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Khách hàng mới',
                        data: d_khachMoi,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // 8. Biểu đồ thống kê biến động Chi Phí Lương (Ăn theo kỳ lọc Tháng/Quý/Năm)
        new Chart(document.getElementById('chartLuongTrend'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($doanhThuData->pluck('thoi_gian')) !!},
                datasets: [{
                    label: 'Chi phí lương trả cho nhân viên',
                    data: d_chiPhiLuong,
                    backgroundColor: '#8b5cf6',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: { callback: value => value.toLocaleString('vi-VN') + ' đ' }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => `Chi phí: ${safeNumber(context.raw).toLocaleString('vi-VN')} đ`
                        }
                    }
                }
            }
        });

    });

    function chayDuBao() {
        let target = document.getElementById('target_dubao').value;
        if(!target) {
            alert('Vui lòng chọn khoảng thời gian trên lịch để phân tích!');
            return;
        }

        let btn = document.querySelector('button[onclick="chayDuBao()"]');
        let txtCu = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(`{{ route('admin.baocao.forecast') }}?target=${target}`)
            .then(res => res.json())
            .then(data => {
                // Check nếu backend ném ra lỗi (quá khứ)
                if(data.error) {
                    alert(data.error);
                    btn.innerHTML = txtCu;
                    return;
                }

                document.getElementById('vung_ket_qua_dubao').classList.remove('d-none');
                document.getElementById('res_dubao_label').innerText = "KẾT QUẢ DỰ BÁO CHO " + data.label;
                document.getElementById('res_dubao_value').innerText = data.value;
                document.getElementById('res_dubao_note').innerText = data.note;

                if (data.occupancy_rate && data.adr && data.revpar && data.forecast_revenue) {
                    document.getElementById('res_dubao_occu').innerText = data.occupancy_rate;
                    document.getElementById('res_dubao_adr').innerText = data.adr;
                    document.getElementById('res_dubao_revpar').innerText = data.revpar;
                    document.getElementById('res_dubao_revenue').innerText = data.forecast_revenue;
                    document.getElementById('vung_du_bao_chi_tiet').classList.remove('d-none');
                } else {
                    document.getElementById('vung_du_bao_chi_tiet').classList.add('d-none');
                }

                btn.innerHTML = txtCu;
            })
            .catch(err => {
                alert("Có lỗi xảy ra trong quá trình tính toán!");
                btn.innerHTML = txtCu;
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const compKieu = document.getElementById('comp_kieu');
        const vungNhap1 = document.getElementById('vung_nhap_1');
        const vungNhap2 = document.getElementById('vung_nhap_2');
        let chartCompareInstance = null;

        compKieu.addEventListener('change', function() {
            if (this.value === 'ngay') {
                vungNhap1.innerHTML = `<input type="date" id="val1_input" class="form-control" required>`;
                vungNhap2.innerHTML = `<input type="date" id="val2_input" class="form-control" required>`;
            } else if (this.value === 'thang') {
                vungNhap1.innerHTML = `<input type="month" id="val1_input" class="form-control" required>`;
                vungNhap2.innerHTML = `<input type="month" id="val2_input" class="form-control" required>`;
            } else {
                vungNhap1.innerHTML = `<select id="val1_input" class="form-select" required><option value="2026-Q1">2026 - Q1</option><option value="2026-Q2">2026 - Q2</option></select>`;
                vungNhap2.innerHTML = `<select id="val2_input" class="form-select" required><option value="2026-Q1">2026 - Q1</option><option value="2026-Q2" selected>2026 - Q2</option></select>`;
            }
        });

        document.getElementById('formExecuteSoSanh').addEventListener('submit', function (e) {
            e.preventDefault();
            let loai = document.getElementById('comp_loai').value;
            let kieu = compKieu.value;
            let val1 = document.getElementById('val1_input') ? document.getElementById('val1_input').value : document.getElementById('val1_date').value;
            let val2 = document.getElementById('val2_input') ? document.getElementById('val2_input').value : document.getElementById('val2_date').value;

            fetch(`{{ route('admin.baocao.comparison') }}?loai=${loai}&kieu=${kieu}&val1=${val1}&val2=${val2}`)
                .then(r => r.json())
                .then(res => {
                    document.getElementById('ketQuaSoSanhVung').classList.remove('d-none');
                    document.getElementById('res_label1').innerText = res.moc_1.nhan;
                    document.getElementById('res_label2').innerText = res.moc_2.nhan;
                    document.getElementById('res_val1').innerText = res.moc_1.chi_tiet;
                    document.getElementById('res_val2').innerText = res.moc_2.chi_tiet;
                    document.getElementById('res_val_diff').innerText = (res.chenh_lech >= 0 ? "+" : "") + res.chenh_lech.toLocaleString() + ` (${res.phan_tram}%)`;
                    document.getElementById('res_color_box').className = "col-4 p-2 rounded " + (res.chenh_lech >= 0 ? "bg-success-subtle text-success" : "bg-danger-subtle text-danger");

                    if (chartCompareInstance) chartCompareInstance.destroy();
                    chartCompareInstance = new Chart(document.getElementById('chartKetQuaSoSanhLive'), {
                        type: 'bar',
                        data: {
                            labels: [res.moc_1.nhan, res.moc_2.nhan],
                            datasets: [{
                                data: [res.moc_1.gia_tri, res.moc_2.gia_tri],
                                backgroundColor: ['#0284c7', '#f43f5e'],
                                maxBarThickness: 50, borderRadius: 4
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { grid: { display: false } } } }
                    });
                });
        });
    });

    // ================= HÀM XUẤT EXCEL =================
    function xuatExcel() {
        const kieuLoc = document.getElementById('kieu_loc').value;
        const giaTriLoc = document.getElementById('gia_tri_loc').value;
        const url = `{{ route('admin.baocao.export-excel') }}?kieu_loc=${kieuLoc}&gia_tri_loc=${giaTriLoc}`;

        // Dùng Fetch để tải file với blob handling
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.blob();
            })
            .then(blob => {
                // Tạo URL tạm từ blob
                const blobUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = blobUrl;
                a.download = `BaoCao_${new Date().toISOString().slice(0, 10)}_${new Date().getHours()}-${new Date().getMinutes()}.csv`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(blobUrl);
                document.body.removeChild(a);
            })
            .catch(error => {
                console.error('Lỗi xuất Excel:', error);
                alert('Lỗi khi xuất file Excel: ' + error.message);
            });
    }

    // ================= CSS CHO IN TẬP TRUNG =================
    const styleIn = `
        @media print {
            body { background: white; }
            .btn, .modal, .btn-group, button { display: none !important; }
            .card { page-break-inside: avoid; box-shadow: none; border: 1px solid #ccc; }
            canvas { max-height: 200px !important; page-break-inside: avoid; }
            h5, h6 { page-break-after: avoid; }
            .row { page-break-inside: avoid; }
            @page { margin: 1cm; size: A4; }
        }
    `;
    const styleElem = document.createElement('style');
    styleElem.textContent = styleIn;
    document.head.appendChild(styleElem);
</script>
@endsection
