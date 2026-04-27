<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phong;

class PhongController extends Controller
{
    public function indexUser()
    {
        // Lấy tất cả phòng từ database
        $phongs = Phong::all();

        // Trả về view phonguser và truyền biến $phongs sang
        return view('user.phonguser', compact('phongs'));
    }
}
