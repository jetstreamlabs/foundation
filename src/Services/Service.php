<?php

namespace Serenity\Services;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Serenity\Contracts\Payload;
use Serenity\Contracts\Service as ServiceInterface;
use Serenity\Payloads\InertiaPayload;

abstract class Service implements ServiceInterface
{
  use AuthorizesRequests;
  use DispatchesJobs;
  use ValidatesRequests;

  /**
   * Return a successful response payload.
   *
   * @param  array  $data
   * @param  int  $status
   * @return \Serenity\Contracts\Payload
   */
  public function successResponse(array $data, $status = 303): Payload
  {
    return $this->respond($data['message'], 'success', $data['route'], $status);
  }

  /**
   * Return an error response payload.
   *
   * @param  array  $data
   * @param  int  $status
   * @return \Serenity\Contracts\Payload
   */
  public function errorResponse(array $data, $status = 302): Payload
  {
    return $this->respond($data['message'], 'error', $data['route'], $status);
  }

  /**
   * Return an info response payload.
   *
   * @param  array  $data
   * @param  int  $status
   * @return \Serenity\Contracts\Payload
   */
  public function infoResponse(array $data, $status = 303): Payload
  {
    return $this->respond($data['message'], 'info', $data['route'], $status);
  }

  /**
   * Return a status response payload.
   *
   * @param  array  $data
   * @param  int  $status
   * @return \Serenity\Contracts\Payload
   */
  public function statusResponse(array $data, $status = 303): Payload
  {
    return $this->respond($data['message'], 'status', $data['route'], $status);
  }

  /**
   * Return a warning response payload.
   *
   * @param  array  $data
   * @param  int  $status
   * @return \Serenity\Contracts\Payload
   */
  public function warningResponse(array $data, $status = 303): Payload
  {
    return $this->respond($data['message'], 'warning', $data['route'], $status);
  }

  /**
   * Build up a payload response.
   *
   * @param  array  $data
   * @return \Serenity\Contracts\Payload
   */
  public function payloadResponse(array $data): Payload
  {
    return $this->payload()->setData($data);
  }

  /**
   * Build up and return a payload.
   *
   * @param  mixed  $message
   * @param  string  $level
   * @param  string  $route
   * @param  int  $status
   * @return \Serenity\Contracts\Payload
   */
  public function respond(mixed $message, string $level, string $route, int $status): Payload
  {
    return $this->payload()->setData([
      'message' => $message,
      'level' => $level,
      'route' => $route,
      'status' => $status,
    ]);
  }

  /**
   * Generate a new payload instance.
   *
   * @return \Serenity\Contracts\Payload
   */
  public function payload(): Payload
  {
    return app(InertiaPayload::class);
  }
}
