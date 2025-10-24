<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Quan hệ với bảng role (N-N)
    // Quan hệ với Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Quan hệ với exam_sessions: 1 User - N ExamSession
    public function examSessions()
    {
        return $this->hasMany(ExamSession::class, 'student_id');
    }

    public function examAccessControls()
    {
        return $this->hasMany(ExamAccessControl::class, 'student_id');
    }

     // Kiểm tra role theo tên
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    // Kiểm tra nhiều role
    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            return $this->role && in_array($this->role->name, $roles);
        }
        return $this->hasRole($roles);
    }

    // Kiểm tra role cụ thể
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isTeacher()
    {
        return $this->hasRole('teacher');
    }

    public function isStudent()
    {
        return $this->hasRole('student');
    }

    // Lấy tên role
    public function getRoleName()
    {
        return $this->role ? $this->role->name : null;
    }

    // Accessor để dễ truy cập trong view
    public function getRoleNameAttribute()
    {
        return $this->getRoleName();
    }
}
