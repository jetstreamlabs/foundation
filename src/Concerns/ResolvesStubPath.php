<?php

namespace Serenity\Concerns;

use Illuminate\Support\Str;
use Serenity\Serenity;
use Symfony\Component\Finder\Finder;

trait ResolvesStubPath
{
  /**
   * Resolve the fully-qualified path to the stub.
   *
   * @param  string  $stub
   * @return string
   */
  protected function resolveStubPath($stub): string
  {
    return file_exists($customPath = Serenity::basePath(trim($stub, '/')))
      ? $customPath
      : dirname(__DIR__).'/Console/Commands'.$stub;
  }

  /**
   * Qualify the given model class base name.
   *
   * @param  string  $model
   * @return string
   */
  protected function qualifyModel(string $model)
  {
    $model = ltrim($model, '\\/');

    $model = str_replace('/', '\\', $model);

    $rootNamespace = $this->rootNamespace();

    if (Str::startsWith($model, $rootNamespace)) {
      return $model;
    }

    return is_dir(app_path('Domain/Models'))
      ? $rootNamespace.'Domain\\Models\\'.$model
      : $rootNamespace.$model;
  }

  /**
   * Get a list of possible model names.
   *
   * @return array<int, string>
   */
  protected function possibleModels()
  {
    $modelPath = is_dir(app_path('Domain/Models')) ? app_path('Domain/Models') : app_path('Models');

    return collect((new Finder)->files()->depth(0)->in($modelPath))
      ->map(fn ($file) => $file->getBasename('.php'))
      ->values()
      ->all();
  }

    /**
     * Get a list of possible event names.
     *
     * @return array<int, string>
     */
    protected function possibleEvents()
    {
      $eventPath = app_path('Domain/Events');

      if (! is_dir($eventPath)) {
        return [];
      }

      return collect((new Finder)->files()->depth(0)->in($eventPath))
          ->map(fn ($file) => $file->getBasename('.php'))
          ->values()
          ->all();
    }
}
