@extends('admin.layout')

@section('title', 'Quản lý khoá học')

@section('content')
    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary mb-3">
        + Thêm khoá học
    </a>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Giáo viên</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>

        @foreach ($courses as $course)
            <tr>
                <td>{{ $course->id }}</td>
                <td>{{ $course->title }}</td>
                <td>{{ $course->teacher->name ?? '—' }}</td>
                <td>{{ $course->status == 1 ? 'Đang Mở' : 'Đóng' }}</td>
                <td>
                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-warning">Sửa</a>

                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Xoá khoá học?')">
                            Xoá
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    {{ $courses->links() }}
@endsection
