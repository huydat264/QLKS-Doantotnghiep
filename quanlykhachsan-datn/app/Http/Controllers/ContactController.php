<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10'
        ], [
            'name.required' => 'Vui lòng nhập họ và tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'subject.required' => 'Vui lòng chọn chủ đề',
            'message.required' => 'Vui lòng nhập nội dung tin nhắn',
            'message.min' => 'Nội dung phải có ít nhất 10 ký tự'
        ]);

        try {
            // Gửi email đến khách sạn
            $hotelEmail = env('HOTEL_CONTACT_EMAIL', 'info@kimboutique.com');

            Mail::raw(
                "Khách hàng: {$validated['name']}\n" .
                "Email: {$validated['email']}\n" .
                "Số điện thoại: {$validated['phone']}\n" .
                "Chủ đề: {$validated['subject']}\n\n" .
                "Nội dung:\n{$validated['message']}",
                function ($message) use ($validated, $hotelEmail) {
                    $message->to($hotelEmail)
                            ->replyTo($validated['email'])
                            ->subject('Liên hệ từ khách hàng: ' . $validated['subject']);
                }
            );

            return redirect()->back()->with('success', 'Gửi tin nhắn thành công! Chúng tôi sẽ liên hệ lại trong thời gian sớm nhất.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại.');
        }
    }
}
