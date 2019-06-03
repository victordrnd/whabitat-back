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
      'created_at', 'updated_at', 'creator_id','status_id', 'deleted_at', 'pivot'
  ];


  public function status()
  {
      return $this->belongsTo(Status::class, "status_id");
  }

  public function users(){
    return $this->belongsToMany(User::class, "user_projects","user_id", "project_id");
  }



  /**
  * Uses to get all details from a specific project
  * @param int $id
  * @return array
  */
  public static function getWithDetails($id){
    $project = Project::find($id);
    $users = User_projects::where('project_id', $id)->get();
    $usersarray = array();
    foreach ($users as $key => $user) {
      $usersarray[] = User::find($user->user_id);
    }
    $project->users = $usersarray;
    $project->status = Status::where('id', $project->status_id)->first();
    unset($project->status_id);
    return $project;
  }


  /**
  * Uses to get all projects with all details
  * @return array
  */
  public static function getAll(){
    $projects = Project::all();
    $projectsarray = array();
    foreach ($projects as $key => $project) {
      $detailedproject = Project::getWithDetails($project->id);
      $projectsarray[] = $detailedproject;
    }
    return $projectsarray;
  }

  public static function getUsers($id){
    $users = User_projects::where('project_id', $id)->get();
    $usersarray = array();
    foreach ($users as $key => $user) {
      $usersarray[] = User::find($user->user_id);
    }
    return $usersarray;
  }

  public static function getStatus($id){
    return Status::select('label')->where('id', $id)->first();
  }
}
