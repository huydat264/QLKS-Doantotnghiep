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

    // 1. Lọc theo Khoảng giá (VND)
    if ($request->filled('gia_max')) {
        $query->where('gia_phong', '<=', $request->gia_max);
    }

    // 2. Lọc theo Loại phòng (Sắp xếp theo)
    if ($request->filled('loai_phong')) {
        $query->whereIn('loai_phong', $request->loai_phong);
    }

    // 3. Lọc theo Bộ lọc nâng cao (Hướng phòng, Số người, Số phòng ngủ)
    if ($request->filled('huong_phong')) {
        $query->where('huong_phong', 'LIKE', '%' . $request->huong_phong . '%');
    }
    if ($request->filled('so_luong_nguoi')) {
        $query->where('so_luong_nguoi', '>=', $request->so_luong_nguoi);
    }
    if ($request->filled('so_phong_ngu')) {
        $query->where('so_phong_ngu', $request->so_phong_ngu);
    }

    // Lấy dữ liệu đã lọc
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

