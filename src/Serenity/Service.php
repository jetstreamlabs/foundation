<?php

namespace Serenity;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Serenity\Contracts\PayloadInterface;
use Serenity\Contracts\ServiceInterface;
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
   * @return \Serenity\Contracts\PayloadInterface
   */
  public function successResponse(array $data, $status = 303): PayloadInterface
  {
    return $this->respond($data['message'], 'success', $data['route'], $status);
  }

  /**
   * Return an error response payload.
   *
   * @param  array  $data
   * @param  int  $status
   * @return \Serenity\Contracts\PayloadInterface
   */
  public function errorResponse(array $data, $status = 302): PayloadInterface
  {
    return $this->respond($data['message'], 'error', $data['route'], $status);
  }

  /**
   * Return an info response payload.
   *
   * @param  array  $data
   * @param  int  $status
   * @return \Serenity\Contracts\PayloadInterface
   */
  public function infoResponse(array $data, $status = 303): PayloadInterface
  {
    return $this->respond($data['message'], 'info', $data['route'], $status);
  }

  /**
   * Return a warning response payload.
   *
   * @param  array  $data
   * @param  int  $status
   * @return \Serenity\Contracts\PayloadInterface
   */
  public function warningResponse(array $data, $status = 303): PayloadInterface
  {
    return $this->respond($data['message'], 'warning', $data['route'], $status);
  }

  /**
   * Build up a payload response.
   *
   * @param  array  $data
   * @return \Serenity\Contracts\PayloadInterface
   */
  public function payloadResponse(array $data): PayloadInterface
  {
    return $this->payload()->setData($data);
  }

  /**
   * Build up and return a payload.
   *
   * @param  string  $message
   * @param  string  $level
   * @param  string  $route
   * @param  int  $status
   * @return \Serenity\Contracts\PayloadInterface
   */
  public function respond($message, $level, $route, $status): PayloadInterface
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
   * @return \Serenity\Contracts\PayloadInterface
   */
  public function payload(): PayloadInterface
  {
    return app(InertiaPayload::class);
  }
}
