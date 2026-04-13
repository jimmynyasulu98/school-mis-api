<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicYearResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'is_active' => (bool) $this->is_active,

            // Optional eager-loaded relationships
            'terms' => $this->whenLoaded('terms', function () {
                return $this->terms->map(function ($term) {
                    return [
                        'id' => $term->id,
                        'name' => $term->name,
                        'term_number' => $term->term_number,
                        'start_date' => $term->start_date?->toDateString(),
                        'end_date' => $term->end_date?->toDateString(),
                    ];
                })->values();
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
