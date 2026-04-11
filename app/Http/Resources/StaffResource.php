<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_number' => $this->employee_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'job_title' => $this->job_title,
            'status' => $this->status,
            'roles' => $this->whenLoaded('user', fn () => $this->user?->roles?->pluck('name')->values() ?? []),
            'created_at' => $this->created_at,
        ];
    }
}
