<?php

namespace Serenity\Console\Wizard\Command\Generators\Subwizards;

use Serenity\Console\Wizard\Command\Subwizard;
use Serenity\Console\Wizard\Contracts\Step;
use Serenity\Console\Wizard\Steps\ChoiceStep;
use Serenity\Console\Wizard\Steps\ConfirmStep;
use Serenity\Console\Wizard\Steps\MultipleAnswerTextStep;
use Serenity\Console\Wizard\Steps\MultipleChoiceStep;
use Serenity\Console\Wizard\Steps\TextStep;
use Serenity\Console\Wizard\Steps\UniqueMultipleChoiceStep;

class StepSubwizard extends Subwizard
{
  private array $stepTypes = [
    'Text step' => TextStep::class,
    'Multiple answer text step' => MultipleAnswerTextStep::class,
    'Choice step' => ChoiceStep::class,
    'Multiple choice step' => MultipleChoiceStep::class,
    'Unique multiple choice step' => UniqueMultipleChoiceStep::class,
    'Confirm step' => ConfirmStep::class,
  ];

  private array $stepSubwizards = [
    ChoiceStep::class => MultipleChoiceOptionsSubwizard::class,
    MultipleChoiceStep::class => MultipleChoiceOptionsSubwizard::class,
    UniqueMultipleChoiceStep::class => MultipleChoiceOptionsSubwizard::class,
  ];

  public function getSteps(): array
  {
    return [
      'name' => new TextStep('Enter step name'),
      'question' => new TextStep('Enter step question'),
      'type' => new ChoiceStep('Choose step type', array_keys($this->stepTypes)),
      'has_taking_modifier' => new ConfirmStep("Do you want a 'taking' modifier method for this step?"),
      'has_answered_modifier' => new ConfirmStep("Do you want an 'answered' modifier method for this step?"),
    ];
  }

  public function answeredType(Step $step, string $type)
  {
    $type = $this->stepTypes[$type];

    if ($followUp = $this->guessFollowUp($type)) {
      $this->followUp(
        'step-data',
        $this->subWizard($followUp)
      );
    }

    return $type;
  }

  private function guessFollowUp(string $type): ?Subwizard
  {
    $followUpClass = $this->stepSubwizards[$type] ?? null;

    if ($followUpClass) {
      return new $followUpClass;
    }

    return null;
  }
}
