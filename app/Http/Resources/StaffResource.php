<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
/**
 * Transform the resource into an array.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return array
 *
 * @property int $id
 * @property string $employee_number
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone_number
 * @property string $job_title
 * @property string $status
 * @property \stdClass|null $user
 * @property array $roles
 * @property array $permissions
 * @property \Illuminate\Support\Collection|\stdClass[] $class_taught
 * @property string $created_at
 * @property string $updated_at
 */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_number' => $this->employee_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'job_title' => $this->job_title,
            'status' => $this->status,

            // Optional eager-loaded relationships
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            'roles' => $this->whenLoaded('user', function () {
                return $this->user?->roles?->pluck('name')->values() ?? [];
            }),

            'permissions' => $this->whenLoaded('user', function () {
                $user = $this->user;
                if (!$user) return [];

                return $user->getAllPermissions()
                    ->pluck('name')
                    ->unique()
                    ->values()
                    ->toArray();
            }),

            'class_taught' => $this->whenLoaded('classRoomsTaught', function () {
                return $this->classRoomsTaught?->map(function ($classroom) {
                    return [
                        'id' => $classroom->id,
                        'class_name' => $classroom->class_name,
                        'form' => $classroom->form,
                        'stream' => $classroom->stream,
                    ];
                })->values();
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
