<?php

namespace Serenity\Markdown\Contracts;

interface BlockParser
{
  /**
   * Parse the source material.
   *
   * @param  string  $source
   * @return \Serenity\Markdown\Contracts\Result
   */
  public function parse(string $source): Result;
}
