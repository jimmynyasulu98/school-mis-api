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
            'current_class' => $this->whenLoaded('currentClassRoom', fn () => [
                'id' => $this->currentClassRoom?->id,
                'name' => $this->currentClassRoom?->name,
                'stream' => $this->currentClassRoom?->stream,
            ]),
            'guardians' => $this->whenLoaded('guardians', fn () => $this->guardians->map(fn ($guardian) => [
                'id' => $guardian->id,
                'first_name' => $guardian->first_name,
                'last_name' => $guardian->last_name,
                'phone' => $guardian->phone,
                'relationship' => $guardian->pivot?->notes ?? $guardian->relationship,
                'is_primary' => (bool) ($guardian->pivot?->is_primary ?? false),
            ])),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
