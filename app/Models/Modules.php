<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Assessments;
use App\Models\Classes;
use App\Models\Materials;

class Modules extends Model
{
    use HasFactory;

    // Define which attributes can be mass-assigned
    protected $fillable = [
        'modules_name',
        'modules_description',
        'subject_id',
    ];

    // Relationship with Subject (Many-to-one)
    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    // Relationship with Assessments (Many-to-many)
    public function assessments()
    {
        return $this->belongsToMany(Assessments::class, 'assessment_module', 'module_id', 'assessment_id');
    }
}
