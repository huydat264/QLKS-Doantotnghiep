<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use Illuminate\Http\Request;

class ComboUserController extends Controller
{
    public function index(Request $request)
    {
        $combos = Combo::query()
            ->when($request->filled('loai_phong'), function ($query) use ($request) {
                $query->whereIn('loai_phong_ap_dung', (array) $request->input('loai_phong'));
            })
            ->when($request->filled('gia_max'), function ($query) use ($request) {
                $query->where('gia_combo', '<=', $request->input('gia_max'));
            })
            ->get();

        return view('user.combouser', compact('combos'));
    }

    public function show($id)
    {
        $combo = Combo::findOrFail($id);
        // Đổi từ user.combo.show sang user.combo.chitietcombouser
        return view('user.chitietcombouser', compact('combo'));
    }
}
