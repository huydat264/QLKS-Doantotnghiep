<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Combo;
use App\Models\DichVu;
use App\Models\Phong;
use Illuminate\Support\Facades\DB;

class ComboManagementController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy danh sách Combo theo đúng cấu trúc bảng combo
        $combos = Combo::with('dichVus')
            ->orderBy('id_combo', 'desc')
            ->paginate(15);

        // 2. Lấy dịch vụ đi kèm cho từng Combo
        foreach ($combos as $combo) {
            $combo->dich_vu = $combo->dichVus;
            $combo->dichvu_ids = $combo->dich_vu->pluck('id_dichvu')->toArray();

            // compute availability and active flag
            $combo->available_rooms = Phong::where('loai_phong', $combo->loai_phong_ap_dung)
                ->where('trang_thai', 'Trống')
                ->count();

            $combo->is_active = isset($combo->active) ? (bool)$combo->active : true;
        }

        // 3. Lấy dữ liệu danh mục để đổ vào Modal
        $hangPhongs = Phong::select('loai_phong')->distinct()->pluck('loai_phong');
        $dichVus = DichVu::all();

        return view('admin.quanlycombo', compact('combos', 'hangPhongs', 'dichVus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_combo' => 'required|string|max:255',
            'loai_phong_ap_dung' => 'required|string',
            'tong_gia' => 'required|numeric',
            'so_dem_luu_tru' => 'required|integer|min:1',
            'gia_phong_dinh_muc' => 'required|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'quyen_loi' => 'nullable|string',
            'dieu_khoan' => 'nullable|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'active' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            // 1. Lưu vào bảng combo (ban đầu chưa xử lý ảnh)
            $combo = Combo::create([
                'ten_combo' => $request->ten_combo,
                'gia_combo' => $request->tong_gia, // Giá trọn gói tự động tính từ client truyền lên
                'so_dem_luu_tru' => $request->so_dem_luu_tru,
                'gia_phong_dinh_muc' => $request->gia_phong_dinh_muc,
                'loai_phong_ap_dung' => $request->loai_phong_ap_dung,
                'mo_ta' => $request->mo_ta,
                'quyen_loi' => $request->quyen_loi,
                'dieu_khoan' => $request->dieu_khoan,
                'active' => $request->has('active') ? 1 : 0,
            ]);

            // Xử lý ảnh (nếu có) thông qua model
            if ($request->hasFile('hinh_anh')) {
                $combo->replaceImage($request->file('hinh_anh'));
            } elseif ($request->filled('hinh_anh')) {
                // Nếu người dùng cung cấp link
                $combo->replaceImage($request->input('hinh_anh'));
            }

            // 2. Lưu các dịch vụ đi kèm vào bảng combo_dichvu
            if ($request->has('dichvu_ids')) {
                $combo->dichVus()->sync($request->dichvu_ids);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Đã tạo Combo mới thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_combo' => 'required|string|max:255',
            'loai_phong_ap_dung' => 'required|string',
            'tong_gia' => 'required|numeric',
            'so_dem_luu_tru' => 'required|integer|min:1',
            'gia_phong_dinh_muc' => 'required|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'quyen_loi' => 'nullable|string',
            'dieu_khoan' => 'nullable|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'active' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $combo = Combo::findOrFail($id);
            // 1. Cập nhật bảng combo (không cập ảnh trực tiếp)
            $combo->update([
                'ten_combo' => $request->ten_combo,
                'gia_combo' => $request->tong_gia,
                'so_dem_luu_tru' => $request->so_dem_luu_tru,
                'gia_phong_dinh_muc' => $request->gia_phong_dinh_muc,
                'loai_phong_ap_dung' => $request->loai_phong_ap_dung,
                'mo_ta' => $request->mo_ta,
                'quyen_loi' => $request->quyen_loi,
                'dieu_khoan' => $request->dieu_khoan,
                'active' => $request->has('active') ? 1 : 0,
            ]);

            // Xử lý ảnh mới nếu có
            if ($request->hasFile('hinh_anh')) {
                $combo->replaceImage($request->file('hinh_anh'));
            } elseif ($request->filled('hinh_anh')) {
                $combo->replaceImage($request->input('hinh_anh'));
            }

            // 2. Cập nhật bảng combo_dichvu (Xóa cũ đi, thêm mới lại)
            $combo->dichVus()->sync([]);

            if ($request->has('dichvu_ids')) {
                $combo->dichVus()->sync($request->dichvu_ids);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Cập nhật Combo thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $combo = Combo::findOrFail($id);
            // Xóa ảnh (nếu lưu cục bộ)
            $combo->deleteImage();

            $combo->dichVus()->sync([]);
            $combo->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Đã xoá Combo và giải phóng liên kết!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }

    // Toggle active/inactive for a combo (AJAX)
    public function toggle(Request $request, $id)
    {
        try {
            $combo = Combo::findOrFail($id);
            if (!$combo) {
                return response()->json(['success' => false, 'message' => 'Combo không tồn tại'], 404);
            }

            $new = (isset($combo->active) && $combo->active) ? 0 : 1;
            $combo->active = $new;
            $combo->save();

            return response()->json(['success' => true, 'active' => (bool)$new]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
