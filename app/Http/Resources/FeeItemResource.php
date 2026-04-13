<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeeItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fee_structure_id' => $this->fee_structure_id,
            'name' => $this->name,
            'amount' => (float) $this->amount,
            'description' => $this->description,

            // Optional eager-loaded relationships
            'fee_structure' => $this->whenLoaded('feeStructure', function () {
                return [
                    'id' => $this->feeStructure->id,
                    'total_amount' => (float) $this->feeStructure->total_amount,
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
