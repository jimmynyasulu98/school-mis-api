<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_fee_account_id' => $this->student_fee_account_id,
            'amount_paid' => (float) $this->amount_paid,
            'payment_date' => $this->payment_date?->toDateString(),
            'payment_method' => $this->payment_method,
            'receipt_number' => $this->receipt_number,
            'recorded_by' => $this->recorded_by,
            
            // Optional eager-loaded relationships
            'account' => $this->whenLoaded('account', function () {
                return [
                    'id' => $this->account->id,
                    'student_id' => $this->account->student_id,
                    'fee_structure_id' => $this->account->fee_structure_id,
                    'balance' => (float) $this->account->balance,
                    
                    // Nested eager loading support
                    'student' => $this->account->whenLoaded('student', function () {
                        return [
                            'id' => $this->account->student->id,
                            'admission_number' => $this->account->student->admission_number,
                            'first_name' => $this->account->student->first_name,
                            'last_name' => $this->account->student->last_name,
                        ];
                    }),
                    
                    'fee_structure' => $this->account->whenLoaded('feeStructure', function () {
                        return [
                            'id' => $this->account->feeStructure->id,
                            'class_room_id' => $this->account->feeStructure->class_room_id,
                            'academic_year_id' => $this->account->feeStructure->academic_year_id,
                            'term_id' => $this->account->feeStructure->term_id,
                            'total_amount' => (float) $this->account->feeStructure->total_amount,
                        ];
                    }),
                ];
            }),
            
            'recorder' => $this->whenLoaded('recorder', function () {
                return [
                    'id' => $this->recorder->id,
                    'employee_number' => $this->recorder->employee_number,
                    'first_name' => $this->recorder->first_name,
                    'last_name' => $this->recorder->last_name,
                ];
            }),
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
