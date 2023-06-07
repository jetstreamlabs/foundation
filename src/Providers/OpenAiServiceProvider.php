<?php

declare(strict_types=1);

namespace Serenity\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use OpenAI;
use OpenAI\Client;
use OpenAI\Contracts\ClientContract;
use OpenAI\Laravel\Exceptions\ApiKeyIsMissing;

/**
 * @internal
 */
final class OpenAiServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->singleton(ClientContract::class, static function (): Client {
      $apiKey = config('serenity.api_key');
      $organization = config('serenity.organization');

      if (! is_string($apiKey) || ($organization !== null && ! is_string($organization))) {
        throw ApiKeyIsMissing::create();
      }

      return OpenAI::client($apiKey, $organization);
    });

    $this->app->alias(ClientContract::class, 'openai');
    $this->app->alias(ClientContract::class, Client::class);
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array<int, string>
   */
  public function provides(): array
  {
    return [
      Client::class,
      ClientContract::class,
      'openai',
    ];
  }
}
