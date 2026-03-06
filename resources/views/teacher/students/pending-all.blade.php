@extends('teacher.layout')

@section('title', 'Tất cả học viên chờ duyệt')
@section('pending-active', 'active')
@section('page-icon', 'hourglass-split')
@section('page-title', 'Duyệt học viên')

@section('content')
    <div class="container-fluid px-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-hourglass-split me-2 text-warning"></i>Danh sách học viên chờ duyệt</h5>
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
                                    <th>Khóa học</th>
                                    <th>Ngày đăng ký</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingStudents as $index => $student)
                                    <tr>
                                        <td class="ps-4">{{ $pendingStudents->firstItem() + $index }}</td>
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
                                            <a href="{{ route('teacher.courses.show', $student->course_id) }}"
                                                class="text-decoration-none">
                                                {{ $student->course_title }}
                                            </a>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ \Carbon\Carbon::parse($student->enrolled_at)->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('teacher.courses.students.pending', ['course' => $student->course_id]) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> Xem chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($pendingStudents->hasPages())
                        <div class="card-footer bg-white">
                            {{ $pendingStudents->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
