<?php

namespace Serenity\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use League\CommonMark\ConverterInterface;
use Serenity\Contracts\Payload;
use Serenity\Service;
use Serenity\Support\CacheManager;

class DocumentationService extends Service
{
  protected string $title = '';

  protected string $description = '';

  protected array $tags = [];

  protected array $index = [];

  protected array $toc = [];

  protected string $version = '';

  protected string $content = '';

  protected string $canonical = '';

  protected string $docsRoute = '';

  protected string $sectionPage = '';

  protected string $defaultVersion = '';

  protected string $currentSection = '';

  protected int $statusCode = 200;

  protected array $publishedVersions = [];

  protected string $defaultVersionUrl = '';

  /**
   * Create an new instance of the class.
   *
   * @param  \League\CommonMark\ConverterInterface  $converter
   * @param  \Serenity\Support\CacheManager  $cache
   */
  public function __construct(
      protected ConverterInterface $converter,
      protected CacheManager $cache
    ) {
    $this->docsRoute = route('docs.index');
    $this->defaultVersion = config('docs.versions.default');
    $this->publishedVersions = config('docs.versions.published');
    $this->defaultVersionUrl = route('docs.show', ['version' => $this->defaultVersion]);
  }

  /**
   * Service method for index action.
   *
   * @return \Serenity\Contracts\Payload
   */
  public function handle(Request $request): Payload
  {
    $route = route('docs.show', [
      'version' => $this->defaultVersion,
      'page' => config('docs.docs.landing'),
    ]);

    return $this->payloadResponse([
      'route' => $route,
      'status' => 303,
    ]);
  }

  /**
   * Service method for create action.
   *
   * @return Payload
   */
  public function show($version, $page = null): Payload
  {
    if ($this->isNotPublishedVersion($version)) {
      $route = route('docs.show', [
        'version' => config('docs.versions.default'),
        'page' => config('docs.docs.landing'),
      ]);

      return $this->payloadResponse([
        'route' => $route,
        'status' => 303,
      ]);
    }

    return $this->make($version, $page);
  }

  /**
   * Generate our requested documentation page.
   *
   * @param  string  $version
   * @param  string|null  $page
   * @param  array  $data
   * @return \Serenity\Payload
   */
  protected function make(string $version, string $page = null, array $data = []): Payload
  {
    $this->version = $version;
    $this->sectionPage = $page ?: config('docs.docs.landing');

    $menu = config('docs.docs.path')
      .DIRECTORY_SEPARATOR
      .$version
      .DIRECTORY_SEPARATOR
      .'navigation.json';

    $this->index = json_decode(File::get($menu), true);

    $this->toc = [];

    $path = config('docs.docs.path')
      .DIRECTORY_SEPARATOR
      .$version
      .DIRECTORY_SEPARATOR
      .$page.'.md';

    if (File::exists($path)) {
      return $this->cache->remember(function () use ($path, $page) {
        $file = File::get($path);

        $this->currentSection = $page;

        $raw = $this->converter->convert($file);
        $frontMatter = $raw->getFrontMatter();

        $this->title = $frontMatter['title'];
        $this->description = $frontMatter['description'];
        $this->tags = $frontMatter['tags'] ?: implode(', ', $frontMatter['tags']);
        $this->content = $raw->getContent();

        $this->canonical = route('docs.show', [
          'version' => $this->defaultVersion,
          'page' => $this->sectionPage,
        ]);

        return $this->payloadResponse([
          'title' => $this->title,
          'description' => $this->description,
          'keywords' => $this->tags,
          'canonical' => $this->canonical,
          'toc' => $this->toc,
          'content' => $this->content,
          'sidebar' => $this->index,
          'versions' => $this->publishedVersions,
          'currentVersion' => $this->version,
          'currentSection' => $this->currentSection,
          'github' => config('docs.docs.github'),
          'twitter' => config('docs.docs.twitter'),
          'status' => $this->statusCode,
        ]);
      }, 'docs.'.$version.'.'.$page);
    }

    $pathNotFound = config('docs.docs.path').'/404.md';

    $content = $this->converter->convert(File::get($pathNotFound));

    $this->title = 'Page Not Found';
    $this->description = '';
    $this->tags = [];
    $this->content = $content->getContent();
    $this->currentSection = '';
    $this->canonical = '';
    $this->statusCode = 404;

    return $this->payloadResponse([
      'title' => $this->title,
      'description' => $this->description,
      'keywords' => $this->tags,
      'canonical' => $this->canonical,
      'toc' => $this->toc,
      'content' => $this->content,
      'sidebar' => $this->index,
      'versions' => $this->publishedVersions,
      'currentVersion' => $this->version,
      'currentSection' => $this->currentSection,
      'github' => config('docs.docs.github'),
      'twitter' => config('docs.docs.twitter'),
      'status' => $this->statusCode,
    ]);
  }

  /**
   * Check if the given version is in the published versions.
   *
   * @param $version
   * @return bool
   */
  public function isPublishedVersion($version)
  {
    return in_array($version, $this->publishedVersions);
  }

  /**
   * Check if the given version is not in the published versions.
   *
   * @param $version
   * @return bool
   */
  public function isNotPublishedVersion($version)
  {
    return ! $this->isPublishedVersion($version);
  }
}
