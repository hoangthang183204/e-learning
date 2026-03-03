@extends('admin.layout')

@section('content')
    <a href="{{ route('admin.lesson.create') }}" class="btn btn-success mb-3">
        + Thêm bài học
    </a>

    <table class="table table-bordered">
        <tr>
            <th>Course</th>
            <th>Bài học</th>
            <th>Thứ tự</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>

        @foreach ($lessons as $lesson)
            <tr>
                <td>{{ $lesson->course->title }}</td>
                <td>{{ $lesson->title }}</td>
                <td>{{ $lesson->order_number }}</td>
                <td>
                    {{ $lesson->status == 0 ? 'Hoạt động' : 'Không hoạt động' }}
                </td>
                <td>
                    <a href="{{ route('admin.lesson.edit', $lesson) }}" class="btn btn-warning btn-sm">
                        Sửa
                    </a>

                    <form method="POST" action="{{ route('admin.lesson.destroy', $lesson) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Xoá bài học?')">
                            Xoá
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    {{ $lessons->links() }}
@endsection
