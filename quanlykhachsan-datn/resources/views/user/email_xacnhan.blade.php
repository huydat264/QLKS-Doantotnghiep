<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đặt phòng thành công</title>
</head>
<body style="margin: 0; padding: 0; background-color: #faf8f5; font-family: 'Arial', sans-serif; -webkit-font-smoothing: antialiased; width: 100% !important;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #faf8f5; padding: 40px 10px;">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #f1eeea;">

                    <tr>
                        <td align="center" style="background-color: #673065; padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; text-transform: uppercase; letter-spacing: 1.5px; font-weight: bold; font-family: 'Georgia', serif;">Xác Nhận Đặt Phòng</h1>
                            <p style="margin: 10px 0 0 0; color: #e1cfe0; font-size: 14px;">Cảm ơn bạn đã tin tưởng lựa chọn Kim Boutique Hotel</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 20px 0; font-size: 18px; color: #333333;">Cảm ơn bạn đã đặt phòng, {{ $donDat->ho_ten }}!</h2>
                            <p style="margin: 0 0 25px 0; font-size: 14px; color: #555555; line-height: 1.6;">
                                Hệ thống đã ghi nhận yêu cầu đặt phòng thành công của Quý khách. Dưới đây là thông tin chi tiết về đơn đặt và bảng tính chi phí dự kiến:
                            </p>

                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 30px; background-color: #fcfbfa; border-radius: 8px; border: 1px solid #f1eeea; padding: 20px;">
                                <tr>
                                    <td style="padding-bottom: 10px; font-size: 14px; color: #666666;" width="40%"><strong>Mã đơn hàng:</strong></td>
                                    <td style="padding-bottom: 10px; font-size: 14px; color: #673065; font-weight: bold;">#DP-{{ $donDat->id_datphong }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 10px; font-size: 14px; color: #666666;"><strong>Hạng mục đặt:</strong></td>
                                    <td style="padding-bottom: 10px; font-size: 14px; color: #333333; font-weight: bold;">
                                        {{ $donDat->loai_hinh_dat === 'LẺ' ? 'Phòng ' . ($donDat->so_phong ?? '—') : ($donDat->ten_combo ?? 'Chi tiết dịch vụ') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 10px; font-size: 14px; color: #666666;"><strong>Ngày nhận phòng:</strong></td>
                                    <td style="padding-bottom: 10px; font-size: 14px; color: #333333;">{{ date('d/m/Y', strtotime($donDat->ngay_nhan)) }} (Từ 14:00)</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 14px; color: #666666;"><strong>Ngày trả phòng:</strong></td>
                                    <td style="font-size: 14px; color: #333333;">{{ date('d/m/Y', strtotime($donDat->ngay_tra)) }} (Trước 12:00)</td>
                                </tr>
                            </table>

                            <h3 style="margin: 0 0 15px 0; font-size: 14px; color: #673065; text-transform: uppercase; letter-spacing: 0.5px; border-left: 3px solid #673065; padding-left: 10px;">Chi tiết tài chính</h3>

                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse; margin-bottom: 25px;">
                                <tr style="border-bottom: 1px solid #f1eeea;">
                                    <td style="padding: 12px 0; font-size: 14px; color: #555555;">
                                        Tổng tiền phòng/Combo
                                    </td>
                                    <td align="right" style="padding: 12px 0; font-size: 14px; font-weight: bold; color: #333333;">
                                        {{ number_format($donDat->tong_tien_phong_combo ?? ($donDat->tong_tien - ($donDat->tong_tien_dich_vu ?? 0)), 0, ',', '.') }} VNĐ
                                    </td>
                                </tr>
                                @if(!empty($donDat->tong_tien_dich_vu) && $donDat->tong_tien_dich_vu > 0)
                                <tr style="border-bottom: 1px solid #f1eeea;">
                                    <td style="padding: 12px 0; font-size: 14px; color: #555555;">
                                        Tổng tiền dịch vụ kèm theo
                                    </td>
                                    <td align="right" style="padding: 12px 0; font-size: 14px; font-weight: bold; color: #333333;">
                                        {{ number_format($donDat->tong_tien_dich_vu, 0, ',', '.') }} VNĐ
                                    </td>
                                </tr>
                                @endif
                                <tr style="border-bottom: 1px solid #f1eeea; background-color: #f4fbf5;">
                                    <td style="padding: 12px 8px; font-size: 14px; color: #555555; font-weight: bold;">
                                        Tổng tiền tạm tính
                                    </td>
                                    <td align="right" style="padding: 12px 8px; font-size: 14px; font-weight: bold; color: #333333;">
                                        {{ number_format($donDat->tong_tien_tam_tinh ?? $donDat->tong_tien, 0, ',', '.') }} VNĐ
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid #f1eeea; background-color: #f4fbf5;">
                                    <td style="padding: 12px 8px; font-size: 14px; color: #2e7d32; font-weight: bold;">
                                        (-) Số tiền đã thanh toán đặt cọc trước
                                    </td>
                                    <td align="right" style="padding: 12px 8px; font-size: 14px; font-weight: bold; color: #2e7d32;">
                                        - {{ number_format($donDat->tien_coc ?? 0, 0, ',', '.') }} VNĐ
                                    </td>
                                </tr>
                                <tr style="background-color: #fdfaf2; border-top: 2px solid #673065;">
                                    <td style="padding: 15px 8px; font-size: 15px; color: #673065; font-weight: bold;">
                                        Tổng số tiền phải thanh toán còn lại
                                    </td>
                                    <td align="right" style="padding: 15px 8px; font-size: 16px; font-weight: bold; color: #673065;">
                                        {{ number_format($donDat->so_tien_con_lai ?? (($donDat->tong_tien ?? 0) - ($donDat->tien_coc ?? 0)), 0, ',', '.') }} VNĐ
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #fff9f3; border-radius: 6px; padding: 15px; border: 1px solid #ffe8cc;">
                                <tr>
                                    <td style="font-size: 13px; color: #666666; line-height: 1.5;">
                                        <span style="color: #673065; font-weight: bold;">* Thông tin Check-in:</span> Voucher xác nhận chi tiết cùng mã QR kiểm tra thông tin check-in đã được hệ thống đồng bộ hóa. Quý khách vui lòng lưu lại email này hoặc cung cấp mã đơn hàng tại quầy lễ tân để làm thủ tục nhận phòng nhanh chóng.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="background-color: #f6f4f6; padding: 25px 30px; border-top: 1px solid #f1eeea; text-align: center;">
                            <p style="margin: 0 0 6px 0; font-size: 12px; color: #999999; line-height: 1.4;">
                                Đây là email tự động gửi từ hệ thống quản lý khách sạn Kim Boutique Hotel. Vui lòng không phản hồi trực tiếp email này.
                            </p>
                            <p style="margin: 0; font-size: 13px; color: #673065; font-weight: bold;">
                                Hệ thống hỗ trợ Hotline 24/7 — Chúc quý khách một kỳ nghỉ tuyệt vời!
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
