<?php

namespace Serenity\Markdown\Contracts;

interface YamlParser
{
  /**
   * Parse the yaml into string.
   *
   * @param  string  $yaml
   * @return mixed
   */
  public function parse(string $yaml): mixed;
}
