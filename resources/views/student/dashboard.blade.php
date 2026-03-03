{{-- resources/views/student/dashboard.blade.php --}}
@extends('student.layout')

@section('title', 'Dashboard')

@section('page-icon', 'house-door')
@section('page-title', 'Trang chủ')

@section('content')
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-light p-4 rounded border">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="mb-2">Xin chào, {{ auth()->user()->name ?? 'Học viên' }}! 👋</h3>
                        <p class="text-secondary mb-0">Chào mừng bạn đến với hệ thống học tập trực tuyến.</p>
                    </div>
                    <div class="mt-2 mt-sm-0">
                        <span class="badge bg-success px-3 py-2">
                            <i class="bi bi-calendar me-1"></i>
                            {{ now()->format('l, d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="bg-white p-3 rounded border h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-book text-success" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Đang học</div>
                        <div class="h3 mb-0 fw-bold">{{ $enrolledCourses ?? 0 }}</div>
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i> {{ $newCourses ?? 0 }} mới
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="bg-white p-3 rounded border h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-check-circle text-primary" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Đã hoàn thành</div>
                        <div class="h3 mb-0 fw-bold">{{ $completedCourses ?? 0 }}</div>
                        <small class="text-primary">{{ $completionRate ?? 0 }}% tổng số</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="bg-white p-3 rounded border h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-pencil-square text-warning" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Bài quiz</div>
                        <div class="h3 mb-0 fw-bold">{{ $totalQuizzes ?? 0 }}</div>
                        <small class="text-warning">{{ $pendingQuizzes ?? 0 }} chưa làm</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="bg-white p-3 rounded border h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                        <i class="bi bi-trophy text-info" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="small text-secondary">Điểm TB</div>
                        <div class="h3 mb-0 fw-bold">{{ $avgScore ?? 0 }}%</div>
                        <small class="text-info">Top {{ $topRank ?? 0 }}%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Left Column - Courses -->
        <div class="col-lg-8">
            <!-- In Progress Courses -->
            <div class="bg-white rounded border mb-4">
                <div class="p-3 border-bottom bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-play-circle-fill text-success me-2"></i>
                            Đang học
                        </h5>
                        <a href="{{ route('student.courses.index') }}" class="text-success text-decoration-none">
                            Xem tất cả <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="p-3">
                    @forelse($inProgressCourses ?? [] as $course)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $course->title }}</h6>
                                    <small class="text-secondary">
                                        <i class="bi bi-collection me-1"></i>{{ $course->lessons_count }} bài học
                                    </small>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                    {{ $course->progress ?? 0 }}%
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ $course->progress ?? 0 }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-secondary">
                                    <i class="bi bi-clock me-1"></i>
                                    Cập nhật: {{ $course->updated_at->diffForHumans() }}
                                </small>
                                <a href="{{ route('student.courses.show', $course) }}" class="btn btn-sm btn-outline-success">
                                    Học tiếp
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-book" style="font-size: 40px; color: #dee2e6;"></i>
                            <p class="mt-2 mb-0">Bạn chưa đăng ký khoá học nào</p>
                            <a href="{{ route('student.courses.index') }}" class="btn btn-success btn-sm mt-2">
                                Khám phá khoá học
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recommended Courses -->
            <div class="bg-white rounded border">
                <div class="p-3 border-bottom bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-star-fill text-warning me-2"></i>
                            Gợi ý cho bạn
                        </h5>
                        <span class="badge bg-warning text-dark">Mới</span>
                    </div>
                </div>
                <div class="p-3">
                    <div class="row g-3">
                        @forelse($recommendedCourses ?? [] as $course)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="mb-2">{{ $course->title }}</h6>
                                    <p class="small text-secondary mb-2">{{ Str::limit($course->description, 60) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-secondary">
                                            <i class="bi bi-people me-1"></i>{{ $course->students_count }} học viên
                                        </small>
                                        <a href="{{ route('student.courses.show', $course) }}" class="btn btn-sm btn-outline-success">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4">
                                <i class="bi bi-star" style="font-size: 40px; color: #dee2e6;"></i>
                                <p class="mt-2 mb-0">Chưa có gợi ý nào</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Activities & Quizzes -->
        <div class="col-lg-4">
            <!-- Upcoming Quizzes -->
            <div class="bg-white rounded border mb-4">
                <div class="p-3 border-bottom bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square text-warning me-2"></i>
                        Bài quiz sắp tới
                    </h5>
                </div>
                <div class="p-3">
                    @forelse($upcomingQuizzes ?? [] as $quiz)
                        <div class="mb-3 pb-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="mb-1">{{ $quiz->title }}</h6>
                                <small class="text-danger">
                                    <i class="bi bi-clock"></i> {{ $quiz->deadline->diffForHumans() }}
                                </small>
                            </div>
                            <small class="text-secondary d-block mb-2">
                                <i class="bi bi-book me-1"></i>{{ $quiz->course->title }}
                            </small>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-secondary">
                                    <i class="bi bi-question-circle"></i> {{ $quiz->questions_count }} câu
                                </small>
                                <a href="{{ route('student.quizzes.show', $quiz) }}" class="btn btn-sm btn-warning">
                                    Làm ngay
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-check" style="font-size: 40px; color: #dee2e6;"></i>
                            <p class="mt-2 mb-0">Không có quiz nào</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded border mb-4">
                <div class="p-3 border-bottom bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-info me-2"></i>
                        Hoạt động gần đây
                    </h5>
                </div>
                <div class="p-3">
                    @forelse($recentActivities ?? [] as $activity)
                        <div class="d-flex align-items-start mb-3 pb-2 border-bottom">
                            <div class="me-3">
                                @if($activity->type == 'lesson')
                                    <i class="bi bi-play-circle-fill text-success" style="font-size: 20px;"></i>
                                @elseif($activity->type == 'quiz')
                                    <i class="bi bi-pencil-fill text-warning" style="font-size: 20px;"></i>
                                @else
                                    <i class="bi bi-check-circle-fill text-primary" style="font-size: 20px;"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="small">{{ $activity->description }}</div>
                                <small class="text-secondary">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="bi bi-activity" style="font-size: 40px; color: #dee2e6;"></i>
                            <p class="mt-2 mb-0">Chưa có hoạt động</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Achievement Badges -->
            <div class="bg-white rounded border">
                <div class="p-3 border-bottom bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy-fill text-warning me-2"></i>
                        Thành tích
                    </h5>
                </div>
                <div class="p-3">
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($achievements ?? [] as $achievement)
                            <div class="text-center" style="width: 70px;">
                                <div class="bg-light rounded-circle p-2 mb-1 mx-auto" style="width: 50px; height: 50px;">
                                    <i class="bi bi-{{ $achievement->icon }} text-success" style="font-size: 24px;"></i>
                                </div>
                                <small class="d-block text-secondary">{{ $achievement->name }}</small>
                            </div>
                        @empty
                            <div class="text-center w-100 py-3">
                                <i class="bi bi-award" style="font-size: 40px; color: #dee2e6;"></i>
                                <p class="mt-2 mb-0">Chưa có thành tích</p>
                                <small class="text-secondary">Học tập để nhận huy hiệu</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <!-- Recent Quiz Results -->
    @if(($recentResults ?? [])->isNotEmpty())
        <div class="row mt-4">
            <div class="col-12">
                <div class="bg-white rounded border">
                    <div class="p-3 border-bottom bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-bar-chart-fill text-info me-2"></i>
                            Kết quả quiz gần đây
                        </h5>
                    </div>
                    <div class="p-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bài quiz</th>
                                        <th>Khoá học</th>
                                        <th>Điểm số</th>
                                        <th>Ngày làm</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentResults as $result)
                                        <tr>
                                            <td>{{ $result->quiz->title }}</td>
                                            <td>{{ $result->quiz->course->title }}</td>
                                            <td>
                                                <span class="badge bg-{{ $result->score >= 80 ? 'success' : ($result->score >= 50 ? 'warning' : 'danger') }} px-3 py-2">
                                                    {{ $result->score }}%
                                                </span>
                                                @if($result->passed)
                                                    <i class="bi bi-check-circle-fill text-success ms-1" title="Đạt"></i>
                                                @endif
                                            </td>
                                            <td>{{ $result->completed_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('student.quizzes.result', $result->quiz) }}" class="btn btn-sm btn-outline-primary">
                                                    Xem chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif --}}

    <!-- Progress Overview -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="bg-white rounded border">
                <div class="p-3 border-bottom bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up text-success me-2"></i>
                        Tổng quan tiến độ
                    </h5>
                </div>
                <div class="p-3">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Tiến độ trung bình</span>
                                    <span class="fw-bold">{{ $avgProgress ?? 0 }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $avgProgress ?? 0 }}%"></div>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="bg-light p-2 rounded text-center">
                                        <small class="text-secondary d-block">Bài đã học</small>
                                        <span class="h5 mb-0">{{ $totalCompletedLessons ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light p-2 rounded text-center">
                                        <small class="text-secondary d-block">Quiz đã làm</small>
                                        <span class="h5 mb-0">{{ $totalCompletedQuizzes ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <h6 class="mb-3">Phân bố thời gian học</h6>
                                <div class="mb-2">
                                    <small class="text-secondary">Tuần này</small>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 me-2">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-success" style="width: {{ $weeklyStudyTime ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="small fw-bold">{{ $weeklyHours ?? 0 }}h</span>
                                    </div>
                                </div>
                                <div>
                                    <small class="text-secondary">Tháng này</small>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 me-2">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-info" style="width: {{ $monthlyStudyTime ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="small fw-bold">{{ $monthlyHours ?? 0 }}h</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection