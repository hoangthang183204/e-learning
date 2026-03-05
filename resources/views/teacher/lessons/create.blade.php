{{-- resources/views/teacher/lessons/create.blade.php --}}
@extends('teacher.layout')

@section('title', 'Thêm bài học mới')
@section('courses-active', 'active')
@section('page-icon', 'plus-circle')
@section('page-title', 'Thêm bài học mới')
@section('page-subtitle', $course->title)

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.index') }}">Khóa học</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.courses.show', $course) }}">{{ $course->title }}</a></li>
            <li class="breadcrumb-item active">Thêm bài học</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Thông tin bài học</h5>
                </div>
                <div class="card-body">
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('teacher.lessons.store', $course) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề bài học <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   placeholder="VD: Giới thiệu về Laravel" 
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="order_number" class="form-label">Thứ tự bài học <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('order_number') is-invalid @enderror" 
                                   id="order_number" 
                                   name="order_number" 
                                   value="{{ old('order_number', $nextOrder) }}"
                                   min="1"
                                   required>
                            @error('order_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Số thứ tự của bài học trong khóa học</small>
                        </div>

                        <div class="mb-3">
                            <label for="video_url" class="form-label">Video URL (YouTube)</label>
                            <input type="url" 
                                   class="form-control @error('video_url') is-invalid @enderror" 
                                   id="video_url" 
                                   name="video_url" 
                                   value="{{ old('video_url') }}"
                                   placeholder="https://www.youtube.com/watch?v=...">
                            @error('video_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung bài học</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="10"
                                      placeholder="Nhập nội dung bài học...">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu bài học
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Thông tin</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Khóa học</label>
                        <div class="fw-bold">{{ $course->title }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted small">Số bài học hiện tại</label>
                        <div class="fw-bold">{{ $course->lessons()->count() }}</div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-lightbulb me-2"></i>
                        <strong>Mẹo:</strong> Nội dung bài học có thể là text, code, hoặc hướng dẫn chi tiết.
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Lưu ý:</strong> Video URL phải là link YouTube hợp lệ.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection