<?php

namespace Serenity\Concerns;

use League\CommonMark\ConverterInterface;

trait HasMarkdownParser
{
  /**
   * @param $text
   * @return null|string|string[]
   *
   * @throws \Exception
   */
  public function parse($text)
  {
    $parser = app(ConverterInterface::class)->convert($text);

    return $parser->getContent();
  }
}
