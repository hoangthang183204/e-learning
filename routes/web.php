<?php

use App\Http\Controllers\Teacher\QuizManageController;
use App\Http\Controllers\Teacher\DashboardTeacherController;
use App\Http\Controllers\Teacher\TeacherCourseController;
use App\Http\Controllers\Teacher\StudentProgressController;
use App\Http\Controllers\Teacher\LessonTeacherController;
use App\Http\Controllers\Teacher\CourseTeacherController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StatisticsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\QuizStudentController;
use App\Http\Controllers\Student\CourseStudentController;
use App\Http\Controllers\Student\LessonStudentController;
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

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student', function () {
        return view('student.dashboard');
    });
    Route::get('/quiz/{id}', [QuizStudentController::class, 'show']);
    Route::post('/quiz/{id}/submit', [QuizStudentController::class, 'submit']);
});


Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::get('/courses', [CourseStudentController::class, 'index'])
            ->name('courses.index');

        Route::get('/courses/{course}', [CourseStudentController::class, 'show'])
            ->name('courses.show');

        Route::get('/lessons/{lesson}', [LessonStudentController::class, 'show'])
            ->name('lessons.show');
    });

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student', function () {
        return view('student.dashboard');
    });
});



Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::post(
            'courses/{course}/enroll',
            [CourseStudentController::class, 'enroll']
        )->name('courses.enroll');

        Route::get(
            'courses/{course}',
            [CourseStudentController::class, 'show']
        )->name('courses.show');

        Route::get(
            'lessons/{lesson}',
            [LessonStudentController::class, 'show']
        )->name('lessons.show');
    });


Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::post(
            'courses/{course}/enroll',
            [CourseStudentController::class, 'enroll']
        )->name('courses.enroll');

        Route::delete(
            'courses/{course}/unenroll',
            [CourseStudentController::class, 'unenroll']
        )->name('courses.unenroll');
    });

Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        // Hiển thị quiz
        Route::get(
            'quiz/{quiz}',
            [QuizStudentController::class, 'show']
        )->name('quiz.show');

        // Nộp bài quiz
        Route::post(
            'quiz/{quiz}/submit',
            [QuizStudentController::class, 'submit']
        )->name('quiz.submit');
    });

// routes/web.php
Route::post('/quiz/{quiz}/submit', [QuizStudentController::class, 'submit'])
    ->name('student.quiz.submit');

Route::get('/quiz/{quiz}/result', [QuizStudentController::class, 'result'])
    ->name('student.quiz.result');

Route::post(
    'lessons/{lesson}/complete',
    [LessonStudentController::class, 'complete']
)->name('lessons.complete');

//====================================================================================================================

// Teacher


Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        // ===== 1. DASHBOARD =====
        Route::get('/', [DashboardTeacherController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/statistics', [DashboardTeacherController::class, 'statistics'])->name('statistics');
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
    });
//====================================================================================================================


Route::middleware(['auth', 'lesson.access'])
    ->group(function () {

        Route::get(
            'lessons/{lesson}',
            [LessonStudentController::class, 'show']
        )->name('lessons.show');

        Route::post(
            'lessons/{lesson}/complete',
            [LessonStudentController::class, 'complete']
        )->name('lessons.complete');
    });

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route::middleware(['auth', 'role:teacher'])->get('/teacher', [DashboardTeacherController::class, 'teacher']);
// Route::middleware(['auth', 'role:admin'])->get('/admin', [DashboardController::class, 'admin']);
// Route::middleware(['auth', 'role:student'])->get('/student', [DashboardStudentController::class, 'student']);
