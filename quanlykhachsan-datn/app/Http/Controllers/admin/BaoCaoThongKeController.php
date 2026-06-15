<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\HoaDon;
use App\Models\DatPhong;
use App\Models\Phong;
use App\Models\BangLuong;
use App\Models\SuDungDichVu;
use App\Models\DichVu;
use App\Models\NhanVien;

class BaoCaoThongKeController extends Controller
{
    public function index(Request $request)
    {
        $kieuLoc = $request->input('kieu_loc', 'nam');
        $giaTriLoc = $request->input('gia_tri_loc', date('Y'));

        // ================= 1. DỮ LIỆU TỔNG QUAN (GIỐNG DASHBOARD MÀ THEO BỘ LỌC) =================
        $qDoanhThuTong = HoaDon::query();
        $qDatPhongTong = DatPhong::where('trang_thai', '!=', 'Đã hủy');

        $this->applyDateFilter($qDoanhThuTong, $kieuLoc, $giaTriLoc, 'ngay_xuat');
        $this->applyDateFilter($qDatPhongTong, $kieuLoc, $giaTriLoc, 'ngay_nhan');

        $tongDoanhThuKy = $qDoanhThuTong->sum('tong_tien');
        $tongDonKy = $qDatPhongTong->count();

        // Real-time metrics
        $tongPhong = Phong::count();
        $phongTrong = Phong::where('trang_thai', 'Trống')->count();
        $phongDangThue = Phong::where('trang_thai', 'Đang có khách')->count();

        // ================= 2. DOANH THU & KHẤU HAO (ĐIỀN ĐẦY DỮ LIỆU TRỐNG) =================
        // Tính chi phí lương và giá vốn dịch vụ theo kỳ lọc thực tế
        $salaryCostByPeriod = $this->buildSalaryCostByPeriod($kieuLoc, $giaTriLoc);
        $serviceCostByPeriod = $this->buildServiceCostByPeriod($kieuLoc, $giaTriLoc);

        $queryDoanhThu = HoaDon::query();
        $this->applyDateFilter($queryDoanhThu, $kieuLoc, $giaTriLoc, 'ngay_xuat');

        $formatGroup = "";
        if ($kieuLoc == 'nam') $formatGroup = "%m";
        elseif ($kieuLoc == 'thang') $formatGroup = "%d";
        elseif ($kieuLoc == 'ngay') $formatGroup = "%H";
        elseif ($kieuLoc == 'quy') $formatGroup = "%m";

        $rawData = $queryDoanhThu
            ->select(DB::raw("DATE_FORMAT(ngay_xuat, '$formatGroup') as thoi_gian"), DB::raw("SUM(tong_tien) as doanh_thu_goc"))
            ->groupBy('thoi_gian')->pluck('doanh_thu_goc', 'thoi_gian')->toArray();

        $doanhThuData = [];
        $chiPhiLuongTB = BangLuong::avg('tong_luong') ?: 0;

        if ($kieuLoc == 'nam') {
            for ($i = 1; $i <= 12; $i++) {
                $thoiGian = sprintf('%02d', $i);
                $dtGoc = $rawData[$thoiGian] ?? 0;
                $cpLuong = $salaryCostByPeriod[$thoiGian] ?? 0;
                $cpDichVu = $serviceCostByPeriod[$thoiGian] ?? 0;
                $cpVanHanh = $dtGoc * 0.10;
                $cp = $cpLuong + $cpDichVu + $cpVanHanh;
                $doanhThuData[] = (object)[
                    'thoi_gian' => "Tháng $i",
                    'doanh_thu_goc' => $dtGoc,
                    'chi_phi_luong' => $cpLuong,
                    'chi_phi_dich_vu' => $cpDichVu,
                    'chi_phi_van_hanh' => $cpVanHanh,
                    'doanh_thu_rong' => $dtGoc - $cp
                ];
            }
        } elseif ($kieuLoc == 'quy') {
            $parts = explode('-Q', $giaTriLoc);
            $q = (int)($parts[1] ?? 1);
            $start = ($q - 1) * 3 + 1;
            for ($i = $start; $i <= $start + 2; $i++) {
                $thoiGian = sprintf('%02d', $i);
                $dtGoc = $rawData[$thoiGian] ?? 0;
                $cpLuong = $salaryCostByPeriod[$thoiGian] ?? 0;
                $cpDichVu = $serviceCostByPeriod[$thoiGian] ?? 0;
                $cpVanHanh = $dtGoc * 0.10;
                $cp = $cpLuong + $cpDichVu + $cpVanHanh;
                $doanhThuData[] = (object)[
                    'thoi_gian' => "Tháng $i",
                    'doanh_thu_goc' => $dtGoc,
                    'chi_phi_luong' => $cpLuong,
                    'chi_phi_dich_vu' => $cpDichVu,
                    'chi_phi_van_hanh' => $cpVanHanh,
                    'doanh_thu_rong' => $dtGoc - $cp
                ];
            }
        } elseif ($kieuLoc == 'thang') {
            [$year, $month] = $this->normalizeYearMonth($giaTriLoc);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $thoiGian = sprintf('%02d', $i);
                $dtGoc = $rawData[$thoiGian] ?? 0;
                $cpLuong = $salaryCostByPeriod[$thoiGian] ?? 0;
                $cpDichVu = $serviceCostByPeriod[$thoiGian] ?? 0;
                $cpVanHanh = $dtGoc * 0.10;
                $cp = $cpLuong + $cpDichVu + $cpVanHanh;
                $doanhThuData[] = (object)[
                    'thoi_gian' => "Ngày $i",
                    'doanh_thu_goc' => $dtGoc,
                    'chi_phi_luong' => $cpLuong,
                    'chi_phi_dich_vu' => $cpDichVu,
                    'chi_phi_van_hanh' => $cpVanHanh,
                    'doanh_thu_rong' => $dtGoc - $cp
                ];
            }
        } elseif ($kieuLoc == 'ngay') {
            for ($i = 0; $i <= 23; $i++) {
                $thoiGian = sprintf('%02d', $i);
                $dtGoc = $rawData[$thoiGian] ?? 0;
                $cpLuong = $salaryCostByPeriod[$thoiGian] ?? 0;
                $cpDichVu = $serviceCostByPeriod[$thoiGian] ?? 0;
                $cpVanHanh = $dtGoc * 0.10;
                $cp = $cpLuong + $cpDichVu + $cpVanHanh;
                $doanhThuData[] = (object)[
                    'thoi_gian' => "$i:00",
                    'doanh_thu_goc' => $dtGoc,
                    'chi_phi_luong' => $cpLuong,
                    'chi_phi_dich_vu' => $cpDichVu,
                    'chi_phi_van_hanh' => $cpVanHanh,
                    'doanh_thu_rong' => $dtGoc - $cp
                ];
            }
        }
        $doanhThuData = collect($doanhThuData);

