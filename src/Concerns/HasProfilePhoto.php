<?php

namespace Serenity\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Serenity\Foundation\Features;

trait HasProfilePhoto
{
  /**
   * Update the user's profile photo.
   *
   * @param  \Illuminate\Http\UploadedFile  $photo
   * @param  string  $storagePath
   * @return void
   */
  public function updateProfilePhoto(UploadedFile $photo, $storagePath = 'profile-photos')
  {
    tap($this->profile_photo_path, function ($previous) use ($photo, $storagePath) {
      $this->forceFill([
        'profile_photo_path' => $photo->storePublicly(
          $storagePath, ['disk' => $this->profilePhotoDisk()]
        ),
      ])->save();

      if ($previous) {
        Storage::disk($this->profilePhotoDisk())->delete($previous);
      }
    });
  }

  /**
   * Delete the user's profile photo.
   *
   * @return void
   */
  public function deleteProfilePhoto()
  {
    if (! Features::managesProfilePhotos()) {
      return;
    }

    if (is_null($this->profile_photo_path)) {
      return;
    }

    Storage::disk($this->profilePhotoDisk())->delete($this->profile_photo_path);

    $this->forceFill([
      'profile_photo_path' => null,
    ])->save();
  }

  /**
   * Get the URL to the user's profile photo.
   *
   * @return \Illuminate\Database\Eloquent\Casts\Attribute
   */
  public function profilePhotoUrl(): Attribute
  {
    return Attribute::get(function () {
      return $this->profile_photo_path
              ? Storage::disk($this->profilePhotoDisk())->url($this->profile_photo_path)
              : $this->defaultProfilePhotoUrl();
    });
  }

  /**
   * Get the default profile photo URL if no profile photo has been uploaded.
   *
   * @return string
   */
  protected function defaultProfilePhotoUrl()
  {
    if (is_null($this->fname) && is_null($this->lname)) {
      $name = mb_strtoupper(mb_substr($this->username, 0, 1));
    } else {
      $name = mb_strtoupper(
        mb_substr($this->fname, 0, 1).
        mb_substr($this->lname, 0, 1)
      );
    }

    return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
  }

  /**
   * Get the disk that profile photos should be stored on.
   *
   * @return string
   */
  protected function profilePhotoDisk()
  {
    return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : config('serenity.profile_photo_disk', 'public');
  }
}
