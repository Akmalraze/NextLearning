<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subjects;
use App\Models\Modules;

class Classes extends Model
{
    use HasFactory;

    // Define which attributes can be mass-assigned
    protected $fillable = [
        'class_name',
        'user_id',
        'subjects_id',
    ];

    // Relationship with Users (Many-to-one)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'users_id');
    }

    // Relationship with Subjects (One-to-many)
    public function subjects()
    {
        return $this->hasMany(Subjects::class, 'class_id', 'class_id');
    }
}