        // ================= [THÊM MỚI] THỐNG KÊ LƯỢNG KHÁCH (TỔNG & MỚI THEO KỲ) =================
        $qAllKhach = DatPhong::query();
        $this->applyDateFilter($qAllKhach, $kieuLoc, $giaTriLoc, 'ngay_nhan');
        $rawKhach = $qAllKhach->select(DB::raw("DATE_FORMAT(ngay_nhan, '$formatGroup') as thoi_gian"), DB::raw("COUNT(DISTINCT id_khachhang) as tong_khach"))
            ->groupBy('thoi_gian')->pluck('tong_khach', 'thoi_gian')->toArray();

        // Subquery lấy ngày đặt phòng đầu tiên của mỗi khách hàng
        $firstBookings = DatPhong::select('id_khachhang', DB::raw('MIN(ngay_nhan) as first_date'))
            ->groupBy('id_khachhang');

        $qKhachMoi = DB::query()->fromSub($firstBookings, 'first_bookings');
        $this->applyDateFilter($qKhachMoi, $kieuLoc, $giaTriLoc, 'first_date');
        $rawKhachMoi = $qKhachMoi->select(DB::raw("DATE_FORMAT(first_date, '$formatGroup') as thoi_gian"), DB::raw("COUNT(DISTINCT id_khachhang) as khach_moi"))
            ->groupBy('thoi_gian')->pluck('khach_moi', 'thoi_gian')->toArray();

