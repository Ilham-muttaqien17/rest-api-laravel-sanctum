<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseTransactions;

    protected $customLog;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customLog = Log::channel('custom');
    }
}
