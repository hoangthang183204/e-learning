{{-- resources/views/teacher/courses/show.blade.php --}}
@extends('teacher.layout')

@section('title', $course->title)
@section('courses-active', 'active')
@section('page-icon', 'book')
@section('page-title', $course->title)
@section('page-subtitle', 'Quản lý chi tiết khóa học')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.index') }}">Khóa học</a></li>
            <li class="breadcrumb-item active">{{ $course->title }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <a href="{{ route('teacher.courses.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
                <span class="badge {{ $course->status == 1 ? 'bg-success' : 'bg-secondary' }} fs-6">
                    {{ $course->status == 1 ? 'Đã xuất bản' : 'Bản nháp' }}
                </span>
            </div>
            <h2 class="mb-0 fw-bold">{{ $course->title }}</h2>
        </div>
        <div class="btn-group">
            <a href="{{ route('teacher.courses.edit', $course) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> Sửa
            </a>
            <a href="{{ route('teacher.lessons.create', $course) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Thêm bài học
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-people text-primary fs-4"></i>
                        </div>
                        <div>
                            <div class="text-secondary small">Học viên</div>
                            <div class="fs-3 fw-bold">{{ $totalStudents }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-collection text-info fs-4"></i>
                        </div>
                        <div>
                            <div class="text-secondary small">Bài học</div>
                            <div class="fs-3 fw-bold">{{ $totalLessons }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-pencil-square text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="text-secondary small">Bài quiz</div>
                            <div class="fs-3 fw-bold">{{ $totalQuizzes }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-graph-up text-success fs-4"></i>
                        </div>
                        <div>
                            <div class="text-secondary small">Tiến độ TB</div>
                            <div class="fs-3 fw-bold">{{ $avgProgress }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Course Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Thông tin khóa học</h5>
                </div>
                <div class="card-body">
                    @if($course->short_description)
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-2">Mô tả ngắn</h6>
                            <p class="mb-0">{{ $course->short_description }}</p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary mb-2">Mô tả chi tiết</h6>
                        <p class="mb-0">{{ $course->description ?: 'Chưa có mô tả' }}</p>
                    </div>

                    <div class="row">
                        @if($course->requirements)
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold text-secondary mb-2">
                                    <i class="bi bi-check-circle text-success me-1"></i> Yêu cầu
                                </h6>
                                <p class="mb-0">{{ $course->requirements }}</p>
                            </div>
                        @endif
                        @if($course->what_will_learn)
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold text-secondary mb-2">
                                    <i class="bi bi-star text-warning me-1"></i> Bạn sẽ học được
                                </h6>
                                <p class="mb-0">{{ $course->what_will_learn }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Lessons List Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-check me-2 text-primary"></i>Danh sách bài học</h5>
                    <span class="badge bg-primary">{{ $totalLessons }} bài</span>
                </div>
                <div class="card-body">
                    @if($course->lessons->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-journal-x fs-1 text-secondary mb-3"></i>
                            <h6 class="text-secondary">Chưa có bài học nào</h6>
                            <p class="text-secondary small mb-3">Bắt đầu thêm bài học đầu tiên cho khóa học</p>
                            <a href="{{ route('teacher.lessons.create', $course) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Thêm bài học
                            </a>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($course->lessons as $lesson)
                                <div class="list-group-item list-group-item-action d-flex align-items-center p-3 mb-2 border rounded-3">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <div class="bg-primary bg-opacity-10 rounded-2 p-2 me-3" style="width: 40px; text-align: center;">
                                            <span class="fw-bold text-primary">{{ $lesson->order_number }}</span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-semibold">{{ $lesson->title }}</h6>
                                            @if($lesson->video_url)
                                                <small class="text-success">
                                                    <i class="bi bi-play-circle me-1"></i>Có video
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('teacher.lessons.show', [$course, $lesson]) }}" 
                                           class="btn btn-sm btn-outline-info" title="Xem">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('teacher.lessons.edit', [$course, $lesson]) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Course Image Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" 
                             class="img-fluid rounded-top" style="width: 100%; height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded-top d-flex align-items-center justify-content-center" 
                             style="height: 200px; background: linear-gradient(135deg, {{ $course->color_1 ?? '#4158D0' }}, {{ $course->color_2 ?? '#C850C0' }}) !important;">
                            <i class="bi bi-image text-white fs-1"></i>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Course Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Chi tiết</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="text-secondary" width="100">Ngày tạo</td>
                            <td class="fw-semibold">{{ $course->created_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Thời lượng</td>
                            <td class="fw-semibold">{{ $course->duration ?? 0 }} phút</td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Giá</td>
                            <td class="fw-semibold text-success">
                                {{ $course->price > 0 ? number_format($course->price) . 'đ' : 'Miễn phí' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Ngôn ngữ</td>
                            <td class="fw-semibold">{{ strtoupper($course->language ?? 'vi') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Recent Students Card -->
            @if(isset($recentStudents) && $recentStudents->isNotEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-people me-2 text-primary"></i>Học viên gần đây</h5>
                        <span class="badge bg-primary">{{ $recentStudents->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($recentStudents as $student)
                                <div class="list-group-item d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-person text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong>{{ $student->name }}</strong>
                                        <br>
                                        <small class="text-secondary">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $student->pivot->enrolled_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-white text-center">
                        <a href="{{ route('teacher.courses.students', $course) }}" class="text-decoration-none small">
                            Xem tất cả <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection