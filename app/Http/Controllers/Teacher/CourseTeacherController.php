<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;

class CourseTeacherController extends Controller
{
    public function students(Course $course)
    {
        $students = $course->students()
            ->withPivot('status', 'enrolled_at')
            ->get();

        return view('teacher.courses.students', compact(
            'course',
            'students'
        ));
    }

    public function approve(Course $course, $userId)
    {
        $course->students()->updateExistingPivot(
            $userId,
            ['status' => 'approved']
        );

        return back()->with('success', 'Đã duyệt học viên');
    }

    public function block(Course $course, $userId)
    {
        $course->students()->updateExistingPivot(
            $userId,
            ['status' => 'blocked']
        );

        return back()->with('success', 'Đã chặn học viên');
    }
}
