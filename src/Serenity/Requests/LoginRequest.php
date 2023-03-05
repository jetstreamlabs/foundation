<?php

namespace Serenity\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Serenity\Serenity;

class LoginRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      Serenity::username() => 'required|string',
      'password' => 'required|string',
    ];
  }
}
