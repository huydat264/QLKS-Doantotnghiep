<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HotelManager - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        html, body { height: 100%; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; overflow-x: hidden; }
        .sidebar { width: 260px; height: 100vh; top: 0; left: 0; position: fixed; background-color: #111c43; color: #fff; overflow-y: auto; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.22) transparent; }
        .sidebar::-webkit-scrollbar, .main-content::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track, .main-content::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb, .main-content::-webkit-scrollbar-thumb { background-color: rgba(255,255,255,0.22); border-radius: 999px; border: 1px solid rgba(255,255,255,0.08); }
        .sidebar::-webkit-scrollbar-thumb:hover, .main-content::-webkit-scrollbar-thumb:hover { background-color: rgba(255,255,255,0.32); }
        .sidebar .brand { padding: 20px; font-size: 20px; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar .nav-link { color: #a3aed1; padding: 12px 20px; margin: 4px 15px; border-radius: 8px; transition: 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #3e60d5; color: #fff; }
        .sidebar .nav-link i { margin-right: 10px; width: 20px; text-align: center; }
        .main-content { margin-left: 260px; padding: 20px; min-height: 100vh; max-height: 100vh; overflow-y: auto; scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.15) transparent; padding-bottom: 60px; }
        .topbar { background: #fff; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 0 15px rgba(0,0,0,0.05); }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.03); margin-bottom: 24px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">
            <i class="bi bi-building"></i> KIM BOUTIQUE HOTEL
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-house-door"></i> Tổng quan
                </a>
            </li>

            <li class="nav-item mt-3 mb-1 px-3 text-uppercase" style="font-size: 0.75rem; color: #6c757d;">Quản lý</li>

            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.datphong.*') ? 'active' : '' }}" href="{{ route('admin.datphong.index') }}"><i class="bi bi-calendar-check"></i> Đặt phòng</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.phong.*') ? 'active' : '' }}" href="{{ route('admin.phong.index') }}"><i class="bi bi-door-open"></i> Phòng</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.khachhang.*') ? 'active' : '' }}" href="{{ route('admin.khachhang.index') }}"><i class="bi bi-people"></i> Khách hàng</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dichvu.*') ? 'active' : '' }}" href="{{ route('admin.dichvu.index') }}"><i class="bi bi-cup-hot"></i> Dịch vụ</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.sudungdichvu.*') ? 'active' : '' }}" href="{{ route('admin.sudungdichvu.index') }}"><i class="bi bi-cart-plus"></i> Sử dụng dịch vụ</a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-credit-card"></i> Thanh toán</a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-box-seam"></i> Combo</a></li>

            @if(Auth::check() && strtoupper(trim(Auth::user()->role)) === 'ADMIN')
                <li class="nav-item mt-3 mb-1 px-3 text-uppercase" style="font-size: 0.75rem; color: #6c757d;">Nội bộ & Thống kê</li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-person-badge"></i> Nhân viên</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-clock-history"></i> Chấm công</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-cash-coin"></i> Bảng lương</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-graph-up-arrow"></i> Báo cáo thống kê</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-ticket-perforated"></i> Quản lý voucher</a></li>
            @endif
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold">@yield('page_title', 'Tổng quan')</h4>
            <div class="d-flex align-items-center">
                <i class="bi bi-search fs-5 me-3 text-muted"></i>
                <i class="bi bi-bell fs-5 me-3 text-muted"></i>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->username ?? 'Tài khoản' }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Hồ sơ</a></li>
                        <li>
                            <form id="adminLogoutForm" action="{{ route('admin.logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item">Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
