<?php

namespace Serenity\Markdown;

use Serenity\Markdown\Contracts\Result as ResultContract;

class Result implements ResultContract
{
  /**
   * Instantiate the class.
   *
   * @param  mixed  $frontmatter
   * @param  mixed  $body
   */
  public function __construct(
      protected mixed $frontmatter,
      protected mixed $body
    ) {
  }

  /**
   * Return parsed frontmatter.
   *
   * @return mixed
   */
  public function getFrontmatter(): mixed
  {
    return $this->frontmatter;
  }

  /**
   * Return parsed body.
   *
   * @return mixed
   */
  public function getBody(): mixed
  {
    return $this->body;
  }
}
