{{-- resources/views/teacher/courses/index.blade.php --}}
@extends('teacher.layout')

@section('title', 'Khóa học của tôi')
@section('courses-active', 'active')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý Khóa học</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Khóa học</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Thêm khóa học
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-book text-primary fs-4"></i>
                        </div>
                        <div>
                            <div class="text-secondary small">Tổng khóa học</div>
                            <div class="fs-3 fw-bold">{{ $courses->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-play-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <div class="text-secondary small">Đang mở</div>
                            <div class="fs-3 fw-bold">{{ $courses->where('status', 1)->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-pencil text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="text-secondary small">Bản nháp</div>
                            <div class="fs-3 fw-bold">{{ $courses->where('status', 0)->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-people text-info fs-4"></i>
                        </div>
                        <div>
                            <div class="text-secondary small">Học viên</div>
                            <div class="fs-3 fw-bold">{{ number_format($totalStudents ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-secondary"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0 ps-0" 
                               id="searchInput" 
                               placeholder="Tìm kiếm khóa học...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <select class="form-select w-auto" id="statusFilter">
                            <option value="all">Tất cả trạng thái</option>
                            <option value="published">Đã xuất bản</option>
                            <option value="draft">Bản nháp</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="row g-4" id="coursesGrid">
        @forelse($courses as $course)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm course-card" 
                     data-status="{{ $course->status == 1 ? 'published' : 'draft' }}">
                    <!-- Card Header with Gradient -->
                    <div class="card-header border-0 text-white p-3" 
                         style="background: linear-gradient(135deg, {{ $course->color_1 ?? '#4158D0' }}, {{ $course->color_2 ?? '#C850C0' }}); min-height: 100px;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge {{ $course->status == 1 ? 'bg-success' : 'bg-secondary' }} bg-opacity-75">
                                    {{ $course->status == 1 ? 'Đã xuất bản' : 'Bản nháp' }}
                                </span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="bi bi-book fs-4"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-2">{{ $course->title }}</h5>
                        <p class="card-text text-secondary small mb-3">
                            {{ Str::limit($course->description, 80) }}
                        </p>
                        
                        <div class="d-flex gap-3 mb-3 small text-secondary">
                            <span><i class="bi bi-collection me-1"></i> {{ $course->lessons_count ?? 0 }} bài</span>
                            <span><i class="bi bi-people me-1"></i> {{ $course->students_count ?? 0 }} HV</span>
                        </div>

                        @if($course->completion_rate)
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-secondary">Tiến độ TB</span>
                                    <span class="fw-medium">{{ $course->completion_rate }}%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" 
                                         style="width: {{ $course->completion_rate }}%; background: linear-gradient(135deg, {{ $course->color_1 ?? '#4158D0' }}, {{ $course->color_2 ?? '#C850C0' }});">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Card Footer -->
                    <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-secondary">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $course->created_at->format('d/m/Y') }}
                            </small>
                            <div class="btn-group">
                                <a href="{{ route('teacher.courses.show', $course) }}" 
                                   class="btn btn-outline-secondary btn-sm" 
                                   title="Quản lý">
                                    <i class="bi bi-gear"></i>
                                </a>
                                <a href="{{ route('teacher.courses.edit', $course) }}" 
                                   class="btn btn-outline-secondary btn-sm" 
                                   title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm" 
                                        onclick="confirmDelete({{ $course->id }})"
                                        title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Form -->
            <form id="delete-form-{{ $course->id }}" 
                  action="{{ route('teacher.courses.destroy', $course) }}" 
                  method="POST" 
                  class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-journal-bookmark fs-1 text-secondary mb-3"></i>
                        <h5 class="mb-2">Chưa có khóa học nào</h5>
                        <p class="text-secondary mb-4">Bắt đầu tạo khóa học đầu tiên để chia sẻ kiến thức</p>
                        <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Tạo khóa học
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($courses, 'links'))
        <div class="d-flex justify-content-center mt-4">
            {{ $courses->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.course-card').forEach(card => {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            const shouldShow = title.includes(searchTerm);
            card.closest('.col-md-6, .col-xl-4').style.display = shouldShow ? '' : 'none';
        });
    });

    // Filter by status
    document.getElementById('statusFilter')?.addEventListener('change', function() {
        const filterValue = this.value;
        document.querySelectorAll('.course-card').forEach(card => {
            const status = card.dataset.status;
            const shouldShow = filterValue === 'all' || status === filterValue;
            card.closest('.col-md-6, .col-xl-4').style.display = shouldShow ? '' : 'none';
        });
    });

    // Confirm delete
    window.confirmDelete = function(courseId) {
        if (confirm('Bạn có chắc chắn muốn xóa khóa học này? Hành động này không thể hoàn tác.')) {
            document.getElementById('delete-form-' + courseId).submit();
        }
    };
</script>
@endpush