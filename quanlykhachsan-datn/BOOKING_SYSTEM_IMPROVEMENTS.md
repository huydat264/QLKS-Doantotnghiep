# 🏨 Hướng Dẫn Cải Thiện Luồng Đặt Phòng

## 📋 Các Vấn Đề Được Giải Quyết

### 1. ❌ Phòng bị khoá vĩnh viễn
**Vấn đề cũ:** Sau khi đặt phòng, trạng thái phòng được set thành 'Đã đặt' vĩnh viễn → không thể đặt phòng đó vào các ngày khác

**✅ Giải pháp:** 
- Không cập nhật trạng thái `phong.trang_thai` nữa
- Kiểm tra tính khả dụng dựa trên bảng `datphong` (tìm các booking "Đã xác nhận" hoặc "Đã thanh toán" trong khoảng ngày)
- Cho phép đặt phòng vào bất kỳ ngày nào miễn là không xung đột với booking khác

### 2. ❌ Không kiểm tra xung đột lịch
**Vấn đề cũ:** Không check các ngày đã được đặt → 2 khách có thể đặt cùng 1 phòng cùng lúc

**✅ Giải pháp:**
- Thêm Service `RoomAvailabilityService` với hàm `isRoomAvailable()` 
- Kiểm tra xung đột **trước** khi nhấn thanh toán (trong `saveServices()`)
- Kiểm tra **lần cuối** ngay **sau** thanh toán VNPay thành công (trong `vnpayReturn()`)

### 3. ❌ Race condition: Nhiều người đặt cùng lúc
**Vấn đề cũ:** Nếu 2 người cùng thanh toán VNPay cho cùng 1 phòng → cả 2 đều được insert vào database

**✅ Giải pháp:**
- Sử dụng **database lock** (`lockForUpdate()`) khi xử lý VNPay return
- Kiểm tra xung đột lần cuối với lock bảng phòng
- Nếu phòng bị chiếm bởi người khác → rollback transaction → thông báo lỗi cho khách

---

## 🛠️ Các Thay Đổi Chi Tiết

### 1. Service Mới: `app/Services/RoomAvailabilityService.php`
Cung cấp các hàm kiểm tra tính khả dụng:
- `isRoomAvailable($id_phong, $ngay_nhan, $ngay_tra)` → kiểm tra xung đột
- `getRoomBookingHistory($id_phong)` → lấy lịch đặt phòng
- `getDisabledDates($id_phong)` → lấy danh sách ngày bị khoá
- `findAvailableRoomByType($loai_phong, $ngay_nhan, $ngay_tra)` → tìm phòng trống (cho combo)

### 2. Controller: `app/Http/Controllers/DatPhongController.php`
**Cập nhật phương thức:**

#### a. `saveServices()` - Kiểm tra xung đột **trước** thanh toán
```php
// Kiểm tra phòng lẻ
if (!RoomAvailabilityService::isRoomAvailable($booking_id, $ngay_nhan, $ngay_tra)) {
    return redirect()->back()->withErrors(['availability' => 'Phòng đã được đặt...']);
}
```

#### b. `vnpayReturn()` - Xử lý race condition **sau** thanh toán
```php
// Lock phòng này để ngăn booking khác cùng lúc
$phong_lock = DB::table('phong')
    ->where('id_phong', $booking_id)
    ->lockForUpdate()
    ->first();

// Kiểm tra lần cuối xung đột
if (!RoomAvailabilityService::isRoomAvailable($booking_id, $ngay_nhan, $ngay_tra)) {
    throw new \Exception('Phòng này đã bị đặt bởi khách khác...');
}
```

#### c. Không update trạng thái phòng
❌ Xoá dòng:
```php
DB::table('phong')->where('id_phong', $booking_id)->update(['trang_thai' => 'Đã đặt']);
```

**API endpoints mới:**
- `GET /api/availability?id_phong=1&ngay_nhan=2026-06-15&ngay_tra=2026-06-17` → JSON lịch đã book
- `GET /api/availability-by-type?loai_phong=Standard&ngay_nhan=...` → Lịch cho loại phòng

### 3. Routes: `routes/web.php`
```php
Route::get('/api/availability', [DatPhongController::class, 'getAvailability'])->name('api.availability');
Route::get('/api/availability-by-type', [DatPhongController::class, 'getAvailabilityByType'])->name('api.availability_by_type');
```

### 4. View: `resources/views/user/phonguser.blade.php`
**Cập nhật:**
- Nút "ĐẶT PHÒNG" bây giờ gọi hàm `viewBookingCalendar()` thay vì redirect trực tiếp
- Hiển thị modal popup với lịch đã book
- Cho phép khách xem lịch trước khi đặt

### 5. JavaScript: `public/js/room-availability.js`
Class `RoomAvailabilityChecker` để:
- Fetch dữ liệu lịch từ API
- Render HTML calendar
- Disable ngày đã book trên datepicker (tương lai)

