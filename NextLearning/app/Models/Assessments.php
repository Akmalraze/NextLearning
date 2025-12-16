<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessments extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'class_id',
        'subject_id',
        'teacher_id',
        'start_date',
        'end_date',
        'due_date',
        'total_marks',
        'is_published',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'due_date' => 'date',
        'total_marks' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    /**
     * Get the class this assessment belongs to
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the subject this assessment belongs to
     */
    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    /**
     * Get the teacher who created this assessment
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get questions for this assessment (for quizzes)
     */
    public function questions()
    {
        return $this->hasMany(AssessmentQuestion::class, 'assessment_id')->orderBy('order');
    }

    /**
     * Get materials for this assessment (for tests and homework)
     */
    public function materials()
    {
        return $this->hasMany(AssessmentMaterial::class, 'assessment_id');
    }
}
