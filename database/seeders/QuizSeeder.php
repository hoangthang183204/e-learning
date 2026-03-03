<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Quiz;
use App\Models\Question;

use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quiz = Quiz::create([
            'lesson_id' => 1,
            'title' => 'Quiz Laravel cơ bản'
        ]);

        // Câu 1
        $q1 = Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'Laravel là gì?'
        ]);

        Option::insert([
            ['question_id' => $q1->id, 'option_text' => 'Framework PHP', 'is_correct' => 1],
            ['question_id' => $q1->id, 'option_text' => 'Ngôn ngữ lập trình', 'is_correct' => 0],
            ['question_id' => $q1->id, 'option_text' => 'Hệ điều hành', 'is_correct' => 0],
            ['question_id' => $q1->id, 'option_text' => 'Trình duyệt', 'is_correct' => 0],
        ]);

        // Câu 2
        $q2 = Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'Laravel dùng mô hình nào?'
        ]);

        Option::insert([
            ['question_id' => $q2->id, 'option_text' => 'MVC', 'is_correct' => 1],
            ['question_id' => $q2->id, 'option_text' => 'MVP', 'is_correct' => 0],
            ['question_id' => $q2->id, 'option_text' => 'MVVM', 'is_correct' => 0],
            ['question_id' => $q2->id, 'option_text' => 'None', 'is_correct' => 0],
        ]);
    }
}
