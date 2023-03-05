<?php

namespace Serenity\Contracts;

use Illuminate\Contracts\Support\Responsable;

interface ResponderInterface extends Responsable
{
  /**
   * Build up the HTTP response.
   *
   * @param  \Serenity\Contracts\PayloadInterface
   * @return \Serenity\Contracts\ResponderInterface
   */
  public function make(PayloadInterface $payload): self;

  /**
   * Let the responder know if the action needs a payload.
   *
   * @param  bool  $expects
   * @return \Serenity\Contracts\ResponderInterface
   */
  public function expectsPayload(bool $expects = true): self;
}
