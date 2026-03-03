{{-- resources/views/teacher/courses/progress.blade.php --}}
@extends('teacher.layout')

@section('title', 'Tiến độ học - ' . $course->title)

@section('courses-active', 'active')
@section('page-icon', 'graph-up')
@section('page-title', 'Tiến độ học viên')

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb mb-4">
        <a href="{{ route('teacher.courses.index') }}" class="text-decoration-none">Khoá học</a>
        <span class="mx-2">/</span>
        <a href="{{ route('teacher.courses.show', $course) }}" class="text-decoration-none">{{ $course->title }}</a>
        <span class="mx-2">/</span>
        <span class="text-muted">Tiến độ học</span>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4776E6, #8E54E9)">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-info">
                {{-- <div class="stat-value">{{ $students->total() ?? $students->count() }}</div> --}}
                <div class="stat-label">Tổng học viên</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669)">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalLessons }}</div>
                <div class="stat-label">Tổng bài học</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706)">
                <i class="bi bi-trophy"></i>
            </div>
            <div class="stat-info">
                @php
                    $avgProgress = $students->avg(function($student) use ($totalLessons) {
                        $completed = $student->completedLessons->count();
                        return $totalLessons > 0 ? ($completed / $totalLessons) * 100 : 0;
                    });
                @endphp
                <div class="stat-value">{{ round($avgProgress) }}%</div>
                <div class="stat-label">Tiến độ TB</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626)">
                <i class="bi bi-flag"></i>
            </div>
            <div class="stat-info">
                @php
                    $completedCount = $students->filter(function($student) use ($totalLessons) {
                        return $student->completedLessons->count() >= $totalLessons;
                    })->count();
                @endphp
                <div class="stat-value">{{ $completedCount }}</div>
                <div class="stat-label">Hoàn thành</div>
            </div>
        </div>
    </div>

    <!-- Progress Container -->
    <div class="progress-container">
        <!-- Header -->
        <div class="progress-header">
            <div class="header-left">
                <h3 class="course-title">
                    <i class="bi bi-graph-up"></i>
                    Tiến độ học tập
                </h3>
                <p class="course-subtitle">
                    Khoá học: <span>{{ $course->title }}</span> • 
                    <span class="text-muted">{{ $totalLessons }} bài học</span>
                </p>
            </div>
            <div class="header-actions">
                <a href="" class="btn-export">
                    <i class="bi bi-download"></i> Xuất báo cáo
                </a>
                <button class="btn-refresh" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Làm mới
                </button>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="search-section">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" 
                       id="searchInput" 
                       placeholder="Tìm kiếm theo tên học viên..." 
                       class="search-input">
            </div>
            <div class="filter-options">
                <select class="filter-select" id="progressFilter">
                    <option value="all">Tất cả tiến độ</option>
                    <option value="0-25">0% - 25%</option>
                    <option value="25-50">25% - 50%</option>
                    <option value="50-75">50% - 75%</option>
                    <option value="75-99">75% - 99%</option>
                    <option value="100">Hoàn thành (100%)</option>
                </select>
                <select class="filter-select" id="sortFilter">
                    <option value="progress_desc">Tiến độ cao → thấp</option>
                    <option value="progress_asc">Tiến độ thấp → cao</option>
                    <option value="name_asc">Tên A-Z</option>
                    <option value="name_desc">Tên Z-A</option>
                </select>
            </div>
        </div>

        <!-- Progress Table -->
        <div class="table-responsive">
            <table class="progress-table">
                <thead>
                    <tr>
                        <th>Học viên</th>
                        <th>Email</th>
                        <th>Bài đã học</th>
                        <th>Tiến độ</th>
                        <th>Trạng thái</th>
                        <th width="120">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="progressTableBody">
                    @forelse($students as $student)
                        @php
                            $completed = $student->completedLessons->count();
                            $percent = $totalLessons > 0 ? round(($completed / $totalLessons) * 100) : 0;
                            
                            if ($percent >= 100) {
                                $status = 'completed';
                                $statusText = 'Hoàn thành';
                            } elseif ($percent >= 50) {
                                $status = 'good';
                                $statusText = 'Tốt';
                            } elseif ($percent >= 25) {
                                $status = 'medium';
                                $statusText = 'Trung bình';
                            } elseif ($percent > 0) {
                                $status = 'low';
                                $statusText = 'Mới bắt đầu';
                            } else {
                                $status = 'not-started';
                                $statusText = 'Chưa học';
                            }
                            
                            $progressColor = $percent >= 100 ? '#10b981' : 
                                            ($percent >= 50 ? '#f59e0b' : 
                                            ($percent > 0 ? '#4776E6' : '#e0e0e0'));
                        @endphp
                        
                        <tr class="progress-row" data-progress="{{ $percent }}" data-name="{{ $student->name }}">
                            <td>
                                <div class="student-info">
                                    <div class="student-avatar">
                                        @if($student->avatar)
                                            <img src="{{ $student->avatar }}" alt="{{ $student->name }}">
                                        @else
                                            <div class="avatar-placeholder">
                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="student-name">
                                        <strong>{{ $student->name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="student-email">
                                    <i class="bi bi-envelope"></i>
                                    {{ $student->email }}
                                </div>
                            </td>
                            <td>
                                <div class="lesson-count">
                                    <span class="count-number">{{ $completed }}</span>
                                    <span class="count-total">/ {{ $totalLessons }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="progress-cell">
                                    <div class="progress-circle" style="--progress: {{ $percent }}%; --color: {{ $progressColor }}">
                                        <span>{{ $percent }}%</span>
                                    </div>
                                    <div class="progress-bar-mini">
                                        <div class="progress-fill" style="width: {{ $percent }}%; background: {{ $progressColor }}"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $status }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="" class="btn-icon" title="Chi tiết tiến độ">
                                        <i class="bi bi-graph-up"></i>
                                    </a>
                                    <a href="" 
                                       class="btn-icon delete" 
                                       title="Xóa khỏi khoá học"
                                       onclick="event.preventDefault(); if(confirm('Bạn có chắc muốn xóa học viên này?')) document.getElementById('remove-student-{{ $student->id }}').submit();">
                                        <i class="bi bi-person-x"></i>
                                    </a>
                                    <form id="remove-student-{{ $student->id }}" 
                                          method="POST" 
                                          action="" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <div class="empty-icon">📊</div>
                                <h4>Chưa có dữ liệu tiến độ</h4>
                                <p>Khoá học này chưa có học viên nào tham gia.</p>
                                <a href="{{ route('teacher.courses.students.add', $course) }}" class="btn-add">
                                    <i class="bi bi-person-plus"></i> Thêm học viên
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Progress Summary -->
        @if($students->isNotEmpty())
            <div class="progress-summary">
                <h5><i class="bi bi-pie-chart"></i> Tổng quan tiến độ</h5>
                <div class="summary-stats">
                    @php
                        $ranges = [
                            '0-25' => $students->filter(fn($s) => ($s->completedLessons->count() / $totalLessons) * 100 < 25)->count(),
                            '25-50' => $students->filter(fn($s) => ($s->completedLessons->count() / $totalLessons) * 100 >= 25 && ($s->completedLessons->count() / $totalLessons) * 100 < 50)->count(),
                            '50-75' => $students->filter(fn($s) => ($s->completedLessons->count() / $totalLessons) * 100 >= 50 && ($s->completedLessons->count() / $totalLessons) * 100 < 75)->count(),
                            '75-99' => $students->filter(fn($s) => ($s->completedLessons->count() / $totalLessons) * 100 >= 75 && ($s->completedLessons->count() / $totalLessons) * 100 < 100)->count(),
                            '100' => $students->filter(fn($s) => ($s->completedLessons->count() / $totalLessons) * 100 >= 100)->count(),
                        ];
                    @endphp
                    
                    <div class="summary-item">
                        <span class="summary-label">0% - 25%</span>
                        <div class="summary-bar">
                            <div class="summary-fill" style="width: {{ ($ranges['0-25'] / $students->count()) * 100 }}%; background: #e0e0e0"></div>
                        </div>
                        <span class="summary-value">{{ $ranges['0-25'] }} học viên</span>
                    </div>
                    
                    <div class="summary-item">
                        <span class="summary-label">25% - 50%</span>
                        <div class="summary-bar">
                            <div class="summary-fill" style="width: {{ ($ranges['25-50'] / $students->count()) * 100 }}%; background: #4776E6"></div>
                        </div>
                        <span class="summary-value">{{ $ranges['25-50'] }} học viên</span>
                    </div>
                    
                    <div class="summary-item">
                        <span class="summary-label">50% - 75%</span>
                        <div class="summary-bar">
                            <div class="summary-fill" style="width: {{ ($ranges['50-75'] / $students->count()) * 100 }}%; background: #f59e0b"></div>
                        </div>
                        <span class="summary-value">{{ $ranges['50-75'] }} học viên</span>
                    </div>
                    
                    <div class="summary-item">
                        <span class="summary-label">75% - 99%</span>
                        <div class="summary-bar">
                            <div class="summary-fill" style="width: {{ ($ranges['75-99'] / $students->count()) * 100 }}%; background: #f97316"></div>
                        </div>
                        <span class="summary-value">{{ $ranges['75-99'] }} học viên</span>
                    </div>
                    
                    <div class="summary-item">
                        <span class="summary-label">100%</span>
                        <div class="summary-bar">
                            <div class="summary-fill" style="width: {{ ($ranges['100'] / $students->count()) * 100 }}%; background: #10b981"></div>
                        </div>
                        <span class="summary-value">{{ $ranges['100'] }} học viên</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pagination -->
        @if(method_exists($students, 'links'))
            <div class="pagination-wrapper">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    <style>
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
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

        /* Progress Container */
        .progress-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 30px;
            border: 1px solid rgba(0,0,0,0.05);
            animation: slideUp 0.5s ease;
        }

        /* Header */
        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f2f5;
            flex-wrap: wrap;
            gap: 20px;
        }

        .course-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 5px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .course-title i {
            color: #4776E6;
        }

        .course-subtitle {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
        }

        .course-subtitle span {
            color: #4776E6;
            font-weight: 500;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn-export {
            background: white;
            border: 2px solid #e0e0e0;
            color: #2c3e50;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-export:hover {
            border-color: #4776E6;
            color: #4776E6;
            background: #f8fafd;
        }

        .btn-refresh {
            background: white;
            border: 2px solid #e0e0e0;
            color: #2c3e50;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-refresh:hover {
            border-color: #4776E6;
            color: #4776E6;
            background: #f8fafd;
        }

        /* Search Section */
        .search-section {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 18px;
        }

        .search-input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #4776E6;
            box-shadow: 0 0 0 4px rgba(71, 118, 230, 0.1);
            outline: none;
        }

        .filter-options {
            display: flex;
            gap: 10px;
        }

        .filter-select {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 14px;
            color: #2c3e50;
            background: white;
            cursor: pointer;
            min-width: 160px;
        }

        .filter-select:focus {
            border-color: #4776E6;
            outline: none;
        }

        /* Table */
        .table-responsive {
            overflow-x: auto;
            margin-bottom: 30px;
        }

        .progress-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .progress-table th {
            text-align: left;
            padding: 15px;
            background: #f8fafd;
            color: #2c3e50;
            font-weight: 600;
            border-bottom: 2px solid #e0e0e0;
            white-space: nowrap;
        }

        .progress-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .progress-table tbody tr:hover {
            background: #f8fafd;
        }

        /* Student Info */
        .student-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            overflow: hidden;
            background: linear-gradient(135deg, #4776E6, #8E54E9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }

        .student-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            font-size: 18px;
        }

        .student-name {
            color: #2c3e50;
        }

        .student-name strong {
            font-weight: 600;
        }

        /* Email */
        .student-email {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
        }

        .student-email i {
            color: #4776E6;
            font-size: 14px;
        }

        /* Lesson Count */
        .lesson-count {
            font-weight: 500;
        }

        .count-number {
            font-size: 18px;
            font-weight: 700;
            color: #4776E6;
        }

        .count-total {
            color: #6c757d;
            font-size: 14px;
        }

        /* Progress Cell */
        .progress-cell {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .progress-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: conic-gradient(var(--color) var(--progress), #e0e0e0 0deg);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .progress-circle::before {
            content: '';
            position: absolute;
            width: 35px;
            height: 35px;
            background: white;
            border-radius: 50%;
        }

        .progress-circle span {
            position: relative;
            font-size: 11px;
            font-weight: 600;
            color: #2c3e50;
            z-index: 1;
        }

        .progress-bar-mini {
            flex: 1;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            display: none;
        }

        @media (max-width: 768px) {
            .progress-circle {
                display: none;
            }
            .progress-bar-mini {
                display: block;
            }
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        /* Status Badge */
        .status-badge {
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-completed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-good {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-medium {
            background: #e3f2fd;
            color: #1976d2;
        }

        .status-low {
            background: #e8eaf6;
            color: #3f51b5;
        }

        .status-not-started {
            background: #f5f5f5;
            color: #757575;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #e0e0e0;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-icon:hover {
            background: #4776E6;
            color: white;
            border-color: #4776E6;
        }

        .btn-icon.delete:hover {
            background: #dc3545;
            border-color: #dc3545;
        }

        /* Progress Summary */
        .progress-summary {
            background: #f8fafd;
            border-radius: 16px;
            padding: 25px;
            margin-top: 30px;
        }

        .progress-summary h5 {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .progress-summary h5 i {
            color: #4776E6;
        }

        .summary-stats {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .summary-label {
            min-width: 80px;
            font-size: 14px;
            color: #6c757d;
        }

        .summary-bar {
            flex: 1;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .summary-fill {
            height: 100%;
            border-radius: 4px;
        }

        .summary-value {
            min-width: 100px;
            font-size: 14px;
            color: #2c3e50;
            text-align: right;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
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

        .btn-add {
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
            border: none;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(71, 118, 230, 0.4);
            color: white;
        }

        /* Pagination */
        .pagination-wrapper {
            margin-top: 30px;
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
        }

        @media (max-width: 768px) {
            .progress-container {
                padding: 20px;
            }

            .progress-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
            }

            .btn-export, .btn-refresh {
                flex: 1;
                justify-content: center;
            }

            .search-section {
                flex-direction: column;
            }

            .filter-options {
                flex-direction: column;
            }

            .filter-select {
                width: 100%;
            }

            .summary-item {
                flex-wrap: wrap;
            }

            .summary-label {
                width: 100%;
            }

            .summary-value {
                width: 100%;
                text-align: left;
            }
        }
    </style>

    <script>
        // Search functionality
        document.getElementById('searchInput')?.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.progress-row');
            
            rows.forEach(row => {
                const name = row.dataset.name?.toLowerCase() || '';
                row.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        });

        // Filter by progress range
        document.getElementById('progressFilter')?.addEventListener('change', function() {
            const filter = this.value;
            const rows = document.querySelectorAll('.progress-row');
            
            rows.forEach(row => {
                const progress = parseInt(row.dataset.progress);
                
                if (filter === 'all') {
                    row.style.display = '';
                } else if (filter === '100') {
                    row.style.display = progress >= 100 ? '' : 'none';
                } else {
                    const [min, max] = filter.split('-').map(Number);
                    row.style.display = progress >= min && progress < max ? '' : 'none';
                }
            });
        });

        // Sort functionality
        document.getElementById('sortFilter')?.addEventListener('change', function() {
            const sortBy = this.value;
            const tbody = document.getElementById('progressTableBody');
            const rows = Array.from(tbody.querySelectorAll('.progress-row'));
            
            rows.sort((a, b) => {
                switch(sortBy) {
                    case 'progress_desc':
                        return parseInt(b.dataset.progress) - parseInt(a.dataset.progress);
                    case 'progress_asc':
                        return parseInt(a.dataset.progress) - parseInt(b.dataset.progress);
                    case 'name_asc':
                        return a.dataset.name.localeCompare(b.dataset.name);
                    case 'name_desc':
                        return b.dataset.name.localeCompare(a.dataset.name);
                    default:
                        return 0;
                }
            });
            
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
        });
    </script>
@endsection