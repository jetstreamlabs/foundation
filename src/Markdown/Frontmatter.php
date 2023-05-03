<?php

namespace Serenity\Markdown;

use Serenity\Markdown\Contracts\BlockParser;
use Serenity\Markdown\Contracts\Frontmatter as FrontmatterContract;
use Serenity\Markdown\Contracts\MarkdownParser;
use Serenity\Markdown\Contracts\YamlParser;

class Frontmatter implements FrontmatterContract
{
  /**
   * Instantiate the class.
   *
   * @param  \Serenity\Markdown\Contracts\YamlParser  $frontmatterParser
   * @param  \Serenity\Markdown\Contracts\MarkdownParser  $bodyParser
   * @param  \Serenity\Markdown\Contracts\BlockParser  $blockParser
   */
  public function __construct(
      protected YamlParser $frontmatterParser,
      protected MarkdownParser $bodyParser,
      protected BlockParser $blockParser
    ) {
  }

  /**
   * Parse the source content.
   *
   * @param  string  $source
   * @param  mixed  $context
   * @return \Serenity\Markdown\Result
   */
  public function parse(string $source, $context = null): Result
  {
    $blocks = $this->blockParser->parse($source);

    return new Result(
      ($this->frontmatterParser)($blocks->getFrontmatter(), $context),
      ($this->bodyParser)($blocks->getBody(), $context)
    );
  }
}
