<?php

namespace Serenity\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Serenity\Support\Agency;

class Sessions
{
  public static function fetch(Request $request)
  {
    if (config('session.driver') !== 'database') {
      return collect();
    }

    return collect(
      DB::connection(config('session.connection'))
        ->table(config('session.table', 'sessions'))
        ->where('user_id', $request->user()->getAuthIdentifier())
        ->orderBy('last_activity', 'desc')
        ->get()
    )->map(function ($session) use ($request) {
      $agent = Agency::create($session);

      return (object) [
        'agent' => [
          'is_desktop' => $agent->isDesktop(),
          'platform' => $agent->platform(),
          'browser' => $agent->browser(),
        ],
        'ip_address' => $session->ip_address,
        'is_current_device' => $session->id === $request->session()->getId(),
        'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
      ];
    });
  }

  /**
   * Delete the other browser session records from storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return void
   */
  public static function deleteOtherSessionRecords(Request $request)
  {
    if (config('session.driver') !== 'database') {
      return;
    }

    DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
        ->where('user_id', $request->user()->getAuthIdentifier())
        ->where('id', '!=', $request->session()->getId())
        ->delete();
  }
}
