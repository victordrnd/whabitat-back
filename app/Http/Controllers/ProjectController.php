<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Response\JsonResponse;
use App\Project;
use App\User_projects;
use App\Http\Requests\Project\AddProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Requests\Project\DeleteProjectRequest;
use App\Http\Requests\Project\AssignProjectRequest;
use App\Http\Requests\Project\SearchProjectRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psy\Util\Json;

class ProjectController extends Controller
{
  /**
   * Returns all existing projects with details
   * @return array
   */
  public function showAll()
  {
    $projects = Project::with(['status', 'users'])->get();
    return JsonResponse::setData($projects);
  }



  /**
   * Returns the specified project with details
   *@param int $id
   * @return array
   */
  public function get($id)
  {
    $project = Project::where('id', $id)->with(['status', 'users'])->get();
    if (!is_null(Project::find($id))) {
      return JsonResponse::setData($project);
    } else {
      return JsonResponse::setError("Project " . $id . " can't be found");
    }
  }


  /**
   * Add this project
   * @param string $label
   * @param int $progress
   */
  public function add(AddProjectRequest $request)
  {
    $project = Project::create([
      'label' => $request->label,
      'progress' => $request->progress ?: 0,
      'status_id' => $request->status_id ?: 1,
      'creator_id' => auth()->user()->id
    ]);
    $formattedproject = Project::where('id', $project->id)->with((['status', 'users']))->first();
    return JsonResponse::setData($formattedproject);
  }



  /**
   * Update a specific project
   * @param int $id
   * @param int $progress
   * @param string $label
   * @return array
   */
  public function update(Request $request, $id)
  {
    try{
      $project = Project::findOrFail($id);
    }
    catch(ModelNotFoundException $e){
      return JsonResponse::exception($e);
    }
    $project->parseProjectUpdateRequest($request);
    $project->save();
    $project = Project::find($request->id)->with(['status', 'users']);
    return JsonResponse::setData($project);
  }




  /**
   *Delete a project
   * @param int id
   * @return array
   */
  public function delete(DeleteProjectRequest $request)
  {
    Project::where('id', $request->id)->delete();
    User_projects::where('project_id', $request->id)->delete();
    return JsonResponse::setMessage("Done.");
  }





  /**
   * @param int $users_id
   * @param int project_id
   * @return array
   */
  public function assign(AssignProjectRequest $request)
  {
    if (empty(Project::find($request->project_id))) {
      return JsonResponse::setError('not found');
    }
    if (!$request->filled('users_id')) {
      User_projects::where('project_id', $request->project_id)->delete();
      $formattedproject = Project::where('id', $request->project_id)->with('status', 'users')->get();
      return JsonResponse::setData($formattedproject);
    }
    User_projects::where('project_id', $request->project_id)->delete();
    foreach ($request->users_id as $user) {
      User_projects::firstOrCreate([
        'user_id' => $user,
        'project_id' => $request->project_id
      ]);
    }
    return JsonResponse::setData(Project::where('id', $request->project_id)->with(['users', 'status']))->get();
  }



/**
 * Search for a project
 * @param string $keyword
 * @param 
 */
  public function search(SearchProjectRequest $request)
  {

    $keyword = $request->keyword ?: null;
 
    $status = $request->status ?: null;
    $creator = $request->creator ?: null;
    $created_before = $request->created_before ?:null;
    $created_after = $request->create_after ?: null;

    return Project::search($keyword,$status,$creator,$created_before, $created_after)
      ->with(['users', 'status'])
      ->get();
  }
}
