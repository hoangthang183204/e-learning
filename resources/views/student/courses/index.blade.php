{{-- resources/views/student/courses/index.blade.php --}}
@extends('student.layout')

@section('page-icon', 'book')
@section('page-title', 'Khóa học')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <ul class="nav nav-tabs" id="courseTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                    <i class="bi bi-grid-3x3-gap-fill me-1"></i> Tất cả
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="enrolled-tab" data-bs-toggle="tab" data-bs-target="#enrolled" type="button">
                    <i class="bi bi-check-circle-fill me-1 text-success"></i> Đã đăng ký
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                    <i class="bi bi-hourglass-split me-1 text-warning"></i> Chờ duyệt
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button">
                    <i class="bi bi-check-circle-fill me-1 text-primary"></i> Đã hoàn thành
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content" id="courseTabsContent">
    <!-- Tab Tất cả khóa học -->
    <div class="tab-pane fade show active" id="all" role="tabpanel">
        <div class="row">
            @forelse($courses as $course)
                @include('student.courses.partials.course-card', ['course' => $course])
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-journal-bookmark fs-1 text-secondary"></i>
                        <h5 class="mt-3">Chưa có khóa học nào</h5>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Tab Đã đăng ký -->
    <div class="tab-pane fade" id="enrolled" role="tabpanel">
        <div class="row">
            @forelse($courses->where('enrollment_status', 'approved') as $course)
                @include('student.courses.partials.course-card', ['course' => $course])
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-book fs-1 text-secondary"></i>
                        <h5 class="mt-3">Bạn chưa đăng ký khóa học nào</h5>
                        <a href="#all" class="btn btn-primary mt-3" onclick="document.getElementById('all-tab').click()">
                            Khám phá khóa học
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Tab Chờ duyệt -->
    <div class="tab-pane fade" id="pending" role="tabpanel">
        <div class="row">
            @forelse($courses->where('enrollment_status', 'pending') as $course)
                @include('student.courses.partials.course-card', ['course' => $course])
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-hourglass-split fs-1 text-secondary"></i>
                        <h5 class="mt-3">Không có yêu cầu đăng ký nào đang chờ</h5>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Tab Đã hoàn thành -->
    <div class="tab-pane fade" id="completed" role="tabpanel">
        <div class="row">
            @forelse($courses->where('enrollment_status', 'finished') as $course)
                @include('student.courses.partials.course-card', ['course' => $course])
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-trophy fs-1 text-secondary"></i>
                        <h5 class="mt-3">Bạn chưa hoàn thành khóa học nào</h5>
                        <a href="#enrolled" class="btn btn-primary mt-3" onclick="document.getElementById('enrolled-tab').click()">
                            Tiếp tục học
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection