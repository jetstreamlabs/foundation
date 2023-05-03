<?php

namespace Serenity\Markdown\Parsers;

use Serenity\Markdown\Contracts\YamlParser as YamlParserContract;
use Symfony\Component\Yaml\Parser;

class YamlParser implements YamlParserContract
{
  /**
   * Instantiate the class.
   *
   * @param  \Symfony\Component\Yaml\Parser  $parser
   * @return void
   */
  public function __construct(
      protected Parser $parser
    ) {
  }

  /**
   * Invoke the class.
   *
   * @param  string  $yaml
   * @return string
   */
  public function __invoke(string $yaml): mixed
  {
    return $this->parse($yaml);
  }

  /**
   * Parse the yaml into string.
   *
   * @param  string  $yaml
   * @return mixed
   */
  public function parse(string $yaml): mixed
  {
    return $this->parser->parse($yaml);
  }
}
