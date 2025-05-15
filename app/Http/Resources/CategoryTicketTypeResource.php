<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryTicketTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ticket_type_id'    => $this->id,
            'name'              => $this->name,
            'pivot' => [
                'price'         => $this->pivot->price,
                'quota'         => $this->pivot->quota,
                'valid_from'    => $this->valid_from
            ],
        ];
    }
}
