<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;


use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Http\Request;


class QuizController extends Controller
{
    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options');

        $result = auth()->user()
            ->quizResults()
            ->where('quiz_id', $quiz->id)
            ->first();

        return view('student.quiz.show', compact('quiz', 'result'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $score = 0;

        foreach ($quiz->questions as $question) {
            $answer = $request->answers[$question->id] ?? null;

            if (
                $answer &&
                $question->options()
                ->where('id', $answer)
                ->where('is_correct', 1)
                ->exists()
            ) {
                $score++;
            }
        }

        $passed = $score >= ceil($quiz->questions->count() * 0.7);

        // redirect sang trang result
        return redirect()->route('student.quiz.result', $quiz)
            ->with([
                'score' => $score,
                'passed' => $passed
            ]);
    }
    public function result(Quiz $quiz)
    {
        if (!session()->has('score')) {
            abort(403, 'Bạn chưa nộp bài quiz');
        }

        return view('student.quiz.result', [
            'quiz'   => $quiz,
            'score'  => session('score'),
            'passed' => session('passed')
        ]);
    }
    public function retry(Quiz $quiz)
    {
        auth()->user()
            ->quizResults()
            ->where('quiz_id', $quiz->id)
            ->delete();

        return redirect()->route('student.quiz.show', $quiz);
    }
}
