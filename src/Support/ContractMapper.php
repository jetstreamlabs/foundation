<?php

namespace Serenity\Support;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use Serenity\Contracts\ContractMapper as ContractsContractMapper;

class ContractMapper implements ContractsContractMapper
{
  protected string $interface_path = '';

  protected string $concrete_path = '';

  public function __construct(
    protected string $base_path = '',
    protected string $namespace = ''
    ) {
    if (empty($base_path)) {
      $this->base_path = dirname(__DIR__);
    }

    if (empty($namespace)) {
      $this->namespace = 'Serenity';
    }

    try {
      if (! is_dir($this->base_path)) {
        throw new Exception('The selected base path is not a valid directory.');
      }
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Make a new instance via facade.
   *
   * @param  string  $base_path
   * @param  string  $namespace
   * @return self
   */
  public function make(string $base_path, string $namespace = ''): self
  {
    $this->base_path = $base_path;

    if (! empty($namespace)) {
      $this->namespace = $namespace;
    }

    return $this;
  }

  /**
   * Map our interfaces to concretes and bind to container.
   *
   * @return void
   */
  public function map(): void
  {
    // check first for contract and interface paths
    try {
      if (empty($this->concrete_path) || empty($this->interface_path)) {
        throw new Exception('Concrete and Interface paths must be set before mapping.');
      }

      $files = File::allFiles($this->concrete_path);
      $bindings = collect($files)->map(function ($file) {
        // This handles nested directories
        $path = $file->getRelativePath();
        if (! empty($path)) {
          $path = DIRECTORY_SEPARATOR.$path;
        }

        // Generate concrete class
        $fileClass = rtrim($file, '.'.$file->getExtension());
        $concrete_path = str_replace($this->base_path, $this->namespace, $fileClass);
        $concrete = str_replace(DIRECTORY_SEPARATOR, '\\', $concrete_path);

        // Generate interface class
        //(requires best practice of matching file names for interface and concrete)
        $interface_path =
          $this->namespace
          .str_replace($this->base_path, '', $this->interface_path)
          .$path
          .DIRECTORY_SEPARATOR
          .basename($concrete_path);

        $interface = str_replace(DIRECTORY_SEPARATOR, '\\', $interface_path);

        if ($this->hasInterface($concrete, $interface)) {
          return [
            'contract' => $interface,
            'concrete' => $concrete,
          ];
        }
      }, collect([]));

      if (! $bindings->isEmpty()) {
        $this->bind($bindings);
      }
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Set the namespace for binding classes.
   *
   * @param  string  $namespace
   * @return self
   */
  public function setNamespace(string $namespace): self
  {
    $this->namespace = $namespace;

    return $this;
  }

  /**
   * Set the path to our concrete directory.
   *
   * @param  string  $path
   * @return self
   */
  public function setConcretePath(string $path): self
  {
    $concrete_path = $this->base_path
      .DIRECTORY_SEPARATOR
      .$path;

    try {
      if (! is_dir($concrete_path)) {
        throw new Exception('The selected concrete path is not a valid directory.');
      }

      $this->concrete_path = $concrete_path;

      return $this;
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Set the path to our interface directory.
   *
   * @param  string  $path
   * @return void
   */
  public function setInterfacePath(string $path): self
  {
    $interface_path = $this->base_path
      .DIRECTORY_SEPARATOR
      .$path;

    try {
      if (! is_dir($interface_path)) {
        throw new Exception('The selected interface path is not a valid directory.');
      }

      $this->interface_path = $interface_path;

      return $this;
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Bind our interfaces and concretes to container.
   *
   * @param  \Illuminate\Support\Collection  $bindings
   * @return void
   */
  protected function bind(Collection $bindings): void
  {
    $bindings->each(function ($binding) {
      if (! is_null($binding)) {
        app()->bind($binding['contract'], $binding['concrete']);
      }
    });
  }

  /**
   * Determine if the concrete has an interface to bind.
   *
   * @param  string  $concrete
   * @param  string  $contract
   * @return bool
   */
  protected function hasInterface(string $concrete, string $contract)
  {
    try {
      if (! $reflected = new ReflectionClass($concrete)) {
        throw new Exception("The concrete class {$concrete} wasn't found.");
      }

      $interfaces = $reflected->getInterfaces();

      return array_key_exists($contract, $interfaces);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
