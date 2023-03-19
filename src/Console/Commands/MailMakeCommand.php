<?php

namespace Serenity\Console\Commands;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Serenity\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:mail')]
class MailMakeCommand extends GeneratorCommand
{
  use CreatesMatchingTest;
  use ResolvesStubPath;

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:mail';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new email class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Mailable';

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle()
  {
    if (parent::handle() === false && ! $this->option('force')) {
      return;
    }

    if ($this->option('markdown') !== false) {
      $this->writeMarkdownTemplate();
    }
  }

  /**
   * Write the Markdown template for the mailable.
   *
   * @return void
   */
  protected function writeMarkdownTemplate()
  {
    $path = $this->viewPath(
      str_replace('.', '/', $this->getView()).'.blade.php'
    );

    if (! $this->files->isDirectory(dirname($path))) {
      $this->files->makeDirectory(dirname($path), 0755, true);
    }

    $this->files->put($path, file_get_contents(__DIR__.'/stubs/markdown.stub'));
  }

  /**
   * Build the class with the given name.
   *
   * @param  string  $name
   * @return string
   */
  protected function buildClass($name)
  {
    $class = str_replace(
      '{{ subject }}',
      Str::headline(str_replace($this->getNamespace($name).'\\', '', $name)),
      parent::buildClass($name)
    );

    if ($this->option('markdown') !== false) {
      $class = str_replace(['DummyView', '{{ view }}'], $this->getView(), $class);
    }

    return $class;
  }

  /**
   * Get the view name.
   *
   * @return string
   */
  protected function getView()
  {
    $view = $this->option('markdown');

    if (! $view) {
      $name = str_replace('\\', '/', $this->argument('name'));

      $view = 'mail.'.collect(explode('/', $name))
          ->map(fn ($part) => Str::kebab($part))
          ->implode('.');
    }

    return $view;
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return $this->resolveStubPath(
      $this->option('markdown') !== false
          ? '/stubs/markdown-mail.stub'
          : '/stubs/mail.stub');
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace.'\Domain\Mail';
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the mailable already exists'],
      ['markdown', 'm', InputOption::VALUE_OPTIONAL, 'Create a new Markdown template for the mailable', false],
    ];
  }
}
