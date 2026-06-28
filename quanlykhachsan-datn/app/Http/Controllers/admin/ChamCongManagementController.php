<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ChamCong;
use App\Models\NhanVien;

class ChamCongManagementController extends Controller
{
    // 1. Hiển thị danh sách bảng chấm công
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Join bảng chamcong với nhanvien để lấy tên nhân viên hiển thị
        $query = ChamCong::with('nhanvien')->select(
            'id_chamcong',
            'id_nhanvien',
            'thang',
            'nam',
            'so_ngay_di_lam',
            'so_ngay_nghi_khong_phep',
            'so_ngay_nghi_co_phep'
        );

        if (!empty($search)) {
            $query->whereHas('nhanvien', function ($q) use ($search) {
                $q->where('ho_ten', 'LIKE', "%{$search}%");
            })->orWhere('thang', 'LIKE', "%{$search}%")
              ->orWhere('nam', 'LIKE', "%{$search}%");
        }

        $danhSachChamCong = $query->orderBy('chamcong.nam', 'desc')
                                  ->orderBy('chamcong.thang', 'desc')
                                  ->orderBy('chamcong.id_chamcong', 'desc')
                                  ->paginate(20);

        // Lấy danh sách nhân viên đang làm việc để đổ vào Select Box lúc Thêm mới
        $danhSachNhanVien = NhanVien::select('id_nhanvien', 'ho_ten', 'chuc_vu')->get();

        return view('admin.quanlychamcong', compact('danhSachChamCong', 'search', 'danhSachNhanVien'));
    }

    // 2. Thêm mới bản ghi chấm công
    public function store(Request $request)
    {
        $request->validate([
            'id_nhanvien' => 'required|integer',
            'thang' => 'required|integer|min:1|max:12',
            'nam' => 'required|integer|min:2000',
            'so_ngay_di_lam' => 'required|numeric|min:0',
            'so_ngay_nghi_khong_phep' => 'required|numeric|min:0',
            'so_ngay_nghi_co_phep' => 'required|numeric|min:0',
        ]);

        // Logic chặn chấm công trùng lặp: Kiểm tra xem nhân viên này đã có điểm danh trong tháng/năm này chưa
        $isExist = ChamCong::where('id_nhanvien', $request->id_nhanvien)
            ->where('thang', $request->thang)
            ->where('nam', $request->nam)
            ->exists();

        if ($isExist) {
            return redirect()->back()->with('error', 'Nhân viên này đã được chấm công trong tháng ' . $request->thang . '/' . $request->nam . ' rồi!');
        }

        $nhanVien = NhanVien::find($request->id_nhanvien);
        if (! $nhanVien || ! $nhanVien->ngay_vao_lam) {
            return redirect()->back()->with('error', 'Không tìm thấy ngày vào làm của nhân viên để xác thực chấm công.');
        }

        $hireDate = Carbon::parse($nhanVien->ngay_vao_lam);
        $selectedMonth = Carbon::createFromDate($request->nam, $request->thang, 1);
        $firstWorkMonth = Carbon::createFromDate($hireDate->year, $hireDate->month, 1);

        if ($selectedMonth->lt($firstWorkMonth)) {
            return redirect()->back()->with('error', 'Không thể chấm công cho tháng trước ngày vào làm của nhân viên.');
        }

        if ($selectedMonth->eq($firstWorkMonth)) {
            $maxDaysInFirstMonth = $hireDate->copy()->endOfMonth()->day - $hireDate->day + 1;
            $recordedDays = $request->so_ngay_di_lam + $request->so_ngay_nghi_co_phep + $request->so_ngay_nghi_khong_phep;

            if ($recordedDays > $maxDaysInFirstMonth) {
                return redirect()->back()->with('error', 'Tháng đầu tiên làm việc chỉ được chấm tối đa ' . $maxDaysInFirstMonth . ' ngày từ ngày vào đến cuối tháng.');
            }
        }

        ChamCong::create([
            'id_nhanvien' => $request->id_nhanvien,
            'thang' => $request->thang,
            'nam' => $request->nam,
            'so_ngay_di_lam' => $request->so_ngay_di_lam,
            'so_ngay_nghi_khong_phep' => $request->so_ngay_nghi_khong_phep,
            'so_ngay_nghi_co_phep' => $request->so_ngay_nghi_co_phep,
        ]);

        return redirect()->back()->with('success', 'Đã lưu bản ghi chấm công mới thành công!');
    }

    // 3. Cập nhật sửa đổi điểm danh
    public function update(Request $request, $id)
    {
        $request->validate([
            'thang' => 'required|integer|min:1|max:12',
            'nam' => 'required|integer|min:2000',
            'so_ngay_di_lam' => 'required|numeric|min:0',
            'so_ngay_nghi_khong_phep' => 'required|numeric|min:0',
            'so_ngay_nghi_co_phep' => 'required|numeric|min:0',
        ]);

        // Kiểm tra trùng tháng/năm với bản ghi khác của cùng nhân viên đó (Trừ chính nó ra)
        $chamCongHienTai = ChamCong::findOrFail($id);
        $nhanVien = NhanVien::find($chamCongHienTai->id_nhanvien);

        if (! $nhanVien || ! $nhanVien->ngay_vao_lam) {
            return redirect()->back()->with('error', 'Không tìm thấy ngày vào làm của nhân viên để xác thực chấm công.');
        }

        $selectedMonth = Carbon::createFromDate($request->nam, $request->thang, 1);
        $hireDate = Carbon::parse($nhanVien->ngay_vao_lam);
        $firstWorkMonth = Carbon::createFromDate($hireDate->year, $hireDate->month, 1);

        if ($selectedMonth->lt($firstWorkMonth)) {
            return redirect()->back()->with('error', 'Không thể chấm công cho tháng trước ngày vào làm của nhân viên.');
        }

        if ($selectedMonth->eq($firstWorkMonth)) {
            $maxDaysInFirstMonth = $hireDate->copy()->endOfMonth()->day - $hireDate->day + 1;
            $recordedDays = $request->so_ngay_di_lam + $request->so_ngay_nghi_co_phep + $request->so_ngay_nghi_khong_phep;

            if ($recordedDays > $maxDaysInFirstMonth) {
                return redirect()->back()->with('error', 'Tháng đầu tiên làm việc chỉ được chấm tối đa ' . $maxDaysInFirstMonth . ' ngày từ ngày vào đến cuối tháng.');
            }
        }

        $isExist = ChamCong::where('id_nhanvien', $chamCongHienTai->id_nhanvien)
            ->where('thang', $request->thang)
            ->where('nam', $request->nam)
            ->where('id_chamcong', '!=', $id)
            ->exists();

        if ($isExist) {
            return redirect()->back()->with('error', 'Lỗi: Bị trùng với một bản ghi chấm công khác của tháng ' . $request->thang . '/' . $request->nam);
        }

        $chamCongHienTai->update([
            'thang' => $request->thang,
            'nam' => $request->nam,
            'so_ngay_di_lam' => $request->so_ngay_di_lam,
            'so_ngay_nghi_khong_phep' => $request->so_ngay_nghi_khong_phep,
            'so_ngay_nghi_co_phep' => $request->so_ngay_nghi_co_phep,
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật lại thông tin chấm công!');
    }
}
