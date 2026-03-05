<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardTeacherController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();

        // Các khóa học của giáo viên
        $courses = Course::where('teacher_id', $teacherId)
            ->withCount(['students', 'lessons'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($course) {
                // Tính tiến độ trung bình cho mỗi khóa học
                $avgProgress = $this->calculateAverageProgress($course->id);
                $course->avg_progress = $avgProgress;

                // Màu sắc ngẫu nhiên cho gradient
                $colors = $this->getRandomGradientColors();
                $course->color_1 = $colors[0];
                $course->color_2 = $colors[1];

                // Tính tổng thời lượng (giả định mỗi bài học 30 phút)
                $course->total_duration = $course->lessons_count * 30;

                // Trạng thái
                $course->status = $course->status ? 'published' : 'draft';

                return $course;
            });

        // Lấy ID của các khóa học
        $courseIds = $courses->pluck('id');

        // Lấy ID của các bài học thuộc các khóa học đó
        $lessonIds = Lesson::whereIn('course_id', $courseIds)->pluck('id');

        // Thống kê tổng quan
        $totalCourses = $courses->count();

        // Tổng số học viên (không trùng)
        $totalStudents = DB::table('course_user')
            ->whereIn('course_id', $courseIds)
            ->distinct('user_id')
            ->count('user_id');

        $totalLessons = Lesson::whereIn('course_id', $courseIds)->count();

        $totalQuizzes = Quiz::whereIn('lesson_id', $lessonIds)->count();

        // Tính toán growth (so sánh với tháng trước)
        $lastMonthCourses = Course::where('teacher_id', $teacherId)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();
        $courseGrowth = $lastMonthCourses > 0
            ? round((($totalCourses - $lastMonthCourses) / $lastMonthCourses) * 100, 1)
            : 100;

        $lastMonthStudents = DB::table('course_user')
            ->whereIn('course_id', $courseIds)
            ->whereMonth('enrolled_at', now()->subMonth()->month)
            ->distinct('user_id')
            ->count('user_id');
        $studentGrowth = $lastMonthStudents > 0
            ? round((($totalStudents - $lastMonthStudents) / $lastMonthStudents) * 100, 1)
            : 100;

        // Top 5 khóa học có nhiều học viên nhất
        $topCourses = $courses->sortByDesc('students_count')->take(5);

        // Tiến độ trung bình của tất cả khóa học
        $avgProgress = $courses->avg('avg_progress') ?? 0;

        // Bài quiz gần đây
        $recentQuizzes = Quiz::whereIn('lesson_id', $lessonIds)
            ->with('lesson.course')
            ->withCount('questions')
            ->withCount(['results as attempts_count'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get()
            ->map(function ($quiz) {
                $quiz->avg_score = round(QuizResult::where('quiz_id', $quiz->id)->avg('score') ?? 0, 1);
                return $quiz;
            });

        // Hoạt động gần đây
        $recentActivities = $this->getRecentActivities($teacherId, $courseIds);

        // Dữ liệu cho biểu đồ lượt xem (giả lập)
        $viewsData = $this->getViewsChartData($courseIds);

        // Dữ liệu cho biểu đồ phân bố học viên
        $distributionData = $this->getDistributionData($courseIds);

        return view('teacher.dashboard', compact(
            'courses',
            'totalCourses',
            'totalStudents',
            'totalLessons',
            'totalQuizzes',
            'courseGrowth',
            'studentGrowth',
            'topCourses',
            'avgProgress',
            'recentQuizzes',
            'recentActivities',
            'viewsData',
            'distributionData'
        ));
    }

    public function courseDashboard(Course $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem khóa học này');
        }

        $course->load(['lessons' => function ($q) {
            $q->orderBy('order_number');
        }]);

        $students = $course->students()->paginate(10);
        $totalStudents = $course->students()->count();

        $totalLessons = $course->lessons()->count();

        // Thống kê bài học đã hoàn thành
        $completedLessons = Lesson::where('course_id', $course->id)
            ->withCount('completedBy')
            ->get();

        $totalCompletedLessons = $completedLessons->sum('completed_by_count');
        $avgLessonProgress = $totalLessons > 0
            ? round(($totalCompletedLessons / ($totalStudents * $totalLessons)) * 100, 1)
            : 0;

        // Thống kê quiz
        $quizzes = Quiz::whereIn('lesson_id', $course->lessons->pluck('id'))
            ->withCount('questions')
            ->withCount(['results as attempts_count'])
            ->get()
            ->map(function ($quiz) {
                $quiz->avg_score = round(QuizResult::where('quiz_id', $quiz->id)->avg('score') ?? 0, 1);
                $quiz->pass_rate = round(
                    QuizResult::where('quiz_id', $quiz->id)
                        ->where('passed', 1)
                        ->count() / max(QuizResult::where('quiz_id', $quiz->id)->count(), 1) * 100,
                    1
                );
                return $quiz;
            });

        return view('teacher.courses.dashboard', compact(
            'course',
            'students',
            'totalStudents',
            'totalLessons',
            'completedLessons',
            'avgLessonProgress',
            'quizzes'
        ));
    }

    private function getRecentActivities($teacherId, $courseIds)
    {
        $activities = collect();

        // Học viên mới đăng ký
        $newEnrollments = DB::table('course_user')
            ->whereIn('course_id', $courseIds)
            ->join('users', 'course_user.user_id', '=', 'users.id')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->select(
                'users.name as user_name',
                'courses.title as course_title',
                'course_user.enrolled_at as created_at',
                DB::raw("'enrolled' as type"),
                DB::raw("'Đã đăng ký khóa học' as description")
            )
            ->orderBy('course_user.enrolled_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($newEnrollments as $item) {
            $activities->push((object)[
                'icon' => 'person-plus',
                'user_name' => $item->user_name,
                'description' => "đã đăng ký khóa học \"{$item->course_title}\"",
                'created_at' => $item->created_at,
                'type' => 'enrollment'
            ]);
        }

        // Bài học được hoàn thành
        $completedLessons = DB::table('lesson_user')
            ->whereIn('lesson_id', Lesson::whereIn('course_id', $courseIds)->pluck('id'))
            ->join('users', 'lesson_user.user_id', '=', 'users.id')
            ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
            ->join('courses', 'lessons.course_id', '=', 'courses.id')
            ->select(
                'users.name as user_name',
                'lessons.title as lesson_title',
                'courses.title as course_title',
                'lesson_user.completed_at as created_at'
            )
            ->orderBy('lesson_user.completed_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($completedLessons as $item) {
            $activities->push((object)[
                'icon' => 'check-circle',
                'user_name' => $item->user_name,
                'description' => "đã hoàn thành bài \"{$item->lesson_title}\" trong khóa \"{$item->course_title}\"",
                'created_at' => $item->created_at,
                'type' => 'completion'
            ]);
        }

        // Kết quả quiz
        $quizResults = DB::table('quiz_results')
            ->whereIn('quiz_id', Quiz::whereIn('lesson_id', Lesson::whereIn('course_id', $courseIds)->pluck('id'))->pluck('id'))
            ->join('users', 'quiz_results.user_id', '=', 'users.id')
            ->join('quizzes', 'quiz_results.quiz_id', '=', 'quizzes.id')
            ->join('lessons', 'quizzes.lesson_id', '=', 'lessons.id')
            ->join('courses', 'lessons.course_id', '=', 'courses.id')
            ->select(
                'users.name as user_name',
                'quizzes.title as quiz_title',
                'courses.title as course_title',
                'quiz_results.score',
                'quiz_results.passed',
                'quiz_results.created_at'
            )
            ->orderBy('quiz_results.created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($quizResults as $item) {
            $activities->push((object)[
                'icon' => $item->passed ? 'trophy' : 'x-circle',
                'user_name' => $item->user_name,
                'description' => "đã làm bài quiz \"{$item->quiz_title}\" trong khóa \"{$item->course_title}\" được {$item->score} điểm",
                'created_at' => $item->created_at,
                'type' => 'quiz'
            ]);
        }

        return $activities->sortByDesc('created_at')->take(10);
    }

    private function calculateAverageProgress($courseId)
    {
        $totalLessons = Lesson::where('course_id', $courseId)->count();
        if ($totalLessons == 0) return 0;

        $totalStudents = DB::table('course_user')
            ->where('course_id', $courseId)
            ->count();

        if ($totalStudents == 0) return 0;

        $completedLessons = DB::table('lesson_user')
            ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
            ->where('lessons.course_id', $courseId)
            ->count();

        return round(($completedLessons / ($totalStudents * $totalLessons)) * 100, 1);
    }

    private function getRandomGradientColors()
    {
        $gradients = [
            ['#4776E6', '#8E54E9'],
            ['#FF6B6B', '#FF8E53'],
            ['#4ECDC4', '#556270'],
            ['#FFD93D', '#FF8C42'],
            ['#6B8CFF', '#B15BFF'],
            ['#00B09B', '#96C93D'],
        ];

        return $gradients[array_rand($gradients)];
    }

    private function getViewsChartData($courseIds)
    {
        // Giả lập dữ liệu lượt xem trong 7 ngày
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('D');

            // Trong thực tế, bạn sẽ query từ bảng activity logs
            $data[] = rand(50, 150);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getDistributionData($courseIds)
    {
        // Phân bố học viên theo trạng thái
        return [
            'labels' => ['Đang học', 'Hoàn thành', 'Chưa bắt đầu'],
            'data' => [65, 20, 15] // Giả lập
        ];
    }


    public function statistics()
    {
        $teacherId = Auth::id();

        // Lấy tất cả khóa học
        $courses = Course::where('teacher_id', $teacherId)
            ->withCount(['students', 'lessons'])
            ->get()
            ->map(function ($course) {
                $course->avg_progress = $this->calculateAverageProgress($course->id);
                return $course;
            });

        $courseIds = $courses->pluck('id');

        // Thống kê
        $totalCourses = $courses->count();
        $totalStudents = DB::table('course_user')
            ->whereIn('course_id', $courseIds)
            ->distinct('user_id')
            ->count('user_id');
        $totalLessons = Lesson::whereIn('course_id', $courseIds)->count();
        $totalQuizzes = Quiz::whereIn(
            'lesson_id',
            Lesson::whereIn('course_id', $courseIds)->pluck('id')
        )->count();

        // Top courses
        $topCourses = $courses->sortByDesc('students_count')->take(5);

        // Recent activities
        $recentActivities = $this->getRecentActivities($teacherId, $courseIds);

        return view('teacher.statistics', compact(
            'totalCourses',
            'totalStudents',
            'totalLessons',
            'totalQuizzes',
            'topCourses',
            'recentActivities'
        ));
    }
}
