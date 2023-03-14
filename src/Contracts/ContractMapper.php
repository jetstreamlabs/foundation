<?php

namespace Serenity\Contracts;

interface ContractMapper
{
  /**
   * Make a new instance via facade.
   *
   * @param  string  $base_path
   * @param  string  $namespace
   * @return self
   */
  public function make(string $base_path, string $namespace = ''): self;

  /**
   * Map our interfaces to concretes and bind to container.
   *
   * @return void
   */
  public function map(): void;

  /**
   * Set the namespace for binding classes.
   *
   * @param  string  $namespace
   * @return self
   */
  public function setNamespace(string $namespace): self;

  /**
   * Set the path to our concrete directory.
   *
   * @param  string  $path
   * @return self
   */
  public function setConcretePath(string $path): self;

  /**
   * Set the path to our interface directory.
   *
   * @param  string  $path
   * @return void
   */
  public function setInterfacePath(string $path): self;
}
