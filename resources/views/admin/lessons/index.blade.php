@extends('admin.layout')

@section('title', 'Quản lý bài học')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Quản lý bài học</h1>
            <a href="{{ route('admin.lessons.create', ['course_id' => $courseId]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm bài học
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Bộ lọc theo khóa học --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.lessons.index') }}" class="row">
                    <div class="col-md-4">
                        <label class="form-label">Lọc theo khóa học</label>
                        <select name="course_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Tất cả khóa học --</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        {{-- Danh sách bài học --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tiêu đề</th>
                                <th>Khóa học</th>
                                <th>Thứ tự</th>
                                <th>Video</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lessons as $lesson)
                                <tr>
                                    <td>{{ $lesson->id }}</td>
                                    <td>{{ $lesson->title }}</td>
                                    <td>{{ $lesson->course->title ?? 'N/A' }}</td>
                                    <td>{{ $lesson->order_number }}</>
                                    </td>
                                    <td>
                                        @if ($lesson->video_url)
                                            <a href="{{ $lesson->video_url }}" target="_blank" class="btn btn-sm btn-info"
                                                title="Xem video">
                                                <i class="fas fa-video"></i>
                                            </a>
                                        @else
                                            <span class="badge bg-secondary">Không có</span>
                                        @endif
                                    </td>
                                    <td>{{ $lesson->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.lessons.edit', $lesson->id) }}"
                                            class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if ($lesson->quizzes()->count() == 0)
                                            <form action="{{ route('admin.lessons.destroy', $lesson->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Xóa bài học {{ $lesson->title }}?')"
                                                    title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled
                                                title="Không thể xóa vì đã có bài kiểm tra">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Chưa có bài học nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $lessons->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
