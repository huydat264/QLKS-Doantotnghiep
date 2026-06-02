@extends('admin.layout.master')
@section('title', 'Thanh toán & Trả phòng')
@section('page_title', 'Danh sách chờ thanh toán')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Danh sách chờ thanh toán</h4>
            <p class="text-muted mb-0">Hiển thị các đặt phòng đang chờ thanh toán hoặc đang sử dụng.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã đặt phòng</th>
                    <th>Khách hàng</th>
                    <th>Điện thoại</th>
                    <th>Phòng</th>
                    <th>Ngày nhận</th>
                    <th>Ngày trả</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($danhSachChoThanhToan as $item)
                <tr>
                    <td class="fw-semibold">#DP-{{ $item->id_datphong }}</td>
                    <td>{{ $item->ho_ten ?? 'Khách lẻ' }}</td>
                    <td>{{ $item->so_dien_thoai ?? '-' }}</td>
                    <td>{{ $item->ten_phong ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->ngay_nhan)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->ngay_tra)->format('d/m/Y') }}</td>
                    <td class="fw-semibold text-danger">{{ number_format($item->tong_tien ?? 0, 0, ',', '.') }} đ</td>
                    <td><span class="badge bg-info-subtle text-info">{{ $item->trang_thai }}</span></td>
                    <td class="text-center">
                        <a href="{{ route('admin.thanhtoan.checkout', $item->id_datphong) }}" class="btn btn-sm btn-primary btn-rounded">
                            <i class="bi bi-credit-card"></i> Thanh toán
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Không có đơn chờ thanh toán.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $danhSachChoThanhToan->links() }}
    </div>
</div>
@endsection
