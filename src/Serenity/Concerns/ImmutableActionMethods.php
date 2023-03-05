<?php

namespace Serenity\Concerns;

trait ImmutableActionMethods
{
  /**
   * CRUD based methods disallowed in Actions.
   * Create a separate Action for index responses.
   *
   * @return void
   */
  final private function index()
  {
    // method not allowed
  }

  /**
   * CRUD based methods disallowed in Actions.
   * Create a separate Action for show responses.
   *
   * @return void
   */
  final private function show()
  {
    // method not allowed
  }

  /**
   * CRUD based methods disallowed in Actions.
   * Create a separate Action for create responses.
   *
   * @return void
   */
  final private function create()
  {
    // method not allowed
  }

  /**
   * CRUD based methods disallowed in Actions.
   * Delegate storage to your Domain\Services.
   *
   * @return void
   */
  final private function store()
  {
    // method not allowed
  }

  /**
   * CRUD based methods disallowed in Actions.
   * Create a separate Action for edit responses.
   *
   * @return void
   */
  final private function edit()
  {
    // method not allowed
  }

  /**
   * CRUD based methods disallowed in Actions.
   * Delegate updating to your Domain\Services.
   *
   * @return void
   */
  final private function update()
  {
    // method not allowed
  }

  /**
   * CRUD based methods disallowed in Actions.
   * Delegate destroying to your Domain\Services.
   *
   * @return void
   */
  final private function destroy()
  {
    // method not allowed
  }
}