---

## 🔄 Luồng Đặt Phòng (Cải Thiện)

```
1. Khách nhấn "ĐẶT PHÒNG"
   ↓
2. Modal hiển thị lịch đã book (không block, chỉ thông tin)
   ↓
3. Khách nhấn "Đặt phòng" → Chuyển đến form thông tin khách
   ↓
4. Khách chọn ngày + dịch vụ
   ↓
5. ✅ SERVER KIỂM TRA XUNG ĐỘT (saveServices)
   - Nếu có xung đột → Thông báo lỗi, quay lại form
   ↓
6. Khách xác nhận + thanh toán VNPay
   ↓
7. VNPay gọi callback vnpayReturn()
   ↓
8. ✅ SERVER KIỂM TRA XUNG ĐỘT LẦN CUỐI + LOCK (vnpayReturn)
   - Lock bảng phòng
   - Kiểm tra lại xung đột
   - Nếu OK → Insert booking
   - Nếu fail → Rollback, thông báo lỗi
   ↓
9. ✅ KHÔNG update trạng thái phòng (vẫn 'Trống')
   ↓
10. Gửi email xác nhận
```

---

## 🧪 Test Case

### Test 1: Đặt phòng khác ngày - OK ✅
```
- Khách A đặt Phòng 1, ngày 2026-06-15 → 2026-06-17 → Thành công
- Khách B đặt Phòng 1, ngày 2026-06-18 → 2026-06-20 → Thành công
```

### Test 2: Đặt phòng trùng ngày - FAIL ❌
```
- Khách A đặt Phòng 1, ngày 2026-06-15 → 2026-06-17 → Thành công
- Khách B đặt Phòng 1, ngày 2026-06-16 → 2026-06-18 → 
  Lỗi: "Phòng này đã được đặt trong khoảng thời gian bạn chọn"
```

### Test 3: Race condition - FAIL ❌
```
- Khách A & B cùng lúc thanh toán cho Phòng 1, ngày 2026-06-15 → 2026-06-17
- Server process A: Lock Phòng 1 → Check OK → Insert ✅
- Server process B: Lock Phòng 1 → Check FAIL → Rollback ❌
  Lỗi: "Phòng này đã bị đặt bởi khách khác lúc bạn thanh toán"
```

### Test 4: Combo booking
```
- Khách A chọn Combo, loại phòng Standard
- System tìm phòng Standard trống trong khoảng ngày
- Tìm thấy Phòng 3 → Cấp phát cho Khách A
- Khách B chọn Combo, loại phòng Standard, cùng ngày
- System tìm Phòng 3 → Bị chiếm → Tìm Phòng 5 → OK ✅
```

---

## 📝 Migration Database (Nếu cần)

Hiện tại **không cần tạo migration** vì:
- Bảng `datphong` đã có `ngay_nhan`, `ngay_tra`, `trang_thai`
- Logic kiểm tra dựa trên query hiện tại

**Tương lai có thể thêm:**
```sql
-- Thêm index để tối ưu query kiểm tra xung đột
ALTER TABLE datphong ADD INDEX idx_room_dates (id_phong, ngay_nhan, ngay_tra, trang_thai);
```

---

## ⚠️ Chú Ý

1. **Database Lock:** 
   - `lockForUpdate()` sẽ lock bảng trong transaction
   - Nếu transaction quá dài → có thể gây deadlock
   - Giữ transaction càng ngắn càng tốt

2. **Session timeout:**
   - Nếu khách mất session khi redirect từ VNPay → Không thể lấy ngay_nhan, ngay_tra
   - Hiện có check: nếu mất session → dd() error (tương lai có thể cải thiện)

3. **Phòng bảo trì:**
   - Phòng có `trang_thai = 'Bảo trì'` sẽ không được cấp phát
   - Khách sẽ nhận lỗi "Không có phòng trống"

4. **Cancel booking:**
   - Khi cancel booking → trạng thái `datphong` thành 'Đã hủy'
   - Phòng tự động trở nên trống (vì query chỉ count 'Đã xác nhận' + 'Đã thanh toán')
   - **Không cần update** `phong.trang_thai`

---

## 🚀 Deployment

1. Copy file `app/Services/RoomAvailabilityService.php`
2. Update file `app/Http/Controllers/DatPhongController.php`
3. Update file `routes/web.php`
4. Update file `resources/views/user/phonguser.blade.php`
5. Copy file `public/js/room-availability.js`
6. Clear cache: `php artisan cache:clear` + `php artisan config:cache`
7. Test trên staging trước khi deploy production

---

## 📞 Support

Nếu gặp vấn đề:
- Check logs: `storage/logs/laravel.log`
- Verify database indexes
- Kiểm tra transaction isolation level (default: READ COMMITTED)
