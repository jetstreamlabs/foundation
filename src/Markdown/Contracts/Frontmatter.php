<?php

namespace Serenity\Markdown\Contracts;

interface Frontmatter
{
  /**
   * Parse the source content.
   *
   * @param  string  $source
   * @param  mixed  $context
   * @return \Serenity\Markdown\Contracts\Result
   */
  public function parse(string $source, $context = null): Result;
}