        // Gắn dữ liệu khách hàng vào mảng chung để vẽ biểu đồ đồng bộ
        $doanhThuData->transform(function ($item) use ($rawKhach, $rawKhachMoi) {
            preg_match('/\d+/', $item->thoi_gian, $matches);
            $key = sprintf('%02d', $matches[0] ?? 0);
            $item->tong_khach = $rawKhach[$key] ?? 0;
            $item->khach_moi = $rawKhachMoi[$key] ?? 0;
            return $item;
        });

        // ================= 3. TRẠNG THÁI PHÒNG (DỰA TRÊN ĐẶT PHÒNG THỰC TẾ TRONG KỲ) =================
        $bookedRoomsQuery = DatPhong::join('phong', 'datphong.id_phong', '=', 'phong.id_phong')->select('phong.so_phong')->distinct();
        $this->applyDateFilter($bookedRoomsQuery, $kieuLoc, $giaTriLoc, 'datphong.ngay_nhan');
        $phongCoKhachList = $bookedRoomsQuery->pluck('so_phong')->toArray();
        $phongTrongList = Phong::whereNotIn('so_phong', $phongCoKhachList)->pluck('so_phong')->toArray();

        $phongLopDay = count($phongCoKhachList);
        $phongTrongTheoKy = $tongPhong - $phongLopDay;

        // ================= 4. TẦN SUẤT DỊCH VỤ =================
        $tanSuatDichVu = SuDungDichVu::join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->join('datphong', 'sudungdichvu.id_datphong', '=', 'datphong.id_datphong')
            ->select('dichvu.ten_dich_vu', DB::raw('SUM(sudungdichvu.so_luong) as tong_so_luong'))
            ->groupBy('dichvu.ten_dich_vu');
        $this->applyDateFilter($tanSuatDichVu, $kieuLoc, $giaTriLoc, 'datphong.ngay_nhan');
        $tanSuatDichVu = $tanSuatDichVu->orderBy('tong_so_luong', 'desc')->take(5)->get();

        // ================= 4.1 DOANH THU THEO DỊCH VỤ =================
        $doanhThuDichVu = SuDungDichVu::join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->join('datphong', 'sudungdichvu.id_datphong', '=', 'datphong.id_datphong')
            ->select('dichvu.ten_dich_vu', DB::raw('SUM(sudungdichvu.so_luong * dichvu.gia) as doanh_thu'))
            ->groupBy('dichvu.ten_dich_vu');
        $this->applyDateFilter($doanhThuDichVu, $kieuLoc, $giaTriLoc, 'datphong.ngay_nhan');
        $doanhThuDichVu = $doanhThuDichVu->orderBy('doanh_thu', 'desc')->take(6)->get();

        // ================= 5. TOP PHÒNG ĐẮT KHÁCH =================
        $tanSuatPhong = DatPhong::join('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->select('phong.so_phong', DB::raw('COUNT(datphong.id_datphong) as so_lan_dat'))
            ->groupBy('phong.so_phong');
        $this->applyDateFilter($tanSuatPhong, $kieuLoc, $giaTriLoc, 'datphong.ngay_nhan');
        $tanSuatPhong = $tanSuatPhong->orderBy('so_lan_dat', 'desc')->take(5)->get();

        // ================= 6. HÀNH VI KHÁCH QUAY LẠI =================
        $khachHangTheoLuot = DatPhong::select('id_khachhang', DB::raw('COUNT(id_datphong) as so_lan_o'))->groupBy('id_khachhang');
        $this->applyDateFilter($khachHangTheoLuot, $kieuLoc, $giaTriLoc, 'ngay_nhan');
        $khachHangTheoLuot = $khachHangTheoLuot->get();

        $khachMotLan = $khachHangTheoLuot->where('so_lan_o', 1)->count();
        $khachQuayLai = $khachHangTheoLuot->where('so_lan_o', '>', 1)->count();

        // ================= 7. PHÂN KHÚC KHÁCH HÀNG =================
        $chiTieuKhachHang = DatPhong::leftJoin('hoadon', 'datphong.id_datphong', '=', 'hoadon.id_datphong')
            ->select('datphong.id_khachhang', DB::raw('SUM(hoadon.tong_tien) as tong_chi_tieu'), DB::raw('COUNT(datphong.id_datphong) as so_lan'))
            ->groupBy('datphong.id_khachhang');
        $this->applyDateFilter($chiTieuKhachHang, $kieuLoc, $giaTriLoc, 'datphong.ngay_nhan');
        $chiTieuKhachHang = $chiTieuKhachHang->get();

        $khachVIP = 0; $khachThuong = 0; $khachItQuayLai = 0;
        foreach($chiTieuKhachHang as $kh) {
            if($kh->tong_chi_tieu >= 10000000 || $kh->so_lan >= 5) $khachVIP++;
            elseif($kh->so_lan >= 2) $khachThuong++;
            else $khachItQuayLai++;
        }

        // ================= 8. CƠ CẤU NHÂN SỰ =================
        $nhanSuChucVu = NhanVien::select('chuc_vu', DB::raw('COUNT(*) as so_luong'))->groupBy('chuc_vu')->get();
        $tongNhanVien = NhanVien::count();
        $soChucVu = $nhanSuChucVu->count();

        return view('admin.baocao', compact(
            'kieuLoc', 'giaTriLoc', 'doanhThuData',
            'tongPhong', 'phongTrong', 'phongDangThue', 'tongDoanhThuKy', 'tongDonKy',
            'phongLopDay', 'phongTrongTheoKy', 'phongCoKhachList', 'phongTrongList',
            'tanSuatDichVu', 'doanhThuDichVu', 'tanSuatPhong',
            'khachMotLan', 'khachQuayLai',
            'khachVIP', 'khachThuong', 'khachItQuayLai',
            'nhanSuChucVu', 'tongNhanVien', 'soChucVu'
        ));
    }

