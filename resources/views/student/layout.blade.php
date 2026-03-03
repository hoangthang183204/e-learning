{{-- resources/views/layouts/student.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student - @yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 12px 0;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 24px;
            letter-spacing: 0.5px;
            color: white !important;
        }

        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 8px 16px !important;
            margin: 0 4px;
            border-radius: 6px;
        }

        .navbar-nav .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white !important;
        }

        .navbar-nav .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white !important;
        }

        .logout-btn {
            background: white;
            color: #198754;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: #f8f9fa;
            color: #146c43;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Page Header */
        .page-header {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #212529;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title i {
            color: #198754;
            font-size: 28px;
        }

        .breadcrumb {
            margin-top: 10px;
            margin-bottom: 0;
            background: none;
            padding: 0;
        }

        .breadcrumb-item a {
            color: #198754;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        /* Content Card */
        .content-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
        }

        /* Footer */
        .footer {
            background: white;
            border-top: 1px solid #dee2e6;
            padding: 20px 0;
            margin-top: auto;
            font-size: 14px;
            color: #6c757d;
        }

        .footer a {
            color: #198754;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-nav {
                margin: 10px 0;
            }
            
            .navbar-nav .nav-link {
                padding: 10px !important;
            }
            
            .logout-btn {
                width: 100%;
                justify-content: center;
            }
            
            .page-header {
                padding: 15px;
            }
            
            .page-title {
                font-size: 20px;
            }
        }
    </style>

    @yield('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/student">
                <i class="bi bi-mortarboard-fill me-2"></i>
                E-LEARNING
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('student.dashboard')) active @endif" 
                           href="/student">
                            <i class="bi bi-house-door me-1"></i> Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('student.courses.*')) active @endif" 
                           href="{{ route('student.courses.index') }}">
                            <i class="bi bi-book me-1"></i> Khoá học
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('student.quizzes.*')) active @endif" 
                           href="">
                            <i class="bi bi-pencil-square me-1"></i> Bài quiz
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('student.progress')) active @endif" 
                           href="">
                            <i class="bi bi-graph-up me-1"></i> Tiến độ
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <span class="text-white me-3 d-none d-lg-block">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ auth()->user()->name ?? 'Student' }}
                    </span>
                    
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="d-lg-none">Đăng xuất</span>
                            <span class="d-none d-lg-inline">Đăng xuất</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="bi bi-@yield('page-icon', 'mortarboard')"></i>
                    @yield('page-title')
                </h1>
                @hasSection('breadcrumb')
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                @endif
            </div>

            <!-- Content -->
            <div class="content-card">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">© {{ date('Y') }} E-Learning. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="me-3">Trợ giúp</a>
                    <a href="#">Liên hệ</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>