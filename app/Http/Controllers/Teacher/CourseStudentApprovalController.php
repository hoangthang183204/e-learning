<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseStudentApprovalController extends Controller
{
    /**
     * Hiển thị danh sách học viên chờ duyệt
     */
    public function pending(Course $course)
    {
        // Kiểm tra quyền sở hữu
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền quản lý khóa học này');
        }

        // Lấy danh sách học viên đang chờ duyệt (pending)
        $pendingStudents = $course->students()
            ->wherePivot('status', 'pending')
            ->withPivot('enrolled_at', 'status')
            ->orderBy('course_user.enrolled_at', 'desc')
            ->get();

        // Lấy danh sách học viên đã được duyệt (approved)
        $approvedStudents = $course->students()
            ->wherePivot('status', 'approved')
            ->withPivot('enrolled_at', 'status', 'completed_at')
            ->orderBy('course_user.enrolled_at', 'desc')
            ->paginate(10);

        // Lấy danh sách học viên bị từ chối (rejected)
        $rejectedStudents = $course->students()
            ->wherePivot('status', 'rejected')
            ->withPivot('enrolled_at', 'status')
            ->orderBy('course_user.enrolled_at', 'desc')
            ->get();

        // Lấy danh sách học viên bị khóa (blocked)
        $blockedStudents = $course->students()
            ->wherePivot('status', 'blocked')
            ->withPivot('enrolled_at', 'status')
            ->orderBy('course_user.enrolled_at', 'desc')
            ->get();

        // Thống kê
        $stats = [
            'pending' => $pendingStudents->count(),
            'approved' => $approvedStudents->total(),
            'rejected' => $rejectedStudents->count(),
            'blocked' => $blockedStudents->count(),
            'total' => $course->students()->count(),
        ];

        return view('teacher.courses.students.pending', compact(
            'course',
            'pendingStudents',
            'approvedStudents',
            'rejectedStudents',
            'blockedStudents',
            'stats'
        ));
    }

    /**
     * Duyệt học viên
     */
    public function approve(Course $course, $userId)
    {
        if ($course->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            // Kiểm tra xem học viên có tồn tại trong khóa học không
            $student = $course->students()->where('user_id', $userId)->first();

            if (!$student) {
                return response()->json(['error' => 'Học viên không tồn tại trong khóa học'], 404);
            }

            // Cập nhật status thành approved
            $course->students()->updateExistingPivot($userId, [
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id()
            ]);

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã duyệt học viên thành công'
                ]);
            }

            return back()->with('success', 'Đã duyệt học viên thành công');
        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Từ chối học viên
     */
    public function reject(Course $course, $userId)
    {
        if ($course->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $course->students()->updateExistingPivot($userId, [
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejected_by' => Auth::id(),
                'rejection_reason' => request('reason', '')
            ]);

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã từ chối học viên'
                ]);
            }

            return back()->with('success', 'Đã từ chối học viên');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Khóa học viên
     */
    public function block(Course $course, $userId)
    {
        if ($course->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $course->students()->updateExistingPivot($userId, [
                'status' => 'blocked',
                'blocked_at' => now(),
                'blocked_by' => Auth::id(),
                'block_reason' => request('reason', '')
            ]);

            DB::commit();

            return back()->with('success', 'Đã khóa học viên');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Mở khóa học viên
     */
    public function unblock(Course $course, $userId)
    {
        if ($course->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $course->students()->updateExistingPivot($userId, [
                'status' => 'approved',
                'blocked_at' => null,
                'blocked_by' => null,
                'block_reason' => null
            ]);

            DB::commit();

            return back()->with('success', 'Đã mở khóa học viên');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * API: Lấy danh sách học viên chờ duyệt (cho notification)
     */
    public function getPendingCount(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $count = $course->students()->wherePivot('status', 'pending')->count();

        return response()->json([
            'count' => $count,
            'course_id' => $course->id,
            'course_title' => $course->title
        ]);
    }

    public function allPending()
    {
        $teacherId = Auth::id();

        $pendingStudents = DB::table('course_user')
            ->join('users', 'course_user.user_id', '=', 'users.id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->where('courses.teacher_id', $teacherId)
            ->where('course_user.status', 'pending')
            ->select(
                'users.*',
                'courses.title as course_title',
                'courses.id as course_id',
                'course_user.enrolled_at'
            )
            ->orderBy('course_user.enrolled_at', 'desc')
            ->paginate(20);

        return view('teacher.students.pending-all', compact('pendingStudents'));
    }
}
