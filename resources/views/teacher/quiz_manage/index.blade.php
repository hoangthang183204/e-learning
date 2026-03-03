{{-- resources/views/teacher/quizzes/index.blade.php --}}
@extends('teacher.layout')

@section('title', 'Danh sách Quiz')

@section('quizzes-active', 'active')
@section('page-icon', 'pencil-square')
@section('page-title', 'Quản lý Quiz')

@section('content')
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Danh sách Quiz</h4>
        <a href="/teacher/quizzes/create" class="create-btn">Tạo quiz mới</a>
    </div>

    <!-- Quiz Grid -->
    <div class="quiz-grid">
        @forelse($quizzes as $quiz)
            <div class="quiz-card" style="animation-delay: {{ $loop->index * 0.1 }}s">
                <div class="card-header">
                    <div class="quiz-icon">
                        📝
                    </div>
                </div>

                <div class="card-body">
                    <div class="quiz-title">
                        <span class="quiz-title-main">{{ $quiz->title }}</span>
                    </div>

                    <!-- Thông tin bài học -->
                    <div class="lesson-info">
                        <div class="lesson-icon">
                            📚
                        </div>
                        <div class="lesson-details">
                            <div class="lesson-label">Bài học</div>
                            <div class="lesson-title">{{ $quiz->lesson->title ?? 'Chưa có bài học' }}</div>
                        </div>
                    </div>

                    <!-- Thống kê -->
                    <div class="quiz-stats">
                        <div class="stat-item">
                            <span class="stat-icon">❓</span>
                            <span>{{ $quiz->questions_count ?? 0 }} câu hỏi</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon">⏱️</span>
                            <span>{{ $quiz->duration ?? 15 }} phút</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon">📊</span>
                            <span>{{ $quiz->attempts_count ?? 0 }} lượt làm</span>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="card-footer">
                    <a href="/teacher/quizzes/{{ $quiz->id }}" class="btn btn-primary">
                        <span>👁️</span> Xem chi tiết
                    </a>
                    <a href="/teacher/quizzes/{{ $quiz->id }}/edit" class="btn btn-outline">
                        <span>✏️</span> Sửa
                    </a>
                    <form action="{{ route('teacher.quizzes.destroy', $quiz) }}" method="POST" style="display:inline"
                        onsubmit="return confirm('Bạn chắc chắn muốn xoá quiz này?')">

                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger btn-sm">
                            Xoá
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <h3>Chưa có quiz nào</h3>
                <p>Bắt đầu tạo quiz đầu tiên để kiểm tra kiến thức học viên</p>
                <a href="/teacher/quizzes/create" class="create-btn">+ Tạo quiz ngay</a>
            </div>
        @endforelse
    </div>

    <!-- Phân trang nếu có -->
    @if (method_exists($quizzes, 'links'))
        <div class="mt-4">
            {{ $quizzes->links() }}
        </div>
    @endif

    <style>
        /* CSS riêng cho trang này - kế thừa từ layout */
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

        /* Quiz Grid Layout */
        .quiz-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        /* Quiz Card */
        .quiz-card {
            background: white;
            border-radius: 20px;
            padding: 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .quiz-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        /* Card Header */
        .card-header {
            height: 100px;
            background: linear-gradient(135deg, #4776E6, #8E54E9);
            position: relative;
            padding: 20px;
            display: flex;
            align-items: flex-end;
        }

        /* Màu sắc khác nhau cho mỗi card */
        .quiz-card:nth-child(6n+1) .card-header {
            background: linear-gradient(135deg, #FF6B6B, #FF8E53);
        }

        .quiz-card:nth-child(6n+2) .card-header {
            background: linear-gradient(135deg, #4ECDC4, #556270);
        }

        .quiz-card:nth-child(6n+3) .card-header {
            background: linear-gradient(135deg, #A8E6CF, #3B9E8E);
        }

        .quiz-card:nth-child(6n+4) .card-header {
            background: linear-gradient(135deg, #FFD93D, #FF8C42);
        }

        .quiz-card:nth-child(6n+5) .card-header {
            background: linear-gradient(135deg, #9B59B6, #34495E);
        }

        .quiz-card:nth-child(6n+6) .card-header {
            background: linear-gradient(135deg, #3498DB, #2C3E50);
        }

        .quiz-icon {
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

        .card-body {
            padding: 25px;
        }

        .quiz-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a2639;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .quiz-title-main {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
        }

        .lesson-info {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 12px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #4776E6;
        }

        .lesson-icon {
            width: 35px;
            height: 35px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4776E6;
            font-size: 18px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .lesson-details {
            flex: 1;
        }

        .lesson-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .lesson-title {
            font-weight: 600;
            color: #2c3e50;
            font-size: 15px;
        }

        .quiz-stats {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #6c757d;
            font-size: 13px;
        }

        .stat-icon {
            font-size: 16px;
        }

        .card-footer {
            padding: 15px 25px 25px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #4776E6;
            color: white;
            flex: 1;
            justify-content: center;
        }

        .btn-primary:hover {
            background: #3f67d1;
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #dee2e6;
            color: #495057;
        }

        .btn-outline:hover {
            background: #f8f9fa;
            border-color: #4776E6;
            color: #4776E6;
        }

        .btn-danger {
            background: #fff5f5;
            color: #dc3545;
            border: 1px solid #ffebee;
        }

        .btn-danger:hover {
            background: #dc3545;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 20px;
            grid-column: 1 / -1;
        }

        .empty-state-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
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

        @media (max-width: 768px) {
            .quiz-grid {
                grid-template-columns: 1fr;
            }

            .card-footer {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        document.querySelectorAll('.quiz-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transition = 'all 0.3s ease';
            });
        });
    </script>
@endsection
