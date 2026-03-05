{{-- resources/views/teacher/courses/edit.blade.php --}}
@extends('teacher.layout')

@section('title', 'Sửa khóa học: ' . $course->title)
@section('courses-active', 'active')
@section('page-icon', 'pencil')
@section('page-title', 'Sửa khóa học')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Chỉnh sửa khóa học</h5>
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

                    <form action="{{ route('teacher.courses.update', $course) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Tên khóa học <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $course->title) }}"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả khóa học</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="5">{{ old('description', $course->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Trạng thái</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status1" value="1" {{ old('status', $course->status) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status1">
                                        Hoạt động
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status0" value="0" {{ old('status', $course->status) == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status0">
                                        Ẩn
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Cập nhật
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
                        <label class="text-muted small">Ngày tạo</label>
                        <div class="fw-bold">{{ $course->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Bài học</label>
                        <div class="fw-bold">{{ $course->lessons_count ?? 0 }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Học viên</label>
                        <div class="fw-bold">{{ $course->students_count ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection