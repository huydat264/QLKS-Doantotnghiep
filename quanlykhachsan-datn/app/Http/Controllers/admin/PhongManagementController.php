<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Phong;
use App\Models\DatPhong;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PhongManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Phong::with(['datPhongHienTai.khachhang']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('so_phong', 'LIKE', "%{$search}%")
                  ->orWhere('loai_phong', 'LIKE', "%{$search}%");
            });
        }

        $danhSachPhong = $query->orderBy('so_phong', 'asc')->paginate(20);
        $now = Carbon::now();

        return view('admin.quanlyphong', compact('danhSachPhong', 'search', 'now'));
    }

    public function updateRoom(Request $request, $id)
    {
        $request->validate([
            'loai_phong' => 'required|string',
            'gia_phong' => 'required|numeric|min:0',
            'trang_thai' => 'required|string',
            'so_luong_nguoi' => 'required|integer|min:1',
            'mo_ta' => 'nullable|string',
            'tien_nghi' => 'nullable|string',
            'thong_tin_quan_trong' => 'nullable|string',
            'anh' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // File upload
            'anh_link' => 'nullable|url' // Chấp nhận link web dạng https://
        ]);

        $phong = Phong::findOrFail($id);

        $phong->loai_phong = $request->loai_phong;
        $phong->gia_phong = $request->gia_phong;
        $phong->trang_thai = $request->trang_thai;
        $phong->so_luong_nguoi = $request->so_luong_nguoi;
        $phong->mo_ta = $request->mo_ta;
        $phong->tien_nghi = $request->tien_nghi;
        $phong->thong_tin_quan_trong = $request->thong_tin_quan_trong;

        // LOGIC XỬ LÝ ẢNH THÔNG MINH (Ưu tiên File máy -> Link web)
        if ($request->hasFile('anh')) {
            // Xóa file cũ trong storage (nếu có và nó k phải link ngoài)
            if ($phong->anh && !filter_var($phong->anh, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($phong->anh)) {
                Storage::disk('public')->delete($phong->anh);
            }
            $phong->anh = $request->file('anh')->store('phong', 'public');

        } elseif ($request->filled('anh_link')) {
            // Nếu người dùng dán link thay vì upload
            // Vẫn xóa file cũ đi cho đỡ nặng máy chủ
            if ($phong->anh && !filter_var($phong->anh, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($phong->anh)) {
                Storage::disk('public')->delete($phong->anh);
            }
            $phong->anh = $request->anh_link;
        }

        $phong->save();

        return redirect()->back()->with('success', 'Cập nhật chi tiết phòng thành công!');
    }

    public function giaiPhongPhong($id)
    {
        $phong = Phong::findOrFail($id);

        $datPhong = DatPhong::where('id_phong', $id)
                            ->whereIn('trang_thai', ['Đã đặt', 'Đã xác nhận', 'Trống', 'Bảo trì'])
                            ->first();

        if ($datPhong) {
            $datPhong->trang_thai = 'Đã hủy';
            $datPhong->save();
        }

        $phong->trang_thai = 'Trống';
        $phong->save();

        return redirect()->back()->with('success', 'Đã giải phóng phòng, hủy khách đặt và đưa phòng về trạng thái trống!');
    }

    public function applySale(Request $request)
    {
        $request->validate([
            'giam_gia_percent' => 'required|integer|min:0|max:100',
            'sale_tu_ngay' => 'required|date',
            'sale_den_ngay' => 'required|date|after_or_equal:sale_tu_ngay',
            'scope' => 'required|string'
        ]);

        $percent = $request->giam_gia_percent;
        $tuNgay = Carbon::parse($request->sale_tu_ngay)->startOfDay();
        $denNgay = Carbon::parse($request->sale_den_ngay)->endOfDay();

        if ($request->scope === 'all') {
            Phong::query()->update(['giam_gia_percent' => $percent, 'sale_tu_ngay' => $tuNgay, 'sale_den_ngay' => $denNgay]);
        } elseif ($request->scope === 'selected' || $request->scope === 'single') {
            $ids = $request->input('room_ids', []);
            if (empty($ids)) return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một phòng!');
            Phong::whereIn('id_phong', $ids)->update(['giam_gia_percent' => $percent, 'sale_tu_ngay' => $tuNgay, 'sale_den_ngay' => $denNgay]);
        }

        return redirect()->back()->with('success', 'Kích hoạt chương trình Sale thành công!');
    }
}
