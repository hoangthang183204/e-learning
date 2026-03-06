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
        Schema::table('quiz_results', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_results', 'answers')) {
                $table->json('answers')->nullable()->after('passed');
            }
            if (!Schema::hasColumn('quiz_results', 'total_questions')) {
                $table->integer('total_questions')->default(0)->after('score');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_results', function (Blueprint $table) {
            $table->dropColumn(['answers', 'total_questions']);
        });
    }
};
