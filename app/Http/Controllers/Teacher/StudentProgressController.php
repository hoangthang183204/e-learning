<?php
// app/Http/Controllers/Teacher/StudentProgressController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StudentProgressController extends Controller
{
    public function index(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $students = $course->students()->with([
            'completedLessons' => function ($q) use ($course) {
                $q->where('course_id', $course->id);
            }
        ])->paginate(10);

        $totalLessons = $course->lessons()->count();

        return view('teacher.courses.progress', compact('course', 'students', 'totalLessons'));
    }

    /**
     * Chi tiết tiến độ của 1 học viên
     */
    public function show(Course $course, User $student)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        // Kiểm tra học viên có trong khóa học không
        if (!$course->students()->where('user_id', $student->id)->exists()) {
            abort(404, 'Học viên không tham gia khóa học này');
        }

        $lessons = $course->lessons()->orderBy('order_number')->get();
        $completedLessons = $student->completedLessons()
            ->where('course_id', $course->id)
            ->pluck('lesson_id')
            ->toArray();

        $totalLessons = $lessons->count();
        $completedCount = count($completedLessons);
        $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        // Lấy kết quả quiz
        $quizResults = [];
        foreach ($lessons as $lesson) {
            foreach ($lesson->quizzes as $quiz) {
                $result = $quiz->results()->where('user_id', $student->id)->first();
                $quizResults[] = [
                    'quiz' => $quiz,
                    'result' => $result
                ];
            }
        }

        return view('teacher.courses.progress-detail', compact(
            'course',
            'student',
            'lessons',
            'completedLessons',
            'totalLessons',
            'completedCount',
            'percent',
            'quizResults'
        ));
    }
}