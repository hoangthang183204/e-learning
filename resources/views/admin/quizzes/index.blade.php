
@extends('admin.layout')

@section('title', 'Quản lý bài kiểm tra')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Quản lý bài kiểm tra</h1>
            <a href="{{ route('admin.quizzes.create', ['lesson_id' => $lessonId]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm bài kiểm tra
            </a>
        </div>

        {{-- Bộ lọc --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.quizzes.index') }}" class="row">
                    <div class="col-md-4">
                        <label class="form-label">Lọc theo bài học</label>
                        <select name="lesson_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Tất cả bài học --</option>
                            @foreach ($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ $lessonId == $lesson->id ? 'selected' : '' }}>
                                    {{ $lesson->course->title }} - {{ $lesson->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        {{-- Danh sách quizzes --}}
        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Bài học</th>
                            <th>Khóa học</th>
                            <th>Thời gian</th>
                            <th>Điểm đạt</th>
                            <th>Số câu</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quizzes as $quiz)
                            <tr>
                                <td>{{ $quiz->id }}</td>
                                <td>{{ $quiz->title }}</td>
                                <td>{{ $quiz->lesson->title }}</td>
                                <td>{{ $quiz->lesson->course->title }}</td>
                                <td>{{ $quiz->time_limit ?? 30 }} phút</td>
                                <td>{{ $quiz->pass_score ?? 70 }}%</td>
                                <td>{{ $quiz->questions_count }}</td>
                                <td>
                                    <a href="{{ route('admin.quizzes.questions', $quiz->id) }}" class="btn btn-sm btn-info"
                                        title="Quản lý câu hỏi">
                                        <i class="fas fa-question-circle"></i>
                                    </a>
                                    <a href="{{ route('admin.quizzes.results', $quiz->id) }}"
                                        class="btn btn-sm btn-success" title="Xem kết quả">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="btn btn-sm btn-warning"
                                        title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Xóa bài kiểm tra này?')" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Chưa có bài kiểm tra nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $quizzes->links() }}
            </div>
        </div>
    </div>
@endsection
