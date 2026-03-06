<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuizStudentController extends Controller
{
    public function show(Quiz $quiz)
    {
        // Kiểm tra user đã đăng ký khóa học chưa
        $user = auth()->user();
        $course = $quiz->lesson->course;

        $isEnrolled = $user->courses()
            ->where('course_id', $course->id)
            ->wherePivotIn('status', ['approved', 'finished'])
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.courses.show', $course)
                ->with('error', 'Bạn cần đăng ký khóa học để làm bài quiz');
        }

        $quiz->load('questions.options');

        // Kiểm tra xem user đã làm quiz này chưa
        $result = $user->quizResults()
            ->where('quiz_id', $quiz->id)
            ->first();

        return view('student.quiz.show', compact('quiz', 'result'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|exists:options,id'
        ]);

        $user = auth()->user();

        // Kiểm tra đã làm quiz này chưa
        $existingResult = $user->quizResults()
            ->where('quiz_id', $quiz->id)
            ->first();

        if ($existingResult) {
            return redirect()->route('student.quiz.result', $quiz)
                ->with('info', 'Bạn đã làm bài quiz này rồi');
        }

        // Kiểm tra xem đã học bài học chưa
        $lessonCompleted = $user->completedLessons()
            ->where('lesson_id', $quiz->lesson_id)
            ->exists();
            
        if (!$lessonCompleted) {
            return back()->with('error', 'Bạn cần hoàn thành bài học trước khi làm quiz');
        }

        $score = 0;
        $totalQuestions = $quiz->questions->count();
        
        // Đảm bảo total_questions > 0
        if ($totalQuestions == 0) {
            return back()->with('error', 'Quiz chưa có câu hỏi nào');
        }
        
        $answers = [];

        foreach ($quiz->questions as $question) {
            $answerId = $request->answers[$question->id];
            $isCorrect = $question->options()
                ->where('id', $answerId)
                ->where('is_correct', 1)
                ->exists();

            if ($isCorrect) {
                $score++;
            }

            $answers[$question->id] = [
                'selected' => $answerId,
                'correct' => $isCorrect,
                'question' => $question->question,
                'correct_answer' => $question->options()->where('is_correct', 1)->first()->option_text ?? ''
            ];
        }

        $passed = $score >= ceil($totalQuestions * 0.7); // 70% để đậu

        // Lưu kết quả
        QuizResult::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'passed' => $passed,
            'answers' => json_encode($answers, JSON_UNESCAPED_UNICODE),
            'completed_at' => now()
        ]);

        return redirect()->route('student.quiz.result', $quiz)
            ->with('success', 'Đã nộp bài thành công!');
    }

    public function result(Quiz $quiz)
    {
        $user = auth()->user();
        $result = $user->quizResults()
            ->where('quiz_id', $quiz->id)
            ->first();

        if (!$result) {
            return redirect()->route('student.quiz.show', $quiz)
                ->with('error', 'Bạn chưa làm bài quiz này');
        }

        // Giải mã answers - kiểm tra nếu là string thì mới decode
        if (is_string($result->answers)) {
            $result->answers = json_decode($result->answers, true);
        }

        $lesson = $quiz->lesson;
        $quiz->load('questions.options');

        return view('student.quiz.result', compact('quiz', 'result', 'lesson'));
    }

    public function retry(Quiz $quiz)
    {
        $user = auth()->user();

        // Kiểm tra xem có được phép làm lại không
        $oldResult = $user->quizResults()
            ->where('quiz_id', $quiz->id)
            ->first();
            
        if ($oldResult && $oldResult->passed) {
            return redirect()->route('student.quiz.result', $quiz)
                ->with('error', 'Bạn đã đạt quiz này rồi, không thể làm lại');
        }

        // Xóa kết quả cũ
        $user->quizResults()
            ->where('quiz_id', $quiz->id)
            ->delete();

        return redirect()->route('student.quiz.show', $quiz)
            ->with('success', 'Bạn có thể làm lại bài quiz');
    }
}