<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CourseStudentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $courses = Course::with(['teacher', 'lessons'])
            ->withCount('students')
            ->orderByDesc('id')
            ->get();

        // Đánh dấu khóa học đã đăng ký
        $enrolledCourseIds = $user->courses()->pluck('course_id')->toArray();

        foreach ($courses as $course) {
            $course->is_enrolled = in_array($course->id, $enrolledCourseIds);
            $course->enrollment_status = $user->courses()
                ->where('course_id', $course->id)
                ->first()?->pivot->status;
        }

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

        // Kiểm tra đã hoàn thành chưa
        $isCompleted = $enrollment && (
            $enrollment->pivot->status == 'finished' ||
            $enrollment->pivot->completed_at != null
        );

        // Lấy danh sách bài học kèm trạng thái hoàn thành
        $lessons = $course->lessons()->orderBy('order_number')->get();
        foreach ($lessons as $lesson) {
            $lesson->is_completed = $user->completedLessons()
                ->where('lesson_id', $lesson->id)
                ->exists();

            // Lấy quiz của bài học
            $lesson->quiz = $lesson->quiz;
        }

        return view('student.courses.show', compact(
            'course',
            'progress',
            'completedLessons',
            'totalLessons',
            'isCompleted',
            'isEnrolled',
            'enrollmentStatus',
            'lessons'
        ));
    }

    public function enroll(Course $course)
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        // Kiểm tra khóa học có đang hoạt động không
        if ($course->status != 1) {
            return back()->with('error', 'Khóa học hiện không khả dụng');
        }

        // Kiểm tra đã đăng ký chưa
        $existingEnrollment = $user->courses()
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            $status = $existingEnrollment->pivot->status;

            if ($status == 'pending') {
                return back()->with('info', 'Yêu cầu đăng ký của bạn đang chờ duyệt');
            } elseif ($status == 'approved') {
                return back()->with('info', 'Bạn đã được duyệt vào khoá học này');
            } elseif ($status == 'rejected') {
                return back()->with('error', 'Yêu cầu đăng ký của bạn đã bị từ chối' .
                    ($existingEnrollment->pivot->rejection_reason ? ': ' . $existingEnrollment->pivot->rejection_reason : ''));
            } elseif ($status == 'blocked') {
                return back()->with('error', 'Bạn đã bị khóa khỏi khóa học này' .
                    ($existingEnrollment->pivot->block_reason ? ': ' . $existingEnrollment->pivot->block_reason : ''));
            } elseif ($status == 'finished') {
                return back()->with('info', 'Bạn đã hoàn thành khóa học này');
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

        // Chỉ cho phép hủy khi đang pending hoặc approved
        $enrollment = $user->courses()
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment && in_array($enrollment->pivot->status, ['pending', 'approved'])) {
            $user->courses()->detach($course->id);
            return back()->with('success', 'Đã hủy đăng ký khóa học');
        }

        return back()->with('error', 'Không thể hủy đăng ký khóa học này');
    }


    public function complete(Course $course)
    {
        $user = Auth::user();

        // Kiểm tra xem user đã đăng ký khóa học chưa
        $enrollment = $user->courses()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Bạn chưa đăng ký khóa học này');
        }

        // Kiểm tra trạng thái khóa học
        if ($enrollment->pivot->status == 'finished') {
            return back()->with('info', 'Bạn đã hoàn thành khóa học này rồi');
        }

        if ($enrollment->pivot->status != 'approved') {
            return back()->with('error', 'Khóa học chưa được duyệt hoặc đã bị khóa');
        }

        // Kiểm tra xem đã học hết bài chưa
        $totalLessons = $course->lessons()->count();

        if ($totalLessons == 0) {
            return back()->with('error', 'Khóa học chưa có bài học nào');
        }

        $completedLessons = $user->completedLessons()
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->count();

        if ($completedLessons < $totalLessons) {
            return back()->with('error', 'Bạn cần hoàn thành tất cả bài học trước khi kết thúc khóa học');
        }

        // Cập nhật trạng thái thành finished
        $user->courses()->updateExistingPivot($course->id, [
            'status' => 'finished',
            'completed_at' => now()
        ]);

        // Tạo chứng chỉ tự động
        try {
            $this->generateCertificate($user->id, $course->id);

            return back()->with('success', '🎉 Chúc mừng! Bạn đã hoàn thành khóa học. Chứng chỉ đã được cấp.');
        } catch (\Exception $e) {
            // Log lỗi để debug
            Log::error('Lỗi tạo chứng chỉ: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Vẫn thông báo thành công nhưng báo lỗi chứng chỉ
            return back()->with('warning', '✅ Đã hoàn thành khóa học nhưng không thể tạo chứng chỉ. Vui lòng liên hệ quản trị viên.');
        }
    }

    /**
     * Tạo chứng chỉ cho học viên khi hoàn thành khóa học
     */
    private function generateCertificate($userId, $courseId)
    {
        $user = Auth::user() ?? User::find($userId);
        $course = Course::find($courseId);

        if (!$user || !$course) {
            throw new \Exception('Không tìm thấy thông tin học viên hoặc khóa học');
        }

        // Kiểm tra xem đã có chứng chỉ chưa
        $existingCertificate = Certificate::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if ($existingCertificate) {
            return $existingCertificate; // Đã có chứng chỉ rồi
        }

        // Tạo số chứng chỉ duy nhất
        $certificateNumber = $this->generateCertificateNumber();

        // Tạo chứng chỉ mới
        $certificate = Certificate::create([
            'certificate_number' => $certificateNumber,
            'user_id' => $userId,
            'course_id' => $courseId,
            'student_name' => $user->name,
            'course_name' => $course->title,
            'completion_date' => now(),
            'issued_at' => now(),
            'is_verified' => true
        ]);

        // Log thành công
        Log::info('Đã tạo chứng chỉ thành công', [
            'certificate_number' => $certificateNumber,
            'user_id' => $userId,
            'course_id' => $courseId
        ]);

        return $certificate;
    }

    /**
     * Tạo số chứng chỉ duy nhất
     */
    private function generateCertificateNumber()
    {
        $prefix = 'EL';
        $year = date('Y');
        $month = date('m');
        $random = strtoupper(substr(uniqid(), -6));

        // Kiểm tra trùng lặp
        do {
            $number = "{$prefix}-{$year}{$month}-{$random}";
            $exists = Certificate::where('certificate_number', $number)->exists();
            if ($exists) {
                $random = strtoupper(substr(uniqid(), -6));
            }
        } while ($exists);

        return $number;
    }


    public function getProgress(Course $course)
    {
        $user = auth()->user();

        $totalLessons = $course->lessons()->count();
        $completedLessons = $user->completedLessons()
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->count();

        $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        return response()->json([
            'progress' => $progress,
            'completed' => $completedLessons,
            'total' => $totalLessons,
            'can_complete' => ($completedLessons >= $totalLessons && $totalLessons > 0)
        ]);
    }
}
