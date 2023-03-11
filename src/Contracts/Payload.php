<?php

namespace Serenity\Contracts;

interface Payload
{
  /**
   * Instantiate the class.
   *
   * @param  mixed  $data
   */
  public function __construct(array $data = []);

  /**
   * Return the data property.
   *
   * @return mixed
   */
  public function getData();

  /**
   * Return the status property.
   *
   * @return int
   */
  public function getStatus();

  /**
   * Tell our responder to expect a message.
   *
   * @return bool
   */
  public function expectsMessage();

  /**
   * Return the level property.
   *
   * @return string
   */
  public function getLevel();

  /**
   * Return the message property.
   *
   * @return string
   */
  public function getMessage();

  /**
   * Return our route property.
   *
   * @return string
   */
  public function getRoute();

  /**
   * Dynamically set payload properties when sent via constructor.
   *
   * @param  array  $data
   * @return object
   */
  public function setData(array $data);

  /**
   * Dynamically access payload properties.
   *
   * @param  string  $key
   * @return mixed
   */
  public function __get($key);

  /**
   * Dynamically set container properties.
   *
   * @param  string  $key
   * @param  mixed  $value
   * @return void
   */
  public function __set($key, $value);
}
