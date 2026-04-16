<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeeStructureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     *
     * @property int $id
     * @property int $class_room_id
     * @property int $academic_year_id
     * @property int $term_id
     * @property float $total_amount
     *
     * @property \stdClass|null $classroom
     * @property \stdClass|null $academic_year
     * @property \stdClass|null $term
     * @property array $fee_items
     *
     * @property string $created_at
     * @property string $updated_at
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'class_room_id' => $this->class_room_id,
            'academic_year_id' => $this->academic_year_id,
            'term_id' => $this->term_id,
            'total_amount' => (float) $this->total_amount,

            // Optional eager-loaded relationships
            'classroom' => $this->whenLoaded('classroom', function () {
                return [
                    'id' => $this->classroom->id,
                    'class_name' => $this->classroom->class_name,
                    'form' => $this->classroom->form,
                ];
            }),

            'academic_year' => $this->whenLoaded('academicYear', function () {
                return [
                    'id' => $this->academicYear->id,
                    'year' => $this->academicYear->year,
                ];
            }),

            'term' => $this->whenLoaded('term', function () {
                return [
                    'id' => $this->term->id,
                    'name' => $this->term->name,
                    'term_number' => $this->term->term_number,
                ];
            }),

            'fee_items' => $this->whenLoaded('feeItems', function () {
                return $this->feeItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'amount' => (float) $item->amount,
                        'description' => $item->description,
                    ];
                })->values();
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
