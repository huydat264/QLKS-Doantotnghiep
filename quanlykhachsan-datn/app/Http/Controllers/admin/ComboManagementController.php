<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComboManagementController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy danh sách Combo theo đúng cấu trúc bảng combo
        $combos = DB::table('combo')
            ->select('combo.*')
            ->orderBy('id_combo', 'desc')
            ->paginate(15);

        // 2. Lấy dịch vụ đi kèm cho từng Combo
        foreach ($combos as $combo) {
            $combo->dich_vu = DB::table('combo_dichvu')
                ->join('dichvu', 'combo_dichvu.id_dichvu', '=', 'dichvu.id_dichvu')
                ->where('combo_dichvu.id_combo', $combo->id_combo)
                ->get();

            // Ép mảng ID dịch vụ để ném sang JS xử lý lúc Sửa
            $combo->dichvu_ids = $combo->dich_vu->pluck('id_dichvu')->toArray();
        }

        // 3. Lấy dữ liệu danh mục để đổ vào Modal
        $hangPhongs = DB::table('phong')->select('loai_phong')->distinct()->pluck('loai_phong');
        $dichVus = DB::table('dichvu')->get();

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
        ]);

        DB::beginTransaction();
        try {
            // Xử lý upload hình ảnh combo nếu có
            $hinhAnhPath = null;
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/combos'), $filename);
                $hinhAnhPath = 'uploads/combos/' . $filename;
            }

            // 1. Lưu vào bảng combo
            $comboId = DB::table('combo')->insertGetId([
                'ten_combo' => $request->ten_combo,
                'gia_combo' => $request->tong_gia, // Giá trọn gói tự động tính từ client truyền lên
                'so_dem_luu_tru' => $request->so_dem_luu_tru,
                'gia_phong_dinh_muc' => $request->gia_phong_dinh_muc,
                'loai_phong_ap_dung' => $request->loai_phong_ap_dung,
                'mo_ta' => $request->mo_ta,
                'hinh_anh' => $hinhAnhPath,
                'quyen_loi' => $request->quyen_loi,
                'dieu_khoan' => $request->dieu_khoan,
            ]);

            // 2. Lưu các dịch vụ đi kèm vào bảng combo_dichvu
            if ($request->has('dichvu_ids')) {
                foreach ($request->dichvu_ids as $id_dv) {
                    DB::table('combo_dichvu')->insert([
                        'id_combo' => $comboId,
                        'id_dichvu' => $id_dv
                    ]);
                }
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
        ]);

        DB::beginTransaction();
        try {
            $combo = DB::table('combo')->where('id_combo', $id)->first();
            $hinhAnhPath = $combo->hinh_anh;

            // Xử lý thay đổi file ảnh mới
            if ($request->hasFile('hinh_anh')) {
                // Xóa file ảnh cũ nếu tồn tại trong thư mục để tránh rác host
                if ($hinhAnhPath && file_exists(public_path($hinhAnhPath))) {
                    @unlink(public_path($hinhAnhPath));
                }
                $file = $request->file('hinh_anh');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/combos'), $filename);
                $hinhAnhPath = 'uploads/combos/' . $filename;
            }

            // 1. Cập nhật bảng combo
            DB::table('combo')->where('id_combo', $id)->update([
                'ten_combo' => $request->ten_combo,
                'gia_combo' => $request->tong_gia,
                'so_dem_luu_tru' => $request->so_dem_luu_tru,
                'gia_phong_dinh_muc' => $request->gia_phong_dinh_muc,
                'loai_phong_ap_dung' => $request->loai_phong_ap_dung,
                'mo_ta' => $request->mo_ta,
                'hinh_anh' => $hinhAnhPath,
                'quyen_loi' => $request->quyen_loi,
                'dieu_khoan' => $request->dieu_khoan,
            ]);

            // 2. Cập nhật bảng combo_dichvu (Xóa cũ đi, thêm mới lại)
            DB::table('combo_dichvu')->where('id_combo', $id)->delete();

            if ($request->has('dichvu_ids')) {
                foreach ($request->dichvu_ids as $id_dv) {
                    DB::table('combo_dichvu')->insert([
                        'id_combo' => $id,
                        'id_dichvu' => $id_dv
                    ]);
                }
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
            $combo = DB::table('combo')->where('id_combo', $id)->first();
            if ($combo && $combo->hinh_anh && file_exists(public_path($combo->hinh_anh))) {
                @unlink(public_path($combo->hinh_anh));
            }

            DB::table('combo_dichvu')->where('id_combo', $id)->delete();
            DB::table('combo')->where('id_combo', $id)->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Đã xoá Combo và giải phóng liên kết!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }
}
