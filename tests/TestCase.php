<?php

namespace WillPower232\CloverParserLaravel\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * @return array<class-string>
     */
    protected function getPackageProviders($app)
    {
        return [\WillPower232\CloverParserLaravel\CloverParserServiceProvider::class];
    }
}
