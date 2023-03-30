<?php

namespace Serenity\Services;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Serenity\Contracts\Service;

abstract class ApiService implements Service
{
  use AuthorizesRequests;
  use DispatchesJobs;
  use ValidatesRequests;

  /**
   * Return a response for the Api.
   *
   * @param  array  $data
   * @param  string  $message
   * @param  int  $status
   * @return \Illuminate\Http\Response
   */
  public function respond(array $data): Response
  {
    return response([
      'data' => $data['data'],
      'message' => $data['message'] ?? null,
      'status' => $data['status'],
    ]);
  }
}
