<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    /**
     * Valid form levels (Form 1 to Form 5)
     */
    public const FORM_LEVELS = [1, 2, 3, 4, 5];

    /**
     * Common class names
     */
    public const CLASS_NAMES = ['Raya', 'Perkasa', 'Gemilang', 'Bestari', 'Cemerlang', 'Setia'];

    protected $fillable = [
        'form_level',
        'name',
        'academic_session',
        'homeroom_teacher_id',
    ];

    protected $casts = [
        'form_level' => 'integer',
    ];

    /**
     * Get the full class name (e.g., "Form 1 Raya")
     */
    public function getFullNameAttribute(): string
    {
        return "Form {$this->form_level} {$this->name}";
    }

    // Relationship with homeroom teacher
    public function homeroomTeacher()
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    // Relationship with Users (Many-to-one) - legacy
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Subject-Teacher assignments (master schedule)
    public function subjectAssignments()
    {
        return $this->hasMany(SubjectClassTeacher::class, 'class_id');
    }

    // Teachers assigned to this class
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_class_teacher', 'class_id', 'teacher_id')
            ->withPivot('subject_id')
            ->withTimestamps();
    }

    // Subjects assigned to this class
    public function assignedSubjects()
    {
        return $this->belongsToMany(Subjects::class, 'subject_class_teacher', 'class_id', 'subject_id')
            ->withPivot('teacher_id')
            ->withTimestamps();
    }

    // Active students in this class
    public function activeStudents()
    {
        return $this->belongsToMany(User::class, 'class_students', 'class_id', 'student_id')
            ->wherePivot('status', 'active');
    }
}
