<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('teacher')->orderByDesc('id')->paginate(10);
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
            'title'      => 'required|max:255',
            'description' => 'required',
            'teacher_id' => 'required|exists:users,id',
        ]);

        Course::create([
            'title'       => $request->title,
            'description' => $request->description,
            'teacher_id'  => $request->teacher_id,
            'status'      => $request->status, 
        ]);

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
            'title'      => 'required|max:255',
            'description' => 'required',
            'teacher_id' => 'required|exists:users,id',
            'status'     => 'required',
        ]);

        $course->update($request->only(
            'title',
            'description',
            'teacher_id',
            'status'
        ));

        return redirect()->route('admin.courses.index')
            ->with('success', 'Cập nhật khoá học thành công');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return back()->with('success', 'Đã xoá khoá học');
    }
}
