/**
 * Room Availability Checker
 * Dùng để hiển thị lịch đã book và kiểm tra xung đột khi user chọn ngày
 */

class RoomAvailabilityChecker {
    constructor(options = {}) {
        this.apiBaseUrl = options.apiBaseUrl || '/api';
        this.bookedDates = [];
        this.disabledDates = [];
    }

    /**
     * Lấy lịch đã book cho 1 phòng
     */
    async fetchRoomAvailability(roomId, checkInDate = null, checkOutDate = null) {
        try {
            const params = new URLSearchParams({
                id_phong: roomId,
                ...(checkInDate && { ngay_nhan: checkInDate }),
                ...(checkOutDate && { ngay_tra: checkOutDate })
            });

            const response = await fetch(`${this.apiBaseUrl}/availability?${params}`);
            if (!response.ok) throw new Error('Failed to fetch availability');

            const data = await response.json();
            this.bookedDates = data.booked_dates?.dates || [];
            this.disabledDates = data.disabled_dates || [];

            return {
                available: data.available,
                bookedDates: this.bookedDates,
                disabledDates: this.disabledDates
            };
        } catch (error) {
            console.error('Error fetching room availability:', error);
            return { available: true, bookedDates: [], disabledDates: [] };
        }
    }

    /**
     * Lấy lịch đã book cho loại phòng (dùng cho combo)
     */
    async fetchAvailabilityByType(roomType, checkInDate = null, checkOutDate = null) {
        try {
            const params = new URLSearchParams({
                loai_phong: roomType,
                ...(checkInDate && { ngay_nhan: checkInDate }),
                ...(checkOutDate && { ngay_tra: checkOutDate })
            });

            const response = await fetch(`${this.apiBaseUrl}/availability-by-type?${params}`);
            if (!response.ok) throw new Error('Failed to fetch availability by type');

            return await response.json();
        } catch (error) {
            console.error('Error fetching availability by type:', error);
            return { has_available: true, rooms: [] };
        }
    }

    /**
     * Kiểm tra ngày có bị khoá không (thuộc khoảng đã book)
     */
    isDateDisabled(date) {
        return this.disabledDates.includes(date);
    }

    /**
     * Lấy HTML hiển thị lịch đã book
     */
    getBookingCalendarHTML() {
        if (this.bookedDates.length === 0) {
            return '<p class="text-muted">Không có thông tin lịch đặt.</p>';
        }

        let html = '<div class="booking-calendar"><ul class="list-group">';

        this.bookedDates.forEach(booking => {
            const startDate = new Date(booking.start).toLocaleDateString('vi-VN');
            const endDate = new Date(booking.end).toLocaleDateString('vi-VN');
            const badgeClass = booking.status === 'Đã thanh toán' ? 'badge-danger' : 'badge-warning';

            html += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${startDate} → ${endDate}</span>
                    <span class="badge ${badgeClass}">${booking.status}</span>
                </li>
            `;
        });

        html += '</ul></div>';
        return html;
    }

    /**
     * Disable các input ngày (datepicker) dựa vào booked dates
     */
    disableDatepickerDates(dateInputId) {
        const input = document.getElementById(dateInputId);
        if (!input || !input.hasAttribute('data-litepicker')) return;

        // Nếu dùng Litepicker hoặc thư viện tương tự
        // có thể cấu hình disabled dates
        console.log('Disabled dates:', this.disabledDates);
    }
}

// Export cho dùng global
window.RoomAvailabilityChecker = RoomAvailabilityChecker;
