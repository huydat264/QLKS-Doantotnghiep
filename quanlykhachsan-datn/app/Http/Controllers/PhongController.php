<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phong;
use Carbon\Carbon;
use App\Services\RoomAvailabilityService;
use Illuminate\Support\Facades\DB;

class PhongController extends Controller
{
    private function parseBookingDate($value)
    {
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
            return Carbon::createFromFormat('d/m/Y', $value)->toDateString();
        }

        return Carbon::parse($value)->toDateString();
    }

    public function indexUser(Request $request)
    {
        $keyword = $request->input('keyword');
        // Khởi tạo query từ Model và thêm số lượt đặt phòng đã xác nhận
        $hotScope = $request->input('hot_scope', 'month');
        $useHotScope = $request->filled('hot');

        $query = Phong::withCount(['datPhongs as booking_count' => function ($query) use ($useHotScope, $hotScope) {
            $query->whereIn('trang_thai', ['Đã đặt', 'Đã xác nhận']);

            if ($useHotScope) {
                if ($hotScope === 'year') {
                    $query->whereYear('ngay_nhan', Carbon::now()->year);
                } else {
                    $query->whereYear('ngay_nhan', Carbon::now()->year)
                          ->whereMonth('ngay_nhan', Carbon::now()->month);
                }
            }
        }]);

        // Bộ lọc phòng hot: chỉ hiện những phòng đã có lượt đặt xác nhận và sắp xếp theo độ hot
        if ($useHotScope) {
            $query->whereHas('datPhongs', function ($query) use ($hotScope) {
                    $query->whereIn('trang_thai', ['Đã đặt', 'Đã xác nhận']);

                    if ($hotScope === 'year') {
                        $query->whereYear('ngay_nhan', Carbon::now()->year);
                    } else {
                        $query->whereYear('ngay_nhan', Carbon::now()->year)
                              ->whereMonth('ngay_nhan', Carbon::now()->month);
                    }
                  })
                  ->orderByDesc('booking_count')
                  ->orderBy('gia_phong');
        }

        // Lọc theo Khoảng giá
        if ($request->filled('gia_max')) {
            $query->where('gia_phong', '<=', $request->gia_max);
        }

        // Lọc theo Loại phòng
        if ($request->filled('loai_phong')) {
            $query->whereIn('loai_phong', $request->loai_phong);
        }

        // Lọc theo Bộ lọc nâng cao (Hướng phòng, Số người, Số phòng ngủ)
        if ($request->filled('huong_phong')) {
            $query->where('huong_phong', 'LIKE', '%' . $request->huong_phong . '%');
        }
        if ($request->filled('so_luong_nguoi')) {
            $query->where('so_luong_nguoi', '>=', $request->so_luong_nguoi);
        }
        if ($request->filled('so_phong_ngu')) {
            $query->where('so_phong_ngu', $request->so_phong_ngu);
        }

        // Lọc theo tìm kiếm từ home: số khách
        if ($request->filled('tong_khach')) {
            $tong_khach = $request->input('tong_khach');
            if ($tong_khach > 0) {
                $query->where('so_luong_nguoi', '>=', $tong_khach);
            }
        }

        if ($request->filled('checkin') && $request->filled('checkout')) {
            try {
                $checkin = $this->parseBookingDate($request->checkin);
                $checkout = $this->parseBookingDate($request->checkout);

                if ($checkout > $checkin) {
                    $query->whereNotExists(function ($subQuery) use ($checkin, $checkout) {
                        $subQuery->select(DB::raw(1))
                            ->from('datphong')
                            ->whereColumn('datphong.id_phong', 'phong.id_phong')
                            ->where('trang_thai', 'Đã xác nhận')
                            ->where(function ($q) use ($checkin, $checkout) {
                                $q->where('ngay_nhan', '<', $checkout)
                                  ->where('ngay_tra', '>', $checkin);
                            });
                    });
                } else {
                    $query->whereRaw('1 = 0');
                }
            } catch (\Exception $e) {
                $query->whereRaw('1 = 0');
            }
        }

        // Nếu có từ khóa tìm kiếm, parse token để hỗ trợ nhập tắt, số lượng, và "hot"
        if (!empty($keyword)) {
            $kwLower = mb_strtolower($keyword);

            // detect price patterns like "2tr", "2 triệu", "2000000" and apply as max price
            if (preg_match_all('/(\d+(?:[\.,]\d+)?)\s*(tr|trieu|triệu|tr|k|nghin|nghìn|vnd|đ|d)?/iu', $kwLower, $pm, PREG_SET_ORDER)) {
                $priceFilter = null;
                foreach ($pm as $p) {
                    $num = floatval(str_replace([',', '.'], ['.', '.'], $p[1]));
                    $unit = isset($p[2]) ? mb_strtolower($p[2]) : null;
                    if ($unit && in_array($unit, ['tr', 'trieu', 'triệu', 'trieu'])) {
                        $candidate = (int)round($num * 1000000);
                    } elseif ($unit && in_array($unit, ['k'])) {
                        $candidate = (int)round($num * 1000);
                    } elseif ($unit && in_array($unit, ['vnd','đ','d'])) {
                        $candidate = (int)round($num);
                    } else {
                        // no unit: if large number assume VND (>= 1000)
                        if ($num >= 1000) {
                            $candidate = (int)round($num);
                        } else {
                            $candidate = null;
                        }
                    }

                    if ($candidate !== null) {
                        $priceFilter = $candidate;
                        break;
                    }
                }

                if (!is_null($priceFilter)) {
                    $query->where('gia_phong', '<=', $priceFilter);
                    // remove price fragments so words like "triệu" aren't treated as separate tokens
                    $kwLower = preg_replace('/(\d+(?:[\.,]\d+)?)\s*(tr|trieu|triệu|tr|k|nghin|nghìn|vnd|đ|d)?/iu', ' ', $kwLower);
                }
            }

            // detect numeric patterns: "1 phòng", "2 người", "1 phòng ngủ"
            if (preg_match('/(\d+)\s*(phòng|phong|phòng ngủ|phong ngu|ngu)/u', $kwLower, $m)) {
                $num = (int)$m[1];
                $query->where('so_phong_ngu', '>=', $num);
            }
            if (preg_match('/(\d+)\s*(người|nguoi|khách|khach|ng|kh)/u', $kwLower, $m)) {
                $num = (int)$m[1];
                $query->where('so_luong_nguoi', '>=', $num);
            }

            // if user asked for "hot" rooms in the keyword, prefer hot ordering and filter optionally
            if (mb_strpos($kwLower, 'hot') !== false) {
                // require at least one booking and order by booking_count
                $query->whereHas('datPhongs', function ($q) {
                    $q->whereIn('trang_thai', ['Đã đặt', 'Đã xác nhận']);
                });
                $query->orderByDesc('booking_count')->orderBy('gia_phong');
            }

            // tokenise and apply AND semantics between tokens; per token, OR across columns
            $tokens = preg_split('/[\s,;]+/u', $kwLower, -1, PREG_SPLIT_NO_EMPTY);
            // normalization shortcuts (common abbreviations)
            $norm = [
                'stand' => 'standard',
                'std' => 'standard',
                'delx' => 'deluxe',
                'dlx' => 'deluxe',
                'sui' => 'suite',
                'suite' => 'suite'
            ];

            foreach ($tokens as $token) {
                // skip tokens that are pure numbers (handled above) and price-like tokens
                if (preg_match('/^\d+$/', $token)) {
                    continue;
                }

                // skip tokens that look like prices (e.g., "2tr", "2trieu", "2000000")
                if (preg_match('/^\d+(?:[\.,]\d+)?(tr|trieu|triệu|k|nghìn|nghin|vnd|đ|d)?$/iu', $token)) {
                    continue;
                }

                // normalized token
                $tok = $token;
                if (isset($norm[$tok])) {
                    $tok = $norm[$tok];
                }

                // skip common words
                if (in_array($tok, ['phòng', 'phong', 'phòng ngủ', 'phong ngu', 'người', 'nguoi', 'khách', 'khach', 'có', 'co', 'ngủ', 'ngu', 'giuong', 'giường'])) {
                    continue;
                }

                $query->where(function ($q) use ($tok) {
                    $like = '%' . $tok . '%';
                    $q->where('loai_phong', 'LIKE', $like)
                      ->orWhere('mo_ta', 'LIKE', $like)
                      ->orWhere('thong_tin_quan_trong', 'LIKE', $like)
                      ->orWhere('huong_phong', 'LIKE', $like)
                      ->orWhereRaw('SOUNDEX(loai_phong) = SOUNDEX(?)', [$tok]);
                });
            }
        }

        $phongs = $query->get();

        // build match metadata per room for explanation UI
        if (!empty($keyword)) {
            $kwLower = mb_strtolower($keyword);
            $tokens = preg_split('/[\s,;]+/u', $kwLower, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($phongs as $phong) {
                $matched = [];
                $unmatched = [];

                // numeric matches
                if (preg_match('/(\d+)\s*(phòng|phong|phòng ngủ|phong ngu|ngu)/u', $kwLower, $m)) {
                    $num = (int)$m[1];
                    if ((int)$phong->so_phong_ngu >= $num) {
                        $matched[] = 'Số phòng ngủ';
                    } else {
                        $unmatched[] = 'Số phòng ngủ';
                    }
                }
                if (preg_match('/(\d+)\s*(người|nguoi|khách|khach|ng|kh)/u', $kwLower, $m)) {
                    $num = (int)$m[1];
                    if ((int)$phong->so_luong_nguoi >= $num) {
                        $matched[] = 'Số khách';
                    } else {
                        $unmatched[] = 'Số khách';
                    }
                }

                // token text matches
                foreach ($tokens as $token) {
                    $tok = $token;
                    if (preg_match('/^\d+$/', $tok)) continue;
                    if (in_array($tok, ['phòng', 'phong', 'phòng ngủ', 'phong ngu', 'người', 'nguoi', 'khách', 'khach', 'có', 'co', 'ngủ', 'ngu', 'giuong', 'giường'])) continue;

                    $found = false;
                    $hayFields = [
                        'Loại phòng' => $phong->loai_phong ?? '',
                        'Mô tả' => $phong->mo_ta ?? '',
                        'Thông tin' => $phong->thong_tin_quan_trong ?? '',
                        'Hướng phòng' => $phong->huong_phong ?? '',
                    ];

                    foreach ($hayFields as $label => $value) {
                        if ($value && mb_stripos($value, $tok) !== false) {
                            $matched[] = $label . ' ("' . $token . '")';
                            $found = true;
                        }
                    }

                    // fuzzy via soundex on loai_phong
                    if (!$found && !empty($phong->loai_phong) && soundex(mb_strtoupper($phong->loai_phong)) === soundex(mb_strtoupper($tok))) {
                        $matched[] = 'Loại phòng (tương đồng âm)';
                        $found = true;
                    }

                    if (!$found) {
                        $unmatched[] = $token;
                    }
                }

                // make arrays unique and assign
                $phong->match_meta = [
                    'matched' => array_values(array_unique($matched)),
                    'unmatched' => array_values(array_unique($unmatched)),
                ];
            }
        }

        return view('user.phonguser', compact('phongs'));
    }
    public function chitietUser($id)
{
    // Tìm phòng theo id, nếu không thấy thì báo lỗi 404
    $phong = Phong::where('id_phong', $id)->firstOrFail();

    return view('user.chitietphong', compact('phong'));
}
}

