<?php
// app/Http/Controllers/Admin/StatisticsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\CourseUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index()
    {
        // Thống kê tổng quan
        $totalUsers = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalCourses = Course::count();
        $totalLessons = Lesson::count();
        $totalQuizzes = Quiz::count();

        // Thống kê hôm nay
        $today = Carbon::today();
        $newUsersToday = User::whereDate('created_at', $today)->count();
        $newCoursesToday = Course::whereDate('created_at', $today)->count();

        // Thống kê 7 ngày gần nhất
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $newUsersWeek = User::where('created_at', '>=', $sevenDaysAgo)->count();
        $activeUsersWeek = User::whereHas('quizResults', function ($q) use ($sevenDaysAgo) {
            $q->where('created_at', '>=', $sevenDaysAgo);
        })->count();

        // Top khóa học
        $topCourses = Course::withCount('students')
            ->with('teacher')
            ->orderBy('students_count', 'desc')
            ->take(5)
            ->get();

        // Thống kê quiz
        $quizStats = [
            'total_attempts' => QuizResult::count(),
            'passed_attempts' => QuizResult::where('passed', true)->count(),
            'avg_score' => round(QuizResult::avg('score') ?? 0, 2),
        ];
        $quizStats['pass_rate'] = $quizStats['total_attempts'] > 0
            ? round(($quizStats['passed_attempts'] / $quizStats['total_attempts']) * 100, 2)
            : 0;

        return view('admin.statistics.index', compact(
            'totalUsers',
            'totalStudents',
            'totalTeachers',
            'totalCourses',
            'totalLessons',
            'totalQuizzes',
            'newUsersToday',
            'newCoursesToday',
            'newUsersWeek',
            'activeUsersWeek',
            'topCourses',
            'quizStats'
        ));
    }


    public function users(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $registrations = User::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Phân bố theo role
        $roleDistribution = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->get();

        // Top học viên tích cực
        $activeStudents = User::where('role', 'student')
            ->withCount(['completedLessons', 'quizResults'])
            ->orderBy('quiz_results_count', 'desc')
            ->take(10)
            ->get();

        // Thống kê theo tháng
        $monthlyStats = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as total')
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return view('admin.statistics.users', compact(
            'registrations',
            'roleDistribution',
            'activeStudents',
            'monthlyStats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Thống kê chi tiết khóa học
     */
    public function courses(Request $request)
    {
        $courseId = $request->get('course_id');

        // Danh sách khóa học để chọn
        $courses = Course::with('teacher')
            ->withCount(['students', 'lessons'])
            ->get();

        $selectedCourse = null;
        $lessonStats = [];
        $studentProgress = [];
        $enrollmentStats = [];

        if ($courseId) {
            $selectedCourse = Course::with(['teacher', 'lessons'])->find($courseId);

            if ($selectedCourse) {
                // Thống kê bài học
                $lessonStats = Lesson::where('course_id', $courseId)
                    ->withCount('completedBy')
                    ->get();

                // Tiến độ học viên
                $studentProgress = CourseUser::where('course_id', $courseId)
                    ->with('user')
                    ->withCount(['user.completedLessons' => function ($q) use ($courseId) {
                        $q->whereHas('lesson', function ($q) use ($courseId) {
                            $q->where('course_id', $courseId);
                        });
                    }])
                    ->get()
                    ->map(function ($item) use ($selectedCourse) {
                        $totalLessons = $selectedCourse->lessons->count();
                        $item->progress = $totalLessons > 0
                            ? round(($item->completed_lessons_count / $totalLessons) * 100, 2)
                            : 0;
                        return $item;
                    });

                // Thống kê đăng ký theo ngày
                $enrollmentStats = CourseUser::where('course_id', $courseId)
                    ->select(DB::raw('DATE(enrolled_at) as date'), DB::raw('count(*) as total'))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
            }
        }

        return view('admin.statistics.courses', compact(
            'courses',
            'selectedCourse',
            'lessonStats',
            'studentProgress',
            'enrollmentStats',
            'courseId'
        ));
    }

    /**
     * Thống kê chi tiết bài kiểm tra
     */
    public function quizzes(Request $request)
    {
        $quizId = $request->get('quiz_id');

        $quizzes = Quiz::with('lesson.course')
            ->withCount('questions')
            ->get();

        $selectedQuiz = null;
        $results = collect();
        $scoreDistribution = [];
        $dailyStats = [];
        $topStudents = [];

        if ($quizId) {
            $selectedQuiz = Quiz::with('lesson.course')->find($quizId);

            if ($selectedQuiz) {
                // Kết quả gần đây
                $results = QuizResult::where('quiz_id', $quizId)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

                // Phân phối điểm
                $scoreRanges = [
                    '0-20' => [0, 20],
                    '21-40' => [21, 40],
                    '41-60' => [41, 60],
                    '61-80' => [61, 80],
                    '81-100' => [81, 100]
                ];

                foreach ($scoreRanges as $range => $bounds) {
                    $scoreDistribution[$range] = QuizResult::where('quiz_id', $quizId)
                        ->whereBetween('score', [$bounds[0], $bounds[1]])
                        ->count();
                }

                // Thống kê theo ngày
                $dailyStats = QuizResult::where('quiz_id', $quizId)
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('count(*) as attempts'),
                        DB::raw('avg(score) as avg_score'),
                        DB::raw('sum(case when passed = 1 then 1 else 0 end) as passed')
                    )
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                // Top học viên
                $topStudents = QuizResult::where('quiz_id', $quizId)
                    ->with('user')
                    ->select('user_id', DB::raw('max(score) as best_score'), DB::raw('count(*) as attempts'))
                    ->groupBy('user_id')
                    ->orderBy('best_score', 'desc')
                    ->take(10)
                    ->get();
            }
        }

        return view('admin.statistics.quizzes', compact(
            'quizzes',
            'selectedQuiz',
            'results',
            'scoreDistribution',
            'dailyStats',
            'topStudents',
            'quizId'
        ));
    }

    /**
     * Thống kê doanh thu (nếu có)
     */
    public function revenue(Request $request)
    {
        // Tạm thời redirect về trang tổng quan
        return redirect()->route('admin.statistics.index')
            ->with('info', 'Tính năng thống kê doanh thu đang được phát triển');
    }

    /**
     * Xuất báo cáo
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'users');
        $format = $request->get('format', 'pdf');

        return back()->with('success', 'Đang tạo báo cáo...');
    }
}
