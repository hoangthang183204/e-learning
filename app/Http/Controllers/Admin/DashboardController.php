<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalStudents = User::where('role', 'student')->count();
        $totalCourses = Course::count();
        $totalLessons = Lesson::count();
        $totalQuizzes = Quiz::count();

        $recentUsers = User::latest()->take(5)->get();
        $recentCourses = Course::with('teacher')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalTeachers',
            'totalStudents',
            'totalCourses',
            'totalLessons',
            'totalQuizzes',
            'recentUsers',
            'recentCourses'
        ));
    }
}
