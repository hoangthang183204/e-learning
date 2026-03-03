<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher - @yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f4f6f9;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar styles */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #2c3e50 0%, #1e2a36 100%);
            color: #fff;
            transition: all 0.3s;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: #fff;
        }

        .sidebar-header p {
            margin: 5px 0 0;
            font-size: 13px;
            color: #a0b3c9;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #d0dae8;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-left-color: #4fc3f7;
        }

        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border-left-color: #4fc3f7;
        }

        .sidebar-menu i {
            margin-right: 12px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .sidebar-menu .menu-text {
            flex: 1;
        }

        .sidebar-menu .badge {
            padding: 4px 8px;
            font-size: 11px;
        }

        /* Main content styles */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 20px;
        }

        /* Top navbar styles */
        .top-navbar {
            background: #fff;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .page-title i {
            margin-right: 10px;
            color: #4e73df;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-name {
            color: #555;
            font-weight: 500;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #4e73df;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }

        .logout-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #dc3545;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .logout-btn:hover {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        /* Content card */
        .content-card {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                opacity: 0;
                visibility: hidden;
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Scrollbar styling */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #1e2a36;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #4a5b6e;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #5f738a;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>TEACHER</h3>
                <p>Quản lý giảng dạy</p>
            </div>

            <div class="sidebar-menu">
                <ul>
                    <li>
                        <a href="/teacher" class="@yield('dashboard-active')">
                            <i class="bi bi-speedometer2"></i>
                            <span class="menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/teacher/courses" class="@yield('courses-active')">
                            <i class="bi bi-book"></i>
                            <span class="menu-text">Khoá học</span>
                        </a>
                    </li>
                    <li>
                        <a href="/teacher/quizzes" class="@yield('quizzes-active')">
                            <i class="bi bi-pencil-square"></i>
                            <span class="menu-text">Quản lý Quiz</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teacher.courses.index') }}">
                            <i class="bi bi-people"></i>
                            <span class="menu-text">Học viên</span>
                        </a>
                    </li>
                    <li>
                        <a href="/teacher/analytics" class="@yield('analytics-active')">
                            <i class="bi bi-graph-up"></i>
                            <span class="menu-text">Thống kê</span>
                        </a>
                    </li>
                    <li>
                        <a href="/teacher/settings" class="@yield('settings-active')">
                            <i class="bi bi-gear"></i>
                            <span class="menu-text">Cài đặt</span>
                        </a>
                    </li>
                </ul>

                <hr style="border-color: rgba(255,255,255,0.1); margin: 20px;">

                <ul>
                    <li>
                        <a href="/teacher/help">
                            <i class="bi bi-question-circle"></i>
                            <span class="menu-text">Trợ giúp</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <h4 class="page-title">
                    <i class="bi bi-@yield('page-icon', 'grid')"></i>
                    @yield('page-title')
                </h4>

                <div class="user-info">
                    <span class="user-name">Xin chào, Giáo viên</span>
                    <div class="user-avatar">
                        <i class="bi bi-person"></i>
                    </div>
                    <form method="POST" action="/logout" class="m-0">
                        @csrf
                        <button class="logout-btn" type="submit">
                            <i class="bi bi-box-arrow-right"></i>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>

            <!-- Content -->
            <div class="content-card">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
