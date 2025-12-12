<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectClassTeacher extends Model
{
    use HasFactory;

    protected $table = 'subject_class_teacher';

    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
    ];

    /**
     * Get the class for this assignment
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the subject for this assignment
     */
    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    /**
     * Get the teacher for this assignment
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