    public function getForecastData(Request $request)
    {
        $target = $request->input('target'); // Định dạng: YYYY-MM

        if (!$target) {
            return response()->json(['error' => 'Vui lòng chọn thời gian để dự báo.']);
        }

        $targetDate = Carbon::createFromFormat('Y-m', $target)->startOfMonth();
        $now = Carbon::now()->startOfMonth();

        // Chặn chọn quá khứ ở Backend
        if ($targetDate->lessThan($now)) {
            return response()->json(['error' => 'Không thể dự báo cho thời gian trong quá khứ!']);
        }

        $month = $targetDate->month;
        $year = $targetDate->year;
        $daysInMonth = $targetDate->daysInMonth();
        $totalRooms = Phong::count() ?: 1;
        $availableRoomNights = $totalRooms * $daysInMonth;

            $historicalData = DatPhong::whereMonth('ngay_nhan', $month)
            ->whereMonth('ngay_nhan', $month)
            ->whereYear('ngay_nhan', '<', $year)
            ->where('trang_thai', '!=', 'Đã hủy')
            ->select(
                DB::raw('YEAR(ngay_nhan) as year'),
                DB::raw('SUM(DATEDIFF(ngay_tra, ngay_nhan)) as occupied_nights'),
                DB::raw('SUM(tong_tien_phai_tra) as revenue'),
                DB::raw('COUNT(*) as bookings')
            )
            ->groupBy(DB::raw('YEAR(ngay_nhan)'))
            ->orderBy('year', 'desc')
            ->get();

        $yearsObserved = $historicalData->count();
        $performance = [];

        foreach ($historicalData as $row) {
            $occupiedNights = (int) $row->occupied_nights;
            if ($occupiedNights === 0) {
                continue;
            }

            $occupancyRate = min(100, round($occupiedNights / $availableRoomNights * 100, 2));
            $adr = round($row->revenue / $occupiedNights, 0);
            $revpar = round($adr * ($occupancyRate / 100), 0);

            $performance[] = [
                'occupancy_rate' => $occupancyRate,
                'adr' => $adr,
                'revpar' => $revpar,
                'bookings' => (int) $row->bookings,
                'occupied_nights' => $occupiedNights,
                'revenue' => (float) $row->revenue,
            ];
        }

        if (count($performance) > 0) {
            $averageOccupancyRate = round(array_sum(array_column($performance, 'occupancy_rate')) / count($performance), 2);
            $averageADR = round(array_sum(array_column($performance, 'adr')) / count($performance), 0);
            $averageRevPAR = round(array_sum(array_column($performance, 'revpar')) / count($performance), 0);
            $averageBookings = round(array_sum(array_column($performance, 'bookings')) / count($performance), 0);
            $historyText = "Dựa trên dữ liệu cùng tháng của {$yearsObserved} năm trước.";
        } else {
            $fallback = DatPhong::query()
                ->where('ngay_nhan', '<', $targetDate)
                ->where('trang_thai', '!=', 'Đã hủy')
                ->select(
                    DB::raw('SUM(DATEDIFF(ngay_tra, ngay_nhan)) as occupied_nights'),
                    DB::raw('SUM(tong_tien_phai_tra) as revenue'),
                    DB::raw('COUNT(*) as bookings')
                )
                ->first();

            $yearsObserved = 0;
            $occupiedNights = (int) $fallback->occupied_nights;
            $averageOccupancyRate = $occupiedNights ? min(100, round($occupiedNights / ($totalRooms * 180) * 100, 2)) : 40;
            $averageADR = $occupiedNights ? round($fallback->revenue / $occupiedNights, 0) : 120000;
            $averageRevPAR = round($averageADR * ($averageOccupancyRate / 100), 0);
            $averageBookings = max(1, (int) $fallback->bookings);
            $historyText = 'Dựa trên dữ liệu tương tự trong quá khứ vì chưa có đủ mẫu cùng tháng.';
        }

        $forecastRoomNights = round($availableRoomNights * ($averageOccupancyRate / 100));
        $forecastRevenue = round($averageRevPAR * $availableRoomNights);
        $avgNightsPerBooking = $averageBookings > 0 ? max(1, round($forecastRoomNights / $averageBookings, 1)) : 1;
        $forecastBookings = max(1, round($forecastRoomNights / $avgNightsPerBooking));

        if ($averageOccupancyRate >= 90) {
            $recommendation = 'Dự báo rất cao, cần chuẩn bị thêm nhân sự lễ tân, dọn phòng nhanh và cân nhắc chính sách giá mùa cao điểm.';
        } elseif ($averageOccupancyRate >= 75) {
            $recommendation = 'Dự báo cao, nên tối ưu upsell dịch vụ và duy trì chất lượng phục vụ để tận dụng luồng khách.';
        } elseif ($averageOccupancyRate >= 55) {
            $recommendation = 'Dự báo trung bình, có thể giữ chiến lược hiện tại và kích cầu thêm gói nghỉ dưỡng.';
        } else {
            $recommendation = 'Dự báo thấp, nên tăng cường khuyến mãi và các gói gia đình/đặc biệt ngoài mùa.';
        }

        $note = "Tháng {$targetDate->format('m/Y')} dự kiến có {$forecastBookings} lượt đặt phòng, tương đương {$forecastRoomNights} đêm phòng. "
            . "Occupancy Rate trung bình {$averageOccupancyRate}% - ADR trung bình " . number_format($averageADR, 0, ',', '.') . " đ - RevPAR " . number_format($averageRevPAR, 0, ',', '.') . " đ. {$historyText} {$recommendation}";

        return response()->json([
            'label' => 'THÁNG ' . $targetDate->format('m/Y'),
            'value' => number_format($forecastBookings, 0, ',', '.') . ' lượt đặt phòng',
            'note' => $note,
            'recommendation' => $recommendation,
            'occupancy_rate' => $averageOccupancyRate . '%',
            'adr' => number_format($averageADR, 0, ',', '.') . ' đ',
            'revpar' => number_format($averageRevPAR, 0, ',', '.') . ' đ',
            'forecast_room_nights' => number_format($forecastRoomNights, 0, ',', '.') . ' đêm',
            'forecast_revenue' => number_format($forecastRevenue, 0, ',', '.') . ' đ',
            'years_observed' => $yearsObserved,
        ]);
    }

