@extends('student.layout')

@section('page-icon', 'play-circle')
@section('page-title', $lesson->title)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.courses.index') }}">Khóa học</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student.courses.show', $lesson->course) }}">{{ $lesson->course->title }}</a></li>
    <li class="breadcrumb-item active">{{ $lesson->title }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Video -->
        @if($videoId)
            <div class="ratio ratio-16x9 mb-4">
                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" 
                        title="{{ $lesson->title }}"
                        allowfullscreen>
                </iframe>
            </div>
        @elseif($lesson->video_url)
            {{-- Nếu không parse được ID nhưng có URL, hiển thị link --}}
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                Video bài học: 
                <a href="{{ $lesson->video_url }}" target="_blank" class="alert-link">
                    {{ $lesson->video_url }}
                </a>
            </div>
        @else
            <div class="alert alert-secondary mb-4">
                <i class="bi bi-camera-video-off me-2"></i>
                Bài học chưa có video
            </div>
        @endif

        <!-- Nội dung bài học -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Nội dung bài học</h5>
            </div>
            <div class="card-body">
                @if($lesson->content)
                    <div class="lesson-content">
                        {!! nl2br(e($lesson->content)) !!}
                    </div>
                @else
                    <p class="text-secondary mb-0">Bài học chưa có nội dung.</p>
                @endif
            </div>
        </div>

        <!-- Bài quiz -->
        @if($quiz)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Bài kiểm tra</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6>{{ $quiz->title }}</h6>
                            <p class="text-secondary mb-0">
                                <i class="bi bi-question-circle me-1"></i>
                                {{ $quiz->questions->count() }} câu hỏi
                            </p>
                        </div>
                        <div>
                            @if($quizCompleted)
                                <span class="badge bg-success fs-6 p-2 me-2">
                                    <i class="bi bi-check-circle"></i> Đã làm - {{ $quizResult->score }}/{{ $quiz->questions->count() }}
                                </span>
                                <a href="{{ route('student.quiz.result', $quiz) }}" class="btn btn-outline-info">
                                    <i class="bi bi-eye"></i> Xem kết quả
                                </a>
                            @else
                                <a href="{{ route('student.quiz.show', $quiz) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i> Làm bài kiểm tra
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Điều hướng bài học -->
        <div class="d-flex justify-content-between">
            @if($prevLesson)
                <a href="{{ route('student.lessons.show', $prevLesson) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Bài trước
                </a>
            @else
                <span></span>
            @endif

            @if($nextLesson)
                <a href="{{ route('student.lessons.show', $nextLesson) }}" class="btn btn-outline-primary">
                    Bài tiếp theo <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <span></span>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <!-- Sidebar -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Thông tin bài học</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-book text-primary me-2"></i>
                        <strong>Khóa học:</strong><br>
                        <a href="{{ route('student.courses.show', $lesson->course) }}" class="text-decoration-none">
                            {{ $lesson->course->title }}
                        </a>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-sort-numeric-up text-primary me-2"></i>
                        <strong>Bài số:</strong> {{ $lesson->order_number }}
                    </li>
                    @if($lesson->duration)
                        <li class="mb-2">
                            <i class="bi bi-clock text-primary me-2"></i>
                            <strong>Thời lượng:</strong> {{ $lesson->duration }} phút
                        </li>
                    @endif
                    <li>
                        <i class="bi bi-check-circle text-primary me-2"></i>
                        <strong>Trạng thái:</strong><br>
                        @if($isCompleted)
                            <span class="badge bg-success mt-1">Đã hoàn thành</span>
                        @else
                            <span class="badge bg-secondary mt-1">Chưa học</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        <!-- Nút hoàn thành -->
        @if(!$isCompleted)
            <form method="POST" action="{{ route('student.lessons.complete', $lesson) }}">
                @csrf
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-check-circle"></i> Hoàn thành bài học
                </button>
            </form>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.lesson-content {
    line-height: 1.8;
    font-size: 16px;
}
.lesson-content p {
    margin-bottom: 1rem;
}
.lesson-content h1, 
.lesson-content h2, 
.lesson-content h3 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}
.lesson-content pre {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    overflow-x: auto;
}
.lesson-content code {
    background: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    color: #e83e8c;
}
</style>
@endpush