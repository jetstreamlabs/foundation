<?php

namespace Serenity\Markdown\Contracts;

interface VoidParser
{
  /**
   * parse the provided source material.
   *
   * @param  mixed  $source
   * @return mixed
   */
  public function parse(mixed $source): mixed;
}
