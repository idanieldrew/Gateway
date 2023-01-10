<?php

namespace App\Services;

abstract class Service
{
    abstract protected function response($status, $payload, $code);
}
