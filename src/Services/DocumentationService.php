<?php

namespace Serenity\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use League\CommonMark\ConverterInterface;
use Serenity\Contracts\Payload;
use Serenity\Support\CacheManager;
use Symfony\Component\DomCrawler\Crawler;

class DocumentationService extends Service
{
  protected string $title = '';

  protected string $description = '';

  protected array $tags = [];

  protected array $index = [];

  protected array $toc = [];

  protected string $version = '';

  protected string $content = '';

  protected ?array $prevPage = null;

  protected ?array $nextPage = null;

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
    $this->docsRoute = route('docs.home');
    $this->defaultVersion = config('serenity.versions.default');
    $this->publishedVersions = config('serenity.versions.published');
    $this->defaultVersionUrl = route('docs.show', ['version' => $this->defaultVersion]);
  }

  /**
   * Service method for index action.
   *
   * @return \Serenity\Contracts\Payload
   */
  public function handle(Request $request): Payload
  {
    return $this->payloadResponse([
      'version' => $this->defaultVersion,
      'page' => config('serenity.docs.landing'),
      'status' => 302,
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
        'version' => config('serenity.versions.default'),
        'page' => config('serenity.docs.landing'),
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
    $this->sectionPage = $page ?: config('serenity.docs.landing');

    $this->buildMenuIndex();

    $this->generatePreviousNext();

    $path = config('serenity.docs.path')
      .DIRECTORY_SEPARATOR
      .$version
      .DIRECTORY_SEPARATOR
      .$page.'.md';

    if (File::exists($path)) {
      $this->currentSection = $this->sectionPage;

      $this->buildContent($path);

      $this->renderToc();

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
        'nextPage' => $this->nextPage,
        'prevPage' => $this->prevPage,
        'currentVersion' => $this->version,
        'currentSection' => $this->currentSection,
        'github' => config('serenity.docs.github'),
        'twitter' => config('serenity.docs.twitter'),
        'status' => $this->statusCode,
      ]);
    }

    $pathNotFound = config('serenity.docs.path').'/404.md';

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
      'nextPage' => $this->nextPage,
      'prevPage' => $this->prevPage,
      'currentVersion' => $this->version,
      'currentSection' => $this->currentSection,
      'github' => config('serenity.docs.github'),
      'twitter' => config('serenity.docs.twitter'),
      'status' => $this->statusCode,
    ]);
  }

  protected function buildMenuIndex()
  {
    $this->index = $this->cache->remember(function () {
      $menu = config('serenity.docs.path')
        .DIRECTORY_SEPARATOR
        .$this->version
        .DIRECTORY_SEPARATOR
        .'navigation.json';

      return File::json($menu);
    }, 'doc-nav.'.$this->version);
  }

  protected function buildContent(string $path)
  {
    $raw = $this->cache->remember(function () use ($path) {
      $file = File::get($path);

      return $this->converter->convert($file);
    }, 'doc-page.'.$this->version.'.'.$this->currentSection);

    $frontMatter = $raw->getFrontMatter();

    $this->title = $frontMatter['title'];
    $this->description = $frontMatter['description'];
    $this->tags = $frontMatter['tags'] ?: implode(', ', $frontMatter['tags']);
    $this->content = $raw->getContent();
  }

  protected function generatePreviousNext()
  {
    $links = [];

    foreach ($this->index as $category => $subLinks) {
      foreach ($subLinks as $set) {
        $links[] = [
          'text' => $set['title'],
          'link' => $set['uri'],
          'version' => $set['version'],
        ];
      }
    }

    collect($links)->filter(function ($link, $i) use ($links) {
      if ($link['link'] === $this->sectionPage && $i > 0) {
        $key = $i - 1;

        $prev = $links[$key];

        $this->prevPage = [
          'title' => $prev['text'],
          'link' => route('docs.show', ['version' => $prev['version'], 'page' => $prev['link']]),
        ];
      }

      if ($link['link'] === $this->sectionPage && $i < (count($links) - 1)) {
        $key = $i + 1;
        $next = $links[$key];

        $this->nextPage = [
          'title' => $next['text'],
          'link' => route('docs.show', ['version' => $next['version'], 'page' => $next['link']]),
        ];
      }
    });
  }

  /**
   * Generate TOC links from the toc in content.
   *
   * @return void
   */
  protected function renderToc(): void
  {
    $urls = (new Crawler($this->content, route('docs.home')))
      ->filter('ul:first-of-type > li > a')
      ->links();

    $links = [];

    foreach ($urls as $url) {
      $link = str_replace(route('docs.home'), '', $url->getNode()->getAttribute('href'));

      $links[] = [
        'text' => $url->getNode()->nodeValue,
        'href' => $link,
      ];
    }

    $this->toc = $links;
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
