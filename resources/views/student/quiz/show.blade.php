<!DOCTYPE html>
<html>
<head>
    <title>{{ $quiz->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
            color: #333;
        }

        .question {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .question p {
            font-weight: bold;
            margin-bottom: 10px;
            color: #555;
        }

        .option {
            margin: 8px 0;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .option:hover {
            background-color: #f8f9fa;
        }

        .option label {
            cursor: pointer;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .option input[type="radio"] {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #218838;
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 15px 0;
        }

        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>{{ $quiz->title }}</h2>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('student.quiz.submit', $quiz) }}" method="POST" id="quizForm">
            @csrf

            @foreach ($quiz->questions as $index => $q)
                <div class="question">
                    <p><b>Câu {{ $index + 1 }}: {{ $q->question }}</b></p>

                    @foreach ($q->options as $opt)
                        <div class="option">
                            <label>
                                <input type="radio" 
                                       name="answers[{{ $q->id }}]" 
                                       value="{{ $opt->id }}"
                                       required
                                       class="answer-radio">
                                {{ $opt->option_text }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <hr>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <a href="{{ route('student.lessons.show', $quiz->lesson) }}" class="btn btn-secondary" style="background-color: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px;">
                    Quay lại bài học
                </a>
                <button type="submit" id="submitBtn">
                    Nộp bài
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('quizForm')?.addEventListener('submit', function(e) {
            // Kiểm tra xem đã chọn hết câu chưa
            const questions = document.querySelectorAll('.question');
            let allAnswered = true;
            
            questions.forEach((question, index) => {
                const radios = question.querySelectorAll('input[type="radio"]');
                const checked = Array.from(radios).some(radio => radio.checked);
                
                if (!checked) {
                    allAnswered = false;
                    question.style.borderLeft = '4px solid #dc3545';
                    question.style.paddingLeft = '15px';
                } else {
                    question.style.borderLeft = 'none';
                    question.style.paddingLeft = '0';
                }
            });
            
            if (!allAnswered) {
                e.preventDefault();
                alert('Vui lòng trả lời tất cả các câu hỏi!');
                return false;
            }
            
            // Disable nút submit để tránh submit nhiều lần
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').innerHTML = 'Đang xử lý...';
        });
    </script>
</body>
</html>