    public function getComparisonData(Request $request)
    {
        $loai = $request->input('loai');
        $kieu = $request->input('kieu');
        $val1 = $request->input('val1');
        $val2 = $request->input('val2');

        $data1 = $this->calculatePeriodData($loai, $kieu, $val1);
        $data2 = $this->calculatePeriodData($loai, $kieu, $val2);

        return response()->json([
            'moc_1' => ['nhan' => $val1, 'gia_tri' => $data1['value'], 'chi_tiet' => $data1['detail']],
            'moc_2' => ['nhan' => $val2, 'gia_tri' => $data2['value'], 'chi_tiet' => $data2['detail']],
            'chenh_lech' => $data2['value'] - $data1['value'],
            'phan_tram' => $data1['value'] > 0 ? round((($data2['value'] - $data1['value']) / $data1['value']) * 100, 2) : 0
        ]);
    }

    private function calculatePeriodData($loai, $kieu, $value)
    {
        $queryInvoice = HoaDon::query();
        $queryBooking = DatPhong::query();

        $this->applyDateFilter($queryInvoice, $kieu, $value, 'ngay_xuat');
        $this->applyDateFilter($queryBooking, $kieu, $value, 'ngay_nhan');

        if ($loai == 'doanh_thu') {
            $total = $queryInvoice->sum('tong_tien') ?: 0;
            return ['value' => $total, 'detail' => number_format($total, 0, ',', '.') . ' đ'];
        } elseif ($loai == 'phong') {
            $count = $queryBooking->count();
            return ['value' => $count, 'detail' => $count . ' lượt check-in'];
        } elseif ($loai == 'dich_vu') {
            $countDv = SuDungDichVu::whereIn('id_datphong', $queryBooking->pluck('id_datphong'))->sum('so_luong') ?: 0;
            return ['value' => $countDv, 'detail' => $countDv . ' lượt order'];
        } else {
            $countKh = $queryBooking->distinct('id_khachhang')->count('id_khachhang');
            return ['value' => $countKh, 'detail' => $countKh . ' khách giao dịch'];
        }
    }

