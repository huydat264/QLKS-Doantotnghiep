<style>
    /* CSS bổ sung: Tự động drop khi di chuột vào (Chỉ áp dụng trên máy tính) */
    @media (min-width: 992px) {
        .auth-links .dropdown:hover .dropdown-menu {
            display: block !important;
        }
        /* Tạo một lớp bọc trong suốt để khi di chuột từ tên xuống menu không bị mất dấu */
        .auth-links .dropdown .dropdown-menu::before {
            content: "";
            position: absolute;
            top: -10px;
            left: 0;
            right: 0;
            height: 10px;
            background: transparent;
        }
    }
</style>
<div class="sidebar" id="sidebar">
    <button class="sidebar-close" onclick="toggleMenu()">&times;</button>
    <nav class="sidebar-nav">
        <a href="{{ route('home') }}">Trang chủ</a>
        <a href="{{ url('/diem-den') }}">Điểm đến</a>
        <a href="{{ route('phong.user') }}">Lưu trú</a>
        <a href="{{ route('combo.index') }}">Combo</a>
        <a href="#">Trải nghiệm ẩm thực</a>
        <a href="#">Wellness & Spa</a>
        <a href="#">Phát triển bền vững</a>
        <a href="#">Liên hệ</a>
    </nav>
</div>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container d-flex align-items-center">
        <div style="flex: 1;">
            <button class="menu-toggle" onclick="toggleMenu()">
                <span></span><span></span>
            </button>
        </div>

        <div class="text-center" style="flex: 1;">
            <a href="/">
                <img src="https://www.sixsenses.com/Content/Images/logo-six-senses-white.svg" id="logo" alt="Logo">
            </a>
        </div>

        <div class="d-flex align-items-center justify-content-end" style="flex: 1;">
            <div class="d-none d-lg-flex align-items-center auth-links">

                @auth
                    <div class="dropdown d-inline-block">
                        <span class="auth-link dropdown-toggle" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="text-transform: none; cursor: pointer;">
                            Chào, {{ Auth::user()->username }}
                        </span>
                        <ul class="dropdown-menu dropdown-menu-end shadow mt-2" aria-labelledby="userMenu" style="background-color: #ffffff; border: 1px solid #f1eeea; border-radius: 4px; min-width: 180px;">
                            <li>
                                <a class="dropdown-item py-2 px-3" href="{{ route('booking.history') }}" style="color: #673065; font-size: 13px; font-weight: bold; text-transform: none;">
                                    Lịch sử đặt phòng
                                </a>
                            </li>
                            <li><hr class="dropdown-divider" style="margin: 6px 0; border-color: #f1eeea;"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 px-3 text-danger" style="font-size: 13px; font-weight: bold; background: none; border: none; width: 100%; text-align: left; text-transform: none;">
                                        Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="#" class="auth-link" data-bs-toggle="modal" data-bs-target="#loginModal">ĐĂNG NHẬP</a>
                    <span class="auth-separator mx-2">|</span>
                    <a href="#" class="auth-link" data-bs-toggle="modal" data-bs-target="#registerModal">ĐĂNG KÝ</a>
                @endauth

            </div>

            <a href="{{ route('phong.user') }}" class="btn-book">ĐẶT NGAY</a>
        </div>
    </div>
</nav>
