<div class="sidebar" id="sidebar">
    <button class="sidebar-close" onclick="toggleMenu()">&times;</button>
    <nav class="sidebar-nav">
        <a href="#">Trang chủ</a>
        <a href="#">Điểm đến</a>
        <a href="#">Lưu trú</a>
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
                    <span class="auth-link" style="text-transform: none; cursor: default;">Chào, {{ Auth::user()->username }}</span>
                    <span class="auth-separator mx-2">|</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline m-0 p-0">
                        @csrf
                        <button type="submit" class="auth-link" style="background: transparent; border: none; padding: 0; outline: none;">ĐĂNG XUẤT</button>
                    </form>
                @else
                    <a href="#" class="auth-link" data-bs-toggle="modal" data-bs-target="#loginModal">ĐĂNG NHẬP</a>
                    <span class="auth-separator mx-2">|</span>
                    <a href="#" class="auth-link" data-bs-toggle="modal" data-bs-target="#registerModal">ĐĂNG KÝ</a>
                @endauth

            </div>
            <a href="#" class="btn-book">ĐẶT NGAY</a>
        </div>
    </div>
</nav>
