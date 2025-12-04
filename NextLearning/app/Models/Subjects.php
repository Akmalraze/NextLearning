<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Assessments;
use App\Models\Classes;
use App\Models\Modules;

class Subjects extends Model
{
    use HasFactory;

    protected $fillable = [
        'subjects_name',
        'subjects_code',
        'subjects_totalStudent',
        'class_id',
    ];

    // Relationship with Class (Many-to-one)
    public function classModel()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'class_id');
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
