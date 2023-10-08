<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [

            'id' => $this->id,
            'url' => $this->url,
            'name' => $this->name,
            'extension' => $this->extension,
            'access' => $this->access,
            'size' => $this->size,
            'fullname' => $this->full_name,
        ];
    }
}
