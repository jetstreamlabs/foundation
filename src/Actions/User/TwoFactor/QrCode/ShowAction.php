<?php

namespace Serenity\Actions\User\TwoFactor\QrCode;

use Illuminate\Http\Request;
use Serenity\Action;
use Symfony\Component\HttpFoundation\Response;

class ShowAction extends Action
{
  /**
   * Get the SVG element for the user's two factor authentication QR code.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function __invoke(Request $request): Response
  {
    if (is_null($request->user()->two_factor_secret)) {
      return [];
    }

    return response()->json([
      'svg' => $request->user()->twoFactorQrCodeSvg(),
      'url' => $request->user()->twoFactorQrCodeUrl(),
    ]);
  }
}
