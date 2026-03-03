<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Lesson;

class CheckLessonAccess
{
    public function handle(Request $request, Closure $next)
    {
        $lesson = $request->route('lesson');
        $user = auth()->user();

        if (!$lesson instanceof Lesson) {
            abort(404);
        }

        $course = $lesson->course;

        // 1️⃣ Kiểm tra đã enroll chưa
        $enrollment = $user->courses()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            abort(403, 'Bạn chưa đăng ký khoá học');
        }

        // 2️⃣ Kiểm tra đã được duyệt chưa
        if ($enrollment->pivot->status !== 'approved') {
            abort(403, 'Bạn chưa được giảng viên duyệt');
        }

        // 3️⃣ Kiểm tra quiz bài trước (nếu có)
        $previousLesson = $course->lessons()
            ->where('order_number', '<', $lesson->order_number)
            ->orderByDesc('order_number')
            ->first();

        if ($previousLesson && $previousLesson->quiz) {

            $passed = $user->quizResults()
                ->where('quiz_id', $previousLesson->quiz->id)
                ->where('passed', 1)
                ->exists();

            if (!$passed) {
                abort(403, 'Bạn phải pass quiz bài trước');
            }
        }

        return $next($request);
    }
}
