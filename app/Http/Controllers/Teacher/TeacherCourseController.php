<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TeacherCourseController extends Controller
{
    /**
     * Hiển thị danh sách khóa học
     */
    public function index()
    {
        $teacherId = Auth::id();

        $courses = Course::where('teacher_id', $teacherId)
            ->withCount(['students', 'lessons'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Tính tổng số học viên duy nhất
        $totalStudents = DB::table('course_user')
            ->whereIn('course_id', $courses->pluck('id'))
            ->distinct('user_id')
            ->count('user_id');

        // Tính completion rate cho mỗi khóa học
        foreach ($courses as $course) {
            $course->completion_rate = $this->calculateCompletionRate($course);
            $course->total_duration = $course->lessons_count * 30; // Giả định mỗi bài 30 phút
        }

        return view('teacher.courses.index', compact('courses', 'totalStudents'));
    }

    /**
     * Tính tỷ lệ hoàn thành khóa học
     */
    private function calculateCompletionRate($course)
    {
        $totalLessons = $course->lessons_count;
        if ($totalLessons == 0) return 0;

        $totalStudents = $course->students_count;
        if ($totalStudents == 0) return 0;

        $completedLessons = DB::table('lesson_user')
            ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
            ->where('lessons.course_id', $course->id)
            ->count();

        return round(($completedLessons / ($totalLessons * $totalStudents)) * 100);
    }

    /**
     * Hiển thị form tạo khóa học mới
     */
    public function create()
    {
        return view('teacher.courses.create');
    }

    public function store(Request $request)
    {
        // Validate chỉ với các trường có trong database
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1'
        ]);

        try {
            DB::beginTransaction();

            // Tạo khóa học - CHỈ với các trường có trong database
            $course = Course::create([
                'teacher_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            DB::commit();

            return redirect()
                ->route('teacher.courses.show', $course)
                ->with('success', 'Tạo khóa học thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }


    public function update(Request $request, Course $course)
    {
        // Kiểm tra quyền sở hữu
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa khóa học này');
        }

        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1'
        ]);

        try {
            DB::beginTransaction();

            $course->update([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            DB::commit();

            return redirect()
                ->route('teacher.courses.show', $course)
                ->with('success', 'Cập nhật khóa học thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }


    /**
     * Lấy màu gradient ngẫu nhiên
     */
    private function getRandomGradientColors()
    {
        $colors = [
            ['#4158D0', '#C850C0'],
            ['#0093E9', '#80D0C7'],
            ['#FBAB7E', '#F7CE68'],
            ['#85FFBD', '#FFFB7D'],
            ['#FF6B6B', '#FF8E53'],
            ['#4ECDC4', '#556270'],
            ['#FFD93D', '#FF8C42'],
            ['#A8E6CF', '#FFD3B5'],
        ];
        return $colors[array_rand($colors)];
    }

    /**
     * Hiển thị chi tiết khóa học
     */
    public function show(Course $course)
    {
        // Kiểm tra quyền sở hữu
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem khóa học này');
        }

        // Load dữ liệu
        $course->load(['lessons' => function ($q) {
            $q->orderBy('order_number');
        }]);

        // Thống kê
        $totalStudents = $course->students()->count();
        $totalLessons = $course->lessons()->count();
        $totalQuizzes = Quiz::whereIn('lesson_id', $course->lessons->pluck('id'))->count();

        // Tiến độ trung bình
        $avgProgress = 0;
        if ($totalLessons > 0 && $totalStudents > 0) {
            $completedLessons = DB::table('lesson_user')
                ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
                ->where('lessons.course_id', $course->id)
                ->count();
            $avgProgress = round(($completedLessons / ($totalLessons * $totalStudents)) * 100);
        }

        // Thống kê học viên mới
        $recentStudents = $course->students()
            ->orderBy('course_user.enrolled_at', 'desc')
            ->take(5)
            ->get();

        return view('teacher.courses.show', compact(
            'course',
            'totalStudents',
            'totalLessons',
            'totalQuizzes',
            'avgProgress',
            'recentStudents'
        ));
    }

    public function edit(Course $course)
    {
        // Kiểm tra quyền sở hữu
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa khóa học này');
        }

        $course->loadCount(['students', 'lessons']);

        return view('teacher.courses.edit', compact('course'));
    }


    public function destroy(Course $course)
    {
        // Kiểm tra quyền sở hữu
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xóa khóa học này');
        }

        try {
            DB::beginTransaction();

            // Kiểm tra xem có bài học không
            if ($course->lessons()->count() > 0) {
                return back()->with('error', 'Không thể xóa khóa học đã có bài học. Vui lòng xóa bài học trước.');
            }

            // Xóa thumbnail
            if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                Storage::disk('public')->delete($course->thumbnail);
            }

            // Xóa các bản ghi liên quan
            $course->students()->detach();

            $course->delete();

            DB::commit();

            return redirect()
                ->route('teacher.courses.index')
                ->with('success', 'Đã xóa khóa học thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách học viên của khóa học
     */
    public function students(Course $course)
    {
        // Kiểm tra quyền sở hữu
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem học viên của khóa học này');
        }

        $students = $course->students()
            ->withPivot('enrolled_at')
            ->orderBy('course_user.enrolled_at', 'desc')
            ->paginate(10);

        // Thống kê trạng thái
        $totalStudents = $course->students()->count();

        // Giả sử có các trường status trong bảng course_user
        $pendingCount = 0; // Thay bằng logic thực tế nếu có
        $approvedCount = $totalStudents;
        $blockedCount = 0;

        return view('teacher.courses.students', compact(
            'course',
            'students',
            'totalStudents',
            'pendingCount',
            'approvedCount',
            'blockedCount'
        ));
    }

    /**
     * Hiển thị form thêm học viên
     */
    public function addForm(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        return view('teacher.courses.add-students', compact('course'));
    }

    /**
     * Thêm học viên vào khóa học
     */
    public function add(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id'
        ]);

        $course->students()->syncWithoutDetaching($request->student_ids);

        return redirect()
            ->route('teacher.courses.students', $course)
            ->with('success', 'Đã thêm học viên thành công!');
    }

    /**
     * Xóa học viên khỏi khóa học
     */
    public function remove(Course $course, $userId)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $course->students()->detach($userId);

        return back()->with('success', 'Đã xóa học viên khỏi khóa học!');
    }

    /**
     * Hiển thị kết quả quiz của khóa học
     */
    public function quizResults(Course $course)
    {
        // Kiểm tra quyền sở hữu
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $lessonIds = $course->lessons()->pluck('id');

        $results = QuizResult::whereIn('quiz_id', function ($query) use ($lessonIds) {
            $query->select('id')
                ->from('quizzes')
                ->whereIn('lesson_id', $lessonIds);
        })
            ->with(['user', 'quiz.lesson'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Thống kê
        $totalAttempts = QuizResult::whereIn('quiz_id', function ($query) use ($lessonIds) {
            $query->select('id')->from('quizzes')->whereIn('lesson_id', $lessonIds);
        })->count();

        $avgScore = QuizResult::whereIn('quiz_id', function ($query) use ($lessonIds) {
            $query->select('id')->from('quizzes')->whereIn('lesson_id', $lessonIds);
        })->avg('score');

        $passRate = QuizResult::whereIn('quiz_id', function ($query) use ($lessonIds) {
            $query->select('id')->from('quizzes')->whereIn('lesson_id', $lessonIds);
        })->where('passed', 1)->count();

        $passRate = $totalAttempts > 0 ? round(($passRate / $totalAttempts) * 100, 1) : 0;

        return view('teacher.courses.quiz-results', compact('course', 'results', 'totalAttempts', 'avgScore', 'passRate'));
    }

    /**
     * Xuất danh sách học viên ra Excel
     */
    public function exportStudents(Course $course)
    {
        // Kiểm tra quyền sở hữu
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $students = $course->students()
            ->withPivot('enrolled_at')
            ->orderBy('course_user.enrolled_at', 'desc')
            ->get();

        // TODO: Implement Excel export
        // Bạn có thể sử dụng Laravel Excel package ở đây

        return back()->with('success', 'Tính năng đang được phát triển.');
    }

    public function statistics(Course $course)
    {
        // Kiểm tra quyền sở hữu
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem thống kê của khóa học này');
        }

        // Thống kê tổng quan
        $totalStudents = $course->students()->count();
        $totalLessons = $course->lessons()->count();
        $totalQuizzes = Quiz::whereIn('lesson_id', $course->lessons()->pluck('id'))->count();

        // Tiến độ trung bình
        $avgProgress = 0;
        if ($totalLessons > 0 && $totalStudents > 0) {
            $completedLessons = DB::table('lesson_user')
                ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
                ->where('lessons.course_id', $course->id)
                ->count();
            $avgProgress = round(($completedLessons / ($totalLessons * $totalStudents)) * 100);
        }

        // Thống kê học viên theo ngày đăng ký (30 ngày gần nhất)
        $enrollmentStats = DB::table('course_user')
            ->where('course_id', $course->id)
            ->where('enrolled_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(enrolled_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Phân bố tiến độ học tập
        $students = $course->students()->with('completedLessons')->get();
        $progressRanges = [
            '0-25' => 0,
            '25-50' => 0,
            '50-75' => 0,
            '75-100' => 0
        ];

        foreach ($students as $student) {
            $completed = $student->completedLessons->count();
            $percent = $totalLessons > 0 ? ($completed / $totalLessons) * 100 : 0;

            if ($percent < 25) $progressRanges['0-25']++;
            elseif ($percent < 50) $progressRanges['25-50']++;
            elseif ($percent < 75) $progressRanges['50-75']++;
            else $progressRanges['75-100']++;
        }

        // Bài học được hoàn thành nhiều nhất
        $popularLessons = DB::table('lesson_user')
            ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
            ->where('lessons.course_id', $course->id)
            ->select('lessons.title', DB::raw('count(*) as completed_count'))
            ->groupBy('lessons.id', 'lessons.title')
            ->orderBy('completed_count', 'desc')
            ->limit(5)
            ->get();

        // Thống kê kết quả quiz
        $quizStats = [];
        $quizzes = Quiz::whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->withCount('results')
            ->get();

        foreach ($quizzes as $quiz) {
            $avgScore = $quiz->results()->avg('score') ?? 0;
            $passCount = $quiz->results()->where('passed', true)->count();
            $totalAttempts = $quiz->results_count;

            $quizStats[] = [
                'title' => $quiz->title,
                'attempts' => $totalAttempts,
                'avg_score' => round($avgScore, 1),
                'pass_rate' => $totalAttempts > 0 ? round(($passCount / $totalAttempts) * 100, 1) : 0
            ];
        }

        return view('teacher.courses.statistics', compact(
            'course',
            'totalStudents',
            'totalLessons',
            'totalQuizzes',
            'avgProgress',
            'enrollmentStats',
            'progressRanges',
            'popularLessons',
            'quizStats'
        ));
    }
    public function allStudents()
    {
        $teacherId = Auth::id();

        // Lấy tất cả học viên từ tất cả khóa học của teacher
        $students = DB::table('course_user')
            ->join('users', 'course_user.user_id', '=', 'users.id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->where('courses.teacher_id', $teacherId)
            ->select(
                'users.*',
                'courses.title as course_title',
                'course_user.enrolled_at',
                'course_user.course_id'
            )
            ->orderBy('course_user.enrolled_at', 'desc')
            ->paginate(15);

        return view('teacher.all-students', compact('students'));
    }
}
