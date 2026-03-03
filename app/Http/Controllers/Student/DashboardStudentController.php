<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class DashboardStudentController extends Controller
{
    public function student()
    {
        return view('student.dashboard');
    }
}
