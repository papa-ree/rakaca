<?php

namespace Paparee\Rakaca\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Paparee\Rakaca\Rakaca
 */
class Rakaca extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Paparee\Rakaca\Rakaca::class;
    }
}
