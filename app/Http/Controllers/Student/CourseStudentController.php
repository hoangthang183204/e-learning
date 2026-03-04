<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

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

        // Tổng số bài học
        $totalLessons = $course->lessons()->count();

        // Lấy ID các bài học
        $lessonIds = $course->lessons()->pluck('id')->toArray();

        // Đếm số bài đã học
        $completedLessons = $user->completedLessons()
            ->whereIn('lesson_id', $lessonIds)
            ->count();

        // Tính tiến độ
        $progress = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100)
            : 0;

        // Lấy thông tin enrollment
        $enrollment = $user->courses()
            ->where('course_id', $course->id)
            ->first();

        // Kiểm tra trạng thái
        $isEnrolled = $enrollment ? true : false;
        $enrollmentStatus = $enrollment ? $enrollment->pivot->status : null;

        // Kiểm tra đã hoàn thành chưa (status = finished hoặc completed_at có giá trị)
        $isCompleted = $enrollment && (
            $enrollment->pivot->status == 'finished' ||
            $enrollment->pivot->completed_at != null
        );

        return view('student.courses.show', compact(
            'course',
            'progress',
            'completedLessons',
            'totalLessons',
            'isCompleted',
            'isEnrolled',
            'enrollmentStatus'
        ));
    }

    public function enroll(Course $course)
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        // Kiểm tra đã đăng ký chưa
        $existingEnrollment = $user->courses()
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            $status = $existingEnrollment->pivot->status;

            if ($status == 'pending') {
                return back()->with('info', 'Yêu cầu đăng ký của bạn đang chờ duyệt');
            } elseif ($status == 'approved' || $status == 'finished') {
                return back()->with('info', 'Bạn đã được duyệt vào khoá học này');
            } elseif ($status == 'rejected') {
                return back()->with('error', 'Yêu cầu đăng ký của bạn đã bị từ chối');
            }
        }

        // Thêm mới với status = 'pending'
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

    public function complete(Course $course)
    {
        $user = auth()->user();

        // Cập nhật trạng thái thành finished
        $user->courses()->updateExistingPivot($course->id, [
            'status' => 'finished',
            'completed_at' => now()
        ]);

        // Tạo chứng chỉ (sẽ làm sau)
        // Certificate::create([...]);

        return back()->with('success', 'Chúc mừng! Bạn đã hoàn thành khóa học. Chứng chỉ đã được cấp.');
    }
}
