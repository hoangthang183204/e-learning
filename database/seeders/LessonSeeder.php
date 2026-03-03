<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lesson::create([
            'course_id' => 1,
            'title' => 'Giới thiệu Laravel',
            'content' => 'Bài học nhập môn Laravel',
            'video_url' => 'https://www.youtube.com/watch?v=example'
        ]);
    }
}
