<?php

namespace Serenity\Entities;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Serenity\Concerns\HasBladeParser;
use Serenity\Concerns\HasMarkdownParser;
use Serenity\Concerns\Indexable;
use Serenity\Concerns\SelfResolves;
use Serenity\Entity;
use Serenity\Support\CacheManager;
use Symfony\Component\DomCrawler\Crawler;

class Documentation extends Entity
{
  use SelfResolves;
  use HasMarkdownParser;
  use HasBladeParser;
  use Indexable;

  /**
   * Create a new documentation instance.
   *
   * @param  \Illuminate\Filesystem\Filesystem  $files
   * @param  \Serenity\Support\CacheManager  $cache
   */
  public function __construct(
      protected Filesystem $files,
      protected CacheManager $cache
    ) {
  }

  /**
   * Get the documentation index page.
   *
   * @param  string  $version
   * @return string
   */
  public function getMenu($version)
  {
    return $this->cache->remember(function () use ($version) {
      $path = config('docs.docs.path').'/'.$version.'/menu.md';

      if ($this->files->exists($path)) {
        $parsedContent = $this->parse($this->files->get($path));

        $content = $this->replaceLinks($version, $parsedContent);

        //$content = $this->styleHeaders($content);
        //$content = $this->styleLinks($content);

        return $content;
      }

      return null;
    }, 'docs.docs.'.$version.'.menu');
  }

  /**
   * Get the TOC from the current page for the TOC component.
   *
   * @param  string  $version
   * @param  string  $page
   * @param  array  $data
   * @return mixed
   */
  public function getToc($version, $page, $data = [])
  {
    return $this->cache->remember(function () use ($version, $page) {
      $path = config('docs.docs.path').'/'.$version.'/'.$page.'.md';

      if ($this->files->exists($path)) {
        $parsedContent = $this->parse($this->files->get($path));

        $urls = (new Crawler($parsedContent, route('index')))
            ->filter('ul:first-of-type > li > a')
            ->links();

        $links = [];

        foreach ($urls as $url) {
          $link = str_replace(route('index'), '', $url->getNode()->getAttribute('href'));

          $links[] = [
            'text' => $url->getNode()->nodeValue,
            'href' => $link,
          ];
        }

        return $links;
      }
    }, 'docs.docs.'.$version.'.'.$page.'.toc');
  }

  /**
   * Get the given documentation page.
   *
   * @param $version
   * @param $page
   * @param  array  $data
   * @return mixed
   */
  public function get($version, $page, $data = [])
  {
    return $this->cache->remember(function () use ($version, $page, $data) {
      $path = config('docs.docs.path').'/'.$version.'/'.$page.'.md';

      if ($this->files->exists($path)) {
        $parsedContent = $this->parse($this->files->get($path));

        $crawler = new Crawler($parsedContent, route('index'));
        $crawler->filter('ul:first-of-type')->each(function (Crawler $crawler) {
          $node = $crawler->getNode(0);
          $node->parentNode->removeChild($node);
        });

        $parsedContent = $crawler->html();

        $dom = new \DOMDocument();
        $dom->loadHtml(mb_convert_encoding($parsedContent, 'HTML-ENTITIES', 'UTF-8'));
        $code_elements = $dom->getElementsByTagName('code');

        foreach ($code_elements as $element) {
          $language = str_replace('language-', '', $element->getAttribute('class'));
          $element->setAttribute('class', 'hljs');
          $code = $element->firstChild->wholeText;

          $highlighted = app('highlighter')->highlight($language, $code);

          $element->firstChild->nodeValue = '';
          $template = $dom->createDocumentFragment();
          $template->appendXML($highlighted->value);
          $element->appendChild($template);
        }

        $parsedContent = $dom->saveHTML();

        //$parsedContent = $this->replaceLinks($version, $parsedContent);

        return $this->renderBlade($parsedContent, $data);
      }

      return null;
    }, 'docs.docs.'.$version.'.'.$page);
  }

  public function getNotFound()
  {
    $path = config('docs.docs.path').'/404.md';

    if ($this->files->exists($path)) {
      $parsedContent = $this->parse($this->files->get($path));

      return $this->renderBlade($parsedContent);
    }
  }

  /**
   * Replace the version and route placeholders.
   *
   * @param  string  $version
   * @param  string  $content
   * @return string
   */
  public static function replaceLinks($version, $content)
  {
    $content = Str::replace('{{version}}', $version, $content);
    $content = Str::replace('{{route}}', trim(config('docs.docs.route'), '/'), $content);
    $content = Str::replace('"#', '"'.request()->getRequestUri().'#', $content);

    return $content;
  }

  /**
   * Add styles for index h2s
   *
   * @param  string  $content
   * @return string
   */
  public function styleHeaders($content)
  {
    $crawler = new Crawler($content, route('index'));
    $crawler->filter('h2')->each(function (Crawler $crawler) {
      $h2 = $crawler->getNode(0);
      $h2->setAttribute('class', 'px-2 mt-5 mb-2 text-xs font-semibold text-gray-400 uppercase');
    });

    return $crawler->html();
  }

  /**
   * Add styles for index links.
   *
   * @param  string  $content
   * @return string
   */
  public function styleLinks($content)
  {
    $crawler = new Crawler($content, route('index'));
    $crawler->filter('ul > li > a')->each(function (Crawler $crawler) {
      $link = $crawler->getNode(0);
      $link->setAttribute('class', 'flex items-center mt-1 px-4 py-2 text-sm font-medium leading-5 text-white transition duration-150 ease-in-out rounded-md group hover:bg-ceru-700 focus:outline-none focus:bg-ceru-700');
    });

    return $crawler->html();
  }

  /**
   * Check if the given section exists.
   *
   * @param  string  $version
   * @param  string  $page
   * @return bool
   */
  public function sectionExists($version, $page)
  {
    return $this->files->exists(
      config('docs.docs.path').'/'.$version.'/'.$page.'.md'
    );
  }
}
