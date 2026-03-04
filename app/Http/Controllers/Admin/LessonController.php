<?php
// app/Http/Controllers/Admin/LessonController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $courseId = $request->get('course_id');

        $lessons = Lesson::with('course')
            ->when($courseId, function ($query) use ($courseId) {
                return $query->where('course_id', $courseId);
            })
            ->orderBy('course_id')
            ->orderBy('order_number')
            ->paginate(10);

        $courses = Course::orderBy('title')->get();

        return view('admin.lessons.index', compact('lessons', 'courses', 'courseId'));
    }

    public function create(Request $request)
    {
        $courses = Course::orderBy('title')->get();
        $selectedCourse = $request->get('course_id');

        // Gợi ý order_number tiếp theo
        $nextOrder = 1;
        if ($selectedCourse) {
            $lastLesson = Lesson::where('course_id', $selectedCourse)
                ->orderBy('order_number', 'desc')
                ->first();
            $nextOrder = $lastLesson ? $lastLesson->order_number + 1 : 1;
        }

        return view('admin.lessons.create', compact('courses', 'selectedCourse', 'nextOrder'));
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

        // Kiểm tra order_number đã tồn tại trong course chưa
        $exists = Lesson::where('course_id', $request->course_id)
            ->where('order_number', $request->order_number)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['order_number' => 'Số thứ tự này đã tồn tại trong khoá học']);
        }

        Lesson::create($request->all());

        return redirect()->route('admin.lessons.index', ['course_id' => $request->course_id])
            ->with('success', 'Tạo bài học thành công');
    }

    public function edit(Lesson $lesson)
    {
        $courses = Course::orderBy('title')->get();
        return view('admin.lessons.edit', compact('lesson', 'courses'));
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

        // Kiểm tra order_number trùng (trừ chính nó)
        $exists = Lesson::where('course_id', $request->course_id)
            ->where('order_number', $request->order_number)
            ->where('id', '!=', $lesson->id)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['order_number' => 'Số thứ tự này đã tồn tại trong khoá học']);
        }

        $lesson->update($request->all());

        return redirect()->route('admin.lessons.index', ['course_id' => $lesson->course_id])
            ->with('success', 'Cập nhật bài học thành công');
    }

    public function destroy(Lesson $lesson)
    {
        $courseId = $lesson->course_id;

        // Kiểm tra xem có quiz không
        if ($lesson->quizzes()->count() > 0) {
            return back()->with('error', 'Không thể xoá bài học đã có bài kiểm tra');
        }

        $lesson->delete();

        // Cập nhật lại order_number cho các bài học còn lại
        $remainingLessons = Lesson::where('course_id', $courseId)
            ->orderBy('order_number')
            ->get();

        foreach ($remainingLessons as $index => $remainingLesson) {
            $remainingLesson->update(['order_number' => $index + 1]);
        }

        return redirect()->route('admin.lessons.index', ['course_id' => $courseId])
            ->with('success', 'Đã xoá bài học');
    }
}
