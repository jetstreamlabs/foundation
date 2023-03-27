<?php

namespace Serenity\Services;

use Illuminate\Http\Request;
use Serenity\Contracts\DocumentationRepository;
use Serenity\Contracts\Payload;
use Serenity\Service;

class DocumentationService extends Service
{
  /**
   * Create an new instance of the class.
   *
   * @param \Serenity\Contracts\DocumentationRepository $docs
   */
  public function __construct(
      protected DocumentationRepository $docs
    ) {
  }

  /**
   * Service method for index action.
   *
   * @return \Serenity\Contracts\Payload
   */
  public function handle(Request $request): Payload
  {
    // our initial request to docs should grab the default
    // from the configuration and redirect.

    // assemble this route first
    $route = route('docs.show', [
      'version' => config('docs.versions.default'),
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
  public function getDocs($version, $page = null): Payload
  {
    $doc = $this->docs->get($version, $page);

    if ($this->docs->isNotPublishedVersion($version)) {
      $route = route('docs.show', [
        'version' => config('docs.versions.default'),
        'page' => config('docs.docs.landing'),
      ]);

      return $this->payloadResponse([
        'route' => $route,
        'status' => 303,
      ]);
    }
    dd($doc);

    return $this->payloadResponse([
      'title' => $doc->title,
      'canonical' => $doc->canonical,
      'toc' => $doc->toc,
      'content' => $doc->content,
      'sidebar' => $doc->index,
      'versions' => $doc->publishedVersions,
      'currentVersion' => $version,
      'currentSection' => $doc->currentSection,
      'github' => config('docs.docs.github'),
      'twitter' => config('docs.docs.twitter'),
      'status' => $doc->statusCode,
    ]);
  }
}
