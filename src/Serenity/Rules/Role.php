<?php

namespace Serenity\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Serenity\Serenity;

class Role implements ValidationRule
{
  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function validate($attribute, $value, $fail): void
  {
    if (! in_array($value, array_keys(Serenity::$roles))) {
      $fail($this->message());
    }
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return __('The :attribute must be a valid role.');
  }
}
