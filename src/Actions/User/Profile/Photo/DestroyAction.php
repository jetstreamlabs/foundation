<?php

namespace Serenity\Actions\User\Profile\Photo;

use Illuminate\Http\Request;
use Serenity\Foundation\Action;

class DestroyAction extends Action
{
  /**
   * Delete the current user's profile photo.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(Request $request)
  {
    $request->user()->deleteProfilePhoto();

    return back(303)->with('status', 'profile-photo-deleted');
  }
}
