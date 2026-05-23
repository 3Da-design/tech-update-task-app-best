<?php

namespace App\Http\Controllers\Concerns;

trait ParsesTaskRouteId
{
  private function parseTaskId(string $id): int
  {
    if (! ctype_digit($id)) {
      abort(404);
    }

    return (int) $id;
  }
}
