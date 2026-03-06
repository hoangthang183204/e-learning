<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Thêm dòng này

class DashboardStudentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Lấy ID các khóa học user đã đăng ký (approved)
        $enrolledCourseIds = $user->courses()
            ->wherePivot('status', 'approved')
            ->pluck('course_id')
            ->toArray();

        // Lấy ID các khóa học user đã hoàn thành
        $completedCourseIds = $user->courses()
            ->wherePivot('status', 'finished')
            ->pluck('course_id')
            ->toArray();

        // Thống kê
        $enrolledCourses = count($enrolledCourseIds);
        $completedCourses = count($completedCourseIds);

        // Tổng số quiz đã làm
        $totalQuizzes = Quiz::whereIn('lesson_id', function ($q) use ($enrolledCourseIds) {
            $q->select('id')
                ->from('lessons')
                ->whereIn('course_id', $enrolledCourseIds);
        })->count();

        $completedQuizzes = $user->quizResults()->count();
        $pendingQuizzes = $totalQuizzes - $completedQuizzes;

        // Điểm trung bình
        $avgScore = round($user->quizResults()->avg('score') ?? 0);

        // Tiến độ trung bình
        $totalLessons = Lesson::whereIn('course_id', $enrolledCourseIds)->count();
        $completedLessons = $user->completedLessons()
            ->whereIn('lesson_id', function ($q) use ($enrolledCourseIds) {
                $q->select('id')
                    ->from('lessons')
                    ->whereIn('course_id', $enrolledCourseIds);
            })->count();
        $avgProgress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        // Khóa học đang học (có tiến độ)
        $inProgressCourses = $user->courses()
            ->wherePivot('status', 'approved')
            ->withCount('lessons')
            ->get()
            ->map(function ($course) use ($user) {
                $completedLessons = $user->completedLessons()
                    ->whereIn('lesson_id', $course->lessons()->pluck('id'))
                    ->count();
                $course->progress = $course->lessons_count > 0
                    ? round(($completedLessons / $course->lessons_count) * 100)
                    : 0;
                return $course;
            })
            ->sortByDesc('progress')
            ->take(5);

        // Khóa học gợi ý (chưa đăng ký)
        $recommendedCourses = Course::where('status', 1)
            ->whereNotIn('id', $enrolledCourseIds)
            ->withCount('students')
            ->orderBy('students_count', 'desc')
            ->take(4)
            ->get();

        // Bài quiz sắp tới
        $upcomingQuizzes = Quiz::whereIn('lesson_id', function ($q) use ($enrolledCourseIds) {
            $q->select('id')
                ->from('lessons')
                ->whereIn('course_id', $enrolledCourseIds);
        })
            ->with(['lesson.course'])
            ->withCount('questions')
            ->whereNotIn('id', $user->quizResults()->pluck('quiz_id'))
            ->inRandomOrder()
            ->take(5)
            ->get()
            ->map(function ($quiz) {
                $quiz->deadline = now()->addDays(rand(1, 7));
                return $quiz;
            });

        // Hoạt động gần đây
        $recentActivities = $this->getRecentActivities($user);

        // Kết quả quiz gần đây
        $recentResults = $user->quizResults()
            ->with('quiz.lesson.course')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Thành tích
        $achievements = $this->getAchievements($user);

        return view('student.dashboard', compact(
            'enrolledCourses',
            'completedCourses',
            'totalQuizzes',
            'pendingQuizzes',
            'avgScore',
            'avgProgress',
            'inProgressCourses',
            'recommendedCourses',
            'upcomingQuizzes',
            'recentActivities',
            'recentResults',
            'achievements',
            'completedLessons',
            'completedQuizzes'
        ));
    }

    private function getRecentActivities($user)
    {
        $activities = collect();

        // Bài học vừa hoàn thành
        $completedLessons = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
            ->join('courses', 'lessons.course_id', '=', 'courses.id')
            ->select(
                'lessons.title as lesson_title',
                'courses.title as course_title',
                'lesson_user.completed_at as created_at',
                DB::raw("'lesson' as type"),
                DB::raw("CONCAT('Đã hoàn thành bài \"', lessons.title, '\" trong khóa \"', courses.title, '\"') as description")
            )
            ->orderBy('lesson_user.completed_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($completedLessons as $item) {
            $activities->push((object)[
                'type' => 'lesson',
                'description' => $item->description,
                'created_at' => $item->created_at ? Carbon::parse($item->created_at) : now() // Sửa lỗi
            ]);
        }

        // Quiz vừa làm
        $quizResults = $user->quizResults()
            ->with('quiz')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($result) {
                return (object)[
                    'type' => 'quiz',
                    'description' => "Đã làm bài quiz \"{$result->quiz->title}\" được {$result->score} điểm",
                    'created_at' => $result->created_at // Carbon instance
                ];
            });

        foreach ($quizResults as $item) {
            $activities->push($item);
        }

        return $activities->sortByDesc('created_at')->take(5);
    }

    private function getAchievements($user)
    {
        $achievements = collect();

        $totalQuizzes = $user->quizResults()->count();
        $passedQuizzes = $user->quizResults()->where('passed', 1)->count();
        $totalLessons = $user->completedLessons()->count();

        if ($totalLessons >= 10) {
            $achievements->push((object)[
                'name' => 'Học viên chăm chỉ',
                'icon' => 'star-fill',
                'color' => 'warning'
            ]);
        }

        if ($passedQuizzes >= 5) {
            $achievements->push((object)[
                'name' => 'Cao thủ quiz',
                'icon' => 'trophy-fill',
                'color' => 'success'
            ]);
        }

        if ($user->courses()->wherePivot('status', 'finished')->count() >= 1) {
            $achievements->push((object)[
                'name' => 'Tốt nghiệp',
                'icon' => 'award-fill',
                'color' => 'info'
            ]);
        }

        return $achievements;
    }
}