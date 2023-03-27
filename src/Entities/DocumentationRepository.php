<?php

namespace Serenity\Entities;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Serenity\Concerns\HasDocumentationAttributes;
use Serenity\Contracts\DocumentationRepository as RepositoryContract;
use Serenity\Entities\Documentation;
use Serenity\Repository;
use Symfony\Component\DomCrawler\Crawler;

class DocumentationRepository extends Repository implements RepositoryContract
{
  use HasAttributes;
  use HasDocumentationAttributes;

  /**
   * Set the repository entity.
   *
   * @return Entity
   */
  public function entity()
  {
    return Documentation::class;
  }

  public function __construct()
  {
    if (is_null($this->entity)) {
      $this->entity = app($this->entity());
    }

    $this->docsRoute = route('docs.index');
    $this->defaultVersion = config('docs.versions.default');
    $this->publishedVersions = config('docs.versions.published');
    $this->defaultVersionUrl = route('docs.show', ['version' => $this->defaultVersion]);
  }

  /**
   * @param $version
   * @param null $page
   * @param array $data
   * @return $this|DocumentationRepository
   */
  public function get($version, $page = null, $data = [])
  {
    $this->version = $version;
    $this->sectionPage = $page ?: config('docs.docs.landing');

    $this->index = $this->entity->getMenu($version);

    $this->toc = $this->entity->getToc($version, $this->sectionPage, $data);

    $this->content = $this->entity->get($version, $this->sectionPage, $data);

    if (is_null($this->content)) {
      return $this->prepareNotFound($version, $this->index, $this->toc);
    }

    $this->prepareTitle()
        ->prepareCanonical()
        ->prepareSection($version, $page);

    return $this;
  }

  /**
   * If the docs content is empty then show 404 page.
   *
   * @return $this
   */
  protected function prepareNotFound($version, $index, $toc)
  {
    $this->title = 'Page Not Found';
    $this->content = $this->entity->getNotFound();
    $this->index = $index;
    $this->toc = $toc;
    $this->currentSection = '';
    $this->version = $version;
    $this->canonical = '';
    $this->statusCode = 404;

    return $this;
  }

  /**
   * Prepare the page title from the first h1 found.
   *
   * @return $this
   */
  protected function prepareTitle()
  {
    $this->title = (new Crawler($this->content))->filterXPath('//h1');
    $this->title = count($this->title) ? $this->title->text() : null;

    return $this;
  }

  /**
   * Prepare the current section page.
   *
   * @param $version
   * @param $page
   * @return $this
   */
  protected function prepareSection($version, $page)
  {
    if ($this->entity->sectionExists($version, $page)) {
      $this->currentSection = $page;
    }

    return $this;
  }

  /**
   * Prepare the canonical link.
   *
   * @return $this
   */
  protected function prepareCanonical()
  {
    if ($this->entity->sectionExists($this->defaultVersion, $this->sectionPage)) {
      $this->canonical = route('docs.show', [
        'version' => $this->defaultVersion,
        'page' => $this->sectionPage,
      ]);
    }

    return $this;
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

  /**
   * @param $version
   * @return $this
   */
  public function search($version)
  {
    return $this->entity->index($version);
  }

  /**
   * Dynamically retrieve attributes on the model.
   *
   * @param  string  $key
   * @return mixed
   */
  public function __get($key)
  {
    return $this->getAttribute($key);
  }
}
