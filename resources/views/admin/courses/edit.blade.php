@extends('admin.layout')

@section('content')
    <form method="POST" action="{{ route('admin.courses.update', $course) }}">
        @csrf @method('PUT')

        <input class="form-control mb-2" name="title" value="{{ $course->title }}">

        <textarea class="form-control mb-2" name="description">{{ $course->description }}</textarea>

        <select class="form-control mb-2" name="teacher_id">
            @foreach ($teachers as $teacher)
                <option value="{{ $teacher->id }}" @selected($course->teacher_id == $teacher->id)>
                    {{ $teacher->name }}
                </option>
            @endforeach
        </select>

        <select class="form-control mb-2" name="status">
            <option value="1" @selected($course->status == 1)>Active</option>
            <option value="0" @selected($course->status == 0)>Inactive</option>
        </select>

        <a href="/admin/courses" class="btn btn-primary"><- Quay lại</a>
                <button class="btn btn-success">Cập nhật</button>
    </form>
@endsection
