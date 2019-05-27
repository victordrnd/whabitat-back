<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Response\JsonResponse;
use App\Project;
use App\Status;
class ProjectController extends Controller
{
  public static function showAll(){
    $projects = Project::all();
    foreach ($projects as $index => $project) {

      $projects[$index]->status = Status::where('id', $project->status_id)->first(['id','label']);
      unset($projects[$index]->status_id);
    }
    $response = new JsonResponse;
    $response->setData($projects);
    return $response->throw();
  }




  public static function get($id){
    $project = Project::find($id);
    $response = new JsonResponse;
    $response->setData($project);
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
    $project = Project::find($request->id);
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
      if(is_array($request->users)){
        $users = $request->users;
        foreach ($users as $index => $user) {
          
        }
      }
  }

}
