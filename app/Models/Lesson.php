<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'video_url',
        'order_number',
    ];

    // ✅ QUAN HỆ ĐÚNG
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class)
            ->withPivot('completed_at');
    }
    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }
    public function completedByUsers()
    {
        return $this->belongsToMany(
            User::class,
            'lesson_user'
        )->withPivot('completed_at');
    }

    
}
