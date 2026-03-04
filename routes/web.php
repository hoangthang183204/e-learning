<?php

use App\Http\Controllers\Teacher\QuizManageController;
use App\Http\Controllers\Teacher\DashboardTeacherController;
use App\Http\Controllers\Teacher\TeacherCourseController;
use App\Http\Controllers\Teacher\StudentProgressController;
use App\Http\Controllers\Teacher\CourseTeacherController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\DashboardController;
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
        Route::get(
            '/dashboard',
            [DashboardTeacherController::class, 'teacher']
        )->name('dashboard');

        // Dashboard chi tiết khoá học
        Route::get(
            'courses/{course}/dashboard',
            [DashboardTeacherController::class, 'courseDashboard']
        )->name('courses.dashboard');
    });

Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('teacher/quizzes', [QuizManageController::class, 'index']);
    Route::get('teacher/quizzes/create', [QuizManageController::class, 'create']);
    Route::post('teacher/quizzes', [QuizManageController::class, 'store']);
    Route::get(
        'teacher/quizzes/{quiz}',
        [QuizManageController::class, 'show']
    )->name('teacher.quizzes.show');
    Route::delete(
        'teacher/quizzes/{quiz}',
        [QuizManageController::class, 'destroy']
    )->name('teacher.quizzes.destroy');
    Route::get(
        'teacher/quizzes/{quiz}/edit',
        [QuizManageController::class, 'edit']
    )->name('teacher.quizzes.edit');

    Route::put(
        'teacher/quizzes/{quiz}',
        [QuizManageController::class, 'update']
    )->name('teacher.quizzes.update');
});

Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        Route::get('courses', [TeacherCourseController::class, 'index'])
            ->name('courses.index');

        Route::get('courses/{course}', [TeacherCourseController::class, 'show'])
            ->name('courses.show');

        Route::get('courses/{course}/edit', [TeacherCourseController::class, 'edit'])
            ->name('courses.edit');

        Route::put('courses/{course}', [TeacherCourseController::class, 'update'])
            ->name('courses.update');
    });

Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        Route::get(
            'courses/{course}/students',
            [CourseTeacherController::class, 'students']
        )->name('courses.students');
    });

Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        Route::get(
            'courses/{course}/progress',
            [StudentProgressController::class, 'index']
        )->name('courses.progress');
    });

Route::get(
    'teacher/courses/{course}/progress',
    [StudentProgressController::class, 'index']
)->name('teacher.courses.progress');

Route::get(
    'teacher/courses/{course}/quiz-results',
    [CourseController::class, 'quizResults']
)->name('teacher.courses.quiz_results');

Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        Route::post(
            'courses/{course}/students/{user}/approve',
            [CourseTeacherController::class, 'approve']
        )->name('courses.approve');

        Route::post(
            'courses/{course}/students/{user}/block',
            [CourseTeacherController::class, 'block']
        )->name('courses.block');
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
