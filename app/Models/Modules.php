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

    // Relationship with Materials (One-to-many)
    // In Modules.php model
// In the Modules Model
public function materials()
{
    return $this->hasMany(Material::class, 'module_id');
}


}

