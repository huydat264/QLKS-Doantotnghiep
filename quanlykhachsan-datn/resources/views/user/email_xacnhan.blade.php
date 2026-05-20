<!DOCTYPE html>
<html>
<head>
    <title>Xác nhận đặt phòng</title>
</head>
<body>
    <h2>Cảm ơn bạn đã đặt phòng, {{ $donDat->ho_ten }}!</h2>
    <p>Thông tin mã đơn hàng của bạn là: <strong>{{ $donDat->id_datphong }}</strong></p>
    <p>Tổng tiền: {{ number_format($donDat->tong_tien) }} VNĐ</p>
    </body>
</html>
