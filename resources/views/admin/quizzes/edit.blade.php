{{-- resources/views/admin/quizzes/edit.blade.php --}}
@extends('admin.layout')

@section('title', 'Sửa bài kiểm tra')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Sửa bài kiểm tra: {{ $quiz->title }}</h1>
            <a href="{{ route('admin.quizzes.index', ['lesson_id' => $quiz->lesson_id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="lesson_id" class="form-label">Chọn bài học <span class="text-danger">*</span></label>
                        <select class="form-select @error('lesson_id') is-invalid @enderror" id="lesson_id" name="lesson_id"
                            required>
                            <option value="">-- Chọn bài học --</option>
                            @foreach ($lessons as $lesson)
                                <option value="{{ $lesson->id }}"
                                    {{ old('lesson_id', $quiz->lesson_id) == $lesson->id ? 'selected' : '' }}>
                                    {{ $lesson->course->title }} - {{ $lesson->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('lesson_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề bài kiểm tra <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                            name="title" value="{{ old('title', $quiz->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            rows="3">{{ old('description', $quiz->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="time_limit" class="form-label">Thời gian làm bài (phút) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('time_limit') is-invalid @enderror"
                                id="time_limit" name="time_limit" value="{{ old('time_limit', $quiz->time_limit) }}"
                                min="1" max="180" required>
                            @error('time_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="pass_score" class="form-label">Điểm đạt yêu cầu (%) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('pass_score') is-invalid @enderror"
                                id="pass_score" name="pass_score" value="{{ old('pass_score', $quiz->pass_score) }}"
                                min="0" max="100" required>
                            @error('pass_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="attempts_allowed" class="form-label">Số lần được phép làm <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('attempts_allowed') is-invalid @enderror"
                                id="attempts_allowed" name="attempts_allowed"
                                value="{{ old('attempts_allowed', $quiz->attempts_allowed) }}" min="1"
                                max="10" required>
                            @error('attempts_allowed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('admin.quizzes.index', ['lesson_id' => $quiz->lesson_id]) }}"
                        class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
@endsection
