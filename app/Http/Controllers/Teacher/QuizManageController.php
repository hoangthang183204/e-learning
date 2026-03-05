<?php
// app/Http/Controllers/Teacher/QuizManageController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizManageController extends Controller
{
    /**
     * Hiển thị danh sách quiz
     */
    public function index()
    {
        $teacherId = Auth::id();

        $quizzes = Quiz::whereHas('lesson.course', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })
            ->with(['lesson.course', 'questions'])
            ->withCount('questions')
            ->withCount('results')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('teacher.quizzes.index', compact('quizzes'));
    }

    /**
     * Hiển thị form tạo quiz mới
     */
    public function create(Request $request)
    {
        $teacherId = Auth::id();

        // Lấy lesson_id từ query string nếu có
        $lessonId = $request->lesson_id;
        $selectedLesson = null;

        if ($lessonId) {
            $selectedLesson = Lesson::with('course')
                ->whereHas('course', function ($q) use ($teacherId) {
                    $q->where('teacher_id', $teacherId);
                })
                ->find($lessonId);
        }

        // Lấy tất cả bài học của teacher
        $lessons = Lesson::whereHas('course', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })
            ->with('course')
            ->orderBy('course_id')
            ->orderBy('order_number')
            ->get();

        return view('teacher.quizzes.create', compact('lessons', 'selectedLesson'));
    }

    /**
     * Lưu quiz mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'lesson_id' => 'required|exists:lessons,id',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*' => 'required|string',
            'questions.*.correct' => 'required|integer|min:0|max:3'
        ]);

        // Kiểm tra quyền sở hữu lesson
        $lesson = Lesson::with('course')->findOrFail($request->lesson_id);
        if ($lesson->course->teacher_id !== Auth::id()) {
            return back()->with('error', 'Bạn không có quyền tạo quiz cho bài học này');
        }

        try {
            DB::beginTransaction();

            // Tạo quiz
            $quiz = Quiz::create([
                'title' => $request->title,
                'lesson_id' => $request->lesson_id
            ]);

            // Tạo câu hỏi và đáp án
            foreach ($request->questions as $qData) {
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'question' => $qData['text']
                ]);

                foreach ($qData['options'] as $index => $optText) {
                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $optText,
                        'is_correct' => ($qData['correct'] == $index) ? 1 : 0
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('teacher.quizzes.index')
                ->with('success', 'Tạo quiz thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }


    public function show(Quiz $quiz)
    {

        if ($quiz->lesson->course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem quiz này');
        }

        $quiz->load(['lesson.course', 'questions.options']);

        // Thống kê kết quả
        $results = $quiz->results()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalAttempts = $quiz->results()->count();
        $avgScore = round($quiz->results()->avg('score') ?? 0, 1);
        $passRate = $totalAttempts > 0
            ? round(($quiz->results()->where('passed', 1)->count() / $totalAttempts) * 100, 1)
            : 0;

        return view('teacher.quizzes.show', compact('quiz', 'results', 'totalAttempts', 'avgScore', 'passRate'));
    }

    public function edit(Quiz $quiz)
    {
        if ($quiz->lesson->course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa quiz này');
        }

        $quiz->load(['lesson', 'questions.options']);

        $teacherId = Auth::id();
        $lessons = Lesson::whereHas('course', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })
            ->with('course')
            ->orderBy('course_id')
            ->orderBy('order_number')
            ->get();

        return view('teacher.quizzes.edit', compact('quiz', 'lessons'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        if ($quiz->lesson->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|max:255',
            'lesson_id' => 'required|exists:lessons,id',
        ]);

        try {
            DB::beginTransaction();

            // Cập nhật quiz
            $quiz->update([
                'title' => $request->title,
                'lesson_id' => $request->lesson_id
            ]);

            // Cập nhật câu hỏi cũ (nếu có)
            if ($request->has('questions')) {
                foreach ($request->questions as $qIndex => $qData) {
                    $question = Question::find($qData['id']);
                    if ($question) {
                        $question->update(['question' => $qData['text']]);

                        // Cập nhật đáp án
                        foreach ($qData['options'] as $oIndex => $optData) {
                            if (isset($optData['id'])) {
                                $option = Option::find($optData['id']);
                                if ($option) {
                                    $option->update([
                                        'option_text' => $optData['text'],
                                        'is_correct' => ($qData['correct'] == $oIndex) ? 1 : 0
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            if ($request->has('new_questions')) {
                foreach ($request->new_questions as $qData) {
                    $question = Question::create([
                        'quiz_id' => $quiz->id,
                        'question' => $qData['text']
                    ]);

                    foreach ($qData['options'] as $index => $optText) {
                        Option::create([
                            'question_id' => $question->id,
                            'option_text' => $optText,
                            'is_correct' => ($qData['correct'] == $index) ? 1 : 0
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('teacher.quizzes.index')
                ->with('success', 'Cập nhật quiz thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    public function destroy(Quiz $quiz)
    {
        // Kiểm tra quyền sở hữu
        if ($quiz->lesson->course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xóa quiz này');
        }

        try {
            DB::beginTransaction();

            // Xóa kết quả quiz
            $quiz->results()->delete();

            // Xóa câu hỏi và đáp án
            foreach ($quiz->questions as $question) {
                $question->options()->delete();
            }
            $quiz->questions()->delete();

            // Xóa quiz
            $quiz->delete();

            DB::commit();

            return redirect()
                ->route('teacher.quizzes.index')
                ->with('success', 'Xóa quiz thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
