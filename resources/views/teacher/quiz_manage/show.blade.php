{{-- resources/views/teacher/quizzes/show.blade.php --}}
@extends('teacher.layout')

@section('title', 'Chi tiết Quiz')

@section('quizzes-active', 'active')
@section('page-icon', 'eye')
@section('page-title', 'Chi tiết Quiz')

@section('content')

    <!-- Quiz Detail Container -->
    <div class="detail-container">
        <!-- Header với các action buttons -->
        <div class="detail-header">
            <div class="header-left">
                <h3 class="quiz-title-main">
                    <i class="bi bi-pencil-square"></i> 
                    {{ $quiz->title }}
                </h3>
                <div class="quiz-meta">
                    <span class="badge bg-primary">
                        <i class="bi bi-book"></i> {{ $quiz->lesson->title ?? 'Không có bài học' }}
                    </span>
                    <span class="badge bg-info">
                        <i class="bi bi-clock"></i> {{ $quiz->duration ?? 15 }} phút
                    </span>
                    <span class="badge bg-success">
                        <i class="bi bi-question-circle"></i> {{ $quiz->questions->count() }} câu hỏi
                    </span>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn-edit">
                    <i class="bi bi-pencil"></i> Chỉnh sửa
                </a>
                <form method="POST" action="{{ route('teacher.quizzes.destroy', $quiz) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa quiz này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </form>
            </div>
        </div>

        <!-- Thống kê nhanh -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4776E6, #8E54E9)">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $quiz->attempts_count ?? 0 }}</div>
                    <div class="stat-label">Lượt làm bài</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FF6B6B, #FF8E53)">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $quiz->average_score ?? 0 }}%</div>
                    <div class="stat-label">Điểm trung bình</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4ECDC4, #556270)">
                    <i class="bi bi-trophy"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $quiz->pass_rate ?? 0 }}%</div>
                    <div class="stat-label">Tỷ lệ đạt</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FFD93D, #FF8C42)">
                    <i class="bi bi-calendar"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $quiz->created_at->format('d/m/Y') }}</div>
                    <div class="stat-label">Ngày tạo</div>
                </div>
            </div>
        </div>

        <!-- Danh sách câu hỏi -->
        <div class="questions-section">
            <div class="section-title">
                <i class="bi bi-question-circle"></i>
                Danh sách câu hỏi
                <span class="question-count">{{ $quiz->questions->count() }} câu</span>
            </div>

            @forelse($quiz->questions as $index => $question)
                <div class="question-card">
                    <div class="question-header">
                        <div class="question-number">
                            <span class="number-badge">{{ $index + 1 }}</span>
                            <h4>{{ $question->question }}</h4>
                        </div>
                    </div>

                    <div class="options-list">
                        @foreach($question->options as $opt)
                            <div class="option-item {{ $opt->is_correct ? 'correct' : '' }}">
                                <div class="option-marker">
                                    @if($opt->is_correct)
                                        <span class="correct-icon">✅</span>
                                    @else
                                        <span class="option-letter">{{ chr(65 + $loop->index) }}</span>
                                    @endif
                                </div>
                                <div class="option-text">
                                    {{ $opt->option_text }}
                                    @if($opt->is_correct)
                                        <span class="correct-badge">Đáp án đúng</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($question->explanation)
                        <div class="explanation">
                            <i class="bi bi-info-circle"></i>
                            {{ $question->explanation }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <h4>Chưa có câu hỏi nào</h4>
                    <p>Quiz này hiện chưa có câu hỏi. Hãy thêm câu hỏi để học viên có thể làm bài.</p>
                    <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn-add-question">
                        <i class="bi bi-plus-circle"></i> Thêm câu hỏi
                    </a>
                </div>
            @endforelse
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

        .quiz-title-main {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 10px 0;
        }

        .quiz-title-main i {
            color: #4776E6;
            margin-right: 10px;
        }

        .quiz-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .quiz-meta .badge {
            padding: 8px 15px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 30px;
            background: #f8f9fa;
            color: #2c3e50;
            border: 1px solid #e0e0e0;
        }

        .quiz-meta .badge i {
            margin-right: 5px;
        }

        .quiz-meta .badge.bg-primary {
            background: linear-gradient(135deg, #4776E6, #8E54E9) !important;
            color: white;
            border: none;
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

        /* Questions Section */
        .questions-section {
            margin-top: 30px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #4776E6;
            font-size: 24px;
        }

        .question-count {
            margin-left: auto;
            background: #e9ecef;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: normal;
            color: #495057;
        }

        /* Question Card */
        .question-card {
            background: #f8fafd;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .question-card:hover {
            border-color: #4776E6;
            box-shadow: 0 10px 30px rgba(71, 118, 230, 0.1);
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .question-number {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .number-badge {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #4776E6, #8E54E9);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }

        .question-number h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        .btn-sm-edit {
            width: 35px;
            height: 35px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-sm-edit:hover {
            background: #4776E6;
            color: white;
            border-color: #4776E6;
        }

        /* Options List */
        .options-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .option-item {
            background: white;
            border-radius: 12px;
            padding: 15px;
            border: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.2s ease;
        }

        .option-item.correct {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .option-marker {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .option-letter {
            width: 30px;
            height: 30px;
            background: #e9ecef;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #495057;
        }

        .correct-icon {
            font-size: 20px;
        }

        .option-text {
            flex: 1;
            font-size: 15px;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .correct-badge {
            background: #10b981;
            color: white;
            padding: 3px 10px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 500;
        }

        /* Explanation */
        .explanation {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border: 1px solid #ffe69c;
            border-radius: 10px;
            color: #856404;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .explanation i {
            color: #856404;
            font-size: 18px;
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

        .btn-add-question {
            background: linear-gradient(135deg, #4776E6, #8E54E9);
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(71, 118, 230, 0.3);
        }

        .btn-add-question:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(71, 118, 230, 0.4);
            color: white;
        }

        /* Bottom Actions */
        .bottom-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #f0f2f5;
        }

        .btn-preview, .btn-results {
            flex: 1;
            padding: 14px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .btn-preview {
            background: white;
            border: 2px solid #4776E6;
            color: #4776E6;
        }

        .btn-preview:hover {
            background: #4776E6;
            color: white;
        }

        .btn-results {
            background: #2c3e50;
            color: white;
            border: 2px solid #2c3e50;
        }

        .btn-results:hover {
            background: #1a2639;
            border-color: #1a2639;
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
            .detail-container {
                padding: 20px;
            }

            .detail-header {
                flex-direction: column;
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

            .options-list {
                grid-template-columns: 1fr;
            }

            .bottom-actions {
                flex-direction: column;
            }
        }
    </style>
@endsection