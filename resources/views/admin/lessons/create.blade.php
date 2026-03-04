{{-- resources/views/admin/lessons/create.blade.php --}}
@extends('admin.layout')

@section('content')
<h3>Thêm bài học</h3>

<form method="POST" action="{{ route('admin.lessons.store') }}">
    @csrf

    <select name="course_id" class="form-control mb-2">
        <option value="">-- Chọn khoá học --</option>
        @foreach ($courses as $course)
            <option value="{{ $course->id }}">{{ $course->title }}</option>
        @endforeach
    </select>

    <input class="form-control mb-2" name="title" placeholder="Tiêu đề bài học">

    <textarea class="form-control mb-2" name="content" rows="4"
        placeholder="Nội dung bài học"></textarea>

    <input class="form-control mb-2" name="video_url"
        placeholder="Link video (Youtube)">

    <input class="form-control mb-2" name="order_number" type="number"
        placeholder="Thứ tự bài học">

    <button class="btn btn-success">Lưu</button>
    <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">Quay lại</a>
</form>
@endsection