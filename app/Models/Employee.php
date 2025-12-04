<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id',
        'employee_code',
        'first_name',
        'last_name',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'position',
        'join_date',
        'photo',
        'is_department_manager',
        'annual_leave_balance',
        'sick_leave_balance',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'join_date' => 'date',
            'is_department_manager' => 'boolean',
            'annual_leave_balance' => 'decimal:2',
            'sick_leave_balance' => 'decimal:2',
        ];
    }

    protected $appends = ['full_name'];

    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function approvedLeaves()
    {
        return $this->hasMany(LeaveApplication::class, 'approved_by');
    }

    // Scopes
    public function scopeManagers($query)
    {
        return $query->where('is_department_manager', true);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('is_active', true);
        });
    }
}
