<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseUser extends Model
{
    use HasFactory;

    protected $table = 'course_user'; // Tên bảng trong database

    protected $fillable = [
        'user_id',
        'course_id',
        'enrolled_at'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime'
    ];

    public $timestamps = false; // Vì bảng này không có created_at, updated_at

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
