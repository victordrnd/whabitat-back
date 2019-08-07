<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User_projects;
use App\Status;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
  use SoftDeletes;



  protected $fillable = [
    'label', 'progress', 'status_id', 'creator_id'
  ];

  protected $softDelete = true;

  protected $hidden = [
    'created_at', 'updated_at', 'creator_id', 'deleted_at', 'pivot'
  ];


  public function status()
  {
    return $this->belongsTo(Status::class, "status_id");
  }

  public function users()
  {
    return $this->belongsToMany(User::class, "user_projects", "project_id", "user_id");
  }

  public function parseProjectUpdateRequest($request){
    $this->label = $request->get('label');
    $this->progress = $request->get('progress');
    $this->status_id = $request->get('status_id');
  }

  public function scopeSearch($query, $keyword, $status, $creator, $created_before, $created_after)
  {
    $keywords = explode(' ', $keyword);
    return $query->when($status, function ($q) use ($status) {
      $q->where('status_id', $status);
    })
      ->when($creator, function ($q) use ($creator) {
        $q->where('creator_id', $creator);
      })
      ->when($created_before, function ($q) use ($created_before) {
        $q->where('created_at', '<=', $created_before);
      })
      ->when($created_after, function ($q) use ($created_after) {
        $q->where('created_at', '>=', $created_after);
      })
      ->where(function ($q) use ($keyword, $keywords) {
        $q->where('label', 'LIKE', '%' . $keyword . '%')
          ->orWhereHas('users', function ($profile) use ($keywords) {
            foreach($keywords as $keyword){
                $profile->where('firstname', 'like', '%' . $keyword . '%')
                ->orWhere('lastname', 'like', '%' . $keyword . '%');
            }            
          });
      });
  }
}
