<?php

use Paparee\Rakaca\Tests\TestCase;
use Tests\Concerns\RefreshTestDatabaseSafely;

uses(TestCase::class)
    ->use(RefreshTestDatabaseSafely::class)
    ->in('Feature', 'Unit');
