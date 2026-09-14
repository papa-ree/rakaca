<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Paparee\Rakaca\Tests\TestCase;

uses(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');
