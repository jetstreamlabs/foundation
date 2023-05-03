<?php

namespace Serenity\Markdown\Parsers;

use Serenity\Markdown\Contracts\VoidParser as VoidParserContract;

class VoidParser implements VoidParserContract
{
  /**
   * Invoke our class.
   *
   * @param  mixed  $source
   * @return mixed
   */
  public function __invoke(mixed $source): mixed
  {
    return $this->parse($source);
  }

  /**
   * parse the provided source material.
   *
   * @param  mixed  $source
   * @return mixed
   */
  public function parse(mixed $source): mixed
  {
    return $source;
  }
}
