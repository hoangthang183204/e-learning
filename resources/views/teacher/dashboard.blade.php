@extends('teacher.layout')

@section('title', 'Dashboard Giảng viên')

@section('dashboard-active', 'active')
@section('page-icon', 'speedometer2')
@section('page-title', 'Tổng quan Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-text">
            <h2>Xin chào, {{ auth()->user()->name }}! 👋</h2>
            <p>Chào mừng bạn quay trở lại. Đây là tổng quan về hoạt động giảng dạy của bạn.</p>
        </div>
        <div class="welcome-actions">
            <a href="{{ route('teacher.courses.create') }}" class="btn-create">
                <i class="bi bi-plus-circle"></i> Tạo khoá học mới
            </a>
            <a href="{{ route('teacher.quizzes.create') }}" class="btn-create"
                style="background: linear-gradient(135deg, #FF6B6B, #FF8E53);">
                <i class="bi bi-pencil-square"></i> Tạo quiz mới
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4776E6, #8E54E9)">
                <i class="bi bi-book"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalCourses ?? 0 }}</div>
                <div class="stat-label">Khoá học</div>
                <div class="stat-trend {{ $courseGrowth >= 0 ? 'positive' : 'negative' }}">
                    <i class="bi bi-arrow-{{ $courseGrowth >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($courseGrowth) }}% so với tháng trước
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #FF6B6B, #FF8E53)">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalStudents ?? 0 }}</div>
                <div class="stat-label">Học viên</div>
                <div class="stat-trend {{ $studentGrowth >= 0 ? 'positive' : 'negative' }}">
                    <i class="bi bi-arrow-{{ $studentGrowth >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($studentGrowth) }}% so với tháng trước
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4ECDC4, #556270)">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalQuizzes ?? 0 }}</div>
                <div class="stat-label">Bài quiz</div>
                <div class="stat-trend">
                    <i class="bi bi-plus-circle"></i>
                    {{ $recentQuizzes->where('created_at', '>=', now()->subDays(7))->count() }} bài mới
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #FFD93D, #FF8C42)">
                <i class="bi bi-trophy"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $avgProgress ?? 0 }}%</div>
                <div class="stat-label">Tiến độ TB</div>
                <div class="stat-trend">
                    <i class="bi bi-clock"></i> Cập nhật gần nhất hôm nay
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="charts-row">
        <div class="chart-card">
            <div class="chart-header">
                <h5><i class="bi bi-graph-up"></i> Lượt truy cập khoá học</h5>
                <select class="chart-filter" id="viewsChartFilter">
                    <option value="7" selected>7 ngày qua</option>
                    <option value="30">30 ngày qua</option>
                    <option value="90">3 tháng qua</option>
                </select>
            </div>
            <div class="chart-body">
                <canvas id="viewsChart" height="200"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h5><i class="bi bi-pie-chart"></i> Phân bố học viên</h5>
                <select class="chart-filter" id="distributionFilter">
                    <option value="course">Theo khoá học</option>
                    <option value="progress">Theo tiến độ</option>
                </select>
            </div>
            <div class="chart-body">
                <canvas id="distributionChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Courses Section -->
    @if ($topCourses->isNotEmpty())
        <div class="section-header">
            <div class="section-title">
                <i class="bi bi-star-fill text-warning"></i>
                Khoá học nổi bật
            </div>
            <a href="{{ route('teacher.courses.index') }}" class="view-all">
                Xem tất cả <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="top-courses-grid">
            @foreach ($topCourses as $course)
                <div class="top-course-card">
                    <div class="course-rank">#{{ $loop->iteration }}</div>
                    <div class="course-info">
                        <h4>{{ $course->title }}</h4>
                        <div class="course-meta">
                            <span><i class="bi bi-people"></i> {{ $course->students_count }} học viên</span>
                            <span><i class="bi bi-collection"></i> {{ $course->lessons_count }} bài học</span>
                        </div>
                    </div>
                    <a href="{{ route('teacher.courses.show', $course) }}" class="btn-view">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Recent Courses Section -->
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-book"></i>
            Khoá học gần đây
        </div>
        <a href="{{ route('teacher.courses.index') }}" class="view-all">
            Xem tất cả <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="courses-grid">
        @forelse($courses->take(6) as $course)
            <div class="course-card">
                <div class="card-header"
                    style="background: linear-gradient(135deg, {{ $course->color_1 }}, {{ $course->color_2 }})">
                    <div class="course-icon">
                        @switch($loop->index % 6)
                            @case(0)
                                📘
                            @break

                            @case(1)
                                📗
                            @break

                            @case(2)
                                📙
                            @break

                            @case(3)
                                📕
                            @break

                            @case(4)
                                📓
                            @break

                            @default
                                📚
                        @endswitch
                    </div>
                    <span class="course-status {{ $course->status }}">
                        {{ $course->status == 'published' ? 'Đã xuất bản' : 'Bản nháp' }}
                    </span>
                </div>

                <div class="card-body">
                    <h4 class="course-title">{{ $course->title }}</h4>
                    <p class="course-description">{{ Str::limit($course->description, 80) }}</p>

                    <div class="course-stats">
                        <div class="stat-item">
                            <i class="bi bi-collection"></i>
                            <span>{{ $course->lessons_count ?? 0 }} bài</span>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-people"></i>
                            <span>{{ $course->students_count ?? 0 }} học viên</span>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-clock"></i>
                            <span>{{ $course->total_duration ?? 0 }} phút</span>
                        </div>
                    </div>

                    <div class="course-progress">
                        <div class="progress-label">
                            <span>Tiến độ trung bình</span>
                            <span>{{ $course->avg_progress ?? 0 }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $course->avg_progress ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="course-footer">
                        <a href="{{ route('teacher.courses.dashboard', $course) }}" class="btn-manage">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a href="{{ route('teacher.courses.show', $course) }}" class="btn-icon" title="Xem chi tiết">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('teacher.courses.edit', $course) }}" class="btn-icon" title="Chỉnh sửa">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">📚</div>
                    <h4>Chưa có khoá học nào</h4>
                    <p>Bắt đầu tạo khoá học đầu tiên để chia sẻ kiến thức</p>
                    <a href="{{ route('teacher.courses.create') }}" class="btn-create">
                        <i class="bi bi-plus-circle"></i> Tạo khoá học
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Recent Quizzes Section -->
        @if ($recentQuizzes->isNotEmpty())
            <div class="section-header" style="margin-top: 40px;">
                <div class="section-title">
                    <i class="bi bi-pencil-square"></i>
                    Bài quiz gần đây
                </div>
                <a href="{{ route('teacher.quizzes.index') }}" class="view-all">
                    Xem tất cả <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="quizzes-grid">
                @foreach ($recentQuizzes as $quiz)
                    <div class="quiz-card">
                        <div class="quiz-header">
                            <span class="quiz-badge">{{ $quiz->questions_count ?? 0 }} câu hỏi</span>
                            @if ($quiz->lesson && $quiz->lesson->course)
                                <span class="quiz-course">{{ $quiz->lesson->course->title }}</span>
                            @endif
                        </div>
                        <div class="quiz-body">
                            <h4 class="quiz-title">{{ $quiz->title }}</h4>
                            <p class="quiz-meta">
                                <i class="bi bi-book"></i> {{ $quiz->lesson->title ?? 'Không có bài học' }}
                            </p>
                            <div class="quiz-stats">
                                <div class="stat">
                                    <i class="bi bi-people"></i>
                                    <span>{{ $quiz->attempts_count ?? 0 }} lượt làm</span>
                                </div>
                                <div class="stat">
                                    <i class="bi bi-check-circle"></i>
                                    <span>{{ $quiz->avg_score ?? 0 }}% điểm TB</span>
                                </div>
                            </div>
                        </div>
                        <div class="quiz-footer">
                            <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="btn-view">
                                <i class="bi bi-eye"></i> Xem chi tiết
                            </a>
                            <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn-icon">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Recent Activities -->
        @if ($recentActivities->isNotEmpty())
            <div class="section-header" style="margin-top: 40px;">
                <div class="section-title">
                    <i class="bi bi-activity"></i>
                    Hoạt động gần đây
                </div>
            </div>

            <div class="activities-card">
                <div class="activities-list">
                    @foreach ($recentActivities as $activity)
                        <div class="activity-item">
                            <div class="activity-icon"
                                style="background: {{ $activity->type == 'enrollment' ? '#e3f2fd' : ($activity->type == 'completion' ? '#e8f5e8' : '#fff3e0') }}">
                                <i class="bi bi-{{ $activity->icon }}"
                                    style="color: {{ $activity->type == 'enrollment' ? '#1976d2' : ($activity->type == 'completion' ? '#2e7d32' : '#ed6c02') }}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-text">
                                    <strong>{{ $activity->user_name }}</strong> {{ $activity->description }}
                                </div>
                                <div class="activity-time">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <style>
            /* Welcome Section */
            .welcome-section {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 20px;
                padding: 30px;
                margin-bottom: 30px;
                color: white;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 20px;
            }

            .welcome-text h2 {
                font-size: 24px;
                font-weight: 600;
                margin: 0 0 10px 0;
            }

            .welcome-text p {
                margin: 0;
                opacity: 0.9;
                font-size: 15px;
            }

            .welcome-actions {
                display: flex;
                gap: 10px;
            }

            .btn-create {
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(10px);
                color: white;
                text-decoration: none;
                padding: 12px 24px;
                border-radius: 50px;
                font-size: 14px;
                font-weight: 500;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border: 1px solid rgba(255, 255, 255, 0.3);
                transition: all 0.3s ease;
            }

            .btn-create:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: translateY(-2px);
                color: white;
            }

            /* Stats Grid */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }

            .stat-card {
                background: white;
                border-radius: 20px;
                padding: 25px;
                display: flex;
                gap: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 60px;
                height: 60px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 28px;
                flex-shrink: 0;
            }

            .stat-info {
                flex: 1;
            }

            .stat-value {
                font-size: 32px;
                font-weight: 700;
                color: #2c3e50;
                line-height: 1.2;
                margin-bottom: 5px;
            }

            .stat-label {
                font-size: 14px;
                color: #6c757d;
                margin-bottom: 10px;
            }

            .stat-trend {
                font-size: 13px;
                display: flex;
                align-items: center;
                gap: 3px;
            }

            .stat-trend.positive {
                color: #10b981;
            }

            .stat-trend.negative {
                color: #ef4444;
            }

            .stat-trend i {
                font-size: 14px;
            }

            /* Top Courses Grid */
            .top-courses-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 15px;
                margin-bottom: 30px;
            }

            .top-course-card {
                background: white;
                border-radius: 12px;
                padding: 15px;
                display: flex;
                align-items: center;
                gap: 15px;
                border: 1px solid #e0e0e0;
                transition: all 0.3s ease;
            }

            .top-course-card:hover {
                transform: translateX(5px);
                border-color: #4776E6;
                box-shadow: 0 5px 20px rgba(71, 118, 230, 0.1);
            }

            .course-rank {
                width: 30px;
                height: 30px;
                background: linear-gradient(135deg, #FFD93D, #FF8C42);
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                color: white;
                font-size: 14px;
            }

            .course-info {
                flex: 1;
            }

            .course-info h4 {
                font-size: 15px;
                font-weight: 600;
                color: #2c3e50;
                margin: 0 0 5px 0;
            }

            .course-meta {
                display: flex;
                gap: 15px;
                font-size: 12px;
                color: #6c757d;
            }

            .course-meta i {
                margin-right: 3px;
            }

            /* Charts Row */
            .charts-row {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 40px;
            }

            .chart-card {
                background: white;
                border-radius: 20px;
                padding: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .chart-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }

            .chart-header h5 {
                font-size: 16px;
                font-weight: 600;
                color: #2c3e50;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .chart-header h5 i {
                color: #4776E6;
            }

            .chart-filter {
                padding: 5px 10px;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                font-size: 13px;
                color: #6c757d;
                cursor: pointer;
                background: white;
            }

            .chart-filter:hover {
                border-color: #4776E6;
            }

            .chart-filter:focus {
                outline: none;
                border-color: #4776E6;
                box-shadow: 0 0 0 2px rgba(71, 118, 230, 0.1);
            }

            .chart-body {
                height: 200px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Section Header */
            .section-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
            }

            .section-title {
                font-size: 20px;
                font-weight: 600;
                color: #2c3e50;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .section-title i {
                color: #4776E6;
                font-size: 24px;
            }

            .view-all {
                color: #4776E6;
                text-decoration: none;
                font-size: 14px;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 5px;
                transition: all 0.3s ease;
            }

            .view-all:hover {
                color: #8E54E9;
                gap: 8px;
            }

            /* Courses Grid */
            .courses-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                gap: 25px;
            }

            .course-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .course-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            }

            .card-header {
                height: 100px;
                padding: 20px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                position: relative;
            }

            .course-icon {
                width: 50px;
                height: 50px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 28px;
                color: white;
                backdrop-filter: blur(5px);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }

            .course-status {
                padding: 5px 12px;
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(5px);
                border-radius: 30px;
                color: white;
                font-size: 12px;
                font-weight: 500;
                border: 1px solid rgba(255, 255, 255, 0.3);
            }

            .course-status.published {
                background: rgba(16, 185, 129, 0.9);
            }

            .card-body {
                padding: 25px;
            }

            .course-title {
                font-size: 18px;
                font-weight: 700;
                color: #2c3e50;
                margin: 0 0 10px 0;
            }

            .course-description {
                color: #6c757d;
                font-size: 14px;
                line-height: 1.6;
                margin-bottom: 20px;
            }

            .course-stats {
                display: flex;
                gap: 20px;
                margin-bottom: 20px;
            }

            .stat-item {
                display: flex;
                align-items: center;
                gap: 5px;
                color: #6c757d;
                font-size: 13px;
            }

            .stat-item i {
                color: #4776E6;
            }

            .course-progress {
                margin-bottom: 20px;
            }

            .progress-label {
                display: flex;
                justify-content: space-between;
                font-size: 13px;
                color: #6c757d;
                margin-bottom: 5px;
            }

            .progress-bar {
                height: 6px;
                background: #e9ecef;
                border-radius: 3px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                background: linear-gradient(135deg, #4776E6, #8E54E9);
                border-radius: 3px;
                transition: width 0.3s ease;
            }

            .course-footer {
                display: flex;
                gap: 10px;
                align-items: center;
            }

            .btn-manage {
                flex: 1;
                background: linear-gradient(135deg, #4776E6, #8E54E9);
                color: white;
                text-decoration: none;
                padding: 8px 16px;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 500;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
                transition: all 0.3s ease;
            }

            .btn-manage:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(71, 118, 230, 0.3);
                color: white;
            }

            .btn-icon {
                width: 36px;
                height: 36px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f8f9fa;
                color: #6c757d;
                text-decoration: none;
                transition: all 0.3s ease;
                border: 1px solid #e0e0e0;
            }

            .btn-icon:hover {
                background: #4776E6;
                color: white;
                border-color: #4776E6;
            }

            /* Quizzes Grid */
            .quizzes-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 20px;
                margin-top: 20px;
            }

            .quiz-card {
                background: white;
                border-radius: 16px;
                overflow: hidden;
                border: 1px solid #e0e0e0;
                transition: all 0.3s ease;
            }

            .quiz-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .quiz-header {
                background: linear-gradient(135deg, #f8fafd, #e9ecef);
                padding: 15px;
                border-bottom: 1px solid #e0e0e0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .quiz-badge {
                background: linear-gradient(135deg, #4776E6, #8E54E9);
                color: white;
                padding: 4px 10px;
                border-radius: 30px;
                font-size: 12px;
                font-weight: 500;
            }

            .quiz-course {
                font-size: 11px;
                color: #6c757d;
                background: white;
                padding: 3px 8px;
                border-radius: 20px;
            }

            .quiz-body {
                padding: 20px;
            }

            .quiz-title {
                font-size: 16px;
                font-weight: 600;
                color: #2c3e50;
                margin: 0 0 8px 0;
            }

            .quiz-meta {
                font-size: 13px;
                color: #6c757d;
                margin-bottom: 15px;
            }

            .quiz-meta i {
                color: #4776E6;
            }

            .quiz-stats {
                display: flex;
                gap: 15px;
            }

            .stat {
                display: flex;
                align-items: center;
                gap: 5px;
                font-size: 12px;
                color: #6c757d;
            }

            .quiz-footer {
                padding: 15px 20px;
                border-top: 1px solid #e0e0e0;
                display: flex;
                gap: 10px;
            }

            .btn-view {
                flex: 1;
                background: #f8f9fa;
                border: 1px solid #e0e0e0;
                color: #2c3e50;
                text-decoration: none;
                padding: 8px;
                border-radius: 8px;
                font-size: 13px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
                transition: all 0.3s ease;
            }

            .btn-view:hover {
                background: #4776E6;
                color: white;
                border-color: #4776E6;
            }

            /* Activities */
            .activities-card {
                background: white;
                border-radius: 16px;
                padding: 20px;
                border: 1px solid #e0e0e0;
            }

            .activities-list {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .activity-item {
                display: flex;
                gap: 15px;
                align-items: center;
                padding-bottom: 15px;
                border-bottom: 1px solid #e9ecef;
            }

            .activity-item:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }

            .activity-icon {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                flex-shrink: 0;
            }

            .activity-content {
                flex: 1;
            }

            .activity-text {
                font-size: 14px;
                color: #2c3e50;
                margin-bottom: 3px;
                line-height: 1.4;
            }

            .activity-text strong {
                font-weight: 600;
                color: #2c3e50;
            }

            .activity-time {
                font-size: 12px;
                color: #6c757d;
            }

            /* Empty State */
            .empty-state {
                text-align: center;
                padding: 60px 20px;
                background: white;
                border-radius: 20px;
                border: 1px solid #e0e0e0;
                grid-column: 1 / -1;
            }

            .empty-icon {
                font-size: 60px;
                margin-bottom: 20px;
                opacity: 0.5;
            }

            .empty-state h4 {
                color: #2c3e50;
                margin-bottom: 10px;
                font-size: 18px;
            }

            .empty-state p {
                color: #6c757d;
                margin-bottom: 25px;
            }

            .empty-state .btn-create {
                display: inline-flex;
                background: linear-gradient(135deg, #4776E6, #8E54E9);
                color: white;
                text-decoration: none;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 992px) {
                .charts-row {
                    grid-template-columns: 1fr;
                }

                .courses-grid,
                .quizzes-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 768px) {
                .welcome-section {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .welcome-actions {
                    width: 100%;
                    flex-direction: column;
                }

                .btn-create {
                    width: 100%;
                    justify-content: center;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .courses-grid,
                .quizzes-grid {
                    grid-template-columns: 1fr;
                }

                .course-stats {
                    flex-wrap: wrap;
                }

                .top-courses-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Views Chart
                const viewsCtx = document.getElementById('viewsChart')?.getContext('2d');
                if (viewsCtx) {
                    window.viewsChart = new Chart(viewsCtx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($viewsData['labels'] ?? ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']) !!},
                            datasets: [{
                                label: 'Lượt xem',
                                data: {!! json_encode($viewsData['data'] ?? [65, 78, 82, 95, 88, 102, 110]) !!},
                                borderColor: '#4776E6',
                                backgroundColor: 'rgba(71, 118, 230, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#4776E6',
                                pointBorderColor: 'white',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: 'white',
                                    titleColor: '#2c3e50',
                                    bodyColor: '#6c757d',
                                    borderColor: '#e0e0e0',
                                    borderWidth: 1,
                                    padding: 10,
                                    boxPadding: 5
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }

                // Distribution Chart
                const distCtx = document.getElementById('distributionChart')?.getContext('2d');
                if (distCtx) {
                    window.distributionChart = new Chart(distCtx, {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode($distributionData['labels'] ?? ['Đang học', 'Hoàn thành', 'Chưa bắt đầu']) !!},
                            datasets: [{
                                data: {!! json_encode($distributionData['data'] ?? [65, 20, 15]) !!},
                                backgroundColor: [
                                    '#4776E6',
                                    '#10b981',
                                    '#e0e0e0'
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        padding: 15
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'white',
                                    titleColor: '#2c3e50',
                                    bodyColor: '#6c757d',
                                    borderColor: '#e0e0e0',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            return `${context.label}: ${context.raw}%`;
                                        }
                                    }
                                }
                            },
                            cutout: '65%'
                        }
                    });
                }

                // Chart filter events
                document.getElementById('viewsChartFilter')?.addEventListener('change', function(e) {
                    // Trong thực tế, bạn sẽ gọi API để lấy dữ liệu mới
                    console.log('Filter views by:', e.target.value);
                });

                document.getElementById('distributionFilter')?.addEventListener('change', function(e) {
                    // Trong thực tế, bạn sẽ gọi API để lấy dữ liệu mới
                    console.log('Filter distribution by:', e.target.value);
                });
            });
        </script>
    @endsection
