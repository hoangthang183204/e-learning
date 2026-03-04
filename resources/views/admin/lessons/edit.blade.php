
@extends('admin.layout')

@section('content')
<h3>Cập nhật bài học</h3>

<form method="POST" action="{{ route('admin.lesson.update', $lesson) }}">
    @csrf
    @method('PUT')

    <select name="course_id" class="form-control mb-2">
        @foreach ($courses as $course)
            <option value="{{ $course->id }}"
                @selected($lesson->course_id == $course->id)>
                {{ $course->title }}
            </option>
        @endforeach
    </select>

    <input class="form-control mb-2" name="title"
        value="{{ $lesson->title }}">

    <textarea class="form-control mb-2" name="content" rows="4">
{{ $lesson->content }}</textarea>

    <input class="form-control mb-2" name="video_url"
        value="{{ $lesson->video_url }}">

    <input class="form-control mb-2" name="order_number" type="number"
        value="{{ $lesson->order_number }}">

    <button class="btn btn-success">Cập nhật</button>
    <a href="{{ route('admin.lesson.index') }}" class="btn btn-secondary">Quay lại</a>
</form>
@endsection