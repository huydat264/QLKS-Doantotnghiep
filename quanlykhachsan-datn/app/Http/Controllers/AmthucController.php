<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DatBanAmThuc;

class AmthucController extends Controller
{
    public function index()
    {
        return view('user.amthuc');
    }

    public function datBan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'date' => 'required|date',
            'guests' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        // Gửi email tới khách sạn
        Mail::to(config('mail.from.address'))->send(new DatBanAmThuc($validated));

        return back()->with('success', 'Yêu cầu đặt bàn của bạn đã được gửi. Chúng tôi sẽ liên hệ với bạn sớm!');
    }
}
