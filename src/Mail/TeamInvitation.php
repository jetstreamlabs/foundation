<?php

namespace Serenity\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Serenity\Database\TeamInvitation as TeamInvitationModel;

class TeamInvitation extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * The team invitation instance.
   *
   * @var \Serenity\Database\TeamInvitation
   */
  public $invitation;

  /**
   * Create a new message instance.
   *
   * @param  \Serenity\Database\TeamInvitation  $invitation
   * @return void
   */
  public function __construct(TeamInvitationModel $invitation)
  {
    $this->invitation = $invitation;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->markdown('emails.team-invitation', ['acceptUrl' => URL::signedRoute('team-invitations.accept', [
      'invitation' => $this->invitation,
    ])])->subject(__('Team Invitation'));
  }
}
