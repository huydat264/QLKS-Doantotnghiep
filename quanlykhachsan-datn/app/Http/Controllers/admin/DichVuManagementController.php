<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DichVu;
use App\Models\SuDungDichVu;
use Illuminate\Support\Facades\Storage;

class DichVuManagementController extends Controller
{
    // 1. Hiển thị danh sách
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Bổ sung mo_ta, hinh_anh, gia_von
        $query = DichVu::select('id_dichvu', 'ten_dich_vu', 'gia_von', 'gia', 'mo_ta', 'hinh_anh');

        if (!empty($search)) {
            $query->where('ten_dich_vu', 'LIKE', "%{$search}%");
        }

        $danhSachDichVu = $query->orderBy('id_dichvu', 'desc')->paginate(20);

        return view('admin.quanlydichvu', compact('danhSachDichVu', 'search'));
    }

    // 2. Thêm mới
    public function store(Request $request)
    {
        $request->validate([
            'ten_dich_vu' => 'required|string|max:255',
            'gia_von' => 'required|numeric|min:0',
            'gia' => 'required|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'hinh_anh_link' => 'nullable|url'
        ]);

        $imgPath = null;
        // Ưu tiên file upload từ máy, nếu không có thì lấy link web
        if ($request->hasFile('hinh_anh')) {
            $imgPath = $request->file('hinh_anh')->store('dichvu', 'public');
        } elseif ($request->filled('hinh_anh_link')) {
            $imgPath = $request->hinh_anh_link;
        }

        DichVu::create([
            'ten_dich_vu' => $request->ten_dich_vu,
            'gia_von' => $request->gia_von,
            'gia' => $request->gia,
            'mo_ta' => $request->mo_ta,
            'hinh_anh' => $imgPath
        ]);

        return redirect()->back()->with('success', 'Đã thêm mới dịch vụ thành công!');
    }

    // 3. Cập nhật
    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_dich_vu' => 'required|string|max:255',
            'gia_von' => 'required|numeric|min:0',
            'gia' => 'required|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'hinh_anh_link' => 'nullable|url'
        ]);

        $dichVuCu = DichVu::findOrFail($id);
        $imgPath = $dichVuCu->hinh_anh;

        // Xử lý ghi đè ảnh
        if ($request->hasFile('hinh_anh')) {
            // Xóa ảnh cũ trên máy chủ
            if ($imgPath && !filter_var($imgPath, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($imgPath)) {
                Storage::disk('public')->delete($imgPath);
            }
            $imgPath = $request->file('hinh_anh')->store('dichvu', 'public');
        } elseif ($request->filled('hinh_anh_link')) {
            if ($imgPath && !filter_var($imgPath, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($imgPath)) {
                Storage::disk('public')->delete($imgPath);
            }
            $imgPath = $request->hinh_anh_link;
        }

        $dichVuCu->update([
            'ten_dich_vu' => $request->ten_dich_vu,
            'gia_von' => $request->gia_von,
            'gia' => $request->gia,
            'mo_ta' => $request->mo_ta,
            'hinh_anh' => $imgPath
        ]);

        return redirect()->back()->with('success', 'Cập nhật thông tin dịch vụ thành công!');
    }

    // 4. Xóa
    public function destroy($id)
    {
        $isUsed = SuDungDichVu::where('id_dichvu', $id)->exists();

        if ($isUsed) {
            return redirect()->back()->with('error', 'Không thể xóa vì dịch vụ này đã có khách hàng sử dụng!');
        }

        // Lấy thông tin xóa luôn cả ảnh rác trên máy chủ
        $dichVu = DichVu::findOrFail($id);
        if ($dichVu->hinh_anh && !filter_var($dichVu->hinh_anh, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($dichVu->hinh_anh)) {
            Storage::disk('public')->delete($dichVu->hinh_anh);
        }

        $dichVu->delete();
        return redirect()->back()->with('success', 'Đã xóa dịch vụ thành công!');
    }
}
