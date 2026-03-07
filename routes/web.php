<?php

use App\Http\Controllers\Teacher\QuizManageController;
use App\Http\Controllers\Teacher\DashboardTeacherController;
use App\Http\Controllers\Teacher\TeacherCourseController;
use App\Http\Controllers\Teacher\StudentProgressController;
use App\Http\Controllers\Teacher\LessonTeacherController;
use App\Http\Controllers\Teacher\CourseStudentApprovalController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StatisticsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Student\QuizStudentController;
use App\Http\Controllers\Student\CourseStudentController;
use App\Http\Controllers\Student\LessonStudentController;
use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Student\DashboardStudentController;

// ADMIN

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::resource('users', UserController::class);

        // Courses
        Route::resource('courses', CourseController::class);

        // Lessons
        Route::resource('lessons', LessonController::class);

        // Quizzes
        Route::resource('quizzes', QuizController::class);

        // Questions management
        Route::prefix('quizzes/{quiz}')->name('quizzes.')->group(function () {
            Route::get('questions', [QuizController::class, 'questions'])->name('questions');
            Route::post('questions', [QuizController::class, 'storeQuestion'])->name('questions.store');
            Route::get('results', [QuizController::class, 'results'])->name('results');
        });

        // Question AJAX
        Route::get('questions/{question}', [QuizController::class, 'getQuestion'])->name('questions.get');
        Route::put('questions/{question}', [QuizController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('questions/{question}', [QuizController::class, 'destroyQuestion'])->name('questions.destroy');
        Route::get('questions/{question}', [QuizController::class, 'getQuestion'])
            ->name('questions.get');

        // STATISTICS 
        Route::prefix('statistics')->name('statistics.')->group(function () {
            Route::get('/', [StatisticsController::class, 'index'])->name('index');
            Route::get('/users', [StatisticsController::class, 'users'])->name('users');
            Route::get('/courses', [StatisticsController::class, 'courses'])->name('courses');
            Route::get('/quizzes', [StatisticsController::class, 'quizzes'])->name('quizzes');
            Route::get('/revenue', [StatisticsController::class, 'revenue'])->name('revenue');
            Route::get('/export', [StatisticsController::class, 'export'])->name('export');
        });
    });

//=====================================================================================================================


// Student
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        // Dashboard
        Route::get('/', [App\Http\Controllers\Student\DashboardStudentController::class, 'index'])
            ->name('dashboard');

        // Courses
        Route::controller(CourseStudentController::class)->group(function () {
            Route::get('/courses', 'index')->name('courses.index');
            Route::get('/courses/{course}', 'show')->name('courses.show');
            Route::post('/courses/{course}/enroll', 'enroll')->name('courses.enroll');
            Route::delete('/courses/{course}/unenroll', 'unenroll')->name('courses.unenroll');
            Route::post('/courses/{course}/complete', 'complete')->name('courses.complete');
        });

        // Lessons
        Route::controller(LessonStudentController::class)->group(function () {
            Route::get('/lessons/{lesson}', 'show')->name('lessons.show');
            Route::post('/lessons/{lesson}/complete', 'complete')->name('lessons.complete');
        });

        // Quizzes
        Route::controller(QuizStudentController::class)->group(function () {
            Route::get('/quiz/{quiz}', 'show')->name('quiz.show');
            Route::post('/quiz/{quiz}/submit', 'submit')->name('quiz.submit');
            Route::get('/quiz/{quiz}/result', 'result')->name('quiz.result');
            Route::post('/quiz/{quiz}/retry', 'retry')->name('quiz.retry');
        });
        // Trong routes/web.php - thêm vào group student
        Route::prefix('certificates')->name('certificates.')->group(function () {
            Route::get('/', [CertificateController::class, 'index'])
                ->name('index');
            Route::get('/course/{course}', [CertificateController::class, 'show'])
                ->name('show');
            Route::get('/course/{course}/download', [CertificateController::class, 'download'])
                ->name('download');
        });

        Route::post('/certificates/{course}/generate', [CertificateController::class, 'generate'])
            ->name('certificates.generate');

        // Route công khai để xác thực chứng chỉ
        Route::get(
            '/certificates/verify/{certificateNumber}',
            [CertificateController::class, 'verify']
        )
            ->name('certificates.verify');

        // Trong routes/web.php, thêm vào group student
        Route::get('/courses/{course}/progress', [CourseStudentController::class, 'getProgress'])
            ->name('courses.progress');
    });


//====================================================================================================================

