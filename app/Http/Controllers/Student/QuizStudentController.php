<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Http\Request;

class QuizStudentController extends Controller
{
    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options');

        // Kiểm tra xem user đã làm quiz này chưa
        $result = auth()->user()
            ->quizResults()
            ->where('quiz_id', $quiz->id)
            ->first();

        return view('student.quiz.show', compact('quiz', 'result'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $score = 0;
        $totalQuestions = $quiz->questions->count();
        $answers = [];

        foreach ($quiz->questions as $question) {
            $answer = $request->answers[$question->id] ?? null;

            $answers[$question->id] = [
                'selected' => $answer,
                'correct' => false
            ];

            if ($answer) {
                $isCorrect = $question->options()
                    ->where('id', $answer)
                    ->where('is_correct', 1)
                    ->exists();

                if ($isCorrect) {
                    $score++;
                    $answers[$question->id]['correct'] = true;
                }
            }
        }

        $passed = $score >= ceil($totalQuestions * 0.7);

        // LƯU KẾT QUẢ VÀO DATABASE - THÊM completed_at
        QuizResult::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'quiz_id' => $quiz->id
            ],
            [
                'score' => $score,
                'total_questions' => $totalQuestions,
                'passed' => $passed,
                'answers' => json_encode($answers),
                'completed_at' => now()  // 👈 QUAN TRỌNG: Thêm dòng này
            ]
        );

        return redirect()->route('student.quiz.result', $quiz);
    }

    public function result(Quiz $quiz)
    {
        // Lấy kết quả từ database thay vì session
        $result = auth()->user()
            ->quizResults()
            ->where('quiz_id', $quiz->id)
            ->first();

        if (!$result) {
            return redirect()->route('student.quiz.show', $quiz)
                ->with('error', 'Bạn chưa làm bài quiz này');
        }

        $lesson = $quiz->lesson;

        return view('student.quiz.result', [
            'quiz' => $quiz,
            'result' => $result,
            'score' => $result->score,
            'passed' => $result->passed,
            'lesson' => $lesson
        ]);
    }

    public function retry(Quiz $quiz)
    {
        // Xóa kết quả cũ để làm lại
        auth()->user()
            ->quizResults()
            ->where('quiz_id', $quiz->id)
            ->delete();

        return redirect()->route('student.quiz.show', $quiz)
            ->with('success', 'Bạn có thể làm lại bài quiz');
    }
}
