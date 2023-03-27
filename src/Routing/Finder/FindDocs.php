<?php

namespace Serenity\Routing\Finder;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class FindDocs
{
  public function in(string $directory): void
  {
    $files = (new Finder())->files()->name('*.md')->in($directory);

    collect($files)->each(function (SplFileInfo $file) use ($directory) {
      $this->registerRouteForView($file, $directory);
    });
  }

  protected function registerRouteForView(SplFileInfo $file, string $directory): void
  {
    $uri = $this->determineUri($file, $directory);
    $name = $this->determineName($file, $directory);

    Route::get('/{version}/{page}', \App\Actions\Public\Documentation\ShowAction::class)->name('docs.show');
  }

  protected function determineUri(SplFileInfo $file, string $directory): string
  {
    $uri = Str::of($file->getPathname())
        ->after($directory)
        ->beforeLast('.md');

    $uri = Str::replaceLast(DIRECTORY_SEPARATOR.'index', DIRECTORY_SEPARATOR, (string) $uri);

    $segments = explode(DIRECTORY_SEPARATOR, ltrim($uri, DIRECTORY_SEPARATOR));

    array_shift($segments);

    return collect($segments)
        ->map(function (string $uriSegment) {
          $segment = Str::kebab($uriSegment);
          if (is_numeric($uriSegment)) {
            $segment = Str::replace($uriSegment, '{version}', $uriSegment);
          }

          return $segment;
        })
        ->join('/');
  }

  protected function determineName(SplFileInfo $file, string $baseDirectory): string
  {
    $prefix = Str::of($file->getRelativePath())
      ->before(DIRECTORY_SEPARATOR);

    $uri = $this->determineUri($file, $baseDirectory);

    if ($uri === '{version}/') {
      return 'docs.home';
    }

    return Str::of($file->getRelativePath())
      ->before(DIRECTORY_SEPARATOR)
        .'.'
        .Str::camel(basename($uri));

    // return Str::of($this->determineUri($file, $baseDirectory))
    //     ->after('/')
    //     ->replace('/', '.')
    //     ->rtrim('.');
  }
}
