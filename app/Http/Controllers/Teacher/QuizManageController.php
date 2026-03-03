<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;

use App\Models\Quiz;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;

class QuizManageController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with('lesson')->get();
        return view('teacher.quiz_manage.index', compact('quizzes'));
    }

    public function show(Quiz $quiz)
    {
        // load đầy đủ câu hỏi + đáp án
        $quiz->load('lesson', 'questions.options');

        return view('teacher.quiz_manage.show', compact('quiz'));
    }

    public function create()
    {
        $lessons = Lesson::all();
        return view('teacher.quiz_manage.create', compact('lessons'));
    }

    public function store(Request $request)
    {
        $quiz = Quiz::create([
            'title'     => $request->title,
            'lesson_id' => $request->lesson_id
        ]);

        foreach ($request->questions as $q) {
            $question = Question::create([
                'quiz_id'  => $quiz->id,
                'question' => $q['text']
            ]);

            foreach ($q['options'] as $index => $opt) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct'  => ($q['correct'] == $index)
                ]);
            }
        }

        return redirect('/teacher/quizzes');
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load('lesson', 'questions.options');
        $lessons = Lesson::all();

        return view(
            'teacher.quiz_manage.edit',
            compact('quiz', 'lessons')
        );
    }

    public function update(Request $request, Quiz $quiz)
    {
        // Update quiz
        $quiz->update([
            'title'     => $request->title,
            'lesson_id' => $request->lesson_id
        ]);

        foreach ($request->questions as $qData) {

            $question = Question::find($qData['id']);
            $question->update([
                'question' => $qData['text']
            ]);

            foreach ($qData['options'] as $index => $optData) {

                $option = Option::find($optData['id']);
                $option->update([
                    'option_text' => $optData['text'],
                    'is_correct'  => ($qData['correct'] == $index)
                ]);
            }
        }

        return redirect('/teacher/quizzes')
            ->with('success', 'Cập nhật quiz thành công');
    }

    public function destroy(Quiz $quiz)
    {
        // Xoá options → questions → quiz (tránh lỗi FK)
        foreach ($quiz->questions as $question) {
            $question->options()->delete();
        }

        $quiz->questions()->delete();
        $quiz->delete();

        return redirect('/teacher/quizzes')
            ->with('success', 'Đã xoá quiz thành công');
    }
}
