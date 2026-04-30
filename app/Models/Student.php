<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'admission_number',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'enrollment_date',
        'status',
        'current_class_room_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'enrollment_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function guardians()
    {
        return $this->belongsToMany(Guardian::class, 'student_guardian')->withPivot(['is_primary', 'notes']);
    }
    public function currentClassRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'current_class_room_id');
    }
    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class);
    }
    public function grades()
    {
        return $this->hasMany(StudentGrade::class);
    }
    public function feeAccounts()
    {
        return $this->hasMany(StudentFeeAccount::class);
    }
    public function gradeHistories()
    {
        return $this->hasMany(StudentGradeHistory::class);
    }
    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class);
    }

    // Status Constants
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_PROMOTED = 'PROMOTED';
    public const STATUS_TRANSFERRED = 'TRANSFERRED';
    public const STATUS_WITHDRAWN = 'WITHDRAWN';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_FAILED,
            self::STATUS_PROMOTED,
            self::STATUS_TRANSFERRED,
            self::STATUS_WITHDRAWN,
        ];
    }

    // Check if student is in final year
    public function isInFinalYear(): bool
    {
        if (!$this->currentClassRoom) {
            return false;
        }
        
        // Get the highest form number from all class rooms
        $maxFormNumber = ClassRoom::where('name', 'LIKE', 'Form %')
            ->get()
            ->map(function ($class) {
                if (preg_match('/Form (\d+)/', $class->name, $matches)) {
                    return (int) $matches[1];
                }
                return 0;
            })
            ->max();

        // Extract form number from current class
        if (preg_match('/Form (\d+)/', $this->currentClassRoom->form, $matches)) {
            $currentFormNumber = (int) $matches[1];
            return $currentFormNumber === $maxFormNumber;
        }
        return false;
    }

    // Check if student can be promoted
    public function canBePromoted(): bool
    {
        return $this->is_active && 
               $this->status === self::STATUS_ACTIVE && 
               !$this->isInFinalYear();
    }

    // Deactivate student
    public function deactivate(string $reason = null): void
    {
        $this->update([
            'is_active' => false,
            'status' => self::STATUS_INACTIVE,
        ]);

        if ($reason) {
            AuditLog::create([
                'user_id' => auth()?->id(),
                'action' => 'DEACTIVATED',
                'subject_type' => 'Student',
                'subject_id' => $this->id,
                'notes' => json_encode(['reason' => $reason]),
            ]);
        }
    }
}
