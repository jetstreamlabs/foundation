<?php

namespace Serenity\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use JSW\Container\ContainerExtension;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;
use Serenity\Markdown\Compiler\CommonMarkCompiler;
use Serenity\Markdown\Directive\CommonMarkDirective;
use Serenity\Markdown\Directive\DirectiveInterface;
use Serenity\Markdown\Highlighters\HighlightCodeExtension;
use Serenity\Markdown\Renderers\AnchorHeadingRenderer;

class DocumentationServiceProvider extends ServiceProvider implements DeferrableProvider
{
  public function boot()
  {
    $this->registerEnvironment();
    $this->registerMarkdown();
    $this->registerCompiler();
    $this->registerDirective();
  }

  /**
   * Register the environment class.
   *
   * @return void
   */
  private function registerEnvironment(): void
  {
    $this->app->singleton('markdown.environment', function (Container $app): Environment {
      $config = $app->config->get('serenity');

      $environment = new Environment(Arr::except($config, ['extensions']));

      $environment->addExtension(new CommonMarkCoreExtension());
      $environment->addExtension(new HighlightCodeExtension());
      $environment->addExtension(new FrontMatterExtension());
      $environment->addExtension(new TableOfContentsExtension());
      $environment->addExtension(new HeadingPermalinkExtension());
      $environment->addExtension(new ContainerExtension());

      foreach ((array) Arr::get($config, 'extensions') as $extension) {
        $environment->addExtension($app->make($extension));
      }

      $environment->addRenderer(Heading::class, new AnchorHeadingRenderer());

      return $environment;
    });

    $this->app->alias('markdown.environment', Environment::class);
    $this->app->alias('markdown.environment', EnvironmentInterface::class);
    $this->app->alias('markdown.environment', EnvironmentBuilderInterface::class);
  }

  /**
   * Register the markdowm class.
   *
   * @return void
   */
  private function registerMarkdown(): void
  {
    $this->app->singleton('markdown.converter', function (Container $app): MarkdownConverter {
      $environment = $app['markdown.environment'];

      return new MarkdownConverter($environment);
    });

    $this->app->alias('markdown.converter', MarkdownConverter::class);
    $this->app->alias('markdown.converter', ConverterInterface::class);
  }

  /**
   * Register the markdown compiler class.
   *
   * @return void
   */
  private function registerCompiler(): void
  {
    $this->app->singleton('markdown.compiler', function (Container $app): CommonMarkCompiler {
      $converter = $app['markdown.converter'];
      $files = $app['files'];
      $storagePath = $app->config->get('view.compiled');

      return new CommonMarkCompiler($converter, $files, $storagePath);
    });

    $this->app->alias('markdown.compiler', CommonMarkCompiler::class);
  }

  /**
   * Register the markdown directive class.
   *
   * @return void
   */
  private function registerDirective(): void
  {
    $this->app->singleton('markdown.directive', function (Container $app): CommonMarkDirective {
      $converter = $app['markdown.converter'];

      return new CommonMarkDirective($converter);
    });

    $this->app->alias('markdown.directive', CommonMarkDirective::class);
    $this->app->alias('markdown.directive', DirectiveInterface::class);
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides(): array
  {
    return [
      'markdown.environment',
      'markdown.converter',
      'markdown.compiler',
      'markdown.directive',
    ];
  }
}
