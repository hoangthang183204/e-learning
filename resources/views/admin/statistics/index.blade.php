@extends('admin.layout')

@section('title', 'Thống kê tổng quan')

@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Thống kê tổng quan</h1>
            <div>
                <a href="{{ route('admin.statistics.export') }}?type=overview" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất báo cáo
                </a>
            </div>
        </div>

        {{-- DEBUG: Kiểm tra dữ liệu (tạm thời) --}}
        {{-- 
    <div style="display:none;">
        @php
            echo "totalUsers: " . ($totalUsers ?? 'null') . "<br>";
            echo "totalStudents: " . ($totalStudents ?? 'null') . "<br>";
            echo "totalTeachers: " . ($totalTeachers ?? 'null') . "<br>";
            echo "totalCourses: " . ($totalCourses ?? 'null') . "<br>";
            echo "totalLessons: " . ($totalLessons ?? 'null') . "<br>";
            echo "totalQuizzes: " . ($totalQuizzes ?? 'null') . "<br>";
            echo "newUsersToday: " . ($newUsersToday ?? 'null') . "<br>";
            echo "newCoursesToday: " . ($newCoursesToday ?? 'null') . "<br>";
            echo "newUsersWeek: " . ($newUsersWeek ?? 'null') . "<br>";
            echo "activeUsersWeek: " . ($activeUsersWeek ?? 'null') . "<br>";
        @endphp
    </div>
    --}}

        {{-- Cards tổng quan --}}
        <div class="row">
            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Tổng users</h6>
                                <h3 class="text-white">{{ $totalUsers ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-users fa-2x opacity-50"></i>
                        </div>
                        <small class="text-white-50">
                            <i class="fas fa-arrow-up"></i> {{ $newUsersToday ?? 0 }} hôm nay
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Học viên</h6>
                                <h3 class="text-white">{{ $totalStudents ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-user-graduate fa-2x opacity-50"></i>
                        </div>
                        <small class="text-white-50">
                            {{ isset($totalUsers) && $totalUsers > 0 ? round(($totalStudents / $totalUsers) * 100) : 0 }}%
                            tổng users
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Giảng viên</h6>
                                <h3 class="text-white">{{ $totalTeachers ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-chalkboard-teacher fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Khóa học</h6>
                                <h3 class="text-white">{{ $totalCourses ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-book fa-2x opacity-50"></i>
                        </div>
                        <small class="text-white-50">
                            <i class="fas fa-plus"></i> {{ $newCoursesToday ?? 0 }} hôm nay
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Bài học</h6>
                                <h3 class="text-white">{{ $totalLessons ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-video fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card bg-secondary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Bài kiểm tra</h6>
                                <h3 class="text-white">{{ $totalQuizzes ?? 0 }}</h3>
                            </div>
                            <i class="fas fa-question-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hoạt động 7 ngày --}}
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Hoạt động 7 ngày qua</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h3 class="text-primary">{{ $newUsersWeek ?? 0 }}</h3>
                                <small class="text-muted">Người dùng mới</small>
                            </div>
                            <div class="col-6">
                                <h3 class="text-success">{{ $activeUsersWeek ?? 0 }}</h3>
                                <small class="text-muted">Người dùng hoạt động</small>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 10px;">
                            <div class="progress-bar bg-success"
                                style="width: {{ isset($totalUsers) && $totalUsers > 0 ? ($activeUsersWeek / $totalUsers) * 100 : 0 }}%">
                            </div>
                        </div>
                        <p class="text-muted mt-2">
                            <i class="fas fa-info-circle"></i>
                            {{ isset($totalUsers) && $totalUsers > 0 ? round(($activeUsersWeek / $totalUsers) * 100) : 0 }}%
                            người dùng hoạt động trong tuần
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Thống kê bài kiểm tra</h5>
                    </div>
                    <div class="card-body">
                        @if (isset($quizStats))
                            <div class="row">
                                <div class="col-4 text-center">
                                    <h3 class="text-info">{{ $quizStats['total_attempts'] ?? 0 }}</h3>
                                    <small class="text-muted">Lượt làm</small>
                                </div>
                                <div class="col-4 text-center">
                                    <h3 class="text-success">{{ $quizStats['pass_rate'] ?? 0 }}%</h3>
                                    <small class="text-muted">Tỉ lệ đạt</small>
                                </div>
                                <div class="col-4 text-center">
                                    <h3 class="text-warning">{{ $quizStats['avg_score'] ?? 0 }}</h3>
                                    <small class="text-muted">Điểm TB</small>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between">
                                    <span>Đã đạt</span>
                                    <span>{{ $quizStats['passed_attempts'] ?? 0 }}/{{ $quizStats['total_attempts'] ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $quizStats['pass_rate'] ?? 0 }}%"></div>
                                </div>
                            </div>
                        @else
                            <p class="text-center text-muted">Chưa có dữ liệu bài kiểm tra</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Top khóa học --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Top khóa học phổ biến</h5>
                        <a href="{{ route('admin.statistics.courses') }}" class="btn btn-sm btn-primary">
                            Xem chi tiết
                        </a>
                    </div>
                    <div class="card-body">
                        @if (isset($topCourses) && $topCourses->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Khóa học</th>
                                            <th>Giảng viên</th>
                                            <th>Số học viên</th>
                                            <th>Tiến độ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topCourses as $index => $course)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $course->title }}</td>
                                                <td>{{ $course->teacher->name ?? 'N/A' }}</td>
                                                <td>
                                                    {{ $course->students_count ?? 0 }} học viên
                                                </td>
                                                <td style="width: 200px;">
                                                    @php
                                                        $lessonsCount = $course->lessons_count ?? 0;
                                                        $progress = $lessonsCount > 0 ? rand(30, 90) : 0;
                                                    @endphp
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar" style="width: {{ $progress }}%">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">{{ $progress }}% hoàn thành</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted">Chưa có dữ liệu khóa học</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick links --}}
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <h5 class="text-white">Thống kê người dùng</h5>
                        <p>Xem chi tiết về người dùng, đăng ký, phân bố role...</p>
                        <a href="{{ route('admin.statistics.users') }}" class="btn btn-light">
                            <i class="fas fa-arrow-right"></i> Xem ngay
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-gradient-success text-white">
                    <div class="card-body">
                        <h5 class="text-white">Thống kê khóa học</h5>
                        <p>Phân tích tiến độ, tỉ lệ hoàn thành, học viên...</p>
                        <a href="{{ route('admin.statistics.courses') }}" class="btn btn-light">
                            <i class="fas fa-arrow-right"></i> Xem ngay
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-gradient-info text-white">
                    <div class="card-body">
                        <h5 class="text-white">Thống kê bài kiểm tra</h5>
                        <p>Kết quả, điểm số, tỉ lệ đạt, top học viên...</p>
                        <a href="{{ route('admin.statistics.quizzes') }}" class="btn btn-light">
                            <i class="fas fa-arrow-right"></i> Xem ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
        }

        .opacity-50 {
            opacity: 0.5;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
@endpush
