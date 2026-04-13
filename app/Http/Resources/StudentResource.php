<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'admission_number' => $this->admission_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'enrollment_date' => $this->enrollment_date?->toDateString(),
            'status' => $this->status,

            // Optional eager-loaded relationships
            'current_class' => $this->whenLoaded('currentClassRoom', function () {
                return [
                    'id' => $this->currentClassRoom?->id,
                    'class_name' => $this->currentClassRoom?->class_name,
                    'form' => $this->currentClassRoom?->form,
                    'stream' => $this->currentClassRoom?->stream,
                ];
            }),

            'guardians' => $this->whenLoaded('guardians', function () {
                return $this->guardians->map(function ($guardian) {
                    return [
                        'id' => $guardian->id,
                        'first_name' => $guardian->first_name,
                        'last_name' => $guardian->last_name,
                        'phone_number' => $guardian->phone_number,
                        'email' => $guardian->email,
                        'relationship' => $guardian->relationship,
                        'is_primary' => (bool) ($guardian->pivot?->is_primary ?? false),
                    ];
                })->values();
            }),

            'enrollments' => $this->whenLoaded('enrollments', function () {
                return $this->enrollments->map(function ($enrollment) {
                    return [
                        'id' => $enrollment->id,
                        'class_room_id' => $enrollment->class_room_id,
                        'enrolled_at' => $enrollment->enrolled_at?->toDateString(),
                        'is_active' => (bool) $enrollment->is_active,
                    ];
                })->values();
            }),

            'fee_accounts' => $this->whenLoaded('feeAccounts', function () {
                return $this->feeAccounts->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'fee_structure_id' => $account->fee_structure_id,
                        'balance' => (float) $account->balance,
                    ];
                })->values();
            }),

            'attendance' => $this->whenLoaded('attendance', function () {
                return [
                    'present_count' => $this->attendance->where('status', 'present')->count(),
                    'absent_count' => $this->attendance->where('status', 'absent')->count(),
                    'excused_count' => $this->attendance->where('status', 'excused')->count(),
                ];
            }),

            'grades' => $this->whenLoaded('grades', function () {
                return $this->grades->map(function ($grade) {
                    return [
                        'id' => $grade->id,
                        'assessment_id' => $grade->assessment_id,
                        'marks_obtained' => (float) $grade->marks_obtained,
                        'percentage' => (float) $grade->percentage,
                        'grade' => $grade->grade,
                    ];
                })->values();
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
