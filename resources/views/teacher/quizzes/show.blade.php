{{-- resources/views/teacher/quizzes/show.blade.php --}}
@extends('teacher.layout')

@section('title', $quiz->title)
@section('quizzes-active', 'active')
@section('page-icon', 'eye')
@section('page-title', $quiz->title)

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.quizzes.index') }}">Quiz</a></li>
            <li class="breadcrumb-item active">{{ $quiz->title }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <h4 class="mb-0">{{ $quiz->title }}</h4>
        </div>
        <div class="btn-group">
            <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Sửa
            </a>
            <form action="{{ route('teacher.quizzes.destroy', $quiz) }}" method="POST" class="d-inline" 
                  onsubmit="return confirm('Bạn có chắc muốn xóa quiz này?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-1"></i> Xóa
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-question-circle text-primary fs-4"></i>
                        </div>
                        <div>
                            <div class="text-secondary small">Câu hỏi</div>
                            <div class="fs-3 fw-bold">{{ $quiz->questions->count() }}</div>
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
                            <i class="bi bi-people text-success fs-4"></i>
                        </div>
                        <div>
                            <div class="text-secondary small">Lượt làm</div>
                            <div class="fs-3 fw-bold">{{ $totalAttempts }}</div>
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
                            <i class="bi bi-star text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="text-secondary small">Điểm TB</div>
                            <div class="fs-3 fw-bold">{{ $avgScore }}%</div>
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
                            <i class="bi bi-trophy text-info fs-4"></i>
                        </div>
                        <div>
                            <div class="text-secondary small">Tỷ lệ đạt</div>
                            <div class="fs-3 fw-bold">{{ $passRate }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Danh sách câu hỏi -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-question-circle me-2 text-primary"></i>Danh sách câu hỏi</h5>
                </div>
                <div class="card-body">
                    @if($quiz->questions->isEmpty())
                        <p class="text-secondary text-center py-4">Quiz chưa có câu hỏi nào.</p>
                    @else
                        <div class="accordion" id="questionsAccordion">
                            @foreach($quiz->questions as $index => $question)
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $index }}">
                                            <span class="badge bg-primary me-3">Câu {{ $index + 1 }}</span>
                                            {{ $question->question }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" 
                                         class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                                         data-bs-parent="#questionsAccordion">
                                        <div class="accordion-body">
                                            <div class="list-group">
                                                @foreach($question->options as $optIndex => $option)
                                                    <div class="list-group-item {{ $option->is_correct ? 'list-group-item-success' : '' }}">
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-secondary me-3">{{ chr(65 + $optIndex) }}</span>
                                                            <span class="flex-grow-1">{{ $option->option_text }}</span>
                                                            @if($option->is_correct)
                                                                <span class="badge bg-success">
                                                                    <i class="bi bi-check-circle"></i> Đáp án đúng
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
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
                            <th class="text-secondary">Khóa học:</th>
                            <td>
                                <a href="{{ route('teacher.courses.show', $quiz->lesson->course) }}" class="text-decoration-none">
                                    {{ $quiz->lesson->course->title }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-secondary">Bài học:</th>
                            <td>
                                <a href="{{ route('teacher.lessons.show', [$quiz->lesson->course, $quiz->lesson]) }}" 
                                   class="text-decoration-none">
                                    {{ $quiz->lesson->title }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-secondary">Ngày tạo:</th>
                            <td>{{ $quiz->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th class="text-secondary">Cập nhật:</th>
                            <td>{{ $quiz->updated_at->diffForHumans() }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Kết quả gần đây -->
            @if($results->isNotEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Kết quả gần đây</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($results->take(5) as $result)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $result->user->name }}</strong>
                                            <br>
                                            <small class="text-secondary">{{ $result->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge {{ $result->passed ? 'bg-success' : 'bg-danger' }} fs-6">
                                                {{ $result->score }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if($results->total() > 5)
                        <div class="card-footer bg-white text-center">
                            <a href="#" class="text-decoration-none small">Xem tất cả kết quả</a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection