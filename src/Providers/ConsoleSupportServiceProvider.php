<?php

namespace Serenity\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Providers\ComposerServiceProvider;
use Illuminate\Support\AggregateServiceProvider;
use Serenity\Providers\MigrationServiceProvider;

class ConsoleSupportServiceProvider extends AggregateServiceProvider implements DeferrableProvider
{
  /**
   * The provider class names.
   *
   * @var string[]
   */
  protected $providers = [
    ZenServiceProvider::class,
    ArtisanServiceProvider::class,
    MigrationServiceProvider::class,
    ComposerServiceProvider::class,
  ];
}
