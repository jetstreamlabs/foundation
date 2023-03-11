<?php

namespace Serenity;

use Illuminate\Support\Facades\Route;
use Serenity\Contracts\Payload;
use Serenity\Contracts\Responder as ResponderInterface;

abstract class Responder implements ResponderInterface
{
  /**
   * Local payload property.
   */
  protected Payload $payload;

  /**
   * Does the given action require a payload?
   */
  protected bool $expectsPayload = true;

  /**
   * Local data prop extracted from payload.
   */
  protected ?array $data = [];

  /**
   * Level returned in our payload.
   */
  protected ?string $level = '';

  /**
   * Route returned by our payload.
   */
  protected ?string $route = '';

  /**
   * Message returned by our payload.
   */
  protected ?string $message = '';

  /**
   * Build up the HTTP response.
   *
   * @param  \Serenity\Contracts\Payload
   * @return \Serenity\Contracts\Responder
   */
  public function make(Payload $payload): ResponderInterface
  {
    $this->payload = $payload;

    $this->compileData();

    return $this;
  }

  /**
   * Let the responder know if the action needs a payload.
   *
   * @param  bool  $expects
   * @return \Serenity\Contracts\Responder
   */
  public function expectsPayload(bool $expects = true): ResponderInterface
  {
    $this->expectsPayload = $expects;

    return $this;
  }

  /**
   * Extract data from the payload and store.
   *
   * @return void
   */
  public function compileData(): void
  {
    if ($this->expectsPayload) {
      $data = $this->payload->getData();

      if ($this->payload->expectsMessage()) {
        $this->level = $this->payload->getLevel();
        $this->message = $this->payload->getMessage();

        $data['flash'] = [
          $this->level => $this->message,
        ];
      }
    }

    $data['breadcrumbs'] = app('breadcrumbs')->render();

    $this->data = $data;

    $this->route = Route::has($this->payload->getRoute())
      ? route($this->payload->getRoute())
      : $this->payload->getRoute();
  }

  abstract public function send();

  abstract public function replace();
}
