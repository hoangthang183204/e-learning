@extends('admin.layout')

@section('title', 'Sửa bài học')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Sửa bài học: {{ $lesson->title }}</h1>
        <a href="{{ route('admin.lessons.index', ['course_id' => $lesson->course_id]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.lessons.update', $lesson->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Khóa học <span class="text-danger">*</span></label>
                    <select name="course_id" class="form-control @error('course_id') is-invalid @enderror" required>
                        <option value="">-- Chọn khóa học --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" 
                                {{ old('course_id', $lesson->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Tiêu đề bài học <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                           value="{{ old('title', $lesson->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control @error('content') is-invalid @enderror" 
                              rows="6" required>{{ old('content', $lesson->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Link video (YouTube)</label>
                    <input type="url" name="video_url" class="form-control @error('video_url') is-invalid @enderror" 
                           value="{{ old('video_url', $lesson->video_url) }}" 
                           placeholder="https://www.youtube.com/watch?v=...">
                    @error('video_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Thứ tự bài học <span class="text-danger">*</span></label>
                    <input type="number" name="order_number" class="form-control @error('order_number') is-invalid @enderror" 
                           value="{{ old('order_number', $lesson->order_number) }}" min="1" required>
                    @error('order_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
                <a href="{{ route('admin.lessons.index', ['course_id' => $lesson->course_id]) }}" 
                   class="btn btn-secondary">
                    Hủy
                </a>
            </form>
        </div>
    </div>
</div>
@endsection