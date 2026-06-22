<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\FindsAuthorizedTask;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\Task\TaskListFilters;
use App\Services\Task\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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

  public function index(IndexTaskRequest $request): View
  {
    $userId = (int) $request->user()->id;
    $filters = TaskListFilters::fromQuery($request->validated());

    return view('tasks.index', [
      'tasks' => $this->taskService->listForUser($userId, $filters),
    ]);
  }

  public function create(): View
  {
    return view('tasks.create');
  }

  public function store(StoreTaskRequest $request): RedirectResponse
  {
    $data = $request->validated();
    $userId = (int) $request->user()->id;
    $this->taskService->createForUser($userId, $data);

    return redirect()
      ->route('tasks.index')
      ->with('status', 'タスクを作成しました。');
  }

  public function edit(string $id): View
  {
    $task = $this->findAuthorizedTask($id);

    return view('tasks.edit', compact('task'));
  }

  public function update(UpdateTaskRequest $request, string $id): RedirectResponse
  {
    $data = $request->validated();
    $task = $this->findAuthorizedTask($id, 'update');
    $this->taskService->update($task, $data);

    return redirect()
      ->route('tasks.index', $request->only(['title', 'status', 'due_date_sort']))
      ->with('status', 'タスクを更新しました。');
  }

  public function destroy(string $id): RedirectResponse
  {
    $task = $this->findAuthorizedTask($id, 'delete');
    $this->taskService->delete($task);

    return redirect()
      ->route('tasks.index')
      ->with('status', 'タスクを削除しました。');
  }
}
