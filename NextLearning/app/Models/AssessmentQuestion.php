<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'marks',
        'order',
    ];

    protected $casts = [
        'marks' => 'decimal:2',
        'order' => 'integer',
    ];

    /**
     * Get the assessment this question belongs to
     */
    public function assessment()
    {
        return $this->belongsTo(Assessments::class, 'assessment_id');
    }
}
