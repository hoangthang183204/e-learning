<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    protected $fillable = ['title', 'description', 'teacher_id', 'status'];
    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];


    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class)
            ->withPivot('enrolled_at');
    }

    public function students()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('enrolled_at');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOwnedBy($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }
}