// Teacher


Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        // ===== 1. DASHBOARD =====
        Route::get('/', [DashboardTeacherController::class, 'index'])->name('dashboard');
        Route::get('/statistics', [DashboardTeacherController::class, 'statistics'])->name('statistics');
        Route::get('/courses/{course}/dashboard', [DashboardTeacherController::class, 'courseDashboard'])->name('courses.dashboard');

        // ===== 2. COURSE MANAGEMENT =====
        Route::resource('courses', TeacherCourseController::class);

        Route::get('/all-students', [TeacherCourseController::class, 'allStudents'])->name('all-students');

        // ===== 3. STUDENT MANAGEMENT (TRONG COURSE) =====
        Route::prefix('courses/{course}')->name('courses.')->group(function () {
            // Danh sách học viên
            Route::get('/students', [TeacherCourseController::class, 'students'])->name('students');
            Route::get('/students/add', [TeacherCourseController::class, 'addForm'])->name('students.add');
            Route::post('/students', [TeacherCourseController::class, 'add'])->name('students.store');
            Route::delete('/students/{user}', [TeacherCourseController::class, 'remove'])->name('students.remove');
            Route::get('/students/export', [TeacherCourseController::class, 'exportStudents'])->name('students.export');

            // Tiến độ học tập
            Route::get('/progress', [StudentProgressController::class, 'index'])->name('progress');
            Route::get('/progress/{student}', [StudentProgressController::class, 'show'])->name('progress.detail');

            // Kết quả quiz
            Route::get('/quiz-results', [TeacherCourseController::class, 'quizResults'])->name('quiz-results');
            Route::get('/quiz-results/{student}', [TeacherCourseController::class, 'quizResults'])->name('quiz-results.student');

            // Thống kê khóa học
            Route::get('/statistics', [TeacherCourseController::class, 'statistics'])->name('statistics');
            Route::get('/statistics/export', [TeacherCourseController::class, 'exportStatistics'])->name('statistics.export');
        });

        // ===== 4. LESSON MANAGEMENT =====
        Route::resource('courses.lessons', LessonTeacherController::class);

        // ===== 5. QUIZ MANAGEMENT =====
        Route::resource('quizzes', QuizManageController::class);

        // ===== 6. STUDENT APPROVAL =====
        // Approval trong từng khóa học
        Route::prefix('courses/{course}/students')->name('courses.students.')->group(function () {
            Route::get('/pending', [CourseStudentApprovalController::class, 'pending'])
                ->name('pending');
            Route::get('/pending-count', [CourseStudentApprovalController::class, 'getPendingCount'])
                ->name('pending-count');
            Route::post('/{userId}/approve', [CourseStudentApprovalController::class, 'approve'])
                ->name('approve');
            Route::post('/{userId}/reject', [CourseStudentApprovalController::class, 'reject'])
                ->name('reject');
            Route::post('/{userId}/block', [CourseStudentApprovalController::class, 'block'])
                ->name('block');
            Route::post('/{userId}/unblock', [CourseStudentApprovalController::class, 'unblock'])
                ->name('unblock');
        });

        // ===== 7. ALL PENDING STUDENTS =====
        // 👇 SỬA QUAN TRỌNG: Bỏ /teacher thừa, dùng students/pending/all
        Route::get('/teacher/students/pending/all', [CourseStudentApprovalController::class, 'allPending'])
            ->name('students.pending.all');  // Tên route: teacher.students.pending.all

        // 👇 Route API riêng cho count (nếu cần)
        Route::get('/teacher/students/pending/count', function () {
            $pendingCount = DB::table('course_user')
                ->join('courses', 'course_user.course_id', '=', 'courses.id')
                ->where('courses.teacher_id', auth()->id())
                ->where('course_user.status', 'pending')
                ->count();

            return response()->json(['count' => $pendingCount]);
        })->name('students.pending.count');
    });
//====================================================================================================================


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ===== LESSON ACCESS (yêu cầu đăng nhập) =====
Route::middleware(['auth'])->group(function () {
    Route::get('lessons/{lesson}', [LessonStudentController::class, 'show'])
        ->name('lessons.show')
        ->middleware('lesson.access');

    Route::post('lessons/{lesson}/complete', [LessonStudentController::class, 'complete'])
        ->name('lessons.complete');
});

// routes/web.php
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route quên mật khẩu (nếu có)
Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgot'])->name('password.email');

// Route::middleware(['auth', 'lesson.access'])
//     ->group(function () {

//         Route::get(
//             'lessons/{lesson}',
//             [LessonStudentController::class, 'show']
//         )->name('lessons.show');

//         Route::post(
//             'lessons/{lesson}/complete',
//             [LessonStudentController::class, 'complete']
//         )->name('lessons.complete');
//     });

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route::middleware(['auth', 'role:teacher'])->get('/teacher', [DashboardTeacherController::class, 'teacher']);
// Route::middleware(['auth', 'role:admin'])->get('/admin', [DashboardController::class, 'admin']);
// Route::middleware(['auth', 'role:student'])->get('/student', [DashboardStudentController::class, 'student']);
