<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quizzes';

    protected $fillable = [
        'title',
        'lesson_id',
        'description',
        'time_limit',
        'pass_score',
        'attempts_allowed'
    ];

    protected $casts = [
        'time_limit' => 'integer',
        'pass_score' => 'integer',
        'attempts_allowed' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public $timestamps = true;

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

    public function getAttemptsCountAttribute()
    {
        return $this->results()->count();
    }

    public function getAverageScoreAttribute()
    {
        return round($this->results()->avg('score') ?? 0, 1);
    }

    public function getPassRateAttribute()
    {
        $total = $this->results()->count();
        if ($total == 0) return 0;

        $passed = $this->results()->where('passed', 1)->count();
        return round(($passed / $total) * 100, 1);
    }
}