    private function applyDateFilter($query, $kieuLoc, $giaTriLoc, $dateColumn)
    {
        if ($kieuLoc === 'nam') {
            return $query->whereYear($dateColumn, $giaTriLoc);
        }
        if ($kieuLoc === 'thang') {
            [$year, $month] = $this->normalizeYearMonth($giaTriLoc);
            return $query->whereYear($dateColumn, $year)->whereMonth($dateColumn, $month);
        }
        if ($kieuLoc === 'ngay') {
            return $query->whereDate($dateColumn, $giaTriLoc);
        }
        if ($kieuLoc === 'quy') {
            $parts = explode('-Q', $giaTriLoc);
            $year = $parts[0] ?? date('Y');
            $quarter = (int) ($parts[1] ?? 1);
            $startMonth = ($quarter - 1) * 3 + 1;
            return $query->whereYear($dateColumn, $year)->whereBetween(DB::raw("MONTH($dateColumn)"), [$startMonth, $startMonth + 2]);
        }
        return $query;
    }

    private function normalizeYearMonth($giaTriLoc)
    {
        if (is_string($giaTriLoc) && preg_match('/^(\d{4})-(\d{1,2})$/', $giaTriLoc, $matches)) {
            $year = (int) $matches[1];
            $month = (int) $matches[2];
            if (checkdate($month, 1, $year)) {
                return [$year, $month];
            }
        }
        return [(int) date('Y'), (int) date('m')];
    }

    private function parseDateOrToday($format, $value)
    {
        try {
            $date = Carbon::createFromFormat($format, $value);
            if ($date && $date->format($format) === $value) {
                return $date;
            }
        } catch (\Exception $e) {
        }

        return Carbon::today();
    }

    private function buildSalaryCostByPeriod($kieuLoc, $giaTriLoc)
    {
        $result = [];

        if ($kieuLoc === 'nam') {
            $year = (int) $giaTriLoc;
            $result = BangLuong::selectRaw("LPAD(thang, 2, '0') as period, SUM(tong_luong) as total")
                ->where('nam', $year)
                ->groupBy('thang')
                ->pluck('total', 'period')
                ->toArray();
        } elseif ($kieuLoc === 'quy') {
            $parts = explode('-Q', $giaTriLoc);
            $year = (int) ($parts[0] ?? date('Y'));
            $quarter = (int) ($parts[1] ?? 1);
            $start = ($quarter - 1) * 3 + 1;
            $rows = BangLuong::selectRaw("LPAD(thang, 2, '0') as period, SUM(tong_luong) as total")
                ->where('nam', $year)
                ->whereBetween('thang', [$start, $start + 2])
                ->groupBy('thang')
                ->pluck('total', 'period')
                ->toArray();
            for ($month = $start; $month <= $start + 2; $month++) {
                $key = sprintf('%02d', $month);
                $result[$key] = $rows[$key] ?? 0;
            }
        } elseif ($kieuLoc === 'thang') {
            [$year, $month] = $this->normalizeYearMonth($giaTriLoc);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $monthlyTotal = BangLuong::where('nam', $year)
                ->where('thang', $month)
                ->sum('tong_luong');
            $dailyAvg = $daysInMonth ? $monthlyTotal / $daysInMonth : 0;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $result[sprintf('%02d', $day)] = $dailyAvg;
            }
        } else {
            $date = $this->parseDateOrToday('Y-m-d', $giaTriLoc);
            $year = $date->year;
            $month = $date->month;
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $monthlyTotal = BangLuong::where('nam', $year)
                ->where('thang', $month)
                ->sum('tong_luong');
            $hourlyAvg = $daysInMonth ? $monthlyTotal / ($daysInMonth * 24) : 0;
            for ($hour = 0; $hour <= 23; $hour++) {
                $result[sprintf('%02d', $hour)] = $hourlyAvg;
            }
        }

