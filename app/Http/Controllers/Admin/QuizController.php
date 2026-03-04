<?php
// app/Http/Controllers/Admin/QuizController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Hiển thị danh sách bài kiểm tra
     */
    public function index(Request $request)
    {
        $lessonId = $request->get('lesson_id');

        $quizzes = Quiz::with('lesson.course')
            ->withCount('questions')
            ->when($lessonId, function ($query) use ($lessonId) {
                return $query->where('lesson_id', $lessonId);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        $lessons = Lesson::with('course')
            ->orderBy('course_id')
            ->orderBy('order_number')
            ->get();

        return view('admin.quizzes.index', compact('quizzes', 'lessons', 'lessonId'));
    }

    /**
     * Form thêm bài kiểm tra mới
     */
    public function create(Request $request)
    {
        $lessons = Lesson::with('course')
            ->orderBy('course_id')
            ->orderBy('order_number')
            ->get();

        $selectedLesson = $request->get('lesson_id');

        return view('admin.quizzes.create', compact('lessons', 'selectedLesson'));
    }

    /**
     * Lưu bài kiểm tra mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'required|integer|min:1|max:180',
            'pass_score' => 'required|integer|min:0|max:100',
            'attempts_allowed' => 'required|integer|min:1|max:10',
        ]);

        try {
            $quiz = Quiz::create([
                'lesson_id' => $request->lesson_id,
                'title' => $request->title,
                'description' => $request->description,
                'time_limit' => $request->time_limit,
                'pass_score' => $request->pass_score,
                'attempts_allowed' => $request->attempts_allowed,
            ]);

            return redirect()
                ->route('admin.quizzes.questions', $quiz->id)
                ->with('success', 'Tạo bài kiểm tra thành công! Hãy thêm câu hỏi.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Lỗi database
            $errorCode = $e->errorInfo[1] ?? null;
            $errorMessage = $e->getMessage();

            return back()
                ->withInput()
                ->with('error', "Lỗi database (Mã: $errorCode): $errorMessage");
        } catch (\Exception $e) {
            // Lỗi khác
            return back()
                ->withInput()
                ->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form sửa bài kiểm tra
     */
    public function edit(Quiz $quiz)
    {
        $lessons = Lesson::with('course')
            ->orderBy('course_id')
            ->orderBy('order_number')
            ->get();

        return view('admin.quizzes.edit', compact('quiz', 'lessons'));
    }

    /**
     * Cập nhật bài kiểm tra
     */
    public function update(Request $request, Quiz $quiz)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'required|integer|min:1|max:180',
            'pass_score' => 'required|integer|min:0|max:100',
            'attempts_allowed' => 'required|integer|min:1|max:10',
        ]);

        try {
            DB::beginTransaction();

            $quiz->update([
                'lesson_id' => $request->lesson_id,
                'title' => $request->title,
                'description' => $request->description,
                'time_limit' => $request->time_limit,
                'pass_score' => $request->pass_score,
                'attempts_allowed' => $request->attempts_allowed,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.quizzes.index', ['lesson_id' => $quiz->lesson_id])
                ->with('success', 'Cập nhật bài kiểm tra thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa bài kiểm tra
     */
    public function destroy(Quiz $quiz)
    {
        try {
            DB::beginTransaction();

            $lessonId = $quiz->lesson_id;
            $quiz->delete();

            DB::commit();

            return redirect()
                ->route('admin.quizzes.index', ['lesson_id' => $lessonId])
                ->with('success', 'Đã xóa bài kiểm tra thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Quản lý câu hỏi của bài kiểm tra
     */
    public function questions(Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('admin.quizzes.questions', compact('quiz'));
    }

    /**
     * Thêm câu hỏi mới
     */
    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $request->validate([
            'question' => 'required|string',
            'points' => 'required|integer|min:1|max:10',
            'options' => 'required|array|min:2|max:6',
            'options.*.text' => 'required|string|max:255',
            'correct_answer' => 'required|integer|min:0|max:5',
        ]);

        try {
            DB::beginTransaction();

            // Tạo câu hỏi
            $question = $quiz->questions()->create([
                'question' => $request->question,
                'points' => $request->points,
            ]);

            // Xóa tất cả options cũ của câu hỏi này (nếu có - để an toàn)
            // $question->options()->delete(); // Không cần vì mới tạo

            // Tạo các đáp án mới
            foreach ($request->options as $index => $opt) {
                if (!empty($opt['text'])) {
                    $question->options()->create([
                        'option_text' => $opt['text'],
                        'is_correct' => ($index == $request->correct_answer),
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Thêm câu hỏi thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật câu hỏi
     */
    public function updateQuestion(Request $request, Question $question)
    {
        $request->validate([
            'question' => 'required|string',
            'points' => 'required|integer|min:1|max:10',
            'options' => 'required|array|min:2|max:6',
            'options.*.text' => 'required|string|max:255',
            'correct_answer' => 'required|integer|min:0|max:5',
        ]);

        try {
            DB::beginTransaction();

            // Cập nhật câu hỏi
            $question->update([
                'question' => $request->question,
                'points' => $request->points,
            ]);

            // Xóa options cũ
            $question->options()->delete();

            // Tạo options mới
            foreach ($request->options as $index => $opt) {
                if (!empty($opt['text'])) {
                    $question->options()->create([
                        'option_text' => $opt['text'],
                        'is_correct' => ($index == $request->correct_answer),
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Cập nhật câu hỏi thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa câu hỏi
     */
    public function destroyQuestion(Question $question)
    {
        try {
            DB::beginTransaction();
            $question->delete();
            DB::commit();

            return back()->with('success', 'Đã xóa câu hỏi thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Lấy thông tin câu hỏi để edit (AJAX)
     */
    public function getQuestion(Question $question)
    {
        $question->load('options');
        return response()->json($question);
    }

    /**
     * Xem kết quả bài kiểm tra
     */
    public function results(Quiz $quiz)
    {
        $results = $quiz->results()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.quizzes.results', compact('quiz', 'results'));
    }
}
