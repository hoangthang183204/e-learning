<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // TẮT KHÓA NGOẠI
        Schema::disableForeignKeyConstraints();

        // Xóa bảng questions trước (vì nó phụ thuộc vào quizzes)
        Schema::dropIfExists('questions');

        // Xóa bảng quizzes
        Schema::dropIfExists('quizzes');

        // Tạo lại bảng quizzes với cấu trúc đúng
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('time_limit')->default(30);
            $table->integer('pass_score')->default(70);
            $table->integer('attempts_allowed')->default(1);
            $table->timestamps();
        });

        // Tạo lại bảng questions
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->integer('points')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
        Schema::enableForeignKeyConstraints();
    }
};
