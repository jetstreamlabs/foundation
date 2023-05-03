<?php

namespace Serenity\Markdown\Contracts;

interface MarkdownParser
{
  /**
   * Parse the text into markdown.
   *
   * @param  string  $markdown
   * @return string
   */
  public function parse(string $markdown): string;
}
