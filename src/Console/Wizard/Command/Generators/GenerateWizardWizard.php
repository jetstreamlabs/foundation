<?php

namespace Serenity\Console\Wizard\Command\Generators;

use Serenity\Console\Wizard\Command\Generators\Subwizards\StepSubwizard;
use Serenity\Console\Wizard\Command\GeneratorWizard;
use Serenity\Console\Wizard\Contracts\Step;
use Serenity\Console\Wizard\DataTransfer\WizardSpecification;
use Serenity\Console\Wizard\Steps\TextStep;
use Serenity\Console\Wizard\Templates\WizardTemplate;

class GenerateWizardWizard extends GeneratorWizard
{
  protected $signature = 'wizard:generate';

  protected $type = 'Wizard';

  public function getSteps(): array
  {
    return [
      'signature' => new TextStep('Enter the signature for your wizard'),
      'description' => new TextStep('Enter the description for your wizard'),
      'steps' => $this->repeat(
        $this->subWizard(new StepSubwizard())
      )->withRepeatPrompt('Do you want to add a wizard step?', true),
    ];
  }

  protected function getNameStep(): Step
  {
    return new TextStep('Enter the class name for your wizard');
  }

  protected function generateTarget(): string
  {
    $specification = WizardSpecification::fromArray($this->answers->all())
                                        ->setName($this->getClassShortName())
                                        ->setNamespace($this->getClassNamespace());

    return WizardTemplate::bySpecification($specification)->print();
  }

  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace."\Console\Command";
  }
}
