<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
    ];

    /**
     * Get the assessment this material belongs to
     */
    public function assessment()
    {
        return $this->belongsTo(Assessments::class, 'assessment_id');
    }
}
