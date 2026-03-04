<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;

class LessonStudentController extends Controller
{
    public function show(Lesson $lesson)
    {
        $prevLesson = $lesson->course->lessons()
            ->where('order_number', '<', $lesson->order_number)
            ->orderByDesc('order_number')
            ->first();

        $nextLesson = $lesson->course->lessons()
            ->where('order_number', '>', $lesson->order_number)
            ->orderBy('order_number')
            ->first();

        $quiz = $lesson->quiz;

        $isCompleted = auth()->user()
            ->completedLessons()
            ->where('lesson_id', $lesson->id)
            ->exists();

        return view('student.lessons.show', compact(
            'lesson',
            'prevLesson',
            'nextLesson',
            'quiz',
            'isCompleted'
        ));
    }

    public function complete(Lesson $lesson)
    {
        $user = auth()->user();
        $course = $lesson->course;

        // 1️⃣ Đánh dấu hoàn thành lesson
        $user->completedLessons()->syncWithoutDetaching([
            $lesson->id => ['completed_at' => now()]
        ]);

        // 2️⃣ Lấy ID tất cả bài học của khóa học
        $lessonIds = $course->lessons()->pluck('id')->toArray();

        // 3️⃣ Đếm số bài user đã học trong khóa học này
        $completedLessons = $user->completedLessons()
            ->whereIn('lesson_id', $lessonIds)  // SỬA LỖI Ở ĐÂY
            ->count();

        $totalLessons = $course->lessons()->count();

        // 4️⃣ Nếu đủ 100% → cập nhật completed_at của course
        if ($totalLessons > 0 && $completedLessons >= $totalLessons) {
            $user->courses()->updateExistingPivot($course->id, [
                'completed_at' => now(),
                'status' => 'finished'
            ]);

            // Flash thông báo
            session()->flash('course_completed', true);
            session()->flash('success', '🎉 CHÚC MỪNG! Bạn đã hoàn thành khóa học!');
        }

        return back()->with('success', '✅ Đã hoàn thành bài học');
    }
}