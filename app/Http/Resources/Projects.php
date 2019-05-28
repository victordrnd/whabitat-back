<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Project;
class Projects extends ResourceCollection
{
  /**
  * Transform the resource collection into an array.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return array
  */
  public function toArray($request)
  {
    return
      $this->collection->transform(function($project){
        return [
          'id' => $project->id,
          'label' => $project->label,
          'progress' => $project->progress,
          'users' => Project::getUsers($project->id),
          'status' => Project::getStatus($project->status_id)
        ];
      });
  }
}
