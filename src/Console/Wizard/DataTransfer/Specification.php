<?php

namespace Serenity\Console\Wizard\DataTransfer;

use Illuminate\Support\Arr;

abstract class Specification
{
  private array $specification;

  public function __construct(array $specification)
  {
    $this->assertSpecificationIsValid($specification);

    $this->specification = $specification;
  }

  public static function fromArray(array $specification): self
  {
    return new static($specification);
  }

  abstract protected function assertSpecificationIsValid(array $specification);

  protected function extract(string $key)
  {
    return Arr::get($this->specification, $key);
  }

  protected function place(string $key, $value): self
  {
    $this->specification[$key] = $value;

    return $this;
  }
}
