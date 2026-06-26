<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    // ================= XUẤT PDF & EXCEL =================
    private function getFullReportData($kieuLoc, $giaTriLoc)
    {
        // Copy logic từ index() để lấy toàn bộ dữ liệu
        $qDoanhThuTong = HoaDon::query();
        $qDatPhongTong = DatPhong::where('trang_thai', '!=', 'Đã hủy');
        $this->applyDateFilter($qDoanhThuTong, $kieuLoc, $giaTriLoc, 'ngay_xuat');
        $this->applyDateFilter($qDatPhongTong, $kieuLoc, $giaTriLoc, 'ngay_nhan');

        $tongDoanhThuKy = $qDoanhThuTong->sum('tong_tien');
        $tongDonKy = $qDatPhongTong->count();
        $tongPhong = Phong::count();
        $phongTrong = Phong::where('trang_thai', 'Trống')->count();
        $phongDangThue = Phong::where('trang_thai', 'Đang có khách')->count();
        $tongNhanVien = NhanVien::count();

        // Tính lấp đầy
        $phongLopDay = max(0, $phongDangThue);
        $phongTrongTheoKy = max(0, $tongPhong - $phongDangThue);

        // Dữ liệu dịch vụ
        $tanSuatDichVu = SuDungDichVu::join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->join('datphong', 'sudungdichvu.id_datphong', '=', 'datphong.id_datphong')
            ->select('dichvu.ten_dich_vu', DB::raw('SUM(sudungdichvu.so_luong) as tong_so_luong'));
        $this->applyDateFilter($tanSuatDichVu, $kieuLoc, $giaTriLoc, 'datphong.ngay_nhan');
        $tanSuatDichVu = $tanSuatDichVu->groupBy('dichvu.id_dichvu', 'dichvu.ten_dich_vu')
            ->orderBy('tong_so_luong', 'desc')->limit(10)->get();

        $doanhThuDichVu = SuDungDichVu::join('dichvu', 'sudungdichvu.id_dichvu', '=', 'dichvu.id_dichvu')
            ->join('datphong', 'sudungdichvu.id_datphong', '=', 'datphong.id_datphong')
            ->select('dichvu.ten_dich_vu', DB::raw('SUM(sudungdichvu.thanh_tien) as doanh_thu'));
        $this->applyDateFilter($doanhThuDichVu, $kieuLoc, $giaTriLoc, 'datphong.ngay_nhan');
        $doanhThuDichVu = $doanhThuDichVu->groupBy('dichvu.id_dichvu', 'dichvu.ten_dich_vu')
            ->orderBy('doanh_thu', 'desc')->limit(10)->get();

        $tanSuatPhong = DatPhong::join('phong', 'datphong.id_phong', '=', 'phong.id_phong')
            ->select('phong.so_phong', DB::raw('COUNT(datphong.id_datphong) as so_lan_dat'));
        $this->applyDateFilter($tanSuatPhong, $kieuLoc, $giaTriLoc, 'datphong.ngay_nhan');
        $tanSuatPhong = $tanSuatPhong->groupBy('phong.id_phong', 'phong.so_phong')
            ->orderBy('so_lan_dat', 'desc')->limit(10)->get();

        // Khách hàng
        $chiTieuKhachHang = DatPhong::leftJoin('hoadon', 'datphong.id_datphong', '=', 'hoadon.id_datphong')
            ->select('datphong.id_khachhang', DB::raw('SUM(hoadon.tong_tien) as tong_chi_tieu'), DB::raw('COUNT(datphong.id_datphong) as so_lan'))
            ->groupBy('datphong.id_khachhang');
        $this->applyDateFilter($chiTieuKhachHang, $kieuLoc, $giaTriLoc, 'datphong.ngay_nhan');
        $chiTieuKhachHang = $chiTieuKhachHang->get();

        $khachVIP = 0; $khachThuong = 0; $khachItQuayLai = 0;
        $khachMotLan = 0; $khachQuayLai = 0;
        foreach($chiTieuKhachHang as $kh) {
            if($kh->tong_chi_tieu >= 10000000 || $kh->so_lan >= 5) $khachVIP++;
            elseif($kh->so_lan >= 2) $khachThuong++;
            else $khachItQuayLai++;

            if($kh->so_lan == 1) $khachMotLan++;
            else $khachQuayLai++;
        }

        return [
            'kieuLoc' => $kieuLoc,
            'giaTriLoc' => $giaTriLoc,
            'tongDoanhThuKy' => $tongDoanhThuKy,
            'tongDonKy' => $tongDonKy,
            'phongDangThue' => $phongDangThue,
            'tongPhong' => $tongPhong,
            'phongTrong' => $phongTrong,
            'tongNhanVien' => $tongNhanVien,
            'phongLopDay' => $phongLopDay,
            'phongTrongTheoKy' => $phongTrongTheoKy,
            'tanSuatDichVu' => $tanSuatDichVu,
            'doanhThuDichVu' => $doanhThuDichVu,
            'tanSuatPhong' => $tanSuatPhong,
            'khachVIP' => $khachVIP,
            'khachThuong' => $khachThuong,
            'khachItQuayLai' => $khachItQuayLai,
            'khachMotLan' => $khachMotLan,
            'khachQuayLai' => $khachQuayLai,
        ];
    }

    public function exportPDF(Request $request)
    {
        $kieuLoc = $request->input('kieu_loc', 'nam');
        $giaTriLoc = $request->input('gia_tri_loc', date('Y'));

        $data = $this->getFullReportData($kieuLoc, $giaTriLoc);

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Báo Cáo Thống Kê</title>
            <style>
                * { margin: 0; padding: 0; }
                body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { width: 100%; max-width: 900px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1e40af; padding-bottom: 20px; }
                .header h1 { color: #1e40af; font-size: 24px; margin-bottom: 5px; }
                .header p { color: #666; margin: 3px 0; }
                .section { margin: 30px 0; padding: 20px; background: #f8fafc; border-left: 5px solid #1e40af; page-break-inside: avoid; }
                .section h2 { color: #1e40af; font-size: 16px; margin-bottom: 15px; }
                .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 15px 0; }
                .stat-item { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
                .stat-label { color: #666; font-size: 13px; font-weight: bold; text-transform: uppercase; }
                .stat-value { color: #1e40af; font-size: 20px; font-weight: bold; margin-top: 5px; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; background: white; }
                th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
                th { background: #1e40af; color: white; font-weight: bold; }
                tr:nth-child(even) { background: #f1f5f9; }
                .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 2px solid #e2e8f0; color: #999; font-size: 12px; }
                @page { margin: 15mm; size: A4; }
                @media print { body { margin: 0; padding: 0; } .container { max-width: 100%; } }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📊 BÁO CÁO THỐNG KÊ KINH DOANH</h1>
                    <p><strong>Lọc theo:</strong> " . ($data['kieuLoc'] == 'nam' ? 'Năm' : ($data['kieuLoc'] == 'thang' ? 'Tháng' : ($data['kieuLoc'] == 'quy' ? 'Quý' : 'Ngày'))) . " | <strong>Kỳ:</strong> {$data['giaTriLoc']}</p>
                    <p><strong>Ngày xuất:</strong> " . date('d/m/Y H:i:s') . "</p>
                </div>

                <div class='section'>
                    <h2>📈 CHỈ TIÊU TỔNG QUAN</h2>
                    <div class='stat-grid'>
                        <div class='stat-item'>
                            <div class='stat-label'>💰 Doanh Thu Kỳ</div>
                            <div class='stat-value'>" . number_format($data['tongDoanhThuKy'], 0, ',', '.') . " đ</div>
                        </div>
                        <div class='stat-item'>
                            <div class='stat-label'>📝 Lượt Đặt Phòng</div>
                            <div class='stat-value'>{$data['tongDonKy']} đơn</div>
                        </div>
                        <div class='stat-item'>
                            <div class='stat-label'>🏨 Phòng Đang Thuê</div>
                            <div class='stat-value'>{$data['phongDangThue']} / {$data['tongPhong']}</div>
                        </div>
                        <div class='stat-item'>
                            <div class='stat-label'>👥 Nhân Viên Hoạt Động</div>
                            <div class='stat-value'>{$data['tongNhanVien']} người</div>
                        </div>
                        <div class='stat-item'>
                            <div class='stat-label'>📊 Tỷ Lệ Lấp Đầy</div>
                            <div class='stat-value'>" . ($data['tongPhong'] > 0 ? round($data['phongDangThue'] / $data['tongPhong'] * 100, 1) : 0) . "%</div>
                        </div>
                        <div class='stat-item'>
                            <div class='stat-label'>🏢 Phòng Trống</div>
                            <div class='stat-value'>{$data['phongTrong']}</div>
                        </div>
                    </div>
                </div>

                <div class='section'>
                    <h2>👥 PHÂN KHÚC KHÁCH HÀNG</h2>
                    <table>
                        <tr>
                            <th>Phân Khúc</th>
                            <th>Số Lượng</th>
                            <th>Tỷ Lệ</th>
                        </tr>
                        <tr>
                            <td>👑 Khách VIP</td>
                            <td>{$data['khachVIP']}</td>
                            <td>" . (($data['khachVIP'] + $data['khachThuong'] + $data['khachItQuayLai']) > 0 ? round($data['khachVIP'] / ($data['khachVIP'] + $data['khachThuong'] + $data['khachItQuayLai']) * 100, 1) : 0) . "%</td>
                        </tr>
                        <tr>
                            <td>🟢 Khách Thường</td>
                            <td>{$data['khachThuong']}</td>
                            <td>" . (($data['khachVIP'] + $data['khachThuong'] + $data['khachItQuayLai']) > 0 ? round($data['khachThuong'] / ($data['khachVIP'] + $data['khachThuong'] + $data['khachItQuayLai']) * 100, 1) : 0) . "%</td>
                        </tr>
                        <tr>
                            <td>🔴 Khách Ít Quay Lại</td>
                            <td>{$data['khachItQuayLai']}</td>
                            <td>" . (($data['khachVIP'] + $data['khachThuong'] + $data['khachItQuayLai']) > 0 ? round($data['khachItQuayLai'] / ($data['khachVIP'] + $data['khachThuong'] + $data['khachItQuayLai']) * 100, 1) : 0) . "%</td>
                        </tr>
                        <tr style='background: #e8f4f8; font-weight: bold;'>
                            <td>Quay Lại Vs Lần Đầu</td>
                            <td>{$data['khachQuayLai']} / {$data['khachMotLan']}</td>
                            <td>" . (($data['khachQuayLai'] + $data['khachMotLan']) > 0 ? round($data['khachQuayLai'] / ($data['khachQuayLai'] + $data['khachMotLan']) * 100, 1) : 0) . "%</td>
                        </tr>
                    </table>
                </div>

                <div class='section'>
                    <h2>🎯 TOP DỊCH VỤ (Tần Suất Sử Dụng)</h2>
                    <table>
                        <tr>
                            <th>Tên Dịch Vụ</th>
                            <th>Số Lần Sử Dụng</th>
                        </tr>";
                        foreach($data['tanSuatDichVu'] as $dv) {
                            $html .= "<tr><td>{$dv->ten_dich_vu}</td><td>" . number_format($dv->tong_so_luong, 0) . "</td></tr>";
                        }
                        $html .= "
                    </table>
                </div>

                <div class='section'>
                    <h2>💵 TOP DỊCH VỤ (Doanh Thu)</h2>
                    <table>
                        <tr>
                            <th>Tên Dịch Vụ</th>
                            <th>Doanh Thu</th>
                        </tr>";
                        foreach($data['doanhThuDichVu'] as $dv) {
                            $html .= "<tr><td>{$dv->ten_dich_vu}</td><td>" . number_format($dv->doanh_thu, 0, ',', '.') . " đ</td></tr>";
                        }
                        $html .= "
                    </table>
                </div>

                <div class='section'>
                    <h2>🔥 TOP PHÒNG ĐẮT KHÁCH</h2>
                    <table>
                        <tr>
                            <th>Số Phòng</th>
                            <th>Lượt Đặt</th>
                        </tr>";
                        foreach($data['tanSuatPhong'] as $p) {
                            $html .= "<tr><td>Phòng {$p->so_phong}</td><td>" . number_format($p->so_lan_dat, 0) . "</td></tr>";
                        }
                        $html .= "
                    </table>
                </div>

                <div class='footer'>
                    <p>© " . date('Y') . " - Hệ Thống Quản Lý Khách Sạn</p>
                    <p>Báo cáo được tạo tự động - Dữ liệu được cập nhật thực thời</p>
                </div>
            </div>
        </body>
        </html>";

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="BaoCao_' . date('Y-m-d_H-i-s') . '.html"',
        ]);
    }

    public function exportExcel(Request $request)
    {
        try {
            $kieuLoc = $request->input('kieu_loc', 'nam');
            $giaTriLoc = $request->input('gia_tri_loc', date('Y'));

            Log::debug('Export Excel Start', ['kieuLoc' => $kieuLoc, 'giaTriLoc' => $giaTriLoc]);
            $data = $this->getFullReportData($kieuLoc, $giaTriLoc);
            Log::debug('Export Excel Data Loaded', ['data_keys' => array_keys($data)]);

            // Tạo CSV Unicode
            $csv = chr(239).chr(187).chr(191); // BOM UTF-8

            $csv .= "BÁO CÁO THỐNG KÊ KHÁCH SẠN\n";
            $csv .= "Lọc Theo: " . ($kieuLoc == 'nam' ? 'Năm' : ($kieuLoc == 'thang' ? 'Tháng' : ($kieuLoc == 'quy' ? 'Quý' : 'Ngày'))) . " | Kỳ: $giaTriLoc\n";
            $csv .= "Ngày Xuất: " . date('d/m/Y H:i:s') . "\n";
            $csv .= "\n";

            // Phần 1: Chỉ tiêu tổng quan
            $csv .= "CHỈ TIÊU TỔNG QUAN\n";
            $csv .= "Doanh Thu Kỳ,Lượt Đặt Phòng,Phòng Đang Thuê,Tổng Phòng,Phòng Trống,Nhân Viên,Tỷ Lệ Lấp Đầy\n";
            $csv .= number_format($data['tongDoanhThuKy'], 0) . "," . $data['tongDonKy'] . "," . $data['phongDangThue'] . "," . $data['tongPhong'] . "," . $data['phongTrong'] . "," . $data['tongNhanVien'] . "," . ($data['tongPhong'] > 0 ? round($data['phongDangThue'] / $data['tongPhong'] * 100, 1) : 0) . "%\n";
            $csv .= "\n";

            // Phần 2: Phân khúc khách
            $csv .= "PHÂN KHÚC KHÁCH HÀNG\n";
            $csv .= "Phân Khúc,Số Lượng\n";
            $csv .= "Khách VIP," . $data['khachVIP'] . "\n";
            $csv .= "Khách Thường," . $data['khachThuong'] . "\n";
            $csv .= "Khách Ít Quay Lại," . $data['khachItQuayLai'] . "\n";
            $csv .= "Khách Quay Lại," . $data['khachQuayLai'] . "\n";
            $csv .= "Khách Lần Đầu," . $data['khachMotLan'] . "\n";
            $csv .= "\n";

            // Phần 3: Top dịch vụ
            $csv .= "TOP DỊCH VỤ (TẦN SUẤT SỬ DỤNG)\n";
            $csv .= "Tên Dịch Vụ,Số Lần Sử Dụng\n";
            foreach($data['tanSuatDichVu'] as $dv) {
                $csv .= "\"{$dv->ten_dich_vu}\"," . $dv->tong_so_luong . "\n";
            }
            $csv .= "\n";

            // Phần 4: Top dịch vụ theo doanh thu
            $csv .= "TOP DỊCH VỤ (DOANH THU)\n";
            $csv .= "Tên Dịch Vụ,Doanh Thu\n";
            foreach($data['doanhThuDichVu'] as $dv) {
                $csv .= "\"{$dv->ten_dich_vu}\"," . number_format($dv->doanh_thu, 0) . "\n";
            }
            $csv .= "\n";

            // Phần 5: Top phòng
            $csv .= "TOP PHÒNG ĐẮT KHÁCH\n";
            $csv .= "Số Phòng,Lượt Đặt\n";
            foreach($data['tanSuatPhong'] as $p) {
                $csv .= "Phòng {$p->so_phong}," . $p->so_lan_dat . "\n";
            }
            $csv .= "\n";

            $csv .= "GHI CHÚ\n";
            $csv .= "✓ Dữ liệu được cập nhật thực thời tính đến lúc xuất báo cáo\n";
            $csv .= "✓ Tỷ lệ lấp đầy = (Phòng đang thuê / Tổng phòng) × 100\n";
            $csv .= "✓ Khách VIP: Chi tiêu >= 10 triệu hoặc lượt đặt >= 5\n";
            $csv .= "✓ Khách Thường: Lượt đặt >= 2\n";

            Log::debug('Export Excel CSV Built', ['csv_size' => strlen($csv)]);

            return response($csv, 200, [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="BaoCao_' . date('Y-m-d_H-i-s') . '.csv"',
            ]);
        } catch (\Exception $e) {
            Log::error('Export Excel Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
