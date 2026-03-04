<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'lesson_id',
        'description',
        'time_limit',
        'pass_score',
        'attempts_allowed'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }


    public function results()
    {
        return $this->hasMany(QuizResult::class);
    }
    public function getQuestionsCountAttribute()
    {
        return $this->questions()->count();
    }

    public function getTotalPointsAttribute()
    {
        return $this->questions()->sum('points');
    }
}
