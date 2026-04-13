<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'class_room_id' => $this->class_room_id,
            'date_marked' => $this->date_marked?->toDateString(),
            'status' => $this->status,
            'remarks' => $this->remarks,
            
            // Optional eager-loaded relationships
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'admission_number' => $this->student->admission_number,
                    'first_name' => $this->student->first_name,
                    'last_name' => $this->student->last_name,
                    
                    // If enrollment is also loaded
                    'current_class' => $this->student->whenLoaded('currentClassRoom', function () {
                        return [
                            'id' => $this->student->currentClassRoom->id,
                            'class_name' => $this->student->currentClassRoom->class_name,
                            'form' => $this->student->currentClassRoom->form,
                        ];
                    }),
                ];
            }),
            
            'classroom' => $this->whenLoaded('classroom', function () {
                return [
                    'id' => $this->classroom->id,
                    'class_name' => $this->classroom->class_name,
                    'form' => $this->classroom->form,
                    'stream' => $this->classroom->stream,
                ];
            }),
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
