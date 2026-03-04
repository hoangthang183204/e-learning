{{-- resources/views/admin/lessons/index.blade.php --}}
@extends('admin.layout')

@section('content')
    <div class="container">
        <h1>Quản lý bài học</h1>

        {{-- Nút thêm mới --}}
        <a href="{{ route('admin.lessons.create') }}" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Thêm bài học mới
        </a>

        {{-- Bộ lọc theo khóa học --}}
        <form method="GET" action="{{ route('admin.lessons.index') }}" class="mb-3">
            <select name="course_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- Tất cả khóa học --</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
        </form>

        {{-- Danh sách bài học --}}
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Khóa học</th>
                    <th>Thứ tự</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lessons as $lesson)
                    <tr>
                        <td>{{ $lesson->id }}</td>
                        <td>{{ $lesson->title }}</td>
                        <td>{{ $lesson->course->title }}</td>
                        <td>{{ $lesson->order_number }}</td>
                        <td>
                            {{-- Nút sửa --}}
                            <a href="{{ route('admin.lessons.edit', $lesson->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Sửa
                            </a>

                            {{-- Nút xóa --}}
                            <form action="{{ route('admin.lessons.destroy', $lesson->id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Xóa bài học này?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $lessons->links() }}
    </div>
@endsection
