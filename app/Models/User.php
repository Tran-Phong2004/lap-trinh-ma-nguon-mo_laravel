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
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
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
}
