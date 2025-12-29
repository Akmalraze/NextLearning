<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Class-Teacher assignments (master schedule)
    public function classAssignments()
    {
        return $this->hasMany(SubjectClassTeacher::class, 'subject_id');
    }

    // Classes where this subject is assigned
    public function assignedClasses()
    {
        return $this->belongsToMany(Classes::class, 'subject_class_teacher', 'subject_id', 'class_id')
            ->withPivot('teacher_id')
            ->withTimestamps();
    }

    // Teachers assigned to teach this subject
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_class_teacher', 'subject_id', 'teacher_id')
            ->withPivot('class_id')
            ->withTimestamps();
    }

    // Relationship with Modules (One-to-many)
    public function modules()
    {
        return $this->hasMany(Modules::class, 'subject_id', 'subjects_id');
    }

    // Relationship with Assessments (Many-to-many)
    public function assessments()
    {
        return $this->belongsToMany(Assessments::class, 'assessment_subject', 'subject_id', 'assessment_id');
    }
}
