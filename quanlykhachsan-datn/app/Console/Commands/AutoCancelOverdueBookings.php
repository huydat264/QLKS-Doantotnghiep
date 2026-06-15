<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DatPhong;
use App\Models\Phong;
use Carbon\Carbon;

class AutoCancelOverdueBookings extends Command
{
    protected $signature = 'bookings:auto-cancel';
    protected $description = 'Tự động hủy các đơn đặt phòng nếu khách không nhận phòng trong vòng 1 giờ';

    public function handle()
    {
        // Lấy tất cả các đơn đặt phòng chưa hủy, chưa trả phòng, và ngày nhận <= now - 1 giờ
        $overdueBookings = DatPhong::where('trang_thai', 'Đã xác nhận')
            ->where('ngay_nhan', '<=', Carbon::now()->subHour())
            ->get();

        $count = 0;
        foreach ($overdueBookings as $booking) {
            // Update trạng thái đơn đặt phòng sang "Đã hủy"
            $booking->update(['trang_thai' => 'Đã hủy']);

            // Trả trạng thái phòng về "Trống"
            Phong::where('id_phong', $booking->id_phong)->update(['trang_thai' => 'Trống']);

            $count++;
        }

        if ($count > 0) {
            $this->info("✓ Đã tự động hủy $count đơn đặt phòng quá hạn.");
        } else {
            $this->info("ℹ Không có đơn đặt phòng nào cần hủy.");
        }

        return 0;
    }
}
