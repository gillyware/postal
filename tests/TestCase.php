<?php

namespace Gillyware\Postal\Tests;

use Gillyware\Postal\Providers\PostalServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [PostalServiceProvider::class];
    }
}
