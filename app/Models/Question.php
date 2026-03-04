<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'quiz_id',
        'question',
        'points'
    ];

    protected $casts = [
        'points' => 'integer'
    ];

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Lấy đáp án đúng
    public function correctOption()
    {
        return $this->options()->where('is_correct', true)->first();
    }

    // Kiểm tra đáp án
    public function checkAnswer($optionId)
    {
        $option = $this->options()->find($optionId);
        return $option ? $option->is_correct : false;
    }
}
