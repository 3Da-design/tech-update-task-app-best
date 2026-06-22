<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Concerns\FindsAuthorizedTask;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\Task\TaskListFilters;
use App\Services\Task\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TaskController extends Controller
{
  use FindsAuthorizedTask;

  public function __construct(
    private readonly TaskService $taskService,
  ) {}

  protected function taskService(): TaskService
  {
    return $this->taskService;
  }

  /**
   * Display a listing of the resource.
   */
  public function index(IndexTaskRequest $request): JsonResponse
  {
    $userId = (int) $request->user()->id;
    $filters = TaskListFilters::fromQuery($request->validated());
    $collection = $this->taskService->listForUser($userId, $filters);

    return TaskResource::collection($collection)->response();
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreTaskRequest $request): JsonResponse
  {
    $data = $request->validated();
    $userId = (int) $request->user()->id;
    $task = $this->taskService->createForUser($userId, $data);

    return (new TaskResource($task))->response()->setStatusCode(201);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateTaskRequest $request, string $id): JsonResponse
  {
    $data = $request->validated();
    $task = $this->findAuthorizedTask($id, 'update');
    $task = $this->taskService->update($task, $data);

    return (new TaskResource($task))->response();
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id): Response
  {
    $task = $this->findAuthorizedTask($id, 'delete');
    $this->taskService->delete($task);

    return response()->noContent();
  }
}
