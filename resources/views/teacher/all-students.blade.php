{{-- resources/views/teacher/all-students.blade.php --}}
@extends('teacher.layout')

@section('title', 'Tất cả học viên')
@section('students-active', 'active')
@section('page-icon', 'people')
@section('page-title', 'Tất cả học viên')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Học viên</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Tất cả học viên</h4>
            <p class="text-secondary mb-0">
                <i class="bi bi-people-fill me-1"></i>
                Tổng số: <span class="fw-bold">{{ $students->total() }}</span> học viên
            </p>
        </div>
        <a href="{{ route('teacher.courses.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-plus-circle"></i> Thêm học viên vào khóa học
        </a>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="searchInput" 
                               placeholder="Tìm kiếm theo tên, email hoặc khóa học...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Học viên</th>
                            <th>Email</th>
                            <th>Khóa học</th>
                            <th>Ngày đăng ký</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        @forelse($students as $index => $student)
                            <tr class="student-row">
                                <td class="ps-4">{{ $students->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person text-primary"></i>
                                        </div>
                                        <span class="fw-medium student-name">{{ $student->name }}</span>
                                    </div>
                                </td>
                                <td class="student-email">{{ $student->email }}</td>
                                <td>
                                    <a href="{{ route('teacher.courses.show', $student->course_id) }}" class="text-decoration-none">
                                        {{ $student->course_title }}
                                    </a>
                                </td>
                                <td>
                                    <small>
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($student->enrolled_at)->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('teacher.courses.progress.detail', [$student->course_id, $student->id]) }}" 
                                           class="btn btn-sm btn-outline-info" title="Xem tiến độ">
                                            <i class="bi bi-graph-up"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-people fs-1 text-secondary mb-3"></i>
                                    <h6 class="text-secondary">Chưa có học viên nào</h6>
                                    <p class="text-secondary small mb-3">Bắt đầu thêm học viên vào khóa học</p>
                                    <a href="{{ route('teacher.courses.index') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle"></i> Đến trang khóa học
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($students->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-center">
                    {{ $students->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchInput')?.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    document.querySelectorAll('.student-row').forEach(row => {
        const name = row.querySelector('.student-name')?.textContent.toLowerCase() || '';
        const email = row.querySelector('.student-email')?.textContent.toLowerCase() || '';
        const course = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
        const shouldShow = name.includes(searchTerm) || email.includes(searchTerm) || course.includes(searchTerm);
        row.style.display = shouldShow ? '' : 'none';
    });
});
</script>
@endsection