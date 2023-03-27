<?php

namespace Serenity\Markdown\Renderers;

use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;

class ParseHandler
{
  public static function getFrontMatter(string $markdown)
  {
    $frontMatterParser = new FrontMatterParser(new SymfonyYamlFrontMatterParser());
    $result = $frontMatterParser->parse($markdown);

    return $result->getFrontMatter();
  }
}
