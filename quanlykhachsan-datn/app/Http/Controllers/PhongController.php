<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phong;
use Carbon\Carbon;
use App\Services\RoomAvailabilityService;
use Illuminate\Support\Facades\DB;

class PhongController extends Controller
{
    private function parseBookingDate($value)
    {
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
            return Carbon::createFromFormat('d/m/Y', $value)->toDateString();
        }

        return Carbon::parse($value)->toDateString();
    }

    public function indexUser(Request $request)
    {
        // Khởi tạo query từ Model
        $query = Phong::query();

        // Lọc theo Khoảng giá
        if ($request->filled('gia_max')) {
            $query->where('gia_phong', '<=', $request->gia_max);
        }

        // Lọc theo Loại phòng
        if ($request->filled('loai_phong')) {
            $query->whereIn('loai_phong', $request->loai_phong);
        }

        // Lọc theo Bộ lọc nâng cao (Hướng phòng, Số người, Số phòng ngủ)
        if ($request->filled('huong_phong')) {
            $query->where('huong_phong', 'LIKE', '%' . $request->huong_phong . '%');
        }
        if ($request->filled('so_luong_nguoi')) {
            $query->where('so_luong_nguoi', '>=', $request->so_luong_nguoi);
        }
        if ($request->filled('so_phong_ngu')) {
            $query->where('so_phong_ngu', $request->so_phong_ngu);
        }

        // Lọc theo tìm kiếm từ home: số khách
        if ($request->filled('tong_khach')) {
            $tong_khach = $request->input('tong_khach');
            if ($tong_khach > 0) {
                $query->where('so_luong_nguoi', '>=', $tong_khach);
            }
        }

        if ($request->filled('checkin') && $request->filled('checkout')) {
            try {
                $checkin = $this->parseBookingDate($request->checkin);
                $checkout = $this->parseBookingDate($request->checkout);

                if ($checkout > $checkin) {
                    $query->whereNotExists(function ($subQuery) use ($checkin, $checkout) {
                        $subQuery->select(DB::raw(1))
                            ->from('datphong')
                            ->whereColumn('datphong.id_phong', 'phong.id_phong')
                            ->where('trang_thai', 'Đã xác nhận')
                            ->where(function ($q) use ($checkin, $checkout) {
                                $q->where('ngay_nhan', '<', $checkout)
                                  ->where('ngay_tra', '>', $checkin);
                            });
                    });
                } else {
                    $query->whereRaw('1 = 0');
                }
            } catch (\Exception $e) {
                $query->whereRaw('1 = 0');
            }
        }

        $phongs = $query->get();

        return view('user.phonguser', compact('phongs'));
    }
    public function chitietUser($id)
{
    // Tìm phòng theo id, nếu không thấy thì báo lỗi 404
    $phong = Phong::where('id_phong', $id)->firstOrFail();

    return view('user.chitietphong', compact('phong'));
}
}

