<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KhachHang;

class KhachHangManagementController extends Controller
{
    // 1. Hiển thị danh sách & Tìm kiếm
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = KhachHang::query();

        if (!empty($search)) {
            $query->where('ho_ten', 'LIKE', "%{$search}%")
                  ->orWhere('so_dien_thoai', 'LIKE', "%{$search}%")
                  ->orWhere('cccd', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
        }

        // Lấy 20 dòng trên 1 trang, sắp xếp khách hàng mới nhất lên đầu
        $danhSachKhachHang = $query->orderBy('id_khachhang', 'desc')->paginate(20);

        return view('admin.quanlykhachhang', compact('danhSachKhachHang', 'search'));
    }

    // 2. Thêm mới khách hàng
    public function store(Request $request)
    {
        $request->validate([
            'tai_khoan_khachhang_id' => 'nullable|integer',
            'ho_ten' => 'required|string|max:100',
            'cccd' => 'required|string|max:20|unique:khachhang,cccd',
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|string',
            'so_dien_thoai' => 'required|string|max:15',
            'email' => 'nullable|email|max:100',
            'dia_chi' => 'nullable|string|max:200',
        ]);

        KhachHang::create($request->all());

        return redirect()->route('admin.khachhang.index')->with('success', 'Thêm khách hàng thành công!');
    }

    // 3. Cập nhật thông tin khách hàng
    public function update(Request $request, $id)
    {
        $khachHang = KhachHang::findOrFail($id);

        $request->validate([
            'tai_khoan_khachhang_id' => 'nullable|integer',
            'ho_ten' => 'required|string|max:100',
            'cccd' => 'required|string|max:20|unique:khachhang,cccd,' . $id . ',id_khachhang',
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|string',
            'so_dien_thoai' => 'required|string|max:15',
            'email' => 'nullable|email|max:100',
            'dia_chi' => 'nullable|string|max:200',
        ]);

        $khachHang->update($request->all());

        return redirect()->route('admin.khachhang.index')->with('success', 'Cập nhật thông tin khách hàng thành công!');
    }

    // 4. Xóa khách hàng
    public function destroy($id)
    {
        try {
            $khachHang = KhachHang::findOrFail($id);
            $khachHang->delete();
            return redirect()->route('admin.khachhang.index')->with('success', 'Đã xóa khách hàng khỏi hệ thống!');
        } catch (\Exception $e) {
            // Xử lý lỗi khóa ngoại (nếu khách hàng đã có lịch sử đặt phòng)
            return redirect()->route('admin.khachhang.index')->with('error', 'Không thể xóa khách hàng này vì đã có dữ liệu giao dịch hoặc đặt phòng liên kết!');
        }
    }
}
