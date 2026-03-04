{{-- resources/views/admin/dashboard.blade.php --}}
@extends('admin.layout')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <!-- Content Row - Stats Cards -->
        <div class="row">
            <!-- Users Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Tổng người dùng</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-arrow-right"></i> Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teachers Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Giảng viên</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTeachers }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Học viên</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Khóa học</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCourses }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-arrow-right"></i> Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row - Additional Stats -->
        <div class="row">
            <!-- Lessons Card -->
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Bài học</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLessons }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-video fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quizzes Card -->
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Bài kiểm tra</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalQuizzes }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-question-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row - Recent Data -->
        <div class="row">
            <!-- Recent Users -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Người dùng mới nhất</h6>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentUsers as $index => $user)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if ($user->role == 'admin')
                                                    <span class="badge bg-danger">Admin</span>
                                                @elseif($user->role == 'teacher')
                                                    <span class="badge bg-success">Teacher</span>
                                                @else
                                                    <span class="badge bg-info">Student</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Chưa có người dùng nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Courses -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Khóa học mới nhất</h6>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tên khóa học</th>
                                        <th>Giảng viên</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentCourses as $index => $course)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $course->title }}</td>
                                            <td>{{ $course->teacher->name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($course->status)
                                                    <span class="badge bg-success">Hoạt động</span>
                                                @else
                                                    <span class="badge bg-secondary">Tạm dừng</span>
                                                @endif
                                            </td>
                                            <td>{{ $course->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Chưa có khóa học nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }

        .border-left-success {
            border-left: 4px solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 4px solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: 4px solid #f6c23e !important;
        }

        .border-left-secondary {
            border-left: 4px solid #858796 !important;
        }

        .border-left-dark {
            border-left: 4px solid #5a5c69 !important;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }
    </style>
@endpush
