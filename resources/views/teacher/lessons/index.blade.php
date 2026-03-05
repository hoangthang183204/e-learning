{{-- resources/views/teacher/lessons/index.blade.php --}}
@extends('teacher.layout')

@section('title', 'Danh sách bài học')
@section('courses-active', 'active')
@section('page-icon', 'list-check')
@section('page-title', 'Danh sách bài học')
@section('page-subtitle', $course->title)

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.index') }}">Khóa học</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.show', $course) }}">{{ $course->title }}</a></li>
            <li class="breadcrumb-item active">Bài học</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Danh sách bài học</h4>
            <p class="text-secondary mb-0">Tổng số: {{ $lessons->total() }} bài học</p>
        </div>
        <a href="{{ route('teacher.lessons.create', $course) }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Thêm bài học
        </a>
    </div>

    <!-- Lessons List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($lessons->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-secondary mb-3"></i>
                    <h5>Chưa có bài học nào</h5>
                    <p class="text-secondary mb-4">Bắt đầu thêm bài học đầu tiên cho khóa học này</p>
                    <a href="{{ route('teacher.lessons.create', $course) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Thêm bài học
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="80">Thứ tự</th>
                                <th>Tiêu đề</th>
                                <th width="150">Video</th>
                                <th width="150">Quiz</th>
                                <th width="200">Ngày tạo</th>
                                <th width="120">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lessons as $lesson)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $lesson->order_number }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher.lessons.show', [$course, $lesson]) }}" class="text-decoration-none fw-medium">
                                            {{ $lesson->title }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($lesson->video_url)
                                            <span class="badge bg-success">
                                                <i class="bi bi-play-circle me-1"></i> Có video
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Không</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lesson->quizzes()->count() > 0)
                                            <span class="badge bg-info">
                                                {{ $lesson->quizzes()->count() }} quiz
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Chưa có</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-secondary">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $lesson->created_at->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('teacher.lessons.edit', [$course, $lesson]) }}" 
                                               class="btn btn-sm btn-outline-secondary" 
                                               title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('teacher.lessons.show', [$course, $lesson]) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Xem">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete({{ $lesson->id }})"
                                                    title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $lesson->id }}" 
                                              action="{{ route('teacher.lessons.destroy', [$course, $lesson]) }}" 
                                              method="POST" 
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($lessons->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $lessons->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(lessonId) {
        if (confirm('Bạn có chắc chắn muốn xóa bài học này?')) {
            document.getElementById('delete-form-' + lessonId).submit();
        }
    }
</script>
@endpush
@endsection