<?php

namespace Aeunius\NumeroALetras\Tests;

use Aeunius\NumeroALetras\Facades\NumeroALetras;
use Aeunius\NumeroALetras\NumeroALetrasServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            NumeroALetrasServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'NumeroALetras' => NumeroALetras::class,
        ];
    }
}
