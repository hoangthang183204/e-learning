<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with('course')
            ->orderBy('course_id')
            ->orderBy('order_number')
            ->paginate(10);

        return view('admin.lesson.index', compact('lessons'));
    }

    public function create()
    {
        $courses = Course::orderBy('title')->get();
        return view('admin.lesson.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id'    => 'required|exists:courses,id',
            'title'        => 'required|max:255',
            'content'      => 'required',
            'video_url'    => 'nullable|url',
            'order_number' => 'required|integer|min:1',
        ]);

        Lesson::create($request->all());

        return redirect()->route('admin.lesson.index')
            ->with('success', 'Tạo bài học thành công');
    }

    public function edit(Lesson $lesson)
    {
        $courses = Course::orderBy('title')->get();
        return view('admin.lesson.edit', compact('lesson', 'courses'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $request->validate([
            'course_id'    => 'required|exists:courses,id',
            'title'        => 'required|max:255',
            'content'      => 'required',
            'video_url'    => 'nullable|url',
            'order_number' => 'required|integer|min:1',
        ]);

        $lesson->update($request->all());

        return redirect()->route('admin.lesson.index')
            ->with('success', 'Cập nhật bài học thành công');
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return back()->with('success', 'Đã xoá bài học');
    }
}
