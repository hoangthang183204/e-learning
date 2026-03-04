@extends('admin.layout')

@section('title', 'Thêm khoá học mới')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Thêm khoá học mới</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.courses.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tên khoá học <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                   value="{{ old('title') }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                      rows="4">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Giáo viên phụ trách <span class="text-danger">*</span></label>
            <select name="teacher_id" class="form-control @error('teacher_id') is-invalid @enderror" required>
                <option value="">-- Chọn giáo viên --</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>
            @error('teacher_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input type="checkbox" name="status" class="form-check-input" value="1" 
                       {{ old('status') ? 'checked' : '' }}>
                <label class="form-check-label">Kích hoạt khoá học</label>
            </div>
            <small class="text-muted">Nếu không chọn, khoá học sẽ ở trạng thái đóng</small>
        </div>

        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection