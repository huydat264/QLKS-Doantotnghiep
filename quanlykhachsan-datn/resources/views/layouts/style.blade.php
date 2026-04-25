<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kim Boutique Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root { --primary-color: #63325f; --text-dark: #333; }
        body { font-family: 'Montserrat', sans-serif; overflow-x: hidden; background-color: #fff; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; }

        /* NAVBAR & LOGO */
        .navbar { transition: all 0.4s ease; padding: 30px 0; background: transparent !important; z-index: 1000; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .navbar.scrolled { background: white !important; padding: 15px 0; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-bottom: none; }
        #logo { height: 40px; transition: 0.4s; }

        /* MENU TOGGLE (FIXED) */
        .menu-toggle { background: none; border: none; cursor: pointer; display: flex; flex-direction: column; gap: 7px; padding: 0; z-index: 2100; }
        .menu-toggle span { display: block; width: 28px; height: 1.5px; background: white; transition: 0.3s; }
        .navbar.scrolled .menu-toggle span { background: var(--text-dark); }

        /* SIDEBAR MENU (FIXED) */
        .sidebar { position: fixed; top: 0; left: -100%; width: 350px; height: 100%; background: white; z-index: 2050; transition: 0.5s cubic-bezier(0.77,0.2,0.05,1.0); padding: 100px 50px; box-shadow: 15px 0 40px rgba(0,0,0,0.1); }
        .sidebar.active { left: 0; }
        .sidebar-close { position: absolute; top: 30px; left: 40px; font-size: 2rem; background: none; border: none; cursor: pointer; color: var(--text-dark); }
        .sidebar-nav a { display: block; padding: 15px 0; color: var(--text-dark); text-decoration: none; font-size: 1.2rem; border-bottom: 1px solid #f5f5f5; transition: 0.3s; }
        .sidebar-nav a:hover { padding-left: 10px; color: var(--primary-color); }

        /* AUTH & BUTTONS */
        .auth-link { color: white; text-decoration: none; font-size: 0.7rem; font-weight: 600; letter-spacing: 1.5px; position: relative; }
        .navbar.scrolled .auth-link { color: var(--text-dark); }
        .auth-link::after { content: ''; position: absolute; bottom: -4px; left: 0; width: 0; height: 1px; background: currentColor; transition: 0.3s; }
        .auth-link:hover::after { width: 100%; }

        .btn-book { background: white; color: black; padding: 12px 28px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 0.75rem; border: 1px solid white; transition: 0.3s; margin-left: 20px; }
        .btn-book:hover { background: black !important; color: white !important; border-color: black !important; transform: translateY(-2px); }

        /* POPOVERS */
        .search-container-relative { position: relative; width: 100%; }
        .booking-popover { display: none; position: absolute; bottom: calc(100% + 15px); background: white; border-radius: 12px; z-index: 2000; box-shadow: 0 15px 50px rgba(0,0,0,0.15); padding: 20px; color: var(--text-dark); max-height: 400px; overflow-y: auto; }
        #calendarPopover { left: 50%; transform: translateX(-50%); width: 400px; }
        #guestPopover { right: 0; width: 300px; }
        .calendar-header { border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .calendar-days .d-grid { grid-template-columns: repeat(7, 1fr); }

        /* Cập nhật UI Grid Lịch */
        .date-cell, .month-cell, .year-cell { padding: 8px; font-size: 0.9rem; text-align: center; border-radius: 4px; }
        .date-cell.available { background-color: #f8f9fa; cursor: pointer; }
        .date-cell.past, .month-cell.past, .year-cell.past {
            opacity: 0.4;
            pointer-events: none;
            cursor: not-allowed;
            background-color: #f1f1f1;
        }
        .date-cell.selected { background: var(--primary-color); color: white; }
        .date-cell.in-range { background: #ecdced; } /* Màu nhạt cho ngày ở giữa */

        .cursor-pointer { cursor: pointer; }
        .hover-bg:hover:not(.past) { background: #000 !important; color: #fff !important; }
        .date-cell:hover:not(.muted):not(.past):not(.selected) { background: #000; color: #fff; }

        footer { background: #fff; padding: 60px 0 30px; border-top: 1px solid #eee; }
    </style>
</head>
<body>

    @include('layouts.header')

    <main>
        @yield('content')
    </main>

    @include('layouts.footer')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true });

        // Hàm điều khiển Sidebar
        function toggleMenu() {
            $('#sidebar').toggleClass('active');
        }

        // Hiệu ứng cuộn chuột thay đổi Navbar
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('.navbar').addClass('scrolled');
                $('#logo').attr('src', 'https://www.sixsenses.com/Content/Images/logo-six-senses.svg');
            } else {
                $('.navbar').removeClass('scrolled');
                $('#logo').attr('src', 'https://www.sixsenses.com/Content/Images/logo-six-senses-white.svg');
            }
        });

        // XỬ LÝ LÔ-GÍC LỊCH MỚI CỦA MÀY
        let currentSelecting = 'checkin';
        let checkinDate = null;
        let checkoutDate = null;

        const today = new Date();
        today.setHours(0, 0, 0, 0); // Xóa giờ phút giây để so sánh ngày chính xác

        let currentMonth = today.getMonth() + 1;
        let currentYear = today.getFullYear();

        function openCalendar(type, event) {
            if (event) event.stopPropagation();
            currentSelecting = type;
            $('#guestPopover').hide();
            showCalendar(); // Reset về hiển thị lưới ngày
            $('#calendarPopover').fadeIn(200);
        }

        function openGuests(event) {
            if (event) event.stopPropagation();
            $('#calendarPopover').hide();
            $('#guestPopover').fadeToggle(200);
        }

        function updateHeader() {
            $('#monthDisplay').text('Tháng ' + currentMonth);
            $('#yearDisplay').text(currentYear);
        }

        // --- LUỒNG 1: HIỂN THỊ CHỌN NĂM ---
        function showYearSelector() {
            let html = '<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 10px;">';
            let startYear = today.getFullYear(); // Không cho chọn năm trước năm hiện tại
            for (let i = 0; i < 12; i++) {
                let y = startYear + i;
                html += `<div class="year-cell cursor-pointer hover-bg border" data-year="${y}">${y}</div>`;
            }
            html += '</div>';

            $('.calendar-days').html(html);
            $('#prevMonth, #nextMonth').hide(); // Ẩn mũi tên chuyển tháng
        }

        // --- LUỒNG 2: HIỂN THỊ CHỌN THÁNG ---
        function showMonthSelector() {
            let html = '<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 10px;">';
            for (let m = 1; m <= 12; m++) {
                // Lock tháng trong quá khứ nếu đang ở năm hiện tại
                let isPastMonth = (currentYear === today.getFullYear() && m < (today.getMonth() + 1));
                let classes = isPastMonth ? 'month-cell past border' : 'month-cell cursor-pointer hover-bg border';
                html += `<div class="${classes}" data-month="${m}">Tháng ${m}</div>`;
            }
            html += '</div>';

            $('.calendar-days').html(html);
            $('#prevMonth, #nextMonth').hide();
        }

        // --- LUỒNG 3: HIỂN THỊ LỊCH (CHỌN NGÀY) ---
        function showCalendar() {
            $('#prevMonth, #nextMonth').show(); // Hiện lại mũi tên
            updateHeader();

            let html = `
                <div class="d-grid text-center" style="grid-template-columns: repeat(7, 1fr); font-size: 0.7rem; color: #999; margin-bottom: 10px;">
                    <div>CN</div><div>T2</div><div>T3</div><div>T4</div><div>T5</div><div>T6</div><div>T7</div>
                </div>
                <div id="calendarGrid" class="d-grid" style="grid-template-columns: repeat(7, 1fr);"></div>
            `;
            $('.calendar-days').html(html);

            const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();
            const firstDay = new Date(currentYear, currentMonth - 1, 1).getDay();
            const grid = $('#calendarGrid');

            for (let i = 0; i < firstDay; i++) {
                grid.append('<div></div>');
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dateRender = new Date(currentYear, currentMonth - 1, day);
                const isPast = dateRender < today;

                let isCheckin = checkinDate && dateRender.getTime() === checkinDate.getTime();
                let isCheckout = checkoutDate && dateRender.getTime() === checkoutDate.getTime();
                let inRange = checkinDate && checkoutDate && dateRender > checkinDate && dateRender < checkoutDate;

                let classes = 'date-cell ';
                if (isPast) {
                    classes += 'past';
                } else if (isCheckin || isCheckout) {
                    classes += 'selected';
                } else if (inRange) {
                    classes += 'in-range';
                } else {
                    classes += 'available';
                }

                grid.append(`<div class="${classes}" data-day="${day}">${day}</div>`);
            }
        }

        // --- BẮT SỰ KIỆN CLICK CHO LUỒNG ---

        // Click vào chữ Năm -> Hiện bảng năm
        $(document).on('click', '#yearDisplay', function(e) {
            e.stopPropagation();
            showYearSelector();
        });

        // Click vào chữ Tháng -> Hiện bảng tháng
        $(document).on('click', '#monthDisplay', function(e) {
            e.stopPropagation();
            showMonthSelector();
        });

        // Chọn năm xong -> Quay lại lịch chọn ngày
        $(document).on('click', '.year-cell:not(.past)', function(e) {
            e.stopPropagation();
            currentYear = parseInt($(this).data('year'));
            showCalendar();
        });

        // Chọn tháng xong -> Tự động chuyển sang lịch chọn ngày
        $(document).on('click', '.month-cell:not(.past)', function(e) {
            e.stopPropagation();
            currentMonth = parseInt($(this).data('month'));
            showCalendar();
        });

        // Mũi tên chuyển tháng nhanh ở ngoài lịch
        $('#prevMonth').click(function(e) {
            e.stopPropagation();
            let tempMonth = currentMonth - 1;
            let tempYear = currentYear;
            if (tempMonth < 1) { tempMonth = 12; tempYear--; }

            // Chặn lùi về tháng trước nếu là tháng quá khứ
            if (tempYear < today.getFullYear() || (tempYear === today.getFullYear() && tempMonth < today.getMonth() + 1)) {
                return; // Không làm gì cả
            }
            currentMonth = tempMonth;
            currentYear = tempYear;
            showCalendar();
        });

        $('#nextMonth').click(function(e) {
            e.stopPropagation();
            currentMonth++;
            if (currentMonth > 12) { currentMonth = 1; currentYear++; }
            showCalendar();
        });

        // Xử lý chọn ngày Nhận / Trả phòng
        $(document).on('click', '.date-cell.available, .date-cell.in-range', function(e) {
            e.stopPropagation();
            const day = parseInt($(this).data('day'));
            const selectedDate = new Date(currentYear, currentMonth - 1, day);

            let formattedDate = day + '/' + currentMonth + '/' + currentYear;

            if (currentSelecting === 'checkin') {
                checkinDate = selectedDate;
                checkoutDate = null; // Reset trả phòng
                $('#checkinDisplay').text(formattedDate).removeClass('text-muted');
                $('#checkoutDisplay').text('Chọn ngày...');

                // Chọn xong ngày nhận -> Ẩn luôn popover
                currentSelecting = 'checkout';
                $('#calendarPopover').fadeOut(200);
            } else {
                if (checkinDate && selectedDate <= checkinDate) {
                    alert('Ngày trả phòng phải sau ngày nhận phòng nha sếp!');
                    return;
                }
                checkoutDate = selectedDate;
                $('#checkoutDisplay').text(formattedDate).removeClass('text-muted');
                $('#calendarPopover').fadeOut(200); // Chọn xong 2 cái thì tắt popover
            }
            showCalendar(); // Vẽ lại lịch để đổ màu
        });

        // Đóng Popover khi click ra ngoài
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-box, .booking-popover').length) {
                $('.booking-popover').fadeOut(150);
            }
        });

    </script>
</body>
</html>
