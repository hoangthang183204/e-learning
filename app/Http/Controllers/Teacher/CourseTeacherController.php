<?php
// app/Http/Controllers/Teacher/TeacherCourseController.php

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

        // Tính tổng số học viên
        $totalStudents = Course::where('teacher_id', $teacherId)
            ->with('students')
            ->get()
            ->pluck('students')
            ->flatten()
            ->unique('id')
            ->count();

        return view('teacher.courses.index', compact('courses', 'totalStudents'));
    }

    /**
     * Hiển thị form tạo khóa học mới
     */
    public function create()
    {
        return view('teacher.courses.create');
    }

    /**
     * Lưu khóa học mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|min:50|max:2000',
            'short_description' => 'nullable|max:500',
            'category' => 'nullable|string|max:100',
            'level' => 'nullable|in:beginner,intermediate,advanced,all',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'max_students' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'what_will_learn' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'has_certificate' => 'boolean',
            'is_featured' => 'boolean',
            'allow_discussion' => 'boolean',
            'language' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'tags' => 'nullable|json',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'video_intro' => 'nullable|url',
            'status' => 'required|in:0,1,2',
            'publish_at' => 'nullable|date|required_if:status,2',
            'color_1' => 'nullable|string',
            'color_2' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Xử lý tags
            $tags = $request->tags ? json_decode($request->tags, true) : [];

            // Upload thumbnail
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('courses/thumbnails', 'public');
            }

            // Tạo slug từ title
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $count = 1;
            while (Course::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            // Tạo màu gradient ngẫu nhiên nếu không có
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
            $randomColors = $colors[array_rand($colors)];

            $course = Course::create([
                'teacher_id' => Auth::id(),
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'short_description' => $request->short_description,
                'category' => $request->category,
                'level' => $request->level,
                'price' => $request->price ?? 0,
                'duration' => $request->duration ?? 0,
                'max_students' => $request->max_students,
                'requirements' => $request->requirements,
                'what_will_learn' => $request->what_will_learn,
                'target_audience' => $request->target_audience,
                'has_certificate' => $request->has_certificate ? 1 : 0,
                'is_featured' => $request->is_featured ? 1 : 0,
                'allow_discussion' => $request->allow_discussion ? 1 : 1,
                'language' => $request->language ?? 'vi',
                'subtitle' => $request->subtitle,
                'tags' => $tags,
                'thumbnail' => $thumbnailPath,
                'video_intro' => $request->video_intro,
                'status' => $request->status,
                'publish_at' => $request->status == 2 ? $request->publish_at : null,
                'color_1' => $request->color_1 ?? $randomColors[0],
                'color_2' => $request->color_2 ?? $randomColors[1],
            ]);

            DB::commit();

            return redirect()
                ->route('teacher.courses.show', $course)
                ->with('success', 'Tạo khóa học thành công! Bạn có thể thêm bài học ngay bây giờ.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
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
                ->whereIn('lesson_id', $course->lessons->pluck('id'))
                ->count();
            $avgProgress = round(($completedLessons / ($totalLessons * $totalStudents)) * 100);
        }

        return view('teacher.courses.show', compact(
            'course',
            'totalStudents',
            'totalLessons',
            'totalQuizzes',
            'avgProgress'
        ));
    }

    /**
     * Hiển thị form sửa khóa học
     */
    public function edit(Course $course)
    {
        // Kiểm tra quyền sở hữu
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa khóa học này');
        }

        return view('teacher.courses.edit', compact('course'));
    }

    /**
     * Cập nhật khóa học
     */
    public function update(Request $request, Course $course)
    {
        // Kiểm tra quyền sở hữu
        if ($course->teacher_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa khóa học này');
        }

        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|min:50|max:2000',
            'short_description' => 'nullable|max:500',
            'category' => 'nullable|string|max:100',
            'level' => 'nullable|in:beginner,intermediate,advanced,all',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'max_students' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'what_will_learn' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'has_certificate' => 'boolean',
            'is_featured' => 'boolean',
            'allow_discussion' => 'boolean',
            'language' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'tags' => 'nullable|json',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'video_intro' => 'nullable|url',
            'status' => 'required|in:0,1,2',
            'publish_at' => 'nullable|date|required_if:status,2',
            'color_1' => 'nullable|string',
            'color_2' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Xử lý tags
            $tags = $request->tags ? json_decode($request->tags, true) : [];

            // Upload thumbnail mới nếu có
            $thumbnailPath = $course->thumbnail;
            if ($request->hasFile('thumbnail')) {
                // Xóa thumbnail cũ
                if ($course->thumbnail && file_exists(storage_path('app/public/' . $course->thumbnail))) {
                    unlink(storage_path('app/public/' . $course->thumbnail));
                }
                $thumbnailPath = $request->file('thumbnail')->store('courses/thumbnails', 'public');
            }

            // Cập nhật slug nếu title thay đổi
            if ($course->title !== $request->title) {
                $slug = Str::slug($request->title);
                $originalSlug = $slug;
                $count = 1;
                while (Course::where('slug', $slug)->where('id', '!=', $course->id)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                $course->slug = $slug;
            }

            $course->update([
                'title' => $request->title,
                'description' => $request->description,
                'short_description' => $request->short_description,
                'category' => $request->category,
                'level' => $request->level,
                'price' => $request->price ?? 0,
                'duration' => $request->duration ?? 0,
                'max_students' => $request->max_students,
                'requirements' => $request->requirements,
                'what_will_learn' => $request->what_will_learn,
                'target_audience' => $request->target_audience,
                'has_certificate' => $request->has_certificate ? 1 : 0,
                'is_featured' => $request->is_featured ? 1 : 0,
                'allow_discussion' => $request->allow_discussion ? 1 : 1,
                'language' => $request->language ?? 'vi',
                'subtitle' => $request->subtitle,
                'tags' => $tags,
                'thumbnail' => $thumbnailPath,
                'video_intro' => $request->video_intro,
                'status' => $request->status,
                'publish_at' => $request->status == 2 ? $request->publish_at : null,
                'color_1' => $request->color_1 ?? $course->color_1,
                'color_2' => $request->color_2 ?? $course->color_2,
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
     * Xóa khóa học
     */
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
            if ($course->thumbnail && file_exists(storage_path('app/public/' . $course->thumbnail))) {
                unlink(storage_path('app/public/' . $course->thumbnail));
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
            ->with(['user', 'quiz'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('teacher.courses.quiz-results', compact('course', 'results'));
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
            ->withPivot('status', 'enrolled_at')
            ->get();

        // Logic xuất Excel ở đây
        // Bạn có thể sử dụng Laravel Excel package

        return back()->with('success', 'Đang xuất file Excel...');
    }

    /**
     * Thống kê chi tiết khóa học
     */
    public function statistics(Course $course)
    {
        // Kiểm tra quyền sở hữu
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        // Thống kê học viên theo ngày đăng ký
        $enrollmentStats = DB::table('course_user')
            ->where('course_id', $course->id)
            ->select(DB::raw('DATE(enrolled_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Thống kê kết quả quiz
        $quizStats = [];
        $quizzes = Quiz::whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->withCount('results')
            ->get();

        foreach ($quizzes as $quiz) {
            $quizStats[] = [
                'title' => $quiz->title,
                'attempts' => $quiz->results_count,
                'avg_score' => $quiz->results()->avg('score') ?? 0,
                'pass_rate' => $quiz->results()->count() > 0
                    ? round(($quiz->results()->where('passed', true)->count() / $quiz->results()->count()) * 100, 2)
                    : 0
            ];
        }

        return view('teacher.courses.statistics', compact('course', 'enrollmentStats', 'quizStats'));
    }
}
