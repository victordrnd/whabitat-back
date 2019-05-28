<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Response\JsonResponse;
use Validator;
use App\Project;
use App\Status;
use App\User_projects;
use App\Http\Resources\Project as ProjectResource;
class ProjectController extends Controller
{
  /**
  * Returns all existing projects with details
  * @return array
  */
  public static function showAll(){
    $projects = Project::getAll();
    $response = new JsonResponse;
    return $response->setData($projects);
  }



  /**
  * Returns the specified project with details
  *@param int $id
  * @return array
  */
  public static function get($id){
    
    $project = new ProjectResource(Project::find($id));
    $response = new JsonResponse;
    if(!is_null(Project::find($id))){
      return $response->setData($project);
    }else{
      return $response->addErrors(["Project ".$id." doesn't exist."]);
    }
  }


  /**
  * Add this project
  * @param string $label
  * @param int $progress
  */
  public static function add(Request $request){
    $validator = Validator::make($request->all(),[
      'label' => 'string|required',
      'progress' => 'integer|required'
    ]);
    $response = new JsonResponse;
    if($validator->fails()){
      return $response->addErrors(array($validator->errors()));
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
    $formattedproject = new ProjectResource($project);
    return $response->setData($project);
  }

  /**
  * Update a specific project
  * @param int $id
  * @param int $progress
  * @param string $label
  * @return array
  */
  public static function update(Request $request){
    $validator = Validator::make($request->all(),[
      'id' => 'integer|exists:projects,id',
      'progress' => 'integer|required',
      'label' => 'string|required'
    ]);
    $response = new JsonResponse;
    if($validator->fails()){
      return $response->addErrors(array($validator->errors()));
    }

    Project::where('id', $request->id)->update([
      'label' => $request->label,
      'progress' => $request->progress,
      'status_id' => $request->status_id
    ]);
    $project = new ProjectResource(Project::find($request->id));
    return $response->setData($project);
  }




  /**
  *Detelete a project
  * @param int id
  * @return array
  */
  public static function delete(Request $request){
    $validator = Validator::make($request->all(),[
      'id' => 'required|integer|exists:projects,id'
    ]);
    $response = new JsonResponse;
    if($validator->fails()){
      return $response->addErrors(array($validator->errors()));
    }
    Project::destroy($request->id);
    User_projects::where('project_id', $request->id)->delete();

    return $response->setMessage('Done.');
  }


  /**
  * @param int $users_id
  * @param int project_id
  * @return array
  */
  public static function assign(Request $request){
    $request->validate([
      'users_id' => 'exists:users,id|required',
      'project_id' => 'exists:projects,id|required'
    ]);
    $users = $request->users_id;
    User_projects::where('project_id', $request->project_id)->delete();
    foreach ($users as $user) {
      User_projects::firstOrCreate([
        'user_id' => $user,
        'project_id' => $request->project_id
      ]);
    }
    $response = new JsonResponse;
    return $response->setData(Project::getWithDetails($request->project_id));
  }

}
