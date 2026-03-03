@extends('admin.layout')

@section('content')
    <form method="POST" action="{{ route('admin.courses.store') }}">
        @csrf

        <input class="form-control mb-2" name="title" placeholder="Tên khoá học">

        <textarea class="form-control mb-2" name="description" placeholder="Mô tả"></textarea>
        
        <select class="form-control mb-2" name="status">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>

        <select class="form-control mb-2" name="teacher_id">
            @foreach ($teachers as $teacher)
                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
            @endforeach
        </select>

        <button class="btn btn-success">Lưu</button>
    </form>
@endsection
