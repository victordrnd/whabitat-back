<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User_projects;
use App\Status;
use App\User;
class Project extends Model
{
  public $timestamps = false;
  protected $fillable = [
      'label', 'progress', 'status_id'
  ];

  public static function getWithDetails($id){
    $project = Project::find($id);
    $users = User_projects::where('project_id', $id)->get();
    $usersarray = array();
    foreach ($users as $key => $user) {
      $usersarray[] = User::find($user->user_id);
    }
    $project->users = $usersarray;
    $project->status = Status::select('label')->where('id', $project->status_id)->first();
    unset($project->status_id);
    return $project;
  }


  public static function getAll(){
    $projects = Project::all();
    $projectsarray = array();
    foreach ($projects as $key => $project) {
      $detailedproject = Project::getWithDetails($project->id);
      $projectsarray[] = $detailedproject;
    }
    return $projectsarray;
  }
}
