<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ComboUserController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $combos = Combo::query()
            ->when($request->filled('gia_max'), function ($query) use ($request) {
                $query->where('gia_combo', '<=', $request->input('gia_max'));
            })
            ->when($request->filled('so_dem_luu_tru'), function ($query) use ($request) {
                $query->where('so_dem_luu_tru', '>=', $request->input('so_dem_luu_tru'));
            })
            ->when($request->filled('active'), function ($query) use ($request) {
                $query->where('active', $request->input('active'));
            })
            ->when($request->filled('keyword'), function ($query) use ($keyword) {
                $kwLower = mb_strtolower($keyword);


                // detect price patterns like "2tr", "2 triệu", "2000000" and apply as max combo price
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
                        $query->where('gia_combo', '<=', $priceFilter);
                            // remove price fragments so words like "triệu" aren't treated as separate tokens
                            $kwLower = preg_replace('/(\d+(?:[\.,]\d+)?)\s*(tr|trieu|triệu|tr|k|nghin|nghìn|vnd|đ|d)?/iu', ' ', $kwLower);
                    }
                }

                // numeric: detect nights ("đêm") and apply to so_dem_luu_tru
                if (preg_match('/(\d+)\s*(đêm|dem|ngay|ngày|ng|d)/u', $kwLower, $m)) {
                    $num = (int)$m[1];
                    $query->where('so_dem_luu_tru', '>=', $num);
                }

                // tokenize and AND tokens; per token OR across fields
                $tokens = preg_split('/[\s,;]+/u', $kwLower, -1, PREG_SPLIT_NO_EMPTY);
                $norm = [
                    'stand' => 'standard',
                    'std' => 'standard',
                    'delx' => 'deluxe',
                    'dlx' => 'deluxe',
                    'sui' => 'suite',
                    'suite' => 'suite'
                ];

                foreach ($tokens as $token) {
                    if (preg_match('/^\d+$/', $token)) continue;
                    if (in_array($token, ['đêm','dem','so','số','phòng','phong','nguoi','người','khach','khách','có','co','ngay','ngày','ng','d'])) continue;

                    $tok = $token;
                    if (isset($norm[$tok])) $tok = $norm[$tok];

                    $query->where(function ($q) use ($tok) {
                        $like = '%' . $tok . '%';
                        $q->where('ten_combo', 'LIKE', $like)
                          ->orWhere('mo_ta', 'LIKE', $like)
                          ->orWhere('loai_phong_ap_dung', 'LIKE', $like)
                          ->orWhereRaw('SOUNDEX(loai_phong_ap_dung) = SOUNDEX(?)', [$tok]);
                    });
                }
            })
            ->get();

        // Compute availability per combo
        foreach ($combos as $combo) {
            $combo->available_rooms = DB::table('phong')
                ->where('loai_phong', $combo->loai_phong_ap_dung)
                ->where('trang_thai', 'Trống')
                ->count();

            // handle optional active flag (if migration applied)
            $combo->is_active = isset($combo->active) ? (bool)$combo->active : true;
        }

        // Nếu có từ khóa, thêm metadata cho từng combo để hiển thị tiêu chí khớp/không khớp
        if (!empty($keyword)) {
            $kwLower = mb_strtolower($keyword);
            $tokens = preg_split('/[\s,;]+/u', $kwLower, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($combos as $combo) {
                $matched = [];
                $unmatched = [];

                // numeric nights
                if (preg_match('/(\d+)\s*(đêm|dem|ngay|ngày|ng|d)/u', $kwLower, $m)) {
                    $num = (int)$m[1];
                    if ((int)$combo->so_dem_luu_tru >= $num) {
                        $matched[] = 'Số đêm';
                    } else {
                        $unmatched[] = 'Số đêm';
                    }
                }

                foreach ($tokens as $token) {
                    if (preg_match('/^\d+$/', $token)) continue;
                    if (in_array($token, ['đêm','dem','so','số','phòng','phong','nguoi','người','khach','khách','có','co','ngay','ngày','ng','d'])) continue;

                    $found = false;
                    $hayFields = [
                        'Tên gói' => $combo->ten_combo ?? '',
                        'Mô tả' => $combo->mo_ta ?? '',
                        'Loại phòng áp dụng' => $combo->loai_phong_ap_dung ?? '',
                    ];

                    foreach ($hayFields as $label => $value) {
                        if ($value && mb_stripos($value, $token) !== false) {
                            $matched[] = $label . ' ("' . $token . '")';
                            $found = true;
                        }
                    }

                    // fuzzy via soundex on loai_phong_ap_dung
                    if (!$found && !empty($combo->loai_phong_ap_dung) && soundex(mb_strtoupper($combo->loai_phong_ap_dung)) === soundex(mb_strtoupper($token))) {
                        $matched[] = 'Loại phòng (tương đồng âm)';
                        $found = true;
                    }

                    if (!$found) {
                        $unmatched[] = $token;
                    }
                }

                $combo->match_meta = [
                    'matched' => array_values(array_unique($matched)),
                    'unmatched' => array_values(array_unique($unmatched)),
                ];
            }
        }

        return view('user.combouser', compact('combos'));
    }

    public function show($id)
    {
        $combo = Combo::findOrFail($id);
        return view('user.chitietcombouser', compact('combo'));
    }
}
