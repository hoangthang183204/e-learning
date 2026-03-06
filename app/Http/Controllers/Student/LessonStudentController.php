<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonStudentController extends Controller
{
    public function show(Lesson $lesson)
    {
        $user = auth()->user();

        // Kiểm tra đăng ký
        $isEnrolled = $user->courses()
            ->where('course_id', $lesson->course_id)
            ->wherePivotIn('status', ['approved', 'finished'])
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.courses.show', $lesson->course)
                ->with('error', 'Bạn cần đăng ký khóa học để xem bài học');
        }

        // Lấy bài trước
        $prevLesson = $lesson->course->lessons()
            ->where('order_number', '<', $lesson->order_number)
            ->orderByDesc('order_number')
            ->first();

        // Lấy bài sau
        $nextLesson = $lesson->course->lessons()
            ->where('order_number', '>', $lesson->order_number)
            ->orderBy('order_number')
            ->first();

        // Lấy quiz của bài học
        $quiz = $lesson->quiz;

        // Kiểm tra đã hoàn thành bài học chưa
        $isCompleted = $user->completedLessons()
            ->where('lesson_id', $lesson->id)
            ->exists();

        // KIỂM TRA ĐÃ LÀM QUIZ CHƯA - QUAN TRỌNG
        $quizCompleted = false;
        $quizResult = null;
        if ($quiz) {
            $quizResult = $user->quizResults()
                ->where('quiz_id', $quiz->id)
                ->first();
            $quizCompleted = $quizResult ? true : false;
        }

        // Debug - Bỏ comment để kiểm tra dữ liệu
        // dd([
        //     'quiz' => $quiz,
        //     'quizCompleted' => $quizCompleted,
        //     'quizResult' => $quizResult
        // ]);

        // Lấy video ID
        $videoId = $this->getYoutubeId($lesson->video_url);

        return view('student.lessons.show', compact(
            'lesson',
            'prevLesson',
            'nextLesson',
            'quiz',
            'isCompleted',
            'quizCompleted',  // PHẢI CÓ BIẾN NÀY
            'quizResult',     // PHẢI CÓ BIẾN NÀY
            'videoId'
        ));
    }

    public function complete(Lesson $lesson)
    {
        $user = auth()->user();
        $course = $lesson->course;

        // Kiểm tra đăng ký
        $isEnrolled = $user->courses()
            ->where('course_id', $course->id)
            ->wherePivotIn('status', ['approved', 'finished'])
            ->exists();

        if (!$isEnrolled) {
            return back()->with('error', 'Bạn chưa đăng ký khóa học này');
        }

        // Kiểm tra đã hoàn thành chưa
        if ($user->completedLessons()->where('lesson_id', $lesson->id)->exists()) {
            return back()->with('info', 'Bạn đã hoàn thành bài học này rồi');
        }

        // Đánh dấu hoàn thành lesson
        $user->completedLessons()->attach($lesson->id, [
            'completed_at' => now()
        ]);

        // Kiểm tra tiến độ
        $lessonIds = $course->lessons()->pluck('id')->toArray();
        $completedLessons = $user->completedLessons()
            ->whereIn('lesson_id', $lessonIds)
            ->count();

        $totalLessons = $course->lessons()->count();

        // Nếu đủ 100% → cập nhật completed_at của course
        if ($totalLessons > 0 && $completedLessons >= $totalLessons) {
            $user->courses()->updateExistingPivot($course->id, [
                'completed_at' => now(),
                'status' => 'finished'
            ]);

            return redirect()->route('student.courses.show', $course)
                ->with('success', '🎉 CHÚC MỪNG! Bạn đã hoàn thành khóa học!');
        }

        return back()->with('success', '✅ Đã hoàn thành bài học');
    }

    private function getYoutubeId($url)
    {
        if (!$url) return null;

        $patterns = [
            '/youtube\.com\/watch\?v=([^&\s]+)/',
            '/youtube\.com\/embed\/([^&\s]+)/',
            '/youtu\.be\/([^&\s]+)/',
            '/youtube\.com\/v\/([^&\s]+)/',
            '/youtube\.com\/shorts\/([^&\s]+)/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        return null;
    }
}
