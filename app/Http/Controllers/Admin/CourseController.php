<?php
// app/Http/Controllers/Admin/CourseController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('teacher')
            ->withCount(['lessons', 'students'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.courses.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|max:255',
            'description' => 'nullable|string',
            'teacher_id'  => 'required|exists:users,id,role,teacher',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'teacher_id'  => $request->teacher_id,
            'status'      => $request->has('status') ? 1 : 0,
        ];

        Course::create($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Tạo khoá học thành công');
    }

    public function edit(Course $course)
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.courses.edit', compact('course', 'teachers'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title'       => 'required|max:255',
            'description' => 'nullable|string',
            'teacher_id'  => 'required|exists:users,id,role,teacher',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'teacher_id'  => $request->teacher_id,
            'status'      => $request->has('status') ? 1 : 0,
        ];

        $course->update($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Cập nhật khoá học thành công');
    }

    public function destroy(Course $course)
    {
        // Kiểm tra xem có bài học không trước khi xoá
        if ($course->lessons()->count() > 0) {
            return back()->with('error', 'Không thể xoá khoá học đã có bài học');
        }

        // Xoá các bản ghi liên quan trong bảng course_user
        $course->students()->detach();

        $course->delete();

        return back()->with('success', 'Đã xoá khoá học');
    }
}
