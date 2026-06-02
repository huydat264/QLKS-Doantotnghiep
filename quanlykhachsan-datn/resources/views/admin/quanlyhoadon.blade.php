@extends('admin.layout.master')
@section('title', 'Lịch sử Hóa đơn')
@section('page_title', 'Quản lý Hóa Đơn Đã Xuất')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-5">
            <h4 class="fw-bold mb-1">Lịch sử Hóa đơn</h4>
            <p class="text-muted mb-0">Danh sách toàn bộ hóa đơn đã thanh toán thành công.</p>
        </div>
        <div class="col-md-7 text-end">
            <form action="{{ route('admin.hoadon.index') }}" method="GET" class="d-flex justify-content-end">
                <div class="input-group" style="max-width: 400px;">
                    <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Tìm tên khách, SĐT, số phòng, mã HĐ...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã HĐ</th>
                    <th>Mã Đặt Phòng</th>
                    <th>Khách hàng</th>
                    <th>Điện thoại</th>
                    <th>Phòng</th>
                    <th>Ngày xuất</th>
                    <th class="text-end">Tổng tiền</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($danhSachHoaDon as $hd)
                <tr>
                    <td class="fw-bold text-primary">#INV-{{ str_pad($hd->id_hoadon, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="fw-semibold">#DP-{{ $hd->id_datphong }}</td>
                    <td>{{ $hd->ho_ten }}</td>
                    <td>{{ $hd->so_dien_thoai }}</td>
                    <td><span class="badge bg-secondary">Phòng {{ $hd->ten_phong }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($hd->ngay_xuat)->format('d/m/Y H:i') }}</td>
                    <td class="fw-bold text-success text-end">{{ number_format($hd->tong_tien, 0, ',', '.') }} đ</td>
                    <td class="text-center">
                        <a href="{{ route('admin.thanhtoan.invoice', $hd->id_datphong) }}" class="btn btn-sm btn-outline-primary btn-rounded" target="_blank">
                            <i class="bi bi-printer"></i> Xem / In lại
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Chưa có hóa đơn nào được xuất trong hệ thống.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $danhSachHoaDon->appends(['search' => $search ?? ''])->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
