<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Reponse\JsonResponse;
class Project extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return  [
          'id' => $this->id,
          'label' => $this->label,
          'progress' => $this->progress,
          'users' => Project::getUsers($this->id),
          'status' => Project::getStatus($this->status_id)
        ];
        //return parent::toArray($request);
    }
}
