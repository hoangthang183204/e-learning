
@extends('teacher.layout')

@section('title', 'Học viên - ' . $course->title)

@section('courses-active', 'active')
@section('page-icon', 'people')
@section('page-title', 'Quản lý học viên')

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb mb-4">
        <a href="{{ route('teacher.courses.index') }}" class="text-decoration-none">Khoá học</a>
        <span class="mx-2">/</span>
        <a href="{{ route('teacher.courses.show', $course) }}" class="text-decoration-none">{{ $course->title }}</a>
        <span class="mx-2">/</span>
        <span class="text-muted">Học viên</span>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="bi bi-people-fill me-2 text-success"></i>
                Danh sách học viên
            </h4>
            <p class="text-secondary mb-0">
                Khoá học: <span class="fw-bold">{{ $course->title }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="" class="btn btn-success">
                <i class="bi bi-person-plus me-2"></i>Thêm học viên
            </a>
            <a href="" class="btn btn-outline-secondary">
                <i class="bi bi-download me-2"></i>Xuất Excel
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="bg-white p-3 rounded border">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-people text-primary" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Tổng số</div>
                        {{-- <div class="h4 mb-0 fw-bold">{{ $students->total() ?? $students->count() }}</div> --}}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="bg-white p-3 rounded border">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-hourglass-split text-warning" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Chờ duyệt</div>
                        <div class="h4 mb-0 fw-bold">{{ $pendingCount ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="bg-white p-3 rounded border">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Đã duyệt</div>
                        <div class="h4 mb-0 fw-bold">{{ $approvedCount ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="bg-white p-3 rounded border">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-slash-circle text-danger" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Bị chặn</div>
                        <div class="h4 mb-0 fw-bold">{{ $blockedCount ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white p-3 rounded border mb-4">
        <div class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-secondary"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0 ps-0" 
                           id="searchInput" 
                           placeholder="Tìm kiếm theo tên hoặc email...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="statusFilter">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="pending">Chờ duyệt</option>
                    <option value="approved">Đã duyệt</option>
                    <option value="blocked">Bị chặn</option>
                </select>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="text-secondary" id="resultCount">
                    {{-- Hiển thị {{ $students->count() }}/{{ $students->total() }} học viên --}}
                </span>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded border overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="studentsTable">
                <thead class="bg-light">
                    <tr>
                        <th width="50">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Học viên</th>
                        <th>Email</th>
                        <th>Ngày đăng ký</th>
                        <th>Trạng thái</th>
                        <th width="200">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr class="student-row" data-status="{{ $student->pivot->status }}">
                            <td>
                                <input type="checkbox" class="form-check-input student-checkbox" value="{{ $student->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 40px; height: 40px;">
                                        @if($student->avatar)
                                            <img src="{{ $student->avatar }}" alt="{{ $student->name }}" 
                                                 class="rounded-circle" width="40" height="40">
                                        @else
                                            <span class="fw-bold text-success">
                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $student->name }}</div>
                                        <small class="text-secondary">ID: #{{ $student->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-envelope text-secondary me-2"></i>
                                     <div class="fw-bold">{{ $student->email }}</div>
                                                    </div>
                            </td>
                            <td>
                                <div>
                                    <div>{{ \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('d/m/Y') }}</div>
                                    <small class="text-secondary">
                                        {{ \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('H:i') }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'pending' => ['bg-warning', 'text-dark', 'Chờ duyệt'],
                                        'approved' => ['bg-success', 'text-white', 'Đã duyệt'],
                                        'blocked' => ['bg-danger', 'text-white', 'Bị chặn']
                                    ];
                                    $status = $student->pivot->status ?? 'pending';
                                    $class = $statusClasses[$status] ?? ['bg-secondary', 'text-white', 'Không xác định'];
                                @endphp
                                <span class="badge {{ $class[0] }} {{ $class[1] }} px-3 py-2">
                                    {{ $class[2] }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($student->pivot->status == 'pending')
                                        <form method="POST" 
                                              action="{{ route('teacher.courses.approve', [$course->id, $student->id]) }}"
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    onclick="return confirm('Duyệt học viên này?')">
                                                <i class="bi bi-check-circle me-1"></i>Duyệt
                                            </button>
                                        </form>
                                    @endif

                                    @if($student->pivot->status == 'approved')
                                        <form method="POST" 
                                              action="{{ route('teacher.courses.block', [$course->id, $student->id]) }}"
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning"
                                                    onclick="return confirm('Chặn học viên này?')">
                                                <i class="bi bi-slash-circle me-1"></i>Chặn
                                            </button>
                                        </form>
                                    @endif

                                    @if($student->pivot->status == 'blocked')
                                        <form method="POST" 
                                              action="{{ route('teacher.courses.approve', [$course->id, $student->id]) }}"
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success"
                                                    onclick="return confirm('Bỏ chặn học viên này?')">
                                                <i class="bi bi-check-circle me-1"></i>Bỏ chặn
                                            </button>
                                        </form>
                                    @endif

                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#studentModal{{ $student->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <form method="POST" 
                                          action=""
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Xóa học viên này khỏi khoá học?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Student Detail Modal -->
                                <div class="modal fade" id="studentModal{{ $student->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Chi tiết học viên</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-4">
                                                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                                         style="width: 80px; height: 80px;">
                                                        @if($student->avatar)
                                                            <img src="{{ $student->avatar }}" alt="{{ $student->name }}" 
                                                                 class="rounded-circle" width="80" height="80">
                                                        @else
                                                            <span class="fw-bold text-success" style="font-size: 32px;">
                                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <h5>{{ $student->name }}</h5>
                                                    <p class="text-secondary mb-1">{{ $student->email }}</p>
                                                </div>
                                                
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th width="40%">Mã học viên</th>
                                                        <td>#{{ $student->id }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Ngày đăng ký</th>
                                                        <td>{{ \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('d/m/Y H:i') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Trạng thái</th>
                                                        <td>
                                                            <span class="badge {{ $class[0] }} {{ $class[1] }}">
                                                                {{ $class[2] }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tiến độ</th>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                    <div class="progress-bar bg-success" 
                                                                         style="width: {{ $student->pivot->progress ?? 0 }}%"></div>
                                                                </div>
                                                                <span>{{ $student->pivot->progress ?? 0 }}%</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-people" style="font-size: 48px; color: #dee2e6;"></i>
                                <h5 class="mt-3">Chưa có học viên nào</h5>
                                <p class="text-secondary">Khoá học này hiện chưa có học viên đăng ký.</p>
                                <a href="{{ route('teacher.courses.students.add', $course) }}" class="btn btn-success">
                                    <i class="bi bi-person-plus me-2"></i>Thêm học viên
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bulk Actions -->
        @if($students->isNotEmpty())
            <div class="p-3 border-top bg-light" id="bulkActions" style="display: none;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold" id="selectedCount">0</span> học viên được chọn
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success" onclick="bulkAction('approve')">
                            <i class="bi bi-check-circle me-1"></i>Duyệt
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="bulkAction('block')">
                            <i class="bi bi-slash-circle me-1"></i>Chặn
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                            <i class="bi bi-trash me-1"></i>Xóa
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if(method_exists($students, 'links'))
        <div class="mt-4">
            {{ $students->links() }}
        </div>
    @endif

    <style>
        .table th {
            font-weight: 600;
            color: #495057;
        }
        
        .student-row:hover {
            background-color: #f8f9fa;
        }
        
        .badge {
            font-weight: 500;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }
        
        .pagination {
            margin-bottom: 0;
        }
        
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>

    <script>
        // Select All functionality
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActions();
        });

        // Individual checkbox change
        document.querySelectorAll('.student-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkActions);
        });

        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.student-checkbox:checked');
            const selectedCount = checkboxes.length;
            const bulkActions = document.getElementById('bulkActions');
            
            if (selectedCount > 0) {
                document.getElementById('selectedCount').textContent = selectedCount;
                bulkActions.style.display = 'block';
                
                // Update select all
                const selectAll = document.getElementById('selectAll');
                if (selectAll) {
                    const total = document.querySelectorAll('.student-checkbox').length;
                    selectAll.checked = selectedCount === total;
                    selectAll.indeterminate = selectedCount > 0 && selectedCount < total;
                }
            } else {
                bulkActions.style.display = 'none';
            }
        }

        // Search functionality
        document.getElementById('searchInput')?.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.student-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            document.getElementById('resultCount').textContent = 
                `Hiển thị ${visibleCount}/${rows.length} học viên`;
        });

        // Filter by status
        document.getElementById('statusFilter')?.addEventListener('change', function() {
            const status = this.value;
            const rows = document.querySelectorAll('.student-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                if (status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            document.getElementById('resultCount').textContent = 
                `Hiển thị ${visibleCount}/${rows.length} học viên`;
        });

        // Bulk actions
        function bulkAction(action) {
            const selected = Array.from(document.querySelectorAll('.student-checkbox:checked'))
                .map(cb => cb.value);
            
            if (selected.length === 0) return;
            
            let message = '';
            let url = '';
            
            switch(action) {
                case 'approve':
                    message = `Duyệt ${selected.length} học viên được chọn?`;
                    url = '';
                    break;
                case 'block':
                    message = `Chặn ${selected.length} học viên được chọn?`;
                    url = '';
                    break;
                case 'delete':
                    message = `Xóa ${selected.length} học viên khỏi khoá học?`;
                    url = '';
                    break;
            }
            
            if (confirm(message)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                
                selected.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'students[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection