<?php

namespace Serenity;

use Serenity\Contracts\Payload as PayloadInterface;

abstract class Payload implements PayloadInterface
{
  /**
   * Our response data property.
   *
   * @var array
   */
  protected $data = [];

  /**
   * HTTP status code.
   *
   * @var int
   */
  protected $status = 200;

  /**
   * Does our responder expect a message?
   *
   * @var bool
   */
  protected $expectsMessage = false;

  /**
   * Flash message level.
   *
   * @var string
   */
  protected $level = null;

  /**
   * Flash message string.
   *
   * @var string
   */
  protected $message = null;

  /**
   * Redirect route if passed.
   *
   * @var string
   */
  protected $route = null;

  /**
   * Instantiate the class.
   *
   * @param  mixed  $data
   */
  public function __construct(array $data = [])
  {
    if (! empty($data)) {
      $this->setData($data);
    }

    if (! is_null($this->level) && ! is_null($this->message)) {
      $this->expectsMessage = true;
    }

    return $this;
  }

  /**
   * Return the data property.
   *
   * @return mixed
   */
  public function getData()
  {
    return $this->data;
  }

  /**
   * Return the status property.
   *
   * @return int
   */
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * Tell our responder to expect a message.
   *
   * @return bool
   */
  public function expectsMessage()
  {
    return $this->expectsMessage;
  }

  /**
   * Return the level property.
   *
   * @return string
   */
  public function getLevel()
  {
    return $this->level;
  }

  /**
   * Return the message property.
   *
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }

  /**
   * Return our route property.
   *
   * @return string
   */
  public function getRoute()
  {
    return $this->route;
  }

  /**
   * Dynamically set payload properties when sent via constructor.
   *
   * @param  array  $data
   * @return object
   */
  public function setData(array $data)
  {
    foreach ($data as $key => $value) {
      if (property_exists(__CLASS__, $key)) {
        $this->{$key} = $value;
      } else {
        $this->data[$key] = $value;
      }
    }

    // This will run twice if we set the data in the constructor,
    // but that shouldn't cause us any problems.
    if (! is_null($this->level) && ! is_null($this->message)) {
      $this->expectsMessage = true;
    }

    return $this;
  }

  /**
   * Dynamically access payload properties.
   *
   * @param  string  $key
   * @return mixed
   */
  public function __get($key)
  {
    return $this->data[$key];
  }

  /**
   * Dynamically set container properties.
   *
   * @param  string  $key
   * @param  mixed  $value
   * @return void
   */
  public function __set($key, $value)
  {
    $this->data[$key] = $value;
  }
}
