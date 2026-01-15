<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subjects;
use App\Models\Material;

class SectionTitle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'order',
    ];

    // Relationship with Subject (Many-to-one)
    public function subject()
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    // Relationship with Materials (One-to-many)
    public function materials()
    {
        return $this->hasMany(Material::class, 'section_title_id');
    }
}
