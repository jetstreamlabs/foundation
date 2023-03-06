<?php

namespace Serenity\Actions\Splade\ConfirmPassword;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use ProtoneMedia\Splade\PasswordValidator;
use Serenity\Action;

class StoreAction extends Action
{
  public function __invoke(Request $request, PasswordValidator $passwordValidator): Response
  {
    $passwordValidator->validateRequest($request, 'password');

    $request->session()->put('auth.password_confirmed_at', time());

    return response()->noContent(200)->skipSpladeMiddleware();
  }
}
