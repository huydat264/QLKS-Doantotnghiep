<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Phong;
use App\Models\DatPhong;
use App\Models\ThanhToan;
use App\Models\KhachHang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. DỮ LIỆU THÈ TỔNG QUAN (KPI CARDS)
        $tongPhong = Phong::count();
        $phongTrong = Phong::where('trang_thai', 'Trống')->count();
        $phongDangThue = Phong::where('trang_thai', 'Đã đặt')->count();
        $datPhongHomNay = DatPhong::whereDate('ngay_dat', $today)->count();
        $doanhThuHomNay = ThanhToan::whereDate('ngay_thanh_toan', $today)->sum('so_tien');

        // 2. TÌNH TRẠNG PHÒNG (Group theo Tầng)
        $phongs = Phong::all()->groupBy(function($phong) {
            return substr($phong->so_phong, 0, 1);
        });

        // 3. ĐẶT PHÒNG GẦN ĐÂY (Lấy 5 đơn mới nhất)
        $datPhongGanDay = DatPhong::with(['khachhang', 'phong'])
            ->orderBy('ngay_dat', 'desc')
            ->limit(5)
            ->get();

        // 4. HỆ THỐNG THÔNG BÁO HOẠT ĐỘNG / THAO TÁC NHÂN VIÊN QUAN TRỌNG
        // Thao tác 1: Nhân viên đặt phòng hoặc cập nhật trạng thái đơn đặt
        $logsDatPhong = DatPhong::with('khachhang')
            ->orderBy('ngay_dat', 'desc')
            ->limit(3)
            ->get()
            ->map(function($item) {
                return [
                    'icon' => 'bi-calendar-check-fill text-primary',
                    'tieu_de' => 'Thao tác Đặt phòng',
                    'noi_dung' => 'Xử lý đơn #' . $item->id_datphong . ' cho khách ' . ($item->khachhang->ho_ten ?? 'Ẩn danh') . ' (Trạng thái: ' . $item->trang_thai . ')',
                    'thoi_gian' => Carbon::parse($item->ngay_dat)
                ];
            });

        // Thao tác 2: Nhân viên nhập thông tin / gọi thêm dịch vụ cho khách
        $logsDichVu = DB::table('sudungdichvu')
            ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->select('dichvu.ten_dich_vu', 'sudungdichvu.so_luong', 'sudungdichvu.id_sudungdv')
            ->orderBy('sudungdichvu.id_sudungdv', 'desc')
            ->limit(3)
            ->get()
            ->map(function($item) {
                return [
                    'icon' => 'bi-box-seam-fill text-info',
                    'tieu_de' => 'Thao tác Dịch vụ',
                    'noi_dung' => 'Nhân viên thêm dịch vụ: ' . $item->ten_dich_vu . ' (Số lượng: ' . $item->so_luong . ')',
                    'thoi_gian' => Carbon::now()->subSeconds($item->id_sudungdv)
                ];
            });

        // Thao tác 3: Nhân viên thực hiện thanh toán hóa đơn / Checkout xuất phòng
        $logsThanhToan = ThanhToan::orderBy('ngay_thanh_toan', 'desc')
            ->limit(3)
            ->get()
            ->map(function($item) {
                return [
                    'icon' => 'bi-credit-card-2-back-fill text-success',
                    'tieu_de' => 'Thao tác Checkout',
                    'noi_dung' => 'Thanh toán hóa đơn thu về số tiền ' . number_format($item->so_tien, 0, ',', '.') . ' đ',
                    'thoi_gian' => Carbon::parse($item->ngay_thanh_toan)
                ];
            });

        // Trộn tất cả thao tác lại, sắp xếp theo thời gian thực mới nhất để đẩy lên thông báo
        $thongBaoHeThong = collect()
            ->merge($logsDatPhong)
            ->merge($logsDichVu)
            ->merge($logsThanhToan)
            ->sortByDesc('thoi_gian')
            ->take(5)
            ->map(function($item) {
                $item['thoi_gian_str'] = Carbon::parse($item['thoi_gian'])->diffForHumans();
                return $item;
            });

        // 5. BIỂU ĐỒ ĐƯỜNG: DOANH THU (Thống kê 7 ngày gần nhất)
        $dataDoanhThu = ThanhToan::selectRaw('DATE(ngay_thanh_toan) as ngay, SUM(so_tien) as tong_tien')
            ->where('ngay_thanh_toan', '>=', Carbon::now()->subDays(6))
            ->groupBy('ngay')
            ->orderBy('ngay', 'asc')
            ->get();

        $lineLabels = [];
        $lineValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = Carbon::now()->subDays($i)->format('Y-m-d');
            $lineLabels[] = Carbon::now()->subDays($i)->format('d/m');
            $find = $dataDoanhThu->firstWhere('ngay', $dateStr);
            $lineValues[] = $find ? (float)$find->tong_tien : 0;
        }

        // 6. BIỂU ĐỒ TRÒN: TỰ ĐỘNG LỌC DOANH THU DỊCH VỤ THEO THÁNG HIỆN TẠI
        $dataDichVuThang = DB::table('sudungdichvu')
            ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->selectRaw('dichvu.ten_dich_vu as ten_dich_vu, SUM(sudungdichvu.so_luong * dichvu.gia) as tong_tien_dv')
            ->groupBy('dichvu.ten_dich_vu')
            ->get();

        $pieLabels = $dataDichVuThang->pluck('ten_dich_vu')->toArray();
        $pieValues = $dataDichVuThang->pluck('tong_tien_dv')->toArray();

        // 7. TOP 5 KHÁCH HÀNG THÂN THIẾT
        $topKhachHang = DatPhong::selectRaw('id_khachhang, COUNT(id_datphong) as tong_don, SUM(tong_tien_phai_tra) as tong_chi_tieu')
            ->whereNotNull('id_khachhang')
            ->groupBy('id_khachhang')
            ->orderBy('tong_don', 'desc')
            ->limit(5)
            ->with('khachhang')
            ->get();

        return view('admin.dashboard', compact(
            'tongPhong', 'phongTrong', 'phongDangThue', 'datPhongHomNay', 'doanhThuHomNay',
            'phongs', 'datPhongGanDay', 'thongBaoHeThong',
            'lineLabels', 'lineValues', 'pieLabels', 'pieValues', 'topKhachHang'
        ));
    }
}
