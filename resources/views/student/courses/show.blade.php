@extends('student.layout')

@section('page-icon', 'book')
@section('page-title', $course->title)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Thông tin khóa học -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Giới thiệu khóa học</h5>
                    <p class="card-text">{{ $course->description }}</p>
                    
                    @if($course->teacher)
                        <p class="mb-0">
                            <i class="bi bi-person-circle me-1"></i>
                            Giảng viên: <strong>{{ $course->teacher->name }}</strong>
                        </p>
                    @endif
                </div>
            </div>

            @if($isEnrolled && in_array($enrollmentStatus, ['approved', 'finished']))
                <!-- Tiến độ -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Tiến độ học tập</h5>
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $progress }}%">
                                {{ $progress }}%
                            </div>
                        </div>
                        <p class="text-secondary">
                            Đã học <strong>{{ $completedLessons }}</strong> / 
                            <strong>{{ $totalLessons }}</strong> bài
                        </p>
                    </div>
                </div>

                <!-- Danh sách bài học -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Nội dung khóa học</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($lessons as $lesson)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('student.lessons.show', $lesson) }}" 
                                           class="text-decoration-none">
                                            <i class="bi bi-play-circle me-2"></i>
                                            Bài {{ $lesson->order_number }}: {{ $lesson->title }}
                                        </a>
                                    </div>
                                    <div>
                                        @if($lesson->is_completed)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Đã học
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Chưa học</span>
                                        @endif
                                    </div>
                                </div>
                                @if($lesson->duration)
                                    <small class="text-secondary ms-4">
                                        <i class="bi bi-clock"></i> {{ $lesson->duration }} phút
                                    </small>
                                @endif
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <i class="bi bi-film" style="font-size: 40px; color: #dee2e6;"></i>
                                <p class="mt-2 mb-0">Chưa có bài học nào</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                <!-- Trạng thái đăng ký -->
                <div class="alert alert-info">
                    @if(!$isEnrolled)
                        <h5 class="alert-heading">Bạn chưa đăng ký khóa học</h5>
                        <p>Đăng ký ngay để bắt đầu học tập!</p>
                    @elseif($enrollmentStatus == 'pending')
                        <h5 class="alert-heading">Yêu cầu đang chờ duyệt</h5>
                        <p>Vui lòng chờ giảng viên xác nhận đăng ký của bạn.</p>
                    @elseif($enrollmentStatus == 'rejected')
                        <h5 class="alert-heading text-danger">Yêu cầu bị từ chối</h5>
                        <p>Liên hệ giảng viên để biết thêm chi tiết.</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Sidebar -->
            <div class="card">
                <div class="card-body">
                    @if(!$isEnrolled)
                        <form method="POST" action="{{ route('student.courses.enroll', $course) }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-plus-circle me-1"></i> Đăng ký khóa học
                            </button>
                        </form>
                    @elseif(in_array($enrollmentStatus, ['approved', 'finished']))
                        @if(!$isCompleted && $totalLessons > 0 && $completedLessons == $totalLessons)
                            <form method="POST" action="{{ route('student.courses.complete', $course) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-check-circle me-1"></i> Hoàn thành khóa học
                                </button>
                            </form>
                        @endif
                        
                        <form method="POST" action="{{ route('student.courses.unenroll', $course) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" 
                                    onclick="return confirm('Bạn có chắc muốn hủy đăng ký?')">
                                <i class="bi bi-x-circle me-1"></i> Hủy đăng ký
                            </button>
                        </form>
                    @elseif($enrollmentStatus == 'pending')
                        <form method="POST" action="{{ route('student.courses.unenroll', $course) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Bạn có chắc muốn hủy yêu cầu?')">
                                <i class="bi bi-x-circle me-1"></i> Hủy yêu cầu
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection