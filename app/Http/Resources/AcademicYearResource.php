<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicYearResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     * @property int $id
     * @property string $year
     * @property bool $is_active
     * @property \Illuminate\Support\Collection|null $terms
     * @property string $created_at
     * @property string $updated_at 
     */
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
