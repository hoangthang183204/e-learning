<?php
// app/Http/Controllers/Teacher/LessonController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonTeacherController extends Controller
{
    /**
     * Hiển thị danh sách bài học
     */
    public function index(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem bài học của khóa học này');
        }

        $lessons = $course->lessons()->orderBy('order_number')->paginate(15);
        
        return view('teacher.lessons.index', compact('course', 'lessons'));
    }

    /**
     * Hiển thị form tạo bài học
     */
    public function create(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thêm bài học vào khóa học này');
        }

        // Lấy số thứ tự lớn nhất + 1
        $maxOrder = $course->lessons()->max('order_number') ?? 0;
        $nextOrder = $maxOrder + 1;

        return view('teacher.lessons.create', compact('course', 'nextOrder'));
    }

    /**
     * Lưu bài học mới
     */
    public function store(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thêm bài học vào khóa học này');
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'order_number' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra order_number đã tồn tại chưa
            $exists = Lesson::where('course_id', $course->id)
                ->where('order_number', $request->order_number)
                ->exists();

            if ($exists) {
                // Đẩy các bài học có order_number >= lên 1 đơn vị
                Lesson::where('course_id', $course->id)
                    ->where('order_number', '>=', $request->order_number)
                    ->increment('order_number');
            }

            Lesson::create([
                'course_id' => $course->id,
                'title' => $request->title,
                'content' => $request->content,
                'video_url' => $request->video_url,
                'order_number' => $request->order_number
            ]);

            DB::commit();

            return redirect()
                ->route('teacher.courses.show', $course)
                ->with('success', 'Thêm bài học thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết bài học
     */
    public function show(Course $course, Lesson $lesson)
    {
        if ($course->teacher_id !== Auth::id() || $lesson->course_id !== $course->id) {
            abort(403, 'Bạn không có quyền xem bài học này');
        }

        return view('teacher.lessons.show', compact('course', 'lesson'));
    }

    /**
     * Hiển thị form sửa bài học
     */
    public function edit(Course $course, Lesson $lesson)
    {
        if ($course->teacher_id !== Auth::id() || $lesson->course_id !== $course->id) {
            abort(403, 'Bạn không có quyền sửa bài học này');
        }

        return view('teacher.lessons.edit', compact('course', 'lesson'));
    }

    /**
     * Cập nhật bài học
     */
    public function update(Request $request, Course $course, Lesson $lesson)
    {
        if ($course->teacher_id !== Auth::id() || $lesson->course_id !== $course->id) {
            abort(403, 'Bạn không có quyền sửa bài học này');
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'order_number' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Nếu thay đổi order_number
            if ($lesson->order_number != $request->order_number) {
                // Điều chỉnh các bài học khác
                if ($request->order_number < $lesson->order_number) {
                    // Di chuyển lên trên
                    Lesson::where('course_id', $course->id)
                        ->whereBetween('order_number', [$request->order_number, $lesson->order_number - 1])
                        ->increment('order_number');
                } else {
                    // Di chuyển xuống dưới
                    Lesson::where('course_id', $course->id)
                        ->whereBetween('order_number', [$lesson->order_number + 1, $request->order_number])
                        ->decrement('order_number');
                }
            }

            $lesson->update([
                'title' => $request->title,
                'content' => $request->content,
                'video_url' => $request->video_url,
                'order_number' => $request->order_number
            ]);

            DB::commit();

            return redirect()
                ->route('teacher.courses.show', $course)
                ->with('success', 'Cập nhật bài học thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa bài học
     */
    public function destroy(Course $course, Lesson $lesson)
    {
        if ($course->teacher_id !== Auth::id() || $lesson->course_id !== $course->id) {
            abort(403, 'Bạn không có quyền xóa bài học này');
        }

        try {
            DB::beginTransaction();

            // Kiểm tra có quiz không
            if ($lesson->quizzes()->count() > 0) {
                return back()->with('error', 'Không thể xóa bài học đã có quiz!');
            }

            // Xóa bài học
            $lesson->delete();

            // Cập nhật lại order_number cho các bài học còn lại
            Lesson::where('course_id', $course->id)
                ->orderBy('order_number')
                ->get()
                ->each(function ($item, $index) {
                    $item->update(['order_number' => $index + 1]);
                });

            DB::commit();

            return redirect()
                ->route('teacher.courses.show', $course)
                ->with('success', 'Xóa bài học thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}