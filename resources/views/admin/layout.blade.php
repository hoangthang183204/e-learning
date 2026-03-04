<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 cho icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fc;
            overflow-x: hidden;
        }

        /* Wrapper */
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #2c3e50 0%, #1a2634 100%);
            color: #fff;
            transition: all 0.3s ease;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #2c3e50;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #4a627a;
            border-radius: 10px;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(135deg, #fff 0%, #a8c0ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-header p {
            margin: 5px 0 0;
            font-size: 0.8rem;
            color: #a0aec0;
        }

        /* Menu Items */
        .nav-menu {
            list-style: none;
            padding: 0 15px;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #e2e8f0;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-link i {
            width: 24px;
            font-size: 1.2rem;
            margin-right: 12px;
            color: #a0aec0;
            transition: all 0.3s;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            transform: translateX(5px);
        }

        .nav-link:hover i {
            color: #fff;
        }

        .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .nav-link.active i {
            color: #fff;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 20px 15px;
        }

        /* Menu Label */
        .menu-label {
            padding: 10px 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #a0aec0;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            width: calc(100% - 280px);
            transition: all 0.3s ease;
        }

        /* Top Navbar */
        .top-navbar {
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .navbar-left {
            display: flex;
            align-items: center;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #2c3e50;
            cursor: pointer;
            display: none;
            margin-right: 15px;
        }

        .page-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-badge {
            position: relative;
            cursor: pointer;
        }

        .notification-badge i {
            font-size: 1.2rem;
            color: #2c3e50;
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74c3c;
            color: #fff;
            border-radius: 50%;
            padding: 3px 6px;
            font-size: 0.7rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
        }

        .user-name {
            font-weight: 500;
            color: #2c3e50;
        }

        .user-name small {
            display: block;
            font-size: 0.7rem;
            color: #7f8c8d;
        }

        /* Content Area */
        .content-area {
            padding: 30px;
            background: #f8f9fc;
            min-height: calc(100vh - 76px);
        }

        /* Footer */
        .footer {
            background: #fff;
            padding: 15px 30px;
            text-align: center;
            color: #7f8c8d;
            border-top: 1px solid #ecf0f1;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -280px;
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .toggle-sidebar {
                display: block;
            }

            .user-name {
                display: none;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .content-area {
            animation: fadeIn 0.5s ease;
        }

        /* Custom scrollbar cho main content */
        .main-content::-webkit-scrollbar {
            width: 8px;
        }

        .main-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .main-content::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .main-content::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>AdminPanel</h3>
                <p>Quản trị hệ thống</p>
            </div>

            <!-- Main Menu -->
            <ul class="nav-menu">
                <li class="menu-label">MAIN</li>
                <li class="nav-item">
                    <a href="/admin" class="nav-link @yield('dashboard-active')">
                        <i class="fas fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/users" class="nav-link @yield('users-active')">
                        <i class="fas fa-users"></i>
                        <span>Quản lý User</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/courses" class="nav-link @yield('users-active')">
                        <i class="fas fa-book"></i>
                        <span>Quản lý Khoá Học</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/lessons" class="nav-link @yield('users-active')">
                        <i class="fas fa-book"></i>
                        <span>Bài Học</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.quizzes.index') }}"
                        class="nav-link {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
                        <i class="fas fa-question-circle"></i>
                        <span>Bài Kiểm Tra</span>
                    </a>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Thống kê</span>
                    </a>
                </li>
            </ul>

            <div class="divider"></div>

            <!-- Management Menu -->
            <ul class="nav-menu">
                <li class="menu-label">QUẢN LÝ</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-newspaper"></i>
                        <span>Bài viết</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-tags"></i>
                        <span>Danh mục</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-comments"></i>
                        <span>Bình luận</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-image"></i>
                        <span>Media</span>
                    </a>
                </li>
            </ul>

            <div class="divider"></div>

            <!-- Settings Menu -->
            <ul class="nav-menu">
                <li class="menu-label">CÀI ĐẶT</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-gear"></i>
                        <span>Cài đặt chung</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-shield"></i>
                        <span>Bảo mật</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-palette"></i>
                        <span>Giao diện</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <!-- Top Navbar -->
            <nav class="top-navbar">
                <div class="navbar-left">
                    <button class="toggle-sidebar" id="toggle-sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="page-title">@yield('page-title', 'Dashboard')</h4>
                </div>

                <div class="navbar-right">
                    <div class="notification-badge">
                        <i class="far fa-bell"></i>
                        <span class="badge">3</span>
                    </div>

                    <div class="user-info">
                        <div class="user-avatar">
                            <span>AD</span>
                        </div>
                        <div class="user-name">
                            Admin User
                            <small>Administrator</small>
                        </div>
                    </div>

                    <form method="POST" action="/logout" class="d-inline">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </nav>

            <!-- Content Area -->
            <div class="content-area">
                @yield('content')
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; 2024 AdminPanel. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS và Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toggle Sidebar Script -->
    <script>
        document.getElementById('toggle-sidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggle-sidebar');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Active menu item based on current URL
        document.addEventListener('DOMContentLoaded', function() {
            const currentLocation = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');

            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentLocation) {
                    link.classList.add('active');
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
