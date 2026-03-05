{{-- resources/views/teacher/lessons/show.blade.php --}}
@extends('teacher.layout')

@section('title', $lesson->title)
@section('courses-active', 'active')
@section('page-icon', 'book')
@section('page-title', $lesson->title)
@section('page-subtitle', $course->title)

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.index') }}">Khóa học</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.show', $course) }}">{{ $course->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.lessons.index', $course) }}">Bài học</a></li>
            <li class="breadcrumb-item active">{{ $lesson->title }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('teacher.lessons.index', $course) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <h4 class="mb-0">
                Bài {{ $lesson->order_number }}: {{ $lesson->title }}
            </h4>
        </div>
        <div class="btn-group">
            <a href="{{ route('teacher.lessons.edit', [$course, $lesson]) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Sửa bài học
            </a>
            <a href="{{ route('teacher.quizzes.create', ['lesson_id' => $lesson->id]) }}" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i> Thêm quiz
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Nội dung bài học -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-file-text me-2 text-primary"></i>Nội dung bài học</h5>
                </div>
                <div class="card-body">
                    @if($lesson->content)
                        <div class="lesson-content">
                            {!! nl2br(e($lesson->content)) !!}
                        </div>
                    @else
                        <p class="text-secondary text-center py-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Bài học chưa có nội dung.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Danh sách quiz -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Bài kiểm tra</h5>
                    <a href="{{ route('teacher.quizzes.create', ['lesson_id' => $lesson->id]) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-circle"></i> Thêm quiz
                    </a>
                </div>
                <div class="card-body">
                    @if($lesson->quizzes->isEmpty())
                        <p class="text-secondary text-center py-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Chưa có bài kiểm tra nào cho bài học này.
                        </p>
                    @else
                        <div class="list-group">
                            @foreach($lesson->quizzes as $quiz)
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $quiz->title }}</h6>
                                        <small class="text-secondary">
                                            <i class="bi bi-question-circle me-1"></i>
                                            {{ $quiz->questions_count ?? 0 }} câu hỏi |
                                            <i class="bi bi-people me-1"></i>
                                            {{ $quiz->attempts_count ?? 0 }} lượt làm
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn btn-sm btn-outline-secondary">
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

        <div class="col-md-4">
            <!-- Thông tin bài học -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Thông tin</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th class="text-secondary">Thứ tự:</th>
                            <td class="fw-bold">Bài {{ $lesson->order_number }}</td>
                        </tr>
                        <tr>
                            <th class="text-secondary">Video:</th>
                            <td>
                                @if($lesson->video_url)
                                    <a href="{{ $lesson->video_url }}" target="_blank" class="text-success">
                                        <i class="bi bi-play-circle me-1"></i> Xem video
                                    </a>
                                @else
                                    <span class="text-secondary">Không có</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-secondary">Quiz:</th>
                            <td>
                                <span class="badge bg-info">{{ $lesson->quizzes->count() }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-secondary">Ngày tạo:</th>
                            <td>{{ $lesson->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="text-secondary">Cập nhật:</th>
                            <td>{{ $lesson->updated_at->diffForHumans() }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Video preview -->
            @if($lesson->video_url)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-play-circle me-2 text-primary"></i>Video</h5>
                    </div>
                    <div class="card-body p-0">
                        @php
                            // Convert YouTube URL to embed URL
                            $videoId = '';
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $lesson->video_url, $match)) {
                                $videoId = $match[1];
                            }
                        @endphp
                        
                        @if($videoId)
                            <div class="ratio ratio-16x9">
                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" 
                                        title="YouTube video" 
                                        allowfullscreen>
                                </iframe>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-exclamation-triangle text-warning fs-1 mb-3"></i>
                                <p>Không thể hiển thị video. Link không hợp lệ.</p>
                                <a href="{{ $lesson->video_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    Mở link gốc
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

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
@endsection