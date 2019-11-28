<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                  => $this->id,
            'user_id'             => $this->user_id,
            'user'                => $this->whenLoaded('user', function () {
                return [
                    'id'   => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'point'               => [
                'lat' => $this->point->getLat(),
                'lng' => $this->point->getLng(),
            ],
            'comment'             => $this->comment,
            'created_at_readable' => $this->created_at_readable,
        ];
    }
}
