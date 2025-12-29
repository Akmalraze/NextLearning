<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_number',
        'photo_path',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get classes this student is enrolled in (for students)
     */
    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_students', 'student_id', 'class_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Get active enrolled class (student should have one active class)
     */
    public function activeClass()
    {
        return $this->belongsToMany(Classes::class, 'class_students', 'student_id', 'class_id')
            ->wherePivot('status', 'active')
            ->withTimestamps();
    }

    /**
     * Get teaching assignments (for teachers)
     */
    public function teachingAssignments()
    {
        return $this->hasMany(SubjectClassTeacher::class, 'teacher_id');
    }

    /**
     * Get classes this teacher teaches
     */
    public function teachingClasses()
    {
        return $this->belongsToMany(Classes::class, 'subject_class_teacher', 'teacher_id', 'class_id')
            ->withPivot('subject_id')
            ->withTimestamps();
    }

    /**
     * Get subjects this teacher teaches
     */
    public function teachingSubjects()
    {
        return $this->belongsToMany(Subjects::class, 'subject_class_teacher', 'teacher_id', 'subject_id')
            ->withPivot('class_id')
            ->withTimestamps();
    }

    /**
     * Get class where this teacher is homeroom teacher
     */
    public function homeroomClass()
    {
        return $this->hasOne(Classes::class, 'homeroom_teacher_id');
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Scope for students
     */
    public function scopeStudents($query)
    {
        return $query->role('Student');
    }

    /**
     * Scope for teachers
     */
    public function scopeTeachers($query)
    {
        return $query->role('Teacher');
    }

    /**
     * Scope for admins
     */
    public function scopeAdmins($query)
    {
        return $query->role('Admin');
    }
}
