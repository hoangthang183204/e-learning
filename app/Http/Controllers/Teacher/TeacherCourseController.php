<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class TeacherCourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('teacher_id', Auth::id())->get();
        return view('teacher.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $this->authorizeCourse($course);

        $lessons = $course->lessons()->orderBy('order_number')->get();
        return view('teacher.courses.show', compact('course', 'lessons'));
    }

    public function edit(Course $course)
    {
        $this->authorizeCourse($course);
        return view('teacher.courses.edit', compact('course'));
    }

    public function update(Course $course)
    {
        $this->authorizeCourse($course);

        request()->validate([
            'title' => 'required',
            'description' => 'required'
        ]);

        $course->update(request()->only('title', 'description'));

        return redirect()
            ->route('teacher.courses.index')
            ->with('success', 'Cập nhật khoá học thành công');
    }

    private function authorizeCourse(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền với khoá học này');
        }
    }

    public function students(Course $course)
    {
        $this->authorizeCourse($course);

        $students = $course->students()->get();

        return view('teacher.courses.students', compact('course', 'students'));
    }
}

