<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Response\JsonResponse;
use App\Project;
use App\Status;
use App\User_projects;
class ProjectController extends Controller
{
  public static function showAll(){
    $projects = Project::getAll();
    $response = new JsonResponse;
    $response->setData($projects);
    return $response->throw();
  }




  public static function get($id){
    $response = new JsonResponse;
    if(!is_null(Project::find($id))){
      $project = Project::getWithDetails($id);
      $response->setData($project);
    }else{
      $response->addErrors(["Project ".$id." doesn't exist."]);
    }
    return $response->throw();
  }



  public static function add(Request $request){
    $request->validate([
      'label' => 'string|required',
      'progress' => 'integer'
    ]);
    if($request->filled('progress')){
      if($request->progress <=100 && $request->progress >=0){
        $progress = $request->progress;
      }
      else{
        $progress = 0;
      }
    }
    if($request->filled('status_id')){
      $status_id = $request->status_id;
    }else{
      $status_id = 1;
    }
    $project = Project::create([
      'label' => $request->label,
      'progress' => $progress,
      'status_id' =>$status_id
    ]);

    $response = new JsonResponse;
    $response->setData($project);
    return $response->throw();
  }


  public static function update(Request $request){
    $request->validate([
      'id' => 'integer|exists:projects,id',
      'progress' => 'integer',
      'label' => 'string|required'
    ]);

    Project::where('id', $request->id)->update([
      'label' => $request->label,
      'progress' => $request->progress,
      'status_id' => $request->status_id
    ]);
    $project = Project::getWithDetails($request->id);
    $project->status = Status::where('id', $project->status_id)->first(['id','label']);
    unset($project->status_id);
    $response = new JsonResponse;
    $response->setData($project);
    return $response->throw();
  }

  public static function delete(Request $request){
    $request->validate([
      'id' => 'integer|exists:projects,id'
    ]);
    Project::destroy($request->id);
    $response = new JsonResponse;
    $response->setMessage('Done.');
    return $response->throw();
  }



  public static function assign(Request $request){
    $request->validate([
      'users_id' => 'exists:users,id|required',
      'project_id' => 'exists:projects,id|required'
    ]);
    $users = $request->users_id;
    User_projects::where('project_id', $request->project_id)->delete();
    foreach ($users as $user) {
      User_projects::create([
        'user_id' => $user,
        'project_id' => $request->project_id
      ]);
    }
    $response = new JsonResponse;
    $response->setData(Project::getWithDetails($request->project_id));
    return $response->throw();
  }

}
