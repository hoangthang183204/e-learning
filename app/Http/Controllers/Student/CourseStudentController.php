<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;

class CourseStudentController extends Controller
{
    public function index()
    {
        $courses = Course::orderByDesc('id')->get();
        return view('student.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $user = auth()->user();

        $totalLessons = $course->lessons()->count();

        $completedLessons = $user->completedLessons()
            ->where('course_id', $course->id)
            ->count();

        $progress = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100)
            : 0;

        return view(
            'student.courses.show',
            compact('course', 'progress', 'completedLessons', 'totalLessons')
        );
    }

    public function enroll(Course $course)
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        if ($user->courses()->where('course_id', $course->id)->exists()) {
            return back()->with('info', 'Bạn đã đăng ký khoá học này');
        }

        $user->courses()->attach($course->id, [
            'status' => 'pending',
            'enrolled_at' => now()
        ]);

        return back()->with('success', 'Đã gửi yêu cầu đăng ký. Chờ giảng viên duyệt');
    }

    public function unenroll(Course $course)
    {
        $user = auth()->user();

        $user->courses()->detach($course->id);

        return back()->with('success', 'Đã hủy đăng ký khóa học');
    }
}
