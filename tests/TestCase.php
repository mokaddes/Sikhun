<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Feature tests submit forms directly and do not have a browser to
        // bootstrap the CSRF cookie/token pair. Production web middleware
        // remains enabled; only the test transport bypasses CSRF validation.
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }
}
