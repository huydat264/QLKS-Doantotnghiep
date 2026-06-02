<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Thanh Toán - {{ $datPhong->id_datphong }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Arial', sans-serif; }
        .invoice-container { max-width: 800px; margin: 40px auto; background: #fff; padding: 40px; box-shadow: 0 0 15px rgba(0,0,0,0.1); border-radius: 8px; }
        .invoice-header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .invoice-title { letter-spacing: 2px; text-transform: uppercase; font-weight: 800; }
        .table th { background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact; }

        /* Ẩn nút khi bấm in */
        @media print {
            body { background: transparent; }
            .invoice-container { box-shadow: none; margin: 0; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="text-end mt-3 no-print">
        <a href="{{ route('admin.thanhtoan.checkout', $datPhong->id_datphong) }}">Thanh toán</a>
        <button onclick="window.print()" class="btn btn-primary fw-bold"><i class="bi bi-printer"></i> IN HÓA ĐƠN</button>
    </div>

    <div class="invoice-container">
        <div class="row invoice-header align-items-center">
            <div class="col-sm-6">
                <h3 class="fw-bold mb-0">Kim Boutique Hotel</h3>
                <p class="text-muted mb-0 small">Địa chỉ: 123 Đường ABC, Phú Quốc</p>
                <p class="text-muted mb-0 small">Hotline: 0123.456.789</p>
            </div>
            <div class="col-sm-6 text-end">
                <h2 class="invoice-title text-primary">HÓA ĐƠN</h2>
                <p class="mb-0 fw-bold">Mã HĐ: #INV-{{ str_pad($datPhong->id_datphong, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="mb-0 text-muted">Ngày in: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-sm-6">
                <h6 class="fw-bold text-muted text-uppercase border-bottom pb-1 mb-2">Thông tin khách hàng</h6>
                <p class="mb-1"><strong>Họ tên:</strong> {{ $datPhong->ho_ten }}</p>
                <p class="mb-1"><strong>Điện thoại:</strong> {{ $datPhong->so_dien_thoai }}</p>
            </div>
            <div class="col-sm-6">
                <h6 class="fw-bold text-muted text-uppercase border-bottom pb-1 mb-2">Chi tiết Đặt phòng</h6>
                <p class="mb-1"><strong>Phòng:</strong> {{ $datPhong->ten_phong }}</p>
                <p class="mb-1"><strong>Nhận phòng:</strong> {{ \Carbon\Carbon::parse($datPhong->ngay_nhan)->format('d/m/Y') }}</p>
                <p class="mb-1"><strong>Trả phòng:</strong> {{ \Carbon\Carbon::parse($datPhong->ngay_tra)->format('d/m/Y') }}</p>
            </div>
        </div>

        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th width="15%" class="text-center">Ngày TT</th>
                    <th width="20%">Hạng mục</th>
                    <th width="15%">Hình thức</th>
                    <th width="30%">Ghi chú (Bồi thường/Phụ phí)</th>
                    <th width="20%" class="text-end">Số tiền</th>
                </tr>
            </thead>
            <tbody>
                @php $tongTatCa = 0; @endphp
                @foreach($thanhToans as $tt)
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($tt->ngay_thanh_toan)->format('d/m/Y') }}</td>
                    <td class="fw-semibold">{{ $tt->loai_thanh_toan }}</td>
                    <td>{{ $tt->hinh_thuc }}</td>
                    <td class="small text-muted">{{ $tt->ghi_chu }}</td>
                    <td class="text-end fw-bold">{{ number_format($tt->so_tien, 0, ',', '.') }} đ</td>
                </tr>
                @php $tongTatCa += $tt->so_tien; @endphp
                @endforeach
            </tbody>
        </table>

        @if($dichVus->count() > 0)
        <h6 class="fw-bold text-muted text-uppercase mb-2">Bảng kê Dịch vụ đã dùng</h6>
        <table class="table table-sm table-bordered mb-4">
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
                    <td>{{ $dv->ten_dich_vu }}</td>
                    <td class="text-center">{{ $dv->so_luong }}</td>
                    <td class="text-end">{{ number_format($dv->don_gia, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($dv->so_luong * $dv->don_gia, 0, ',', '.') }} đ</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="row mt-4">
            <div class="col-sm-7">
                <p class="text-muted small"><i>* Giá trị thanh toán đã bao gồm thuế GTGT (VAT).</i></p>
                <p class="text-muted small"><i>* Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi! Hẹn gặp lại.</i></p>
            </div>
            <div class="col-sm-5">
                <table class="table table-borderless">
                    <tr>
                        <td class="fw-bold text-end">TỔNG ĐÃ THANH TOÁN:</td>
                        <td class="text-end fs-4 fw-bold text-success">{{ number_format($tongTatCa, 0, ',', '.') }} đ</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="text-center mt-5 pt-4">
            <p class="fw-bold mb-5 pb-4">Đại diện thu tiền<br><small class="text-muted fw-normal">(Ký, ghi rõ họ tên)</small></p>
        </div>
    </div>
</div>

</body>
</html>
