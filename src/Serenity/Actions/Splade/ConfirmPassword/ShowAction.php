<?php

namespace Serenity\Actions\Splade\ConfirmPassword;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use ProtoneMedia\Splade\PasswordValidator;
use Serenity\Action;

class ShowAction extends Action
{
  public function __invoke(Request $request, PasswordValidator $passwordValidator): Response
  {
    if ($passwordValidator->recentlyConfirmed($request)) {
      return response()->noContent(200)->skipSpladeMiddleware();
    }

    throw ValidationException::withMessages([
      'password' => __('The password confirmation has expired.'),
    ]);
  }
}
