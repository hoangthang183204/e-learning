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
        }

        h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .question {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .question p {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .option {
            margin: 8px 0;
            padding: 5px;
        }

        .option label {
            cursor: pointer;
        }

        .option input[type="radio"] {
            margin-right: 8px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 25px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #0056b3;
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>{{ $quiz->title }}</h2>

        <form action="{{ route('student.quiz.submit', $quiz) }}" method="POST">
            @csrf

            @foreach ($quiz->questions as $index => $q)
                <div class="question">
                    <p><b>Câu {{ $index + 1 }}: {{ $q->question }}</b></p>

                    @foreach ($q->options as $opt)
                        <div class="option">
                            <label>
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt->id }}"
                                    required>
                                {{ $opt->option_text }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <hr>
            <button type="submit">Nộp bài</button>
        </form>
    </div>
</body>

</html>
