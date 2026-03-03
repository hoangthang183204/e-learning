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
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\QuizController;
use App\Http\Controllers\Student\CourseStudentController;
use App\Http\Controllers\Student\LessonStudentController;
use App\Http\Controllers\Student\DashboardStudentController;

// ADMIN

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.layout');
    });
});

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

Route::get('/login', function () {
    return view('auth.login');
})->name('login');


Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/courses', [CourseController::class, 'index'])->name('admin.courses.index');
        Route::get('/courses/create', [CourseController::class, 'create'])->name('admin.courses.create');
        Route::post('/courses', [CourseController::class, 'store'])->name('admin.courses.store');
        Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('admin.courses.edit');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('admin.courses.update');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('admin.courses.destroy');
    });

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::resource('lessons', LessonController::class)
        ->names('admin.lesson');
});

//=====================================================================================================================


// Student

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student', function () {
        return view('student.dashboard');
    });
    Route::get('/quiz/{id}', [QuizController::class, 'show']);
    Route::post('/quiz/{id}/submit', [QuizController::class, 'submit']);
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
            [QuizController::class, 'show']
        )->name('quiz.show');

        // Nộp bài quiz
        Route::post(
            'quiz/{quiz}/submit',
            [QuizController::class, 'submit']
        )->name('quiz.submit');
    });

// routes/web.php
Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])
    ->name('student.quiz.submit');

Route::get('/quiz/{quiz}/result', [QuizController::class, 'result'])
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

Route::middleware(['auth', 'role:teacher'])->get('/teacher', [DashboardTeacherController::class, 'teacher']);
Route::middleware(['auth', 'role:admin'])->get('/admin', [DashboardController::class, 'admin']);
Route::middleware(['auth', 'role:student'])->get('/student', [DashboardStudentController::class, 'student']);
