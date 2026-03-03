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

        $user->completedLessons()->syncWithoutDetaching([
            $lesson->id => ['completed_at' => now()]
        ]);

        return back()->with('success', 'Đã hoàn thành bài học');
    }
}
