<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BangLuongManagementController extends Controller
{
    // Hàm hỗ trợ tính Thuế TNCN (Barem chuẩn VN: Lũy tiến từng phần, giảm trừ bản thân 11tr)
    private function tinhThueTNCN($tongThuNhap)
    {
        $giamTruBanThan = 11000000;
        $thuNhapTinhThue = $tongThuNhap - $giamTruBanThan;

        if ($thuNhapTinhThue <= 0) return 0;

        // Công thức tính nhanh thuế TNCN
        if ($thuNhapTinhThue <= 5000000) {
            return $thuNhapTinhThue * 0.05;
        } elseif ($thuNhapTinhThue <= 10000000) {
            return ($thuNhapTinhThue * 0.10) - 250000;
        } elseif ($thuNhapTinhThue <= 18000000) {
            return ($thuNhapTinhThue * 0.15) - 750000;
        } elseif ($thuNhapTinhThue <= 32000000) {
            return ($thuNhapTinhThue * 0.20) - 1650000;
        } elseif ($thuNhapTinhThue <= 52000000) {
            return ($thuNhapTinhThue * 0.25) - 3250000;
        } elseif ($thuNhapTinhThue <= 80000000) {
            return ($thuNhapTinhThue * 0.30) - 5850000;
        } else {
            return ($thuNhapTinhThue * 0.35) - 9850000;
        }
    }

    // 1. Hiển thị danh sách (Tháng/Năm hoặc toàn bộ)
    public function index(Request $request)
    {
        // Nếu không có tham số lọc, hiển thị tất cả; nếu có thì lọc theo tháng/năm
        $thangLoc = $request->input('thangLoc');
        $namLoc = $request->input('namLoc');

        $query = DB::table('bangluong')
            ->join('nhanvien', 'bangluong.id_nhanvien', '=', 'nhanvien.id_nhanvien')
            ->select('bangluong.*', 'nhanvien.ho_ten', 'nhanvien.chuc_vu');

        // Chỉ lọc nếu có cả tham số thangLoc và namLoc
        if (!empty($thangLoc) && !empty($namLoc)) {
            $query->where('bangluong.thang', $thangLoc)
                  ->where('bangluong.nam', $namLoc);
        } else {
            // Nếu không có bộ lọc, mặc định hiển thị tháng/năm hiện tại cho giao diện
            $thangLoc = date('m');
            $namLoc = date('Y');
        }

        $danhSachBangLuong = $query->orderBy('bangluong.nam', 'desc')
                                    ->orderBy('bangluong.thang', 'desc')
                                    ->orderBy('bangluong.id_bangluong', 'desc')
                                    ->paginate(20);

        return view('admin.quanlybangluong', compact('danhSachBangLuong', 'thangLoc', 'namLoc'));
    }

    // 2. Tính lương tự động + Tính luôn Thuế TNCN
    public function calculate(Request $request)
    {
        $request->validate([
            'thang' => 'required|integer|min:1|max:12',
            'nam' => 'required|integer|min:2000'
        ]);

        $thang = $request->thang;
        $nam = $request->nam;

        $danhSachChamCong = DB::table('chamcong')->where('thang', $thang)->where('nam', $nam)->get();

        if ($danhSachChamCong->isEmpty()) {
            return redirect()->back()->with('error', "Tháng $thang/$nam chưa có dữ liệu chấm công nào!");
        }

        $count = 0;
        foreach ($danhSachChamCong as $cc) {
            $nhanVien = DB::table('nhanvien')->where('id_nhanvien', $cc->id_nhanvien)->first();
            if (!$nhanVien) continue;

            $daTonTai = DB::table('bangluong')
                ->where('id_nhanvien', $cc->id_nhanvien)
                ->where('thang', $thang)
                ->where('nam', $nam)
                ->exists();

            if (!$daTonTai) {
                $luongCoBan = $nhanVien->luong_co_ban ?? 0;
                $soNgayCong = $cc->so_ngay_di_lam;
                $thuong = 0;
                $phat = 0;

                // Tính lương theo ngày công
                $luongChinh = ($luongCoBan / 26) * $soNgayCong;
                $tongThuNhap = $luongChinh + $thuong; // Tổng thu nhập để tính thuế

                // Tính thuế tạm thu (Chỉ tính, chưa trừ)
                $thueTNCN = $this->tinhThueTNCN($tongThuNhap);

                // Tổng thực nhận: Theo ý mày là TỔNG không trừ thuế ở đây
                $tongLuongThucNhan = $luongChinh + $thuong - $phat;

                DB::table('bangluong')->insert([
                    'id_nhanvien' => $nhanVien->id_nhanvien,
                    'thang' => $thang,
                    'nam' => $nam,
                    'so_ngay_cong' => $soNgayCong,
                    'thuong' => $thuong,
                    'phat' => $phat,
                    'thue_tncn' => round($thueTNCN),
                    'luong_co_ban' => $luongCoBan,
                    'tong_luong' => round($tongLuongThucNhan)
                ]);
                $count++;
            }
        }

        if ($count > 0) {
            return redirect()->route('admin.bangluong.index', ['thangLoc' => $thang, 'namLoc' => $nam])
                             ->with('success', "Đã tạo bảng lương thành công cho $count nhân viên trong tháng $thang/$nam!");
        } else {
            return redirect()->back()->with('error', "Toàn bộ nhân viên có mặt trong tháng $thang/$nam đã được tính lương từ trước.");
        }
    }

    // 3. Cập nhật sửa lương (Tính lại thuế thời gian thực)
    public function update(Request $request, $id)
    {
        $request->validate([
            'luong_co_ban' => 'required|numeric|min:0',
            'thuong' => 'required|numeric|min:0',
            'phat' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $bangLuong = DB::table('bangluong')->where('id_bangluong', $id)->first();
            if (!$bangLuong) throw new \Exception("Không tìm thấy bảng lương.");

            $luongCoBanMoi = $request->luong_co_ban;
            $thuong = $request->thuong;
            $phat = $request->phat;
            $soNgayCong = $bangLuong->so_ngay_cong;

            // Tính toán lại
            $luongChinhMoi = ($luongCoBanMoi / 26) * $soNgayCong;
            $tongThuNhap = $luongChinhMoi + $thuong;

            $thueTNCN = $this->tinhThueTNCN($tongThuNhap);
            $tongLuongThucNhan = $luongChinhMoi + $thuong - $phat;

            // Cập nhật vào bảng Lương
            DB::table('bangluong')->where('id_bangluong', $id)->update([
                'luong_co_ban' => $luongCoBanMoi,
                'thuong' => $thuong,
                'phat' => $phat,
                'thue_tncn' => round($thueTNCN),
                'tong_luong' => round($tongLuongThucNhan)
            ]);

            // Cập nhật Lương Cơ Bản mới vào thẳng bảng Nhân viên
            DB::table('nhanvien')
                ->where('id_nhanvien', $bangLuong->id_nhanvien)
                ->update(['luong_co_ban' => $luongCoBanMoi]);

            DB::commit();
            return redirect()->back()->with('success', 'Đã cập nhật bảng lương, thuế TNCN và đồng bộ lương cơ bản thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
