<?php

namespace Serenity\Markdown\Contracts;

interface Result
{
  /**
   * Return parsed frontmatter.
   *
   * @return mixed
   */
  public function getFrontmatter(): mixed;

  /**
   * Return parsed body.
   *
   * @return mixed
   */
  public function getBody(): mixed;
}
