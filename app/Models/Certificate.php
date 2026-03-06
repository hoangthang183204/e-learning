<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $table = 'certificates';

    protected $fillable = [
        'certificate_number',
        'user_id',
        'course_id',
        'student_name',
        'course_name',
        'completion_date',
        'issued_at',
        'is_verified'
    ];

    protected $casts = [
        'completion_date' => 'datetime',
        'issued_at' => 'datetime',
        'is_verified' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
