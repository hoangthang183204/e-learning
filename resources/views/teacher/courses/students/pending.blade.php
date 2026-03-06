{{-- resources/views/teacher/courses/students/pending.blade.php --}}
@extends('teacher.layout')

@section('title', 'Duyệt học viên - ' . $course->title)
@section('courses-active', 'active')
@section('page-icon', 'person-check')
@section('page-title', 'Duyệt học viên')
@section('page-subtitle', $course->title)

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.index') }}">Khóa học</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.show', $course) }}">{{ $course->title }}</a></li>
            <li class="breadcrumb-item active">Duyệt học viên</li>
        </ol>
    </nav>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Chờ duyệt</h6>
                            <h2 class="mt-2 mb-0">{{ $stats['pending'] }}</h2>
                        </div>
                        <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Đã duyệt</h6>
                            <h2 class="mt-2 mb-0">{{ $stats['approved'] }}</h2>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Từ chối</h6>
                            <h2 class="mt-2 mb-0">{{ $stats['rejected'] }}</h2>
                        </div>
                        <i class="bi bi-x-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Bị khóa</h6>
                            <h2 class="mt-2 mb-0">{{ $stats['blocked'] }}</h2>
                        </div>
                        <i class="bi bi-lock fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="studentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                type="button" role="tab">
                <i class="bi bi-hourglass-split me-1"></i>
                Chờ duyệt <span class="badge bg-warning ms-1">{{ $stats['pending'] }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button"
                role="tab">
                <i class="bi bi-check-circle me-1"></i>
                Đã duyệt <span class="badge bg-success ms-1">{{ $stats['approved'] }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button"
                role="tab">
                <i class="bi bi-x-circle me-1"></i>
                Từ chối <span class="badge bg-danger ms-1">{{ $stats['rejected'] }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="blocked-tab" data-bs-toggle="tab" data-bs-target="#blocked" type="button"
                role="tab">
                <i class="bi bi-lock me-1"></i>
                Bị khóa <span class="badge bg-secondary ms-1">{{ $stats['blocked'] }}</span>
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="studentTabsContent">
        <!-- Pending Tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-hourglass-split me-2 text-warning"></i>Danh sách chờ duyệt</h5>
                </div>
                <div class="card-body p-0">
                    @if ($pendingStudents->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-secondary mb-3"></i>
                            <h6 class="text-secondary">Không có học viên nào chờ duyệt</h6>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Học viên</th>
                                        <th>Email</th>
                                        <th>Ngày đăng ký</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingStudents as $index => $student)
                                        <tr>
                                            <td class="ps-4">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="bi bi-person text-warning"></i>
                                                    </div>
                                                    <span class="fw-medium">{{ $student->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $student->email }}</td>
                                            <td>
                                                <small>
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('d/m/Y H:i') }}
                                                    <br>
                                                    <span class="text-secondary">
                                                        {{ \Carbon\Carbon::parse($student->pivot->enrolled_at)->diffForHumans() }}
                                                    </span>
                                                </small>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-success me-1"
                                                    onclick="approveStudent({{ $student->id }})" title="Duyệt">
                                                    <i class="bi bi-check-lg"></i> Duyệt
                                                </button>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="rejectStudent({{ $student->id }})" title="Từ chối">
                                                    <i class="bi bi-x-lg"></i> Từ chối
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Approved Tab -->
        <div class="tab-pane fade" id="approved" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-check-circle me-2 text-success"></i>Danh sách đã duyệt</h5>
                    <span class="badge bg-success">Tổng: {{ $approvedStudents->total() }}</span>
                </div>
                <div class="card-body p-0">
                    @if ($approvedStudents->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-secondary mb-3"></i>
                            <h6 class="text-secondary">Chưa có học viên nào được duyệt</h6>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Học viên</th>
                                        <th>Email</th>
                                        <th>Ngày duyệt</th>
                                        <th>Tiến độ</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($approvedStudents as $index => $student)
                                        @php
                                            $completedCount = $student->completedLessons->count();
                                            $percent = $course->lessons->count() > 0
                                                ? round(($completedCount / $course->lessons->count()) * 100)
                                                : 0;
                                        @endphp
                                        <tr>
                                            <td class="ps-4">{{ $approvedStudents->firstItem() + $index }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="bi bi-person text-success"></i>
                                                    </div>
                                                    <span class="fw-medium">{{ $student->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $student->email }}</td>
                                            <td>
                                                <small>
                                                    {{ $student->pivot->approved_at ? \Carbon\Carbon::parse($student->pivot->approved_at)->format('d/m/Y') : 'N/A' }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                        <div class="progress-bar bg-success"
                                                            style="width: {{ $percent }}%"></div>
                                                    </div>
                                                    <span class="small">{{ $percent }}%</span>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('teacher.courses.progress.detail', [$course->id, $student->id]) }}"
                                                    class="btn btn-sm btn-outline-info me-1" title="Xem tiến độ">
                                                    <i class="bi bi-graph-up"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="blockStudent({{ $student->id }})" title="Khóa">
                                                    <i class="bi bi-lock"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($approvedStudents->hasPages())
                            <div class="card-footer bg-white">
                                {{ $approvedStudents->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Rejected Tab -->
        <div class="tab-pane fade" id="rejected" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-x-circle me-2 text-danger"></i>Danh sách bị từ chối</h5>
                </div>
                <div class="card-body p-0">
                    @if ($rejectedStudents->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-secondary mb-3"></i>
                            <h6 class="text-secondary">Không có học viên nào bị từ chối</h6>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Học viên</th>
                                        <th>Email</th>
                                        <th>Ngày từ chối</th>
                                        <th>Lý do</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rejectedStudents as $index => $student)
                                        <tr>
                                            <td class="ps-4">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="bi bi-person text-danger"></i>
                                                    </div>
                                                    <span class="fw-medium">{{ $student->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $student->email }}</td>
                                            <td>
                                                <small>{{ $student->pivot->rejected_at ? \Carbon\Carbon::parse($student->pivot->rejected_at)->format('d/m/Y') : 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <small class="text-danger">{{ $student->pivot->rejection_reason ?: 'Không có lý do' }}</small>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-success"
                                                    onclick="approveStudent({{ $student->id }})" title="Duyệt lại">
                                                    <i class="bi bi-check-lg"></i> Duyệt lại
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Blocked Tab -->
        <div class="tab-pane fade" id="blocked" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-lock me-2 text-secondary"></i>Danh sách bị khóa</h5>
                </div>
                <div class="card-body p-0">
                    @if ($blockedStudents->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-secondary mb-3"></i>
                            <h6 class="text-secondary">Không có học viên nào bị khóa</h6>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Học viên</th>
                                        <th>Email</th>
                                        <th>Ngày khóa</th>
                                        <th>Lý do</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($blockedStudents as $index => $student)
                                        <tr>
                                            <td class="ps-4">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="bi bi-person text-secondary"></i>
                                                    </div>
                                                    <span class="fw-medium">{{ $student->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $student->email }}</td>
                                            <td>
                                                <small>{{ $student->pivot->blocked_at ? \Carbon\Carbon::parse($student->pivot->blocked_at)->format('d/m/Y') : 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <small class="text-secondary">{{ $student->pivot->block_reason ?: 'Không có lý do' }}</small>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-success"
                                                    onclick="unblockStudent({{ $student->id }})" title="Mở khóa">
                                                    <i class="bi bi-unlock"></i> Mở khóa
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal từ chối -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Từ chối học viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Lý do từ chối</label>
                        <textarea class="form-control" id="rejectReason" name="reason" rows="3" placeholder="Nhập lý do từ chối..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal khóa -->
<div class="modal fade" id="blockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Khóa học viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="blockForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="blockReason" class="form-label">Lý do khóa</label>
                        <textarea class="form-control" id="blockReason" name="reason" rows="3" placeholder="Nhập lý do khóa..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning">Xác nhận khóa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentStudentId = null;

    function approveStudent(studentId) {
        if (confirm('Bạn có chắc muốn duyệt học viên này?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('teacher.courses.students.approve', ['course' => $course->id, 'userId' => 'STUDENT_ID']) }}'.replace(
                'STUDENT_ID', studentId);
            form.innerHTML = '@csrf';
            document.body.appendChild(form);
            form.submit();
        }
    }

    function rejectStudent(studentId) {
        currentStudentId = studentId;
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        const form = document.getElementById('rejectForm');
        form.action = '{{ route('teacher.courses.students.reject', ['course' => $course->id, 'userId' => 'STUDENT_ID']) }}'.replace(
            'STUDENT_ID', studentId);
        modal.show();
    }

    function blockStudent(studentId) {
        currentStudentId = studentId;
        const modal = new bootstrap.Modal(document.getElementById('blockModal'));
        const form = document.getElementById('blockForm');
        form.action = '{{ route('teacher.courses.students.block', ['course' => $course->id, 'userId' => 'STUDENT_ID']) }}'.replace(
            'STUDENT_ID', studentId);
        modal.show();
    }

    function unblockStudent(studentId) {
        if (confirm('Bạn có chắc muốn mở khóa học viên này?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('teacher.courses.students.unblock', ['course' => $course->id, 'userId' => 'STUDENT_ID']) }}'.replace(
                'STUDENT_ID', studentId);
            form.innerHTML = '@csrf';
            document.body.appendChild(form);
            form.submit();
        }
    }

    setInterval(function() {
        fetch('{{ route('teacher.courses.students.pending-count', $course) }}')
            .then(response => response.json())
            .then(data => {
                const pendingTab = document.querySelector('#pending-tab .badge');
                if (pendingTab) {
                    pendingTab.textContent = data.count;
                }

                if (data.count > 0) {
                    document.title = '(' + data.count + ') ' + document.title.replace(/^\(\d+\)\s/, '');
                }
            })
            .catch(error => console.error('Error:', error));
    }, 30000);
</script>

<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        padding: 10px 20px;
    }

    .nav-tabs .nav-link:hover {
        color: #495057;
        border: none;
        background: #f8f9fa;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border: none;
        border-bottom: 3px solid #0d6efd;
        background: transparent;
    }

    .nav-tabs .nav-link .badge {
        font-size: 11px;
    }

    .table td {
        vertical-align: middle;
    }
</style>
@endsection