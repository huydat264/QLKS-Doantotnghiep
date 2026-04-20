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
        .booking-popover { display: none; position: absolute; bottom: calc(100% + 15px); background: white; border-radius: 12px; z-index: 2000; box-shadow: 0 15px 50px rgba(0,0,0,0.15); padding: 30px; color: var(--text-dark); }
        #calendarPopover { left: 0; width: 100%; }
        #guestPopover { right: 0; width: 350px; }
        .date-cell { padding: 10px; font-size: 0.9rem; cursor: pointer; text-align: center; }
        .date-cell.available { background-color: #D9E6A9; border-radius: 4px; }
        .date-cell:hover:not(.muted) { background: #000; color: #fff; border-radius: 4px; }

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
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true });

        // Hàm điều khiển Sidebar (Menu)
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

        // Logic ẩn hiện Popover
        function openCalendar() { $('#guestPopover').hide(); $('#calendarPopover').fadeToggle(200); }
        function openGuests() { $('#calendarPopover').hide(); $('#guestPopover').fadeToggle(200); }

        // Đóng Popover khi click ra ngoài
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-box, .booking-popover').length) {
                $('.booking-popover').fadeOut(150);
            }
        });
    </script>
</body>
</html>
