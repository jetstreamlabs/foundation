<?php

namespace Serenity\Markdown\Highlighters;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\ExtensionInterface;
use Serenity\Markdown\Renderers\FencedCodeRenderer;
use Serenity\Markdown\Renderers\IndentedCodeRenderer;

class HighlightCodeExtension implements ExtensionInterface
{
  protected string $theme = 'nord';

  public function __construct()
  {
    //
  }

  public function register(EnvironmentBuilderInterface $environment): void
  {
    $shiki = new Shiki(defaultTheme: $this->theme);

    $codeBlockHighlighter = new ShikiHighlighter($shiki);

    $environment
        ->addRenderer(FencedCode::class, new FencedCodeRenderer($codeBlockHighlighter), 10)
        ->addRenderer(IndentedCode::class, new IndentedCodeRenderer($codeBlockHighlighter), 10);
  }
}
