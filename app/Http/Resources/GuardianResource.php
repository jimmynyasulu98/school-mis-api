<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuardianResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     * @property int $id
     * @property string $first_name
     * @property string $last_name
     * @property string $relationship
     * @property string|null $phone_number
     * @property string|null $email
     * @property string|null $national_id
     * @property string|null $occupation
     * @property string|null $employer
     * @property \Illuminate\Support\Collection|null $students
     * @property string $created_at
     * @property string $updated_at
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'relationship' => $this->relationship,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'national_id' => $this->national_id,
            'occupation' => $this->occupation,
            'employer' => $this->employer,

            // Optional eager-loaded relationships
            'students' => $this->whenLoaded('students', function () {
                return $this->students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'admission_number' => $student->admission_number,
                        'first_name' => $student->first_name,
                        'last_name' => $student->last_name,
                    ];
                })->values();
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
