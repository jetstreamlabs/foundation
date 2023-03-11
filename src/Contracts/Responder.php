<?php

namespace Serenity\Contracts;

interface Responder
{
  /**
   * Build up the HTTP response.
   *
   * @param  \Serenity\Contracts\Payload
   * @return \Serenity\Contracts\Responder
   */
  public function make(Payload $payload): self;

  /**
   * Let the responder know if the action needs a payload.
   *
   * @param  bool  $expects
   * @return \Serenity\Contracts\Responder
   */
  public function expectsPayload(bool $expects = true): self;
}
