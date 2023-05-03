<?php

namespace Serenity\Markdown\Parsers;

use Serenity\Markdown\Contracts\MarkdownParser as MarkdownParserContract;
use Serenity\Markdown\Parsedown\ParsedownExtra;

class MarkdownParser implements MarkdownParserContract
{
  /**
   * Instantiate the class.
   *
   * @param  \Serenity\Markdown\Parsedown\ParsedownExtra  $parser
   * @return void
   */
  public function __construct(
      protected ParsedownExtra $parser
    ) {
  }

  /**
   * Invoke the class.
   *
   * @param  string  $markdown
   * @return string
   */
  public function __invoke(string $markdown): string
  {
    return $this->parse($markdown);
  }

  /**
   * Parse the text into markdown.
   *
   * @param  string  $markdown
   * @return string
   */
  public function parse(string $markdown): string
  {
    return $this->parser->parse($markdown);
  }
}
