<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\TaskService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
  use RefreshDatabase;

  private MockInterface $repository;

  private TaskService $service;

  protected function setUp(): void
  {
    parent::setUp();

    $this->repository = Mockery::mock(TaskRepositoryInterface::class);
    $this->service = new TaskService($this->repository);
  }

  protected function tearDown(): void
  {
    Mockery::close();

    parent::tearDown();
  }

  public function test_list_for_default_user_passes_normalized_filters_to_repository(): void
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $collection = new Collection;

    $this->repository
      ->shouldReceive('getFiltered')
      ->once()
      ->with($user->id, ['title' => 'search'])
      ->andReturn($collection);

    $result = $this->service->listForDefaultUser([
      'title' => ' search ',
      'status' => '',
      'due_date_sort' => 'invalid',
    ]);

    $this->assertSame($collection, $result);
  }

  public function test_create_for_default_user_strips_user_id_and_calls_repository(): void
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $task = new Task([
      'title' => 'Created',
      'status' => 'todo',
    ]);

    $this->repository
      ->shouldReceive('create')
      ->once()
      ->with($user->id, [
        'title' => 'Created',
        'status' => 'todo',
      ])
      ->andReturn($task);

    $result = $this->service->createForDefaultUser([
      'title' => ' Created ',
      'status' => 'todo',
      'user_id' => 999,
    ]);

    $this->assertSame($task, $result);
  }

  public function test_find_for_default_user_throws_when_repository_returns_null(): void
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->repository
      ->shouldReceive('findById')
      ->once()
      ->with($user->id, 42)
      ->andReturn(null);

    $this->expectException(ModelNotFoundException::class);

    $this->service->findForDefaultUser(42);
  }

  public function test_delete_for_default_user_deletes_task_from_repository(): void
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $task = new Task([
      'id' => 7,
      'user_id' => $user->id,
      'title' => 'Delete me',
      'status' => 'todo',
    ]);
    $task->id = 7;

    $this->repository
      ->shouldReceive('findById')
      ->once()
      ->with($user->id, 7)
      ->andReturn($task);

    $this->repository
      ->shouldReceive('delete')
      ->once()
      ->with($task)
      ->andReturn(true);

    $this->service->deleteForDefaultUser(7);

    $this->addToAssertionCount(1);
  }
}
