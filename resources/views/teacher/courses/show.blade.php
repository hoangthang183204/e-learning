{{-- resources/views/teacher/courses/show.blade.php --}}
@extends('teacher.layout')

@section('title', $course->title)

@section('courses-active', 'active')
@section('page-icon', 'book')
@section('page-title', 'Chi tiết Khoá học')

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb mb-4">
        <a href="{{ route('teacher.courses.index') }}" class="text-decoration-none">Khoá học</a>
        <span class="mx-2">/</span>
        <span class="text-muted">{{ $course->title }}</span>
    </div>

    <!-- Course Detail Container -->
    <div class="detail-container">
        <!-- Header với các action buttons -->
        <div class="detail-header">
            <div class="header-left">
                <div class="course-icon-large">
                    @switch($course->id % 6)
                        @case(1) 📘 @break
                        @case(2) 📗 @break
                        @case(3) 📙 @break
                        @case(4) 📕 @break
                        @case(5) 📓 @break
                        @default 📚
                    @endswitch
                </div>
                <div class="course-info">
                    <h3 class="course-title-main">
                        {{ $course->title }}
                        @if($course->status === 'published')
                            <span class="status-badge published">Đã xuất bản</span>
                        @else
                            <span class="status-badge draft">Bản nháp</span>
                        @endif
                    </h3>
                    <div class="course-meta">
                        <span class="meta-item">
                            <i class="bi bi-calendar"></i>
                            Tạo ngày: {{ $course->created_at->format('d/m/Y') }}
                        </span>
                        @if($course->category)
                            <span class="meta-item">
                                <i class="bi bi-tag"></i>
                                {{ $course->category }}
                            </span>
                        @endif
                        @if($course->level)
                            <span class="meta-item">
                                <i class="bi bi-bar-chart"></i>
                                @switch($course->level)
                                    @case('beginner') Cơ bản @break
                                    @case('intermediate') Trung cấp @break
                                    @case('advanced') Nâng cao @break
                                    @default {{ $course->level }}
                                @endswitch
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('teacher.courses.edit', $course) }}" class="btn-edit">
                    <i class="bi bi-pencil"></i> Sửa khoá học
                </a>
                <form method="POST" action="" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa khoá học này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </form>
                <a href="{{ route('teacher.courses.index') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <!-- Course Description -->
        @if($course->description)
            <div class="description-section">
                <h4><i class="bi bi-info-circle"></i> Mô tả khoá học</h4>
                <div class="description-content">
                    {{ $course->description }}
                </div>
            </div>
        @endif

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4776E6, #8E54E9)">
                    <i class="bi bi-collection"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $lessons->count() }}</div>
                    <div class="stat-label">Bài học</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FF6B6B, #FF8E53)">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $course->students_count ?? 0 }}</div>
                    <div class="stat-label">Học viên</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4ECDC4, #556270)">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $course->total_duration ?? 0 }} ph</div>
                    <div class="stat-label">Tổng thời gian</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FFD93D, #FF8C42)">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $course->completion_rate ?? 0 }}%</div>
                    <div class="stat-label">Hoàn thành</div>
                </div>
            </div>
        </div>

        <!-- Lessons Section -->
        <div class="lessons-section">
            <div class="section-header">
                <div class="section-title">
                    <i class="bi bi-list-check"></i>
                    Danh sách bài học
                    <span class="lesson-count">{{ $lessons->count() }} bài</span>
                </div>
                <a href="" class="btn-add-lesson">
                    <i class="bi bi-plus-circle"></i> Thêm bài học
                </a>
            </div>

            @if($lessons->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">📖</div>
                    <h4>Chưa có bài học nào</h4>
                    <p>Bắt đầu thêm bài học đầu tiên cho khoá học này</p>
                    <a href="{{ route('teacher.courses.lessons.create', $course) }}" class="btn-add-lesson">
                        <i class="bi bi-plus-circle"></i> Thêm bài học
                    </a>
                </div>
            @else
                <div class="lessons-list">
                    @foreach($lessons as $lesson)
                        <div class="lesson-item">
                            <div class="lesson-order">
                                <span class="order-badge">{{ $lesson->order_number }}</span>
                            </div>
                            
                            <div class="lesson-content">
                                <div class="lesson-header">
                                    <h5 class="lesson-title">{{ $lesson->title }}</h5>
                                    <div class="lesson-actions">
                                        <a href="" class="btn-icon" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="" class="btn-icon" title="Xem">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form method="POST" action="" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon delete" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa bài học này?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                @if($lesson->description)
                                    <p class="lesson-description">{{ Str::limit($lesson->description, 100) }}</p>
                                @endif

                                <div class="lesson-meta">
                                    @if($lesson->video_url)
                                        <span class="meta-tag">
                                            <i class="bi bi-play-circle"></i> Video
                                        </span>
                                    @endif
                                    @if($lesson->duration)
                                        <span class="meta-tag">
                                            <i class="bi bi-clock"></i> {{ $lesson->duration }} phút
                                        </span>
                                    @endif
                                    <span class="meta-tag">
                                        <i class="bi bi-file-text"></i> Tài liệu
                                    </span>
                                    @if($lesson->quiz)
                                        <a href="{{ route('teacher.quizzes.show', $lesson->quiz) }}" class="meta-tag quiz">
                                            <i class="bi bi-pencil-square"></i> Quiz: {{ $lesson->quiz->title }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="lesson-status">
                                @if($lesson->is_published)
                                    <span class="status-dot published" title="Đã xuất bản"></span>
                                @else
                                    <span class="status-dot draft" title="Bản nháp"></span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h4><i class="bi bi-lightning"></i> Truy cập nhanh</h4>
            <div class="actions-grid">
                <a href="" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, #4776E6, #8E54E9)">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div class="action-info">
                        <h5>Quản lý Quiz</h5>
                        <p>Tạo và quản lý các bài kiểm tra</p>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, #FF6B6B, #FF8E53)">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="action-info">
                        <h5>Quản lý học viên</h5>
                        <p>Xem danh sách và tiến độ học viên</p>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, #4ECDC4, #556270)">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <div class="action-info">
                        <h5>Thống kê</h5>
                        <p>Xem báo cáo và phân tích</p>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, #FFD93D, #FF8C42)">
                        <i class="bi bi-gear"></i>
                    </div>
                    <div class="action-info">
                        <h5>Cài đặt</h5>
                        <p>Cấu hình khoá học</p>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>

    <style>
        /* Breadcrumb */
        .breadcrumb {
            color: #6c757d;
            font-size: 14px;
        }
        
        .breadcrumb a {
            color: #4776E6;
            transition: color 0.3s ease;
        }
        
        .breadcrumb a:hover {
            color: #8E54E9;
        }

        /* Detail Container */
        .detail-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 30px;
            border: 1px solid rgba(0,0,0,0.05);
            animation: slideUp 0.5s ease;
        }

        /* Header */
        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f2f5;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .course-icon-large {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #4776E6, #8E54E9);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
            box-shadow: 0 10px 20px rgba(71, 118, 230, 0.3);
        }

        .course-title-main {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 500;
            color: white;
        }

        .status-badge.published {
            background: #10b981;
        }

        .status-badge.draft {
            background: #6c757d;
        }

        .course-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #6c757d;
            font-size: 14px;
        }

        .meta-item i {
            color: #4776E6;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-edit, .btn-delete, .btn-back {
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-edit {
            background: linear-gradient(135deg, #4776E6, #8E54E9);
            color: white;
            box-shadow: 0 4px 15px rgba(71, 118, 230, 0.3);
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(71, 118, 230, 0.4);
            color: white;
        }

        .btn-delete {
            background: #fee;
            color: #dc3545;
            border: 1px solid #ffebee;
        }

        .btn-delete:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-2px);
        }

        .btn-back {
            background: white;
            border: 2px solid #e0e0e0;
            color: #2c3e50;
        }

        .btn-back:hover {
            border-color: #4776E6;
            color: #4776E6;
        }

        /* Description Section */
        .description-section {
            background: #f8fafd;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .description-section h4 {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .description-section h4 i {
            color: #4776E6;
        }

        .description-content {
            color: #495057;
            line-height: 1.6;
            font-size: 15px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: #f8fafd;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .stat-info {
            flex: 1;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 13px;
            color: #6c757d;
        }

        /* Lessons Section */
        .lessons-section {
            margin-top: 30px;
            margin-bottom: 40px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
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

        .lesson-count {
            background: #e9ecef;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: normal;
            color: #495057;
        }

        .btn-add-lesson {
            background: linear-gradient(135deg, #4776E6, #8E54E9);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(71, 118, 230, 0.3);
        }

        .btn-add-lesson:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(71, 118, 230, 0.4);
            color: white;
        }

        /* Lessons List */
        .lessons-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .lesson-item {
            background: #f8fafd;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }

        .lesson-item:hover {
            border-color: #4776E6;
            box-shadow: 0 10px 30px rgba(71, 118, 230, 0.1);
        }

        .lesson-order {
            min-width: 50px;
        }

        .order-badge {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4776E6, #8E54E9);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            box-shadow: 0 5px 10px rgba(71, 118, 230, 0.3);
        }

        .lesson-content {
            flex: 1;
        }

        .lesson-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .lesson-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .lesson-actions {
            display: flex;
            gap: 5px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #e0e0e0;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-icon:hover {
            background: #4776E6;
            color: white;
            border-color: #4776E6;
        }

        .btn-icon.delete:hover {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .lesson-description {
            color: #6c757d;
            font-size: 14px;
            margin: 5px 0 10px 0;
        }

        .lesson-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .meta-tag {
            padding: 4px 10px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 30px;
            font-size: 12px;
            color: #495057;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }

        .meta-tag i {
            color: #4776E6;
        }

        .meta-tag.quiz {
            background: #e3f2fd;
            border-color: #90caf9;
            color: #1976d2;
        }

        .meta-tag.quiz i {
            color: #1976d2;
        }

        .lesson-status {
            min-width: 30px;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-dot.published {
            background: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .status-dot.draft {
            background: #6c757d;
            box-shadow: 0 0 0 3px rgba(108, 117, 125, 0.2);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: #f8fafd;
            border-radius: 16px;
        }

        .empty-icon {
            font-size: 60px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h4 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .empty-state p {
            color: #6c757d;
            margin-bottom: 25px;
        }

        /* Quick Actions */
        .quick-actions {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #f0f2f5;
        }

        .quick-actions h4 {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .quick-actions h4 i {
            color: #4776E6;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .action-card {
            background: #f8fafd;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .action-card:hover {
            border-color: #4776E6;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(71, 118, 230, 0.1);
        }

        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .action-info {
            flex: 1;
        }

        .action-info h5 {
            font-size: 15px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 3px 0;
        }

        .action-info p {
            font-size: 12px;
            color: #6c757d;
            margin: 0;
        }

        .action-card i.bi-chevron-right {
            color: #4776E6;
            font-size: 18px;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .action-card:hover i.bi-chevron-right {
            opacity: 1;
            transform: translateX(5px);
        }

        /* Animation */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .actions-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .detail-container {
                padding: 20px;
            }

            .detail-header {
                flex-direction: column;
            }

            .header-left {
                width: 100%;
            }

            .course-title-main {
                font-size: 24px;
            }

            .header-actions {
                width: 100%;
            }

            .btn-edit, .btn-delete, .btn-back {
                flex: 1;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .lesson-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .lesson-order {
                align-self: flex-start;
            }

            .lesson-status {
                position: absolute;
                top: 15px;
                right: 15px;
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection