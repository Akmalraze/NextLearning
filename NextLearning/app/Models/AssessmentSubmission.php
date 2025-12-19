<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'student_id',
        'type',
        'attempt_number',
        'answers',
        'answer_file_path',
        'answer_original_name',
        'score',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'decimal:2',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessments::class, 'assessment_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