        return $result;
    }

    private function buildServiceCostByPeriod($kieuLoc, $giaTriLoc)
    {
        $result = [];

        if ($kieuLoc === 'nam') {
            $year = (int) $giaTriLoc;
            $rows = SuDungDichVu::join('datphong', 'sudungdichvu.id_datphong', '=', 'datphong.id_datphong')
                ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
                ->selectRaw("LPAD(MONTH(datphong.ngay_nhan), 2, '0') as period, SUM(sudungdichvu.so_luong * dichvu.gia) * 0.5 as total")
                ->whereYear('datphong.ngay_nhan', $year)
                ->groupBy(DB::raw("LPAD(MONTH(datphong.ngay_nhan), 2, '0')"))
                ->pluck('total', 'period')
                ->toArray();
            for ($month = 1; $month <= 12; $month++) {
                $result[sprintf('%02d', $month)] = $rows[sprintf('%02d', $month)] ?? 0;
            }
        } elseif ($kieuLoc === 'quy') {
            $parts = explode('-Q', $giaTriLoc);
            $year = (int) ($parts[0] ?? date('Y'));
            $quarter = (int) ($parts[1] ?? 1);
            $start = ($quarter - 1) * 3 + 1;
            $rows = SuDungDichVu::join('datphong', 'sudungdichvu.id_datphong', '=', 'datphong.id_datphong')
                ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
                ->selectRaw("LPAD(MONTH(datphong.ngay_nhan), 2, '0') as period, SUM(sudungdichvu.so_luong * dichvu.gia) * 0.5 as total")
                ->whereYear('datphong.ngay_nhan', $year)
                ->whereBetween(DB::raw('MONTH(datphong.ngay_nhan)'), [$start, $start + 2])
                ->groupBy(DB::raw("LPAD(MONTH(datphong.ngay_nhan), 2, '0')"))
                ->pluck('total', 'period')
                ->toArray();
            for ($month = $start; $month <= $start + 2; $month++) {
                $result[sprintf('%02d', $month)] = $rows[sprintf('%02d', $month)] ?? 0;
            }
        } elseif ($kieuLoc === 'thang') {
            [$year, $month] = $this->normalizeYearMonth($giaTriLoc);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $rows = SuDungDichVu::join('datphong', 'sudungdichvu.id_datphong', '=', 'datphong.id_datphong')
                ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
                ->selectRaw("LPAD(DAY(datphong.ngay_nhan), 2, '0') as period, SUM(sudungdichvu.so_luong * dichvu.gia) * 0.5 as total")
                ->whereYear('datphong.ngay_nhan', $year)
                ->whereMonth('datphong.ngay_nhan', $month)
                ->groupBy(DB::raw("LPAD(DAY(datphong.ngay_nhan), 2, '0')"))
                ->pluck('total', 'period')
                ->toArray();
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $result[sprintf('%02d', $day)] = $rows[sprintf('%02d', $day)] ?? 0;
            }
        } else {
            $date = $this->parseDateOrToday('Y-m-d', $giaTriLoc)->toDateString();
            $total = SuDungDichVu::join('datphong', 'sudungdichvu.id_datphong', '=', 'datphong.id_datphong')
                ->join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
                ->whereDate('datphong.ngay_nhan', $date)
                ->sum(DB::raw('sudungdichvu.so_luong * dichvu.gia')) * 0.5;
            $hourlyAvg = $total / 24;
            for ($hour = 0; $hour <= 23; $hour++) {
                $result[sprintf('%02d', $hour)] = $hourlyAvg;
            }
        }

        return $result;
    }
}
