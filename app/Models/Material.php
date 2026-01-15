<?php

namespace App\Models;
use App\Models\Modules;
use App\Models\Subjects;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    // Define which attributes can be mass-assigned
    protected $fillable = [
        'materials_name', 
        'file_path', // File path instead of materials_format
        'materials_uploadDate',
        'materials_notes',
        'module_id',
        'section_title_id',
        'subject_id'
    ];

    // Relationship with Module (Many-to-one)
    public function modules()
    {
        return $this->belongsTo(Modules::class);
    }

    // Relationship with SectionTitle (Many-to-one)
    public function sectionTitle()
    {
        return $this->belongsTo(SectionTitle::class, 'section_title_id');
    }

    // Relationship with Subject (Many-to-one)
    public function subjects()
    {
        return $this->belongsTo(Subjects::class);
    }
}
