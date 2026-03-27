@extends('teacher.layout')

@section('title', 'Dashboard - ' . $course->title)

@section('content')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #10b981, #059669);
            --warning-gradient: linear-gradient(135deg, #f59e0b, #d97706);
            --info-gradient: linear-gradient(135deg, #3b82f6, #2563eb);
            --card-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        /* Course Header */
        .course-header {
            background: var(--primary-gradient);
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 30px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .course-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1%, transparent 1%);
            background-size: 50px 50px;
            animation: moveBackground 20s linear infinite;
        }

        @keyframes moveBackground {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(50px, 50px);
            }
        }

        .course-header-content {
            position: relative;
            z-index: 1;
        }

        .course-header h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .course-header h2 i {
            font-size: 36px;
        }

        .course-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            opacity: 0.9;
        }

        .course-breadcrumb a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .course-breadcrumb a:hover {
            opacity: 0.8;
        }

        .course-breadcrumb i {
            font-size: 12px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .stat-card.students::before {
            background: var(--info-gradient);
        }

        .stat-card.pass-rate::before {
            background: var(--success-gradient);
        }

        .stat-card.score::before {
            background: var(--warning-gradient);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-icon.students {
            background: var(--info-gradient);
        }

        .stat-icon.pass-rate {
            background: var(--success-gradient);
        }

        .stat-icon.score {
            background: var(--warning-gradient);
        }

        .stat-value {
            font-size: 36px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            padding: 4px 10px;
            background: #f1f5f9;
            border-radius: 20px;
            width: fit-content;
        }

        .stat-trend i {
            font-size: 12px;
        }

        .stat-trend.up {
            color: #10b981;
        }

        .stat-trend.down {
            color: #ef4444;
        }

        /* Progress Section */
        .progress-section {
            background: white;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #667eea;
            font-size: 20px;
        }

        .progress-bars {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .progress-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .progress-label {
            width: 120px;
            font-size: 14px;
            font-weight: 500;
            color: #475569;
        }

        .progress-bar-container {
            flex: 1;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary-gradient);
            border-radius: 10px;
            transition: width 1s ease;
            position: relative;
            overflow: hidden;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .progress-percent {
            width: 50px;
            text-align: right;
            font-size: 14px;
            font-weight: 600;
            color: #667eea;
        }

        /* Top Students Table */
        .top-students-section {
            background: white;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
        }

        .students-table {
            width: 100%;
            overflow-x: auto;
        }

        .students-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .students-table th {
            text-align: left;
            padding: 16px 12px;
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 2px solid #e2e8f0;
        }

        .students-table td {
            padding: 16px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        .students-table tr:hover {
            background: #f8fafc;
            transition: background 0.3s;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 14px;
        }

        .rank-1 {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: white;
        }

        .rank-2 {
            background: linear-gradient(135deg, #C0C0C0, #A0A0A0);
            color: white;
        }

        .rank-3 {
            background: linear-gradient(135deg, #CD7F32, #B87333);
            color: white;
        }

        .rank-other {
            background: #e2e8f0;
            color: #475569;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        .student-details {
            flex: 1;
        }

        .student-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .student-email {
            font-size: 12px;
            color: #64748b;
        }

        .score-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            color: #d97706;
        }

        .score-badge i {
            font-size: 14px;
        }

        .score-excellent {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #059669;
        }

        .score-good {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #2563eb;
        }

        .score-average {
            background: linear-gradient(135deg, #fed7aa, #ffedd5);
            color: #ea580c;
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 16px;
            margin-top: 20px;
        }

        .btn-action {
            flex: 1;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .course-header {
                padding: 24px;
            }

            .course-header h2 {
                font-size: 24px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .progress-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .progress-label {
                width: auto;
            }

            .quick-actions {
                flex-direction: column;
            }

            .students-table {
                overflow-x: auto;
            }

            .students-table table {
                min-width: 600px;
            }
        }

        /* Loading Animation */
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

        .stat-card,
        .progress-section,
        .top-students-section {
            animation: fadeInUp 0.5s ease forwards;
        }

        .stat-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .stat-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .stat-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .progress-section {
            animation-delay: 0.4s;
        }

        .top-students-section {
            animation-delay: 0.5s;
        }
    </style>

    <div class="course-header">
        <div class="course-header-content">
            <div class="course-breadcrumb">
                <a href="{{ route('teacher.dashboard') }}">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
                <i class="bi bi-chevron-right"></i>
                <span>{{ $course->title }}</span>
            </div>
            <h2>
                <i class="bi bi-trophy"></i>
                {{ $course->title }}
            </h2>
            <p class="mb-0">{{ Str::limit($course->description ?? 'Không có mô tả', 150) }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card students">
            <div class="stat-header">
                <div class="stat-icon students">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-trend up">
                    <i class="bi bi-arrow-up-short"></i>
                    12% so với tháng trước
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalStudents) }}</div>
            <div class="stat-label">Học viên đăng ký</div>
        </div>

        <div class="stat-card pass-rate">
            <div class="stat-header">
                <div class="stat-icon pass-rate">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-trend up">
                    <i class="bi bi-arrow-up-short"></i>
                    5% so với tháng trước
                </div>
            </div>
            <div class="stat-value">{{ $passRate ?? 0 }}%</div>
            <div class="stat-label">Tỷ lệ vượt qua (Pass Rate)</div>
        </div>

        <div class="stat-card score">
            <div class="stat-header">
                <div class="stat-icon score">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div class="stat-trend up">
                    <i class="bi bi-arrow-up-short"></i>
                    3% so với tháng trước
                </div>
            </div>
            <div class="stat-value">{{ round($averageScore ?? 0, 1) }}</div>
            <div class="stat-label">Điểm trung bình / 10</div>
        </div>
    </div>

    <!-- Progress Overview -->
    <div class="progress-section">
        <div class="section-header">
            <div class="section-title">
                <i class="bi bi-graph-up"></i>
                Tổng quan tiến độ
            </div>
            <span class="badge bg-light text-dark">
                <i class="bi bi-calendar3"></i> Cập nhật hôm nay
            </span>
        </div>
        <div class="progress-bars">
            <div class="progress-item">
                <div class="progress-label">
                    <i class="bi bi-journal-bookmark-fill me-2"></i>
                    Hoàn thành khóa học
                </div>
                <div class="progress-bar-container">
                    <div class="progress-fill" style="width: {{ $passRate ?? 0 }}%"></div>
                </div>
                <div class="progress-percent">{{ $passRate ?? 0 }}%</div>
            </div>
            <div class="progress-item">
                <div class="progress-label">
                    <i class="bi bi-pencil-square me-2"></i>
                    Điểm trung bình
                </div>
                <div class="progress-bar-container">
                    <div class="progress-fill" style="width: {{ ($averageScore ?? 0) * 10 }}%"></div>
                </div>
                <div class="progress-percent">{{ round($averageScore ?? 0, 1) }}/10</div>
            </div>
            <div class="progress-item">
                <div class="progress-label">
                    <i class="bi bi-people me-2"></i>
                    Học viên tích cực
                </div>
                <div class="progress-bar-container">
                    <div class="progress-fill" style="width: 78%"></div>
                </div>
                <div class="progress-percent">78%</div>
            </div>
        </div>
    </div>

    <!-- Top Students -->
    <div class="top-students-section">
        <div class="section-header">
            <div class="section-title">
                <i class="bi bi-trophy-fill"></i>
                Bảng xếp hạng học viên xuất sắc
            </div>
            <div>
                <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip"
                    title="Xếp hạng dựa trên điểm trung bình các bài quiz"></i>
            </div>
        </div>

        @if (isset($topStudents) && count($topStudents) > 0)
            <div class="students-table">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Học viên</th>
                            <th>Điểm trung bình</th>
                            <th>Thành tích</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topStudents as $index => $row)
                            @php
                                $rank = $index + 1;
                                $score = round($row->avg_score ?? 0, 2);
                                $scoreClass = '';
                                $achievement = '';

                                if ($score >= 9) {
                                    $scoreClass = 'score-excellent';
                                    $achievement = 'Xuất sắc 🏆';
                                } elseif ($score >= 8) {
                                    $scoreClass = 'score-good';
                                    $achievement = 'Giỏi ⭐';
                                } elseif ($score >= 6.5) {
                                    $scoreClass = 'score-average';
                                    $achievement = 'Khá 👍';
                                } else {
                                    $achievement = 'Cần cố gắng 💪';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div class="rank-badge rank-{{ $rank <= 3 ? $rank : 'other' }}">
                                        {{ $rank }}
                                    </div>
                                </td>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            {{ strtoupper(substr($row->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="student-details">
                                            <div class="student-name">{{ $row->user->name ?? 'Không xác định' }}</div>
                                            <div class="student-email">{{ $row->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="score-badge {{ $scoreClass }}">
                                        <i class="bi bi-star-fill"></i>
                                        {{ $score }}/10
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size: 13px;">
                                        {{ $achievement }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-emoji-frown" style="font-size: 48px; color: #cbd5e1;"></i>
                <h5 class="mt-3 text-muted">Chưa có dữ liệu học viên</h5>
                <p class="text-muted">Hãy khuyến khích học viên tham gia các bài quiz để có xếp hạng</p>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('teacher.courses.edit', $course) }}" class="btn-action btn-primary">
            <i class="bi bi-pencil-square"></i>
            Chỉnh sửa khóa học
        </a>
        <a href="{{ route('teacher.quizzes.create', ['course' => $course->id]) }}" class="btn-action btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tạo quiz mới
        </a>
        <a href="{{ route('teacher.courses.index') }}" class="btn-action btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Quay lại dashboard
        </a>
    </div>

    <script>
        // Animation cho progress bars khi load
        document.addEventListener('DOMContentLoaded', function() {
            const progressFills = document.querySelectorAll('.progress-fill');
            progressFills.forEach(fill => {
                const width = fill.style.width;
                fill.style.width = '0%';
                setTimeout(() => {
                    fill.style.width = width;
                }, 100);
            });

            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endsection
