{{-- resources/views/teacher/statistics.blade.php --}}
@extends('teacher.layout')

@section('title', 'Thống kê tổng quan')
@section('statistics-active', 'active')
@section('page-icon', 'graph-up')
@section('page-title', 'Thống kê tổng quan')

@section('content')
<div class="container-fluid px-4">
    <h4 class="mb-4">Thống kê tổng quan</h4>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="mb-0">Tổng khóa học</h6>
                    <h2 class="mt-2 mb-0">{{ $totalCourses }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-0">Tổng học viên</h6>
                    <h2 class="mt-2 mb-0">{{ $totalStudents }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <h6 class="mb-0">Tổng bài học</h6>
                    <h2 class="mt-2 mb-0">{{ $totalLessons }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <h6 class="mb-0">Tổng quiz</h6>
                    <h2 class="mt-2 mb-0">{{ $totalQuizzes }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Courses -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-star me-2 text-primary"></i>Top khóa học có nhiều học viên nhất</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Khóa học</th>
                            <th>Số học viên</th>
                            <th>Số bài học</th>
                            <th>Tiến độ TB</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCourses as $index => $course)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('teacher.courses.show', $course) }}" class="text-decoration-none">
                                        {{ $course->title }}
                                    </a>
                                </td>
                                <td>{{ $course->students_count }}</td>
                                <td>{{ $course->lessons_count }}</td>
                                <td>{{ $course->avg_progress }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Hoạt động gần đây</h5>
        </div>
        <div class="card-body">
            <div class="list-group">
                @forelse($recentActivities as $activity)
                    <div class="list-group-item border-0 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-{{ $activity->type == 'enrollment' ? 'primary' : ($activity->type == 'completion' ? 'success' : 'warning') }} bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-{{ $activity->icon }} text-{{ $activity->type == 'enrollment' ? 'primary' : ($activity->type == 'completion' ? 'success' : 'warning') }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong>{{ $activity->user_name }}</strong> {{ $activity->description }}
                                <br>
                                <small class="text-secondary">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-secondary text-center py-4">Chưa có hoạt động nào</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection