<?php

namespace App\Services;

use Bootstrap\Services\Service\ServiceContainer;

class Response extends ServiceContainer
{
  public $_alias = 'response';

  public $_shared = true;

  public function boot()
  {
    return new \Phalcon\Http\Response;
  }
}