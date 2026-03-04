{{-- resources/views/admin/quizzes/results.blade.php --}}
@extends('admin.layout')

@section('title', 'Kết quả bài kiểm tra')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                Kết quả bài kiểm tra: <span class="text-primary">{{ $quiz->title }}</span>
                <small class="text-muted">({{ $quiz->lesson->course->title }} - {{ $quiz->lesson->title }})</small>
            </h1>
            <a href="{{ route('admin.quizzes.index', ['lesson_id' => $quiz->lesson_id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        {{-- Thống kê nhanh --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Tổng lượt làm</h6>
                        <h3>{{ $results->total() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Đạt yêu cầu</h6>
                        <h3>{{ $results->where('passed', true)->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>Chưa đạt</h6>
                        <h3>{{ $results->where('passed', false)->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Điểm trung bình</h6>
                        <h3>{{ round($results->avg('score'), 2) }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Chi tiết kết quả</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Học viên</th>
                            <th>Email</th>
                            <th>Điểm số</th>
                            <th>Kết quả</th>
                            <th>Ngày làm bài</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $index => $result)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $result->user->name ?? 'N/A' }}</td>
                                <td>{{ $result->user->email ?? 'N/A' }}</td>
                                <td>
                                    {{ $result->score }}
                                </td>
                                <td>
                                    {{ $result->passed ? 'Đạt yêu cầu' : 'Chưa đạt' }}
                                </td>
                                <td>{{ $result->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Chưa có kết quả nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-end">
                    {{ $results->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
