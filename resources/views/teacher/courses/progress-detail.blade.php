{{-- resources/views/teacher/courses/progress-detail.blade.php --}}
@extends('teacher.layout')

@section('title', 'Chi tiết tiến độ - ' . $student->name)
@section('courses-active', 'active')
@section('page-icon', 'person-lines-fill')
@section('page-title', 'Chi tiết tiến độ học viên')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.index') }}">Khóa học</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.show', $course) }}">{{ $course->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.progress', $course) }}">Tiến độ</a></li>
            <li class="breadcrumb-item active">{{ $student->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ $student->name }}</h4>
            <p class="text-secondary mb-0">{{ $student->email }}</p>
        </div>
        <a href="{{ route('teacher.courses.progress', $course) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Progress Overview -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="mb-0">Tiến độ</h6>
                    <h2 class="mt-2 mb-0">{{ $percent }}%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-0">Bài đã học</h6>
                    <h2 class="mt-2 mb-0">{{ $completedCount }}/{{ $totalLessons }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <h6 class="mb-0">Bài còn lại</h6>
                    <h2 class="mt-2 mb-0">{{ $totalLessons - $completedCount }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="mb-3">Tiến độ tổng thể</h6>
            <div class="progress" style="height: 25px;">
                <div class="progress-bar bg-success" style="width: {{ $percent }}%">
                    {{ $percent }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Lessons List -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-list-check me-2 text-primary"></i>Chi tiết bài học</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Bài học</th>
                            <th>Trạng thái</th>
                            <th>Ngày hoàn thành</th>
                            <th class="text-end pe-4">Quiz</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lessons as $lesson)
                            @php
                                $isCompleted = in_array($lesson->id, $completedLessons);
                                $completedAt = $isCompleted ? $student->completedLessons()->find($lesson->id)?->pivot?->completed_at : null;
                            @endphp
                            <tr>
                                <td class="ps-4">{{ $lesson->order_number }}</td>
                                <td>{{ $lesson->title }}</td>
                                <td>
                                    @if($isCompleted)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Đã học
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-clock"></i> Chưa học
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($completedAt)
                                        {{ \Carbon\Carbon::parse($completedAt)->format('d/m/Y H:i') }}
                                    @else
                                        --
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @php
                                        $quizForLesson = $lesson->quizzes->first();
                                        $quizResult = $quizForLesson ? $quizForLesson->results()->where('user_id', $student->id)->first() : null;
                                    @endphp
                                    @if($quizForLesson)
                                        @if($quizResult)
                                            <span class="badge bg-{{ $quizResult->passed ? 'success' : 'danger' }}">
                                                {{ $quizResult->score }}% - {{ $quizResult->passed ? 'Đạt' : 'Trượt' }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Chưa làm</span>
                                        @endif
                                    @else
                                        <span class="text-secondary">Không có quiz</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quiz Results Summary -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Kết quả quiz</h5>
        </div>
        <div class="card-body">
            @if(empty($quizResults))
                <p class="text-secondary text-center py-4">Học viên chưa làm quiz nào.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Quiz</th>
                                <th>Điểm</th>
                                <th>Kết quả</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quizResults as $item)
                                @if($item['result'])
                                    <tr>
                                        <td>{{ $item['quiz']->title }}</td>
                                        <td>{{ $item['result']->score }}%</td>
                                        <td>
                                            @if($item['result']->passed)
                                                <span class="badge bg-success">ĐẠT</span>
                                            @else
                                                <span class="badge bg-danger">TRƯỢT</span>
                                            @endif
                                        </td>
                                        <td>{{ $item['result']->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection