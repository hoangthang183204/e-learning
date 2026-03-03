<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::create([
            'title' => 'Laravel cơ bản',
            'description' => 'Khoá học Laravel cho người mới',
            'teacher_id' => 2,
            'status' => 1
        ]);
    }
}
