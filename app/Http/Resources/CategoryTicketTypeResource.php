<?php

namespace App\Http\Resources;

use App\Models\CategoryTicketType;
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
        $categoryTicketType = CategoryTicketType::withCount('registrations')->find($this->pivot->id);
        $used = $categoryTicketType->registrations_count ?? 0;

        return [
            'ticket_type_id'    => $this->id,
            'name'              => $this->name,
            'pivot' => [
                'price'         => $this->pivot->price,
                'quota'         => $this->pivot->quota,
                'used'          => $used,
                'remaining'     => $this->pivot->quota - $used,
                'valid_from'    => $this->valid_from
            ],
        ];
    }
}
