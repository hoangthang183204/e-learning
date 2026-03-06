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
        Schema::table('course_user', function (Blueprint $table) {
            // Thêm các trạng thái mới nếu chưa có
            // pending, approved, rejected, blocked, finished

            // Thêm các trường thời gian
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->timestamp('blocked_at')->nullable()->after('rejected_at');

            // Thêm người thực hiện
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
            $table->unsignedBigInteger('blocked_by')->nullable()->after('blocked_at');

            // Thêm lý do
            $table->text('rejection_reason')->nullable()->after('rejected_by');
            $table->text('block_reason')->nullable()->after('blocked_by');

            // Khóa ngoại
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('blocked_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_user', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropForeign(['blocked_by']);

            $table->dropColumn([
                'approved_at',
                'rejected_at',
                'blocked_at',
                'approved_by',
                'rejected_by',
                'blocked_by',
                'rejection_reason',
                'block_reason'
            ]);
        });
    }
};
