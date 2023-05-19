<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

// use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return[
            'id' =>$this->id,
            'name'=>$this->name,
            'company_name'=>$this->company_name,
            'position'=>$this->position,
            'number'=>$this->number,
            'service'=>$this->service,
            'email'=>$this->email,
            'content'=>$this->content,
            'created_at'=> $this->created_at->format('d/m/Y'),
            'updated_at'=> $this->updated_at->format('d/m/Y'),
        ];
    }
}
