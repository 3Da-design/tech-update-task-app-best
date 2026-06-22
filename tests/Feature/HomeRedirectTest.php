<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeRedirectTest extends TestCase
{
  public function test_root_redirects_to_tasks_index(): void
  {
    $response = $this->get('/');

    $response->assertRedirect('/tasks');
  }
}
