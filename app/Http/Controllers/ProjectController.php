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
      'progress' => $request->progress,
      'status_id' => $request->status_id,
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
  public function update(UpdateProjectRequest $request)
  {
    Project::where('id', $request->id)->update([
      'label' => $request->label,
      'progress' => $request->progress,
      'status_id' => $request->status_id
    ]);
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

    return Project::when($status, function ($q) use ($status) {
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
    ->where(function ($q) use ($keyword) {
      $q->where('label', 'LIKE', '%' . $keyword . '%')
        ->orWhereHas('users', function ($profile) use ($keyword) {
          $profile->where('firstname', 'like', '%' . $keyword . '%')
            ->orWhere('lastname', 'like', '%' . $keyword . '%');
        });
    })
      ->with(['users', 'status'])
      ->get();
  }
}
