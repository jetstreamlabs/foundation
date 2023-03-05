<?php

namespace Serenity\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'serenity:install 
    {--teams : Indicates if team support should be installed}
    {--api : Indicates if API support should be installed}
    {--verification : Indicates if email verification support should be installed}
    {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Install the Serenity components and resources';

  /**
   * Execute the console command.
   *
   * @return int|null
   */
  public function handle()
  {
    $this->callSilent('storage:link');
    $this->callSilent('vendor:publish', ['--tag' => 'sanctum-config', '--force' => true]);

    $this->runCommands(['npm install', 'npm run prod']);

    $this->output->newLine(2);
    $this->output->writeln('  <bg=green;fg=white> SUCCESS </> <options=bold>FEEL the Zen!</> Serenity has been installed and compiled.'.PHP_EOL);
    $this->output->writeln('  Now go out and build something <bg=magenta;fg=white;options=bold>BEAUTIFUL!</>'.PHP_EOL);

    return 1;
  }

  /**
   * Run the given commands.
   *
   * @param  array  $commands
   * @return void
   */
  protected function runCommands($commands)
  {
    $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

    if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
      try {
        $process->setTty(true);
      } catch (RuntimeException $e) {
        $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
      }
    }

    $process->run(function ($type, $line) {
      $this->output->writeln('  <bg=blue;fg=white> INFO </> '.$line.PHP_EOL);
    });
  }
}
