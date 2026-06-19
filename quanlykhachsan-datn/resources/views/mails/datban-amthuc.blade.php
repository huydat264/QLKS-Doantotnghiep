<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        h2 {
            color: #673065;
            border-bottom: 2px solid #673065;
            padding-bottom: 10px;
        }
        .info-group {
            margin: 15px 0;
            padding: 10px;
            background-color: #fff;
            border-left: 4px solid #673065;
        }
        .label {
            font-weight: bold;
            color: #673065;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Yêu Cầu Đặt Bàn Ẩm Thực Mới</h2>

        <p>Bạn nhận được một yêu cầu đặt bàn mới từ khách hàng:</p>

        <div class="info-group">
            <p><span class="label">Họ và Tên:</span> {{ $data['name'] }}</p>
        </div>

        <div class="info-group">
            <p><span class="label">Số Điện Thoại:</span> {{ $data['phone'] }}</p>
        </div>

        <div class="info-group">
            <p><span class="label">Ngày Đặt:</span> {{ \Carbon\Carbon::parse($data['date'])->format('d/m/Y') }}</p>
        </div>

        <div class="info-group">
            <p><span class="label">Số Khách:</span> {{ $data['guests'] }} người</p>
        </div>

        @if($data['notes'])
        <div class="info-group">
            <p><span class="label">Ghi Chú Đặc Biệt:</span></p>
            <p>{{ $data['notes'] }}</p>
        </div>
        @endif

        <p style="margin-top: 30px; padding: 15px; background-color: #e8f5e9; border-radius: 5px;">
            Vui lòng liên hệ với khách hàng để xác nhận yêu cầu đặt bàn.
        </p>

        <div class="footer">
            <p>---</p>
            <p>Email này được gửi từ Hệ Thống Quản Lý Khách Sạn</p>
        </div>
    </div>
</body>
</html>

