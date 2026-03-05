{{-- resources/views/teacher/courses/quiz-results.blade.php --}}
@extends('teacher.layout')

@section('title', 'Kết quả quiz - ' . $course->title)
@section('courses-active', 'active')
@section('page-icon', 'pencil-square')
@section('page-title', 'Kết quả bài kiểm tra')
@section('page-subtitle', $course->title)

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb mb-4">
        <a href="{{ route('teacher.courses.index') }}" class="text-decoration-none">Khoá học</a>
        <span class="mx-2">/</span>
        <a href="{{ route('teacher.courses.show', $course) }}" class="text-decoration-none">{{ $course->title }}</a>
        <span class="mx-2">/</span>
        <span class="text-muted">Kết quả quiz</span>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="bg-white p-3 rounded border">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-pencil-square text-primary" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Tổng lượt làm</div>
                        <div class="h4 mb-0 fw-bold">{{ $totalAttempts ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="bg-white p-3 rounded border">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-star text-warning" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Điểm trung bình</div>
                        <div class="h4 mb-0 fw-bold">{{ round($avgScore ?? 0, 1) }}%</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="bg-white p-3 rounded border">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-trophy text-success" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Tỷ lệ đạt</div>
                        <div class="h4 mb-0 fw-bold">{{ $passRate ?? 0 }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded border p-4">
        <h5 class="mb-4">
            <i class="bi bi-table me-2"></i>
            Chi tiết kết quả
        </h5>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Học viên</th>
                        <th>Quiz</th>
                        <th>Bài học</th>
                        <th>Điểm</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $r)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                        <i class="bi bi-person text-primary"></i>
                                    </div>
                                    <strong>{{ $r->user->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $r->quiz->title }}</td>
                            <td>
                                @if($r->quiz->lesson)
                                    <span class="text-muted small">{{ $r->quiz->lesson->title }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold">{{ $r->score }}%</span>
                            </td>
                            <td>
                                @if($r->passed)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> ĐẠT
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle"></i> TRƯỢT
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $r->created_at->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    <h6>Chưa có kết quả quiz nào</h6>
                                    <p class="small">Học viên chưa làm bài kiểm tra nào trong khóa học này.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($results, 'links'))
            <div class="mt-4">
                {{ $results->links() }}
            </div>
        @endif
    </div>
@endsection