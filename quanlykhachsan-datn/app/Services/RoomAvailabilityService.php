<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoomAvailabilityService
{
    /**
     * Kiểm tra phòng có sẵn trong khoảng ngày nhất định
     *
     * @param int $id_phong ID phòng
     * @param string $ngay_nhan Ngày nhận (YYYY-MM-DD)
     * @param string $ngay_tra Ngày trả (YYYY-MM-DD)
     * @param int|null $exclude_booking_id ID đơn đặt để loại trừ (dùng khi update)
     * @return bool
     */
    public static function isRoomAvailable($id_phong, $ngay_nhan, $ngay_tra, $exclude_booking_id = null)
    {
        // Kiểm tra trạng thái phòng (nếu là 'Bảo trì' thì không được đặt)
        $phong = DB::table('phong')->where('id_phong', $id_phong)->first();

        if (!$phong || $phong->trang_thai === 'Bảo trì') {
            return false;
        }

        // Query lấy tất cả các booking xung đột
        // (Chỉ lấy 'Đã xác nhận' - những booking chưa hoàn thành)
        // 'Đã thanh toán' = khách đã trả phòng xong, không cần khóa nữa
        $query = DB::table('datphong')
            ->where('id_phong', $id_phong)
            ->where('trang_thai', 'Đã xác nhận')
            // Điều kiện xung đột ngày:
            // Booking mới: [ngay_nhan, ngay_tra)
            // Booking cũ: [old_ngay_nhan, old_ngay_tra)
            // Xung đột khi: ngay_nhan < old_ngay_tra AND ngay_tra > old_ngay_nhan
            ->where(function ($q) use ($ngay_nhan, $ngay_tra) {
                $q->whereBetween('ngay_nhan', [$ngay_nhan, Carbon::parse($ngay_tra)->subDay()->toDateString()])
                  ->orWhereBetween('ngay_tra', [Carbon::parse($ngay_nhan)->addDay()->toDateString(), $ngay_tra])
                  ->orWhere(function ($q2) use ($ngay_nhan, $ngay_tra) {
                      $q2->where('ngay_nhan', '<=', $ngay_nhan)
                         ->where('ngay_tra', '>=', $ngay_tra);
                  });
            });

        if ($exclude_booking_id) {
            $query->where('id_datphong', '!=', $exclude_booking_id);
        }

        return $query->count() === 0;
    }

    /**
     * Lấy lịch sử booking của phòng (để hiển thị calendar)
     *
     * @param int $id_phong ID phòng
     * @param string|null $from_date Ngày bắt đầu (YYYY-MM-DD) - default: hôm nay
     * @param string|null $to_date Ngày kết thúc (YYYY-MM-DD) - default: +90 ngày
     * @return array
     */
    public static function getRoomBookingHistory($id_phong, $from_date = null, $to_date = null)
    {
        if (!$from_date) {
            $from_date = Carbon::today()->toDateString();
        }
        if (!$to_date) {
            $to_date = Carbon::today()->addDays(90)->toDateString();
        }

        return DB::table('datphong')
            ->where('id_phong', $id_phong)
            ->where('trang_thai', 'Đã xác nhận')
            ->where('ngay_tra', '>', $from_date) // Chỉ lấy future bookings
            ->where('ngay_nhan', '<=', $to_date)
            ->select(
                'id_datphong',
                'ngay_nhan',
                'ngay_tra',
                'trang_thai',
                'id_khachhang'
            )
            ->orderBy('ngay_nhan', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Lấy những ngày được đặt (để hiển thị disabled calendar)
     *
     * @param int $id_phong ID phòng
     * @return array Mảng các ngày (YYYY-MM-DD)
     */
    public static function getDisabledDates($id_phong)
    {
        $bookings = self::getRoomBookingHistory($id_phong);
        $disabledDates = [];

        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->ngay_nhan);
            $end = Carbon::parse($booking->ngay_tra);

            while ($start->lt($end)) {
                $disabledDates[] = $start->toDateString();
                $start->addDay();
            }
        }

        return $disabledDates;
    }

    /**
     * Kiểm tra xung đột cho combo booking
     * Combo được cấp phát phòng ngẫu nhiên, nhưng vẫn phải check xung đột
     *
     * @param string $loai_phong Loại phòng (Standard/Deluxe/Suite)
     * @param string $ngay_nhan Ngày nhận
     * @param string $ngay_tra Ngày trả
     * @return int|null ID phòng khả dụng, hoặc null nếu không có
     */
    public static function findAvailableRoomByType($loai_phong, $ngay_nhan, $ngay_tra)
    {
        // Lấy tất cả phòng có loại phòng này
        $availableRooms = DB::table('phong')
            ->where('loai_phong', $loai_phong)
            ->where('trang_thai', '!=', 'Bảo trì')
            ->pluck('id_phong')
            ->toArray();

        // Filter những phòng không có xung đột
        foreach ($availableRooms as $id_phong) {
            if (self::isRoomAvailable($id_phong, $ngay_nhan, $ngay_tra)) {
                return $id_phong;
            }
        }

        return null;
    }

    /**
     * Get JSON array of booked dates for calendar display
     * Format: {dates: [{start: 'YYYY-MM-DD', end: 'YYYY-MM-DD', status: 'Đã xác nhận'}]}
     *
     * @param int $id_phong
     * @return string JSON
     */
    public static function getBookedDatesJSON($id_phong)
    {
        $bookings = self::getRoomBookingHistory($id_phong);
        $dates = [];

        foreach ($bookings as $booking) {
            $dates[] = [
                'start' => $booking->ngay_nhan,
                'end' => $booking->ngay_tra,
                'status' => $booking->trang_thai,
                'booking_id' => $booking->id_datphong
            ];
        }

        return json_encode(['dates' => $dates]);
    }
}
