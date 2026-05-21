<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phong;

class PhongController extends Controller
{
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

    // Lọc theo tìm kiếm từ home: ngày nhận/trả phòng và số khách
    if ($request->filled('tong_khach')) {
        $tong_khach = $request->input('tong_khach');
        if ($tong_khach > 0) {
            $query->where('so_luong_nguoi', '>=', $tong_khach);
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

