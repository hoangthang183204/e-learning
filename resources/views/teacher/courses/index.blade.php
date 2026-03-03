{{-- resources/views/teacher/courses/index.blade.php --}}
@extends('teacher.layout')

@section('title', 'Khoá học của tôi')

@section('courses-active', 'active')
@section('page-icon', 'book')
@section('page-title', 'Quản lý Khoá học')

@section('content')
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Danh sách khoá học</h4>
        <a href="/teacher.courses.create" class="create-btn">
            <i class="bi bi-plus-circle"></i> Tạo khoá học mới
        </a>
    </div>

    <!-- Courses Grid -->
    <div class="courses-grid">
        @forelse($courses as $course)
            <div class="course-card" style="animation-delay: {{ $loop->index * 0.1 }}s">
                <!-- Card Header với gradient ngẫu nhiên -->
                <div class="card-header"
                    style="background: linear-gradient(135deg, {{ $course->color_1 ?? '#4776E6' }}, {{ $course->color_2 ?? '#8E54E9' }})">
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
                    @if ($course->status === 'published')
                        <span class="status-badge published">Đã xuất bản</span>
                    @else
                        <span class="status-badge draft">Bản nháp</span>
                    @endif
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <h3 class="course-title">{{ $course->title }}</h3>

                    @if ($course->description)
                        <p class="course-description">{{ Str::limit($course->description, 100) }}</p>
                    @endif

                    <!-- Course Stats -->
                    <div class="course-stats">
                        <div class="stat-item">
                            <i class="bi bi-collection"></i>
                            <span>{{ $course->lessons_count ?? 0 }} bài học</span>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-people"></i>
                            <a href="{{ route('teacher.courses.students', $course->id) }}">
                                Học viên
                            </a>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-clock"></i>
                            <span>{{ $course->total_duration ?? 0 }} phút</span>
                        </div>
                    </div>

                    <!-- Progress Bar (nếu có) -->
                    @if ($course->completion_rate)
                        <div class="progress-section">
                            <div class="progress-label">
                                <span>Tiến độ hoàn thành</span>
                                <span>{{ $course->completion_rate }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $course->completion_rate }}%"></div>
                            </div>
                        </div>
                    @endif

                    <!-- Tags/Categories -->
                    @if ($course->category)
                        <div class="course-tags">
                            <span class="tag">
                                <i class="bi bi-tag"></i> {{ $course->category }}
                            </span>
                            @if ($course->level)
                                <span class="tag">
                                    <i class="bi bi-bar-chart"></i>
                                    @switch($course->level)
                                        @case('beginner')
                                            Cơ bản
                                        @break

                                        @case('intermediate')
                                            Trung cấp
                                        @break

                                        @case('advanced')
                                            Nâng cao
                                        @break

                                        @default
                                            {{ $course->level }}
                                    @endswitch
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Card Footer với actions -->
                <div class="card-footer">
                    <a href="{{ route('teacher.courses.show', $course) }}" class="btn-manage">
                        <i class="bi bi-gear"></i> Quản lý
                    </a>
                    <a href="{{ route('teacher.courses.edit', $course) }}" class="btn-edit" title="Chỉnh sửa">
                        <i class="bi bi-pencil"></i>
                    </a>


                </div>
            </div>
            @empty
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-icon">📚</div>
                    <h3>Chưa có khoá học nào</h3>
                    <p>Bắt đầu tạo khoá học đầu tiên để chia sẻ kiến thức với học viên</p>
                    <a href="{{ route('teacher.courses.create') }}" class="create-btn">
                        <i class="bi bi-plus-circle"></i> Tạo khoá học ngay
                    </a>
                </div>
            @endforelse
        </div>



        <!-- Phân trang -->
        @if (method_exists($courses, 'links'))
            <div class="mt-4">
                {{ $courses->links() }}
            </div>
        @endif

        <style>
            /* Create Button */
            .create-btn {
                background: linear-gradient(135deg, #4776E6 0%, #8E54E9 100%);
                color: white;
                text-decoration: none;
                padding: 10px 20px;
                border-radius: 50px;
                font-weight: 500;
                font-size: 14px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(71, 118, 230, 0.3);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .create-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(71, 118, 230, 0.4);
                background: linear-gradient(135deg, #3f67d1 0%, #7d4ad4 100%);
                color: white;
            }

            /* Courses Grid */
            .courses-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                gap: 25px;
            }

            /* Course Card */
            .course-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
                border: 1px solid rgba(0, 0, 0, 0.05);
                overflow: hidden;
                position: relative;
                animation: fadeInUp 0.5s ease forwards;
                opacity: 0;
                display: flex;
                flex-direction: column;
            }

            .course-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
            }

            /* Card Header */
            .card-header {
                height: 120px;
                position: relative;
                padding: 20px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
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

            .status-badge {
                padding: 5px 12px;
                border-radius: 30px;
                font-size: 12px;
                font-weight: 500;
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(5px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                color: white;
            }

            .status-badge.published {
                background: rgba(16, 185, 129, 0.9);
            }

            .status-badge.draft {
                background: rgba(108, 117, 125, 0.9);
            }

            /* Card Body */
            .card-body {
                padding: 25px;
                flex: 1;
            }

            .course-title {
                font-size: 20px;
                font-weight: 700;
                color: #2c3e50;
                margin: 0 0 10px 0;
                line-height: 1.4;
            }

            .course-description {
                color: #6c757d;
                font-size: 14px;
                line-height: 1.6;
                margin-bottom: 20px;
            }

            /* Course Stats */
            .course-stats {
                display: flex;
                gap: 20px;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #e9ecef;
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
                font-size: 16px;
            }

            /* Progress Bar */
            .progress-section {
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

            /* Tags */
            .course-tags {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .tag {
                padding: 4px 10px;
                background: #f8f9fa;
                border: 1px solid #e0e0e0;
                border-radius: 30px;
                font-size: 12px;
                color: #495057;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .tag i {
                color: #4776E6;
                font-size: 12px;
            }

            /* Card Footer */
            .card-footer {
                padding: 15px 25px 25px;
                display: flex;
                gap: 10px;
                border-top: 1px solid #e9ecef;
                background: #f8fafd;
            }

            .btn-manage {
                background: linear-gradient(135deg, #4776E6, #8E54E9);
                color: white;
                text-decoration: none;
                padding: 8px 16px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 500;
                display: inline-flex;
                align-items: center;
                gap: 5px;
                flex: 1;
                justify-content: center;
                transition: all 0.3s ease;
                box-shadow: 0 4px 10px rgba(71, 118, 230, 0.2);
            }

            .btn-manage:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(71, 118, 230, 0.3);
                color: white;
            }

            .btn-edit,
            .btn-lessons,
            .btn-delete {
                width: 38px;
                height: 38px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
            }

            .btn-edit {
                background: #e3f2fd;
                color: #1976d2;
            }

            .btn-edit:hover {
                background: #1976d2;
                color: white;
            }

            .btn-lessons {
                background: #e8f5e9;
                color: #2e7d32;
            }

            .btn-lessons:hover {
                background: #2e7d32;
                color: white;
            }

            .btn-delete {
                background: #ffebee;
                color: #c62828;
            }

            .btn-delete:hover {
                background: #c62828;
                color: white;
            }

            /* Empty State */
            .empty-state {
                text-align: center;
                padding: 60px 20px;
                background: white;
                border-radius: 20px;
                grid-column: 1 / -1;
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .empty-icon {
                font-size: 80px;
                margin-bottom: 20px;
                opacity: 0.5;
            }

            .empty-state h3 {
                color: #2c3e50;
                margin-bottom: 10px;
                font-size: 24px;
            }

            .empty-state p {
                color: #6c757d;
                margin-bottom: 25px;
            }

            .empty-state .create-btn {
                display: inline-flex;
            }

            /* Animation */
            @keyframes fadeInUp {
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
            @media (max-width: 768px) {
                .courses-grid {
                    grid-template-columns: 1fr;
                }

                .course-stats {
                    flex-wrap: wrap;
                    gap: 10px;
                }

                .card-footer {
                    flex-wrap: wrap;
                }

                .btn-edit,
                .btn-lessons,
                .btn-delete {
                    width: 100%;
                    flex: 1;
                }

                .btn-manage {
                    width: 100%;
                    margin-bottom: 5px;
                }
            }
        </style>

        <script>
            // Animation khi hover
            document.querySelectorAll('.course-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transition = 'all 0.3s ease';
                });
            });
        </script>
    @endsection
