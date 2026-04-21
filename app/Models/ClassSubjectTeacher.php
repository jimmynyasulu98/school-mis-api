<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubjectTeacher extends Model
{
    use HasFactory;

    protected $fillable = ['class_subject_id', 'teacher_id', 'is_core', 'starts_on', 'ends_on'];

    protected function casts(): array
    {
        return [
            'is_core' => 'boolean',
            'starts_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function scopeCurrent($query, ?string $date = null)
    {
        $effectiveDate = $date ?? now()->toDateString();

        return $query
            ->whereDate('starts_on', '<=', $effectiveDate)
            ->where(function ($innerQuery) use ($effectiveDate) {
                $innerQuery
                    ->whereNull('ends_on')
                    ->orWhereDate('ends_on', '>', $effectiveDate);
            });
    }

    public function scopeForDate($query, string $date)
    {
        return $query->current($date);
    }

    public function isCurrent(?string $date = null): bool
    {
        $effectiveDate = $date ?? now()->toDateString();

        return $this->starts_on !== null
            && $this->starts_on->toDateString() <= $effectiveDate
            && ($this->ends_on === null || $this->ends_on->toDateString() > $effectiveDate);
    }
}
