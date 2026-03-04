@extends('admin.layout')

@section('title', 'Quản lý khoá học')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Quản lý khoá học</h1>
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm khoá học
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Giáo viên</th>
                            <th>Số bài học</th>
                            <th>Số học viên</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td>{{ $course->id }}</td>
                                <td>{{ $course->title }}</td>
                                <td>{{ $course->teacher->name ?? '—' }}</td>
                                <td>{{ $course->lessons_count }} Bài</td>
                                <td>{{ $course->students_count }} Học viên</td>
                                <td>
                                    {{ $course->status == 1 ? 'Hoạt động' : 'Tạm dừng' }}
                                </td>
                                <td>{{ $course->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-sm btn-warning"
                                        title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if ($course->lessons_count == 0)
                                        <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Xoá khoá học {{ $course->title }}?')"
                                                title="Xoá">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled
                                            title="Không thể xoá vì đã có bài học">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Chưa có khoá học nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-end">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
