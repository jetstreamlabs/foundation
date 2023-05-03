<?php

namespace Serenity\Markdown\Parsers;

use Serenity\Markdown\Contracts\BlockParser as BlockParserContract;
use Serenity\Markdown\Result;

class BlockParser implements BlockParserContract
{
  const STATE_INIT = 'STATE_INIT';

  const STATE_FRONTMATTER = 'STATE_FRONTMATTER';

  const STATE_BODY = 'STATE_BODY';

  /**
   * Initialize the class.
   *
   * @param  string  $frontmatterStartToken
   * @param  string  $frontmatterEndToken
   */
  public function __construct(
      protected string $frontmatterStartToken = '---',
      protected string $frontmatterEndToken = '---'
    ) {
  }

  /**
   * Parse the source material.
   *
   * @param  string  $source
   * @return \Serenity\Markdown\Contracts\Result
   */
  public function parse(string $source): Result
  {
    $state = self::STATE_INIT;

    $blocks = [
      self::STATE_FRONTMATTER => '',
      self::STATE_BODY => '',
    ];

    $stream = fopen('php://memory', 'r+');
    fwrite($stream, $source);
    rewind($stream);

    while (($line = fgets($stream)) !== false) {
      $trimmedLine = rtrim($line, "\r\n");

      if ($state == self::STATE_INIT && $trimmedLine == $this->frontmatterStartToken) {
        $state = self::STATE_FRONTMATTER;

        continue;
      }

      if ($state == self::STATE_FRONTMATTER && $trimmedLine == $this->frontmatterEndToken) {
        $state = self::STATE_BODY;

        continue;
      }

      if ($state == self::STATE_INIT) {
        $state = self::STATE_BODY;
      }

      $blocks[$state] .= $line;
    }

    return new Result($blocks[self::STATE_FRONTMATTER], $blocks[self::STATE_BODY]);
  }
}
