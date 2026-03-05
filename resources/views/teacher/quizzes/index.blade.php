{{-- resources/views/teacher/quizzes/index.blade.php --}}
@extends('teacher.layout')

@section('title', 'Danh sách Quiz')
@section('quizzes-active', 'active')
@section('page-icon', 'pencil-square')
@section('page-title', 'Quản lý Quiz')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Danh sách Quiz</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Quiz</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tạo quiz mới
        </a>
    </div>

    <!-- Quiz Grid -->
    <div class="row g-4">
        @forelse($quizzes as $quiz)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm quiz-card">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-2">
                                    <i class="bi bi-collection me-1"></i>
                                    {{ $quiz->lesson->course->title ?? 'N/A' }}
                                </span>
                                <h5 class="card-title mb-0">{{ $quiz->title }}</h5>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link text-secondary p-0" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('teacher.quizzes.show', $quiz) }}">
                                            <i class="bi bi-eye me-2"></i>Xem chi tiết
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('teacher.quizzes.edit', $quiz) }}">
                                            <i class="bi bi-pencil me-2"></i>Chỉnh sửa
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('teacher.quizzes.destroy', $quiz) }}" method="POST" 
                                              onsubmit="return confirm('Bạn có chắc muốn xóa quiz này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i>Xóa
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2 px-4">
                        <p class="text-secondary small mb-3">
                            <i class="bi bi-book me-1"></i>
                            Bài: <span class="fw-medium">{{ $quiz->lesson->title ?? 'Không có' }}</span>
                        </p>
                        
                        <div class="d-flex gap-3 mb-3">
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-primary">{{ $quiz->questions_count ?? 0 }}</div>
                                <small class="text-secondary">Câu hỏi</small>
                            </div>
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-success">{{ $quiz->attempts_count ?? 0 }}</div>
                                <small class="text-secondary">Lượt làm</small>
                            </div>
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-warning">{{ $quiz->average_score }}%</div>
                                <small class="text-secondary">Điểm TB</small>
                            </div>
                        </div>

                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $quiz->pass_rate }}%"></div>
                        </div>
                        <small class="text-secondary">Tỷ lệ đạt: {{ $quiz->pass_rate }}%</small>
                    </div>
                    <div class="card-footer bg-white border-0 pb-4 px-4">
                        <small class="text-secondary">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $quiz->created_at->format('d/m/Y') }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-pencil-square fs-1 text-secondary mb-3"></i>
                        <h5 class="mb-2">Chưa có quiz nào</h5>
                        <p class="text-secondary mb-4">Bắt đầu tạo quiz đầu tiên để kiểm tra kiến thức học viên</p>
                        <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Tạo quiz
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($quizzes, 'links'))
        <div class="d-flex justify-content-center mt-4">
            {{ $quizzes->withQueryString()->links() }}
        </div>
    @endif
</div>

<style>
.quiz-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.quiz-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
.dropdown-item {
    cursor: pointer;
}
</style>
@endsection