<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
/**
 * Transform the resource into an array.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return array
 *
 * @property int $id
 * @property int $student_fee_account_id
 * @property float $amount_paid
 * @property string|null $payment_date
 * @property string $payment_method
 * @property string $receipt_number
 * @property string $recorded_by
 *
 * @property \stdClass|null $account
 * @property \stdClass|null $recorder
 * @property string $created_at
 * @property string $updated_at
 * // Optional eager-loaded relationships
 * @property \stdClass|null $account->student   
 * @property \stdClass|null $account->feeStructure
 * @property \stdClass|null $recorder
 */     
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
            'account' => $this->whenLoaded('studentFeeAccount', function () {
                return [
                    'id' => $this->studentFeeAccount->id,
                    'student_id' => $this->studentFeeAccount->student_id,
                    'fee_structure_id' => $this->studentFeeAccount->fee_structure_id,
                    'balance' => (float) $this->studentFeeAccount->balance,

                    // Nested eager loading support
                    'student' => isset($this->studentFeeAccount->student) ? [
                        'id' => $this->studentFeeAccount->student->id,
                        'admission_number' => $this->studentFeeAccount->student->admission_number,
                        'first_name' => $this->studentFeeAccount->student->first_name,
                        'last_name' => $this->studentFeeAccount->student->last_name,
                    ] : null,

                    'fee_structure' => isset($this->studentFeeAccount->feeStructure) ? [
                        'id' => $this->studentFeeAccount->feeStructure->id,
                        'class_room_id' => $this->studentFeeAccount->feeStructure->class_room_id,
                        'academic_year_id' => $this->studentFeeAccount->feeStructure->academic_year_id,
                        'term_id' => $this->studentFeeAccount->feeStructure->term_id,
                        'total_amount' => (float) $this->studentFeeAccount->feeStructure->total_amount,
                    ] : null,
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
