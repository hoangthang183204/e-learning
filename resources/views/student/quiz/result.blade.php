@extends('student.layout')

@section('title', 'Kết quả bài kiểm tra')
@section('page-icon', 'check-circle-fill')
@section('page-title', 'Kết quả bài kiểm tra')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.courses.index') }}">Khóa học</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student.courses.show', $lesson->course) }}">{{ $lesson->course->title }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student.lessons.show', $lesson) }}">{{ $lesson->title }}</a></li>
    <li class="breadcrumb-item active">Kết quả</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <!-- Card kết quả -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-5">
                <!-- Icon và trạng thái -->
                <div class="mb-4">
                    @if($result->passed)
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                            <i class="bi bi-trophy-fill text-success" style="font-size: 48px;"></i>
                        </div>
                        <h2 class="text-success">🎉 CHÚC MỪNG!</h2>
                        <p class="lead text-muted">Bạn đã vượt qua bài kiểm tra</p>
                    @else
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 48px;"></i>
                        </div>
                        <h2 class="text-danger">📝 CHƯA ĐẠT</h2>
                        <p class="lead text-muted">Hãy ôn tập và làm lại bài kiểm tra</p>
                    @endif
                </div>
                
                <!-- Điểm số -->
                <div class="row justify-content-center mb-4">
                    <div class="col-md-6">
                        <div class="bg-light p-4 rounded">
                            <h1 class="display-1 fw-bold {{ $result->passed ? 'text-success' : 'text-danger' }}">
                                {{ $result->score }}
                            </h1>
                            <p class="text-secondary fs-5">/ {{ $result->total_questions }} câu</p>
                            @php
                                $percentage = $result->total_questions > 0 
                                    ? round(($result->score / $result->total_questions) * 100) 
                                    : 0;
                            @endphp
                            <p class="mb-0 fw-medium">Tỷ lệ đúng: <span class="text-primary">{{ $percentage }}%</span></p>
                        </div>
                    </div>
                </div>
                
                <!-- Thông tin chi tiết -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-2">
                        <div class="border rounded p-3 bg-light bg-opacity-50">
                            <small class="text-secondary text-uppercase fw-bold">Học viên</small>
                            <div class="fw-medium fs-5">{{ auth()->user()->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="border rounded p-3 bg-light bg-opacity-50">
                            <small class="text-secondary text-uppercase fw-bold">Thời gian</small>
                            <div class="fw-medium fs-5">
                                @if($result->completed_at)
                                    {{ \Carbon\Carbon::parse($result->completed_at)->format('H:i - d/m/Y') }}
                                @else
                                    Chưa xác định
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Nút điều hướng -->
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('student.lessons.show', $lesson) }}" class="btn btn-outline-primary px-4">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại bài học
                    </a>
                    
                    @if(!$result->passed)
                        <form method="POST" action="{{ route('student.quiz.retry', $quiz) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-arrow-repeat me-1"></i> Làm lại quiz
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Chi tiết câu hỏi -->
        @if($result->answers && count(json_decode($result->answers, true)) > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-question-circle-fill text-primary me-2"></i>
                        Chi tiết câu hỏi
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($quiz->questions as $index => $question)
                        @php
                            $answers = is_string($result->answers) 
                                ? json_decode($result->answers, true) 
                                : $result->answers;
                            $answer = isset($answers[$question->id]) ? $answers[$question->id] : null;
                            
                            // Lấy đáp án đúng
                            $correctOption = $question->options()->where('is_correct', 1)->first();
                        @endphp
                        
                        <div class="mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <p class="fw-bold mb-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary me-2">Câu {{ $index + 1 }}</span>
                                {{ $question->question }}
                            </p>
                            
                            @if($answer)
                                @php
                                    $selectedOption = $question->options()->find($answer['selected'] ?? null);
                                    $isCorrect = $answer['correct'] ?? false;
                                @endphp
                                
                                <div class="ms-4">
                                    <!-- Đáp án đã chọn -->
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-2">
                                            @if($isCorrect)
                                                <span class="badge bg-success rounded-circle p-1">
                                                    <i class="bi bi-check-lg"></i>
                                                </span>
                                            @else
                                                <span class="badge bg-danger rounded-circle p-1">
                                                    <i class="bi bi-x-lg"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="fw-medium">Đáp án của bạn:</span>
                                            <span class="{{ $isCorrect ? 'text-success' : 'text-danger' }}">
                                                {{ $selectedOption->option_text ?? 'Không xác định' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Đáp án đúng (nếu sai) -->
                                    @if(!$isCorrect && $correctOption)
                                        <div class="d-flex align-items-center text-success">
                                            <i class="bi bi-check-circle-fill me-2"></i>
                                            <span>
                                                <span class="fw-medium">Đáp án đúng:</span> 
                                                {{ $correctOption->option_text }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-secondary ms-4">
                                    <i class="bi bi-dash-circle me-1"></i> Không có dữ liệu
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Nút quay lại đầu trang -->
        <div class="text-center mt-4">
            <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="text-decoration-none">
                <i class="bi bi-arrow-up-circle me-1"></i> Lên đầu trang
            </a>
        </div>
    </div>
</div>
@endsection