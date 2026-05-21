@extends('admin.layout.master')
@section('title', 'Dashboard')
@section('page_title', 'Tổng quan hệ thống')

@section('content')
<style>
    .card-custom {
        background: #ffffff;
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease;
    }
    .card-custom:hover {
        transform: translateY(-2px);
    }
    .room-box {
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        text-align: center;
        padding: 15px 10px;
    }
    .room-trong { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .room-da-dat { background-color: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }
    .scroll-box {
        max-height: 340px;
        overflow-y: auto;
    }
    .scroll-box::-webkit-scrollbar { width: 4px; }
    .scroll-box::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>

<div class="row g-3 mb-4">
    <div class="col-md-2 col-sm-6">
        <div class="card card-custom p-3 border-primary border-start border-4">
            <h6 class="text-muted mb-2 small text-uppercase fw-bold"><i class="bi bi-door-closed"></i> Tổng phòng</h6>
            <h3 class="mb-0 fw-bold text-dark">{{ $tongPhong }}</h3>
        </div>
    </div>
    <div class="col-md-2 col-sm-6">
        <div class="card card-custom p-3 border-success border-start border-4">
            <h6 class="text-muted mb-2 small text-uppercase fw-bold"><i class="bi bi-check-circle"></i> Đang thuê</h6>
            <h3 class="mb-0 fw-bold text-dark">{{ $phongDangThue }}</h3>
        </div>
    </div>
    <div class="col-md-2 col-sm-6">
        <div class="card card-custom p-3 border-warning border-start border-4">
            <h6 class="text-muted mb-2 small text-uppercase fw-bold"><i class="bi bi-grid-3x3"></i> Trống</h6>
            <h3 class="mb-0 fw-bold text-dark">{{ $phongTrong }}</h3>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-custom p-3 border-info border-start border-4">
            <h6 class="text-muted mb-2 small text-uppercase fw-bold"><i class="bi bi-calendar-check"></i> Đặt hôm nay</h6>
            <h3 class="mb-0 fw-bold text-dark">{{ $datPhongHomNay }} đơn</h3>
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="card card-custom p-3 border-danger border-start border-4">
            <h6 class="text-muted mb-2 small text-uppercase fw-bold"><i class="bi bi-cash-stack"></i> Doanh thu ngày</h6>
            <h3 class="mb-0 fw-bold text-dark">{{ number_format($doanhThuHomNay ?? 0, 0, ',', '.') }} đ</h3>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-6 col-lg-12">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-dark mb-4"><i class="bi bi-building"></i> Tình trạng phòng theo tầng</h5>
            <div class="scroll-box pe-1">
                @foreach($phongs as $tang => $danhSachPhong)
                    <div class="mb-4">
                        <h6 class="fw-bold border-bottom pb-2 text-secondary">Tầng {{ $tang }}</h6>
                        <div class="row g-2">
                            @foreach($danhSachPhong as $phong)
                                <div class="col-md-3 col-sm-4 col-6">
                                    <div class="room-box {{ $phong->trang_thai == 'Trống' ? 'room-trong' : 'room-da-dat' }}">
                                        <div>P.{{ $phong->so_phong }}</div>
                                        <small style="font-size: 0.75rem; font-weight: normal;">{{ $phong->loai_phong }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history"></i> Đặt phòng gần đây</h5>
            <div class="scroll-box pe-1">
                @foreach($datPhongGanDay as $dp)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark small">{{ $dp->khachhang->ho_ten ?? 'Khách vãng lai' }}</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            Phòng {{ $dp->phong->so_phong ?? 'Combo' }} - {{ \Carbon\Carbon::parse($dp->ngay_nhan)->format('d/m') }}
                        </small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-light text-dark border small">{{ number_format($dp->tong_tien_phai_tra, 0, ',', '.') }}đ</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-fill-check text-success"></i> Nhật ký hoạt động</h5>
            <div class="scroll-box pe-1">
                @if(count($thongBaoHeThong) > 0)
                    @foreach($thongBaoHeThong as $tb)
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold small text-dark"><i class="bi {{ $tb['icon'] }} me-1"></i> {{ $tb['tieu_de'] }}</span>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ $tb['thoi_gian_str'] }}</small>
                        </div>
                        <p class="text-secondary mb-0 small" style="font-size: 0.8rem; line-height: 1.4;">{{ $tb['noi_dung'] }}</p>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted small text-center mt-4">Chưa ghi nhận thao tác nào từ nhân viên.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 align-items-stretch">
    <div class="col-xl-5 col-lg-12 d-flex">
        <div class="card card-custom p-4 flex-fill h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-graph-up-arrow text-success"></i> Tăng trưởng doanh thu theo Tuần</h5>
            <div style="height: 280px; position: relative;">
                <canvas id="revenueLineChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 d-flex">
        <div class="card card-custom p-4 flex-fill h-100">
            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-pie-chart-fill text-info"></i> Doanh thu dịch vụ</h5>
            <small class="text-muted mb-3 d-block">Thống kê riêng tháng: {{ \Carbon\Carbon::now()->format('m/Y') }}</small>
            <div style="height: 250px; position: relative; display: flex; align-items: center; justify-content: center;">
                <canvas id="servicePieChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 d-flex">
        <div class="card card-custom p-4 h-100 d-flex flex-column justify-content-between flex-fill">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-trophy-fill text-danger"></i> Khách hàng VIP</h5>
                    <span class="badge bg-danger-subtle text-danger px-2 py-1 small">Top 5</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm align-middle mb-0">
                        <thead>
                            <tr class="text-muted small border-bottom" style="font-size: 0.75rem;">
                                <th>HỌ TÊN</th>
                                <th class="text-center">SỐ ĐƠN</th>
                                <th class="text-end">TỔNG CHI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topKhachHang as $kh)
                            <tr class="border-bottom-dashed py-2" style="font-size: 0.85rem;">
                                <td class="fw-bold text-dark py-2">{{ $kh->khachhang->ho_ten ?? 'Khách VIP ẩn danh' }}</td>
                                <td class="text-center"><span class="badge bg-secondary rounded-pill">{{ $kh->tong_don }}</span></td>
                                <td class="text-end fw-bold text-secondary">{{ number_format($kh->tong_chi_tieu, 0, ',', '.') }}đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3 text-center">
                <a href="#" class="btn btn-sm btn-outline-dark w-100 fw-bold py-2"><i class="bi bi-people"></i> Xem tất cả khách hàng</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- 1. BIỂU ĐỒ ĐƯỜNG TĂNG TRƯỞNG ---
        const ctxLine = document.getElementById('revenueLineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: {!! json_encode($lineLabels) !!},
                datasets: [{
                    label: 'Doanh thu phòng & combo (đ)',
                    data: {!! json_encode($lineValues) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // --- 2. BIỂU ĐỒ TRÒN DỊCH VỤ HIỆN SỐ TIỀN CHI TIẾT (LỌC THEO THÁNG) ---
        const ctxPie = document.getElementById('servicePieChart').getContext('2d');
        const rawLabels = {!! json_encode($pieLabels) !!};
        const rawValues = {!! json_encode($pieValues) !!};

        if (rawLabels.length === 0) {
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: ['Tháng này chưa có doanh thu dịch vụ'],
                    datasets: [{ data: [1], backgroundColor: ['#e2e8f0'] }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });
        } else {
            // Định dạng format tiền VNĐ cho nhãn text hiển thị
            const formatter = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' });

            // Ép mảng nhãn mới chứa kèm theo số tiền chi tiết ngay cạnh tên dịch vụ giống ảnh minh họa
            const formattedLabels = rawLabels.map((label, index) => {
                return `${label}: ${formatter.format(rawValues[index]).replace('₫', 'đ')}`;
            });

            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: formattedLabels, // Đưa nhãn đính kèm tiền vào đây
                    datasets: [{
                        data: rawValues,
                        backgroundColor: ['#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                font: { size: 10, weight: 'bold' },
                                padding: 8
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` Doanh thu: ${formatter.format(context.raw).replace('₫', 'đ')}`;
                                }
                            }
                        }
                    },
                    cutout: '55%'
                }
            });
        }
    });
</script>
@endsection
