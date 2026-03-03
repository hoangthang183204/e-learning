<!DOCTYPE html>
<html>
<head>
    <title>Kết quả bài quiz | {{ $quiz->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
        }

        .result-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        .quiz-title {
            color: #666;
            font-size: 18px;
        }

        .score-box {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .score-label {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .score-value {
            font-size: 48px;
            font-weight: bold;
            color: #007bff;
        }

        .score-divider {
            font-size: 24px;
            color: #999;
            margin: 0 5px;
        }

        .status-box {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .status-passed {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .status-failed {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .status-text {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .status-message {
            font-size: 14px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border: 1px solid #ddd;
        }

        .info-row {
            display: table-row;
        }

        .info-label, .info-value {
            display: table-cell;
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        .info-label {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 40%;
        }

        .info-value {
            width: 60%;
        }

        .info-row:last-child .info-label,
        .info-row:last-child .info-value {
            border-bottom: none;
        }

        .actions {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border: 1px solid #007bff;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            margin-left: 10px;
        }

        .btn-secondary:hover {
            background-color: #545b62;
            border-color: #545b62;
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="result-card">
            <div class="header">
                <h2>Kết quả bài kiểm tra</h2>
                <div class="quiz-title">{{ $quiz->title }}</div>
            </div>

            <div class="score-box">
                <div class="score-label">Điểm số của bạn</div>
                <div>
                    <span class="score-value">{{ $score }}</span>
                    <span class="score-divider">/</span>
                    <span class="score-value" style="color: #666; font-size: 32px;">{{ $quiz->questions->count() }}</span>
                </div>
            </div>

            @if ($passed)
                <div class="status-box status-passed">
                    <div class="status-text">🎉 ĐẠT YÊU CẦU</div>
                    <div class="status-message">Chúc mừng bạn đã hoàn thành tốt bài kiểm tra!</div>
                </div>
            @else
                <div class="status-box status-failed">
                    <div class="status-text">📝 CHƯA ĐẠT</div>
                    <div class="status-message">Bạn cần ôn tập thêm và làm lại bài kiểm tra</div>
                </div>
            @endif

            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Tên học viên</div>
                    <div class="info-value">{{ auth()->user()->name ?? '---' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Thời gian hoàn thành</div>
                    <div class="info-value">{{ now()->format('H:i d/m/Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Số câu đúng</div>
                    <div class="info-value">{{ $score }} / {{ $quiz->questions->count() }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tỷ lệ đúng</div>
                    <div class="info-value">{{ round(($score / $quiz->questions->count()) * 100) }}%</div>
                </div>
            </div>

            <hr>

            <div class="actions">
                <a href="/student/courses" class="btn">Quay lại khóa học</a>
                @if(!$passed)
                    <a href="/quiz/{{ $quiz->id }}" class="btn btn-secondary">Làm lại bài quiz</a>
                @endif
            </div>
        </div>
    </div>
</body>
</html>