<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;

class DashboardTeacherController extends Controller
{
    public function teacher()
    {
        $courses = Course::withCount('students')->get();

        return view('teacher.dashboard', compact('courses'));
    }

    public function courseDashboard(Course $course)
    {
        return view('teacher.courses.dashboard', compact('course'));
    }
}
