<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassEnrollment extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['student_id', 'class_room_id', 'term_id', 'enrollment_date', 'status', 'enrollment_type', 'promoted_from_class_id', 'enrolled_by', 'promotion_reason'];

    protected function casts(): array
    {
        return ['enrollment_date' => 'date'];
    }

    // Enrollment Type Constants
    public const TYPE_AUTO = 'AUTO';
    public const TYPE_MANUAL = 'MANUAL';
    public const TYPE_REPEAT = 'REPEAT';
    public const TYPE_TRANSFER = 'TRANSFER';

    // Enrollment Status Constants
    public const STATUS_ENROLLED = 'ENROLLED';
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_PROMOTED = 'PROMOTED';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_REPEATED = 'REPEATED';
    public const STATUS_LEFT = 'LEFT';
    public const STATUS_TRANSFERRED = 'TRANSFERRED';

    public static function getTypes(): array
    {
        return [self::TYPE_AUTO, self::TYPE_MANUAL, self::TYPE_REPEAT, self::TYPE_TRANSFER];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ENROLLED,
            self::STATUS_ACTIVE,
            self::STATUS_PROMOTED,
            self::STATUS_FAILED,
            self::STATUS_REPEATED,
            self::STATUS_LEFT,
            self::STATUS_TRANSFERRED,
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function promotedFromClass()
    {
        return $this->belongsTo(ClassRoom::class, 'promoted_from_class_id');
    }

    public function enrolledByUser()
    {
        return $this->belongsTo(User::class, 'enrolled_by');
    }
}
