<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VoucherManagementController extends Controller
{
    public function index()
    {
        // Lấy danh sách voucher kèm theo thông tin người được phát riêng (nếu có)
        $vouchers = DB::table('voucher')
            ->leftJoin('khachhang', 'voucher.id_khachhang', '=', 'khachhang.id_khachhang')
            ->select('voucher.*', 'khachhang.ho_ten as ten_nguoi_so_huu')
            ->orderBy('id_voucher', 'desc')
            ->paginate(15);

        // Lấy danh sách khách hàng để đổ vào Select Box "Phát riêng"
        $khachHangs = DB::table('khachhang')->select('id_khachhang', 'ho_ten', 'so_dien_thoai')->get();

        return view('admin.quanlyvoucher', compact('vouchers', 'khachHangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_code' => 'required|string|unique:voucher,ma_code|max:50',
            'loai_voucher' => 'required|string',
            'muc_giam' => ['required', 'numeric', 'min:1', function ($attribute, $value, $fail) use ($request) {
                if ($request->is_percent && $value > 100) {
                    $fail('Mức giảm tối đa khi chọn phần trăm là 100%.');
                }
            }],
            'is_percent' => 'required|boolean',
            'ngay_het_han' => 'required|date',
            'id_khachhang' => 'nullable|integer|exists:khachhang,id_khachhang'
        ]);

        try {
            DB::table('voucher')->insert([
                'ma_code' => strtoupper($request->ma_code),
                'loai_voucher' => $request->loai_voucher, // PHONG, DICH_VU, ALL
                'muc_giam' => $request->muc_giam,
                'is_percent' => $request->is_percent,
                'ngay_het_han' => $request->ngay_het_han,
                'trang_thai' => 1, // Mặc định tạo ra là bật
                'id_khachhang' => $request->id_khachhang ?: null,
            ]);

            return redirect()->back()->with('success', 'Tạo mã Voucher thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ma_code' => 'required|string|max:50|unique:voucher,ma_code,' . $id . ',id_voucher',
            'loai_voucher' => 'required|string',
            'muc_giam' => ['required', 'numeric', 'min:1', function ($attribute, $value, $fail) use ($request) {
                if ($request->is_percent && $value > 100) {
                    $fail('Mức giảm tối đa khi chọn phần trăm là 100%.');
                }
            }],
            'is_percent' => 'required|boolean',
            'ngay_het_han' => 'required|date',
            'id_khachhang' => 'nullable|integer|exists:khachhang,id_khachhang'
        ]);

        try {
            DB::table('voucher')->where('id_voucher', $id)->update([
                'ma_code' => strtoupper($request->ma_code),
                'loai_voucher' => $request->loai_voucher,
                'muc_giam' => $request->muc_giam,
                'is_percent' => $request->is_percent,
                'ngay_het_han' => $request->ngay_het_han,
                'id_khachhang' => $request->id_khachhang ?: null,
            ]);

            return redirect()->back()->with('success', 'Cập nhật Voucher thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $voucher = DB::table('voucher')->where('id_voucher', $id)->first();
            if ($voucher) {
                // Đảo ngược trạng thái 1 -> 0, 0 -> 1
                $newStatus = $voucher->trang_thai == 1 ? 0 : 1;
                DB::table('voucher')->where('id_voucher', $id)->update(['trang_thai' => $newStatus]);

                $msg = $newStatus == 1 ? 'Đã BẬT phát hành Voucher.' : 'Đã TẮT phát hành Voucher.';
                return redirect()->back()->with('success', $msg);
            }
            return redirect()->back()->with('error', 'Không tìm thấy Voucher!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::table('voucher')->where('id_voucher', $id)->delete();
            return redirect()->back()->with('success', 'Xóa Voucher thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }
}
