<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;

class StudentProgressController extends Controller
{
    public function index(Course $course)
    {
        $students = $course->students()->with([
            'completedLessons' => function ($q) use ($course) {
                $q->where('course_id', $course->id);
            }
        ])->get();

        $totalLessons = $course->lessons()->count();

        return view(
            'teacher.courses.progress',
            compact('course', 'students', 'totalLessons')
        );
    }
}
