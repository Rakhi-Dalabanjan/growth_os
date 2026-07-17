<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example — root redirects to dashboard (auth required).
     */
    public function test_the_application_root_redirects(): void
    {
        $response = $this->get('/');

        // Root redirects unauthenticated users to login
        $response->assertRedirect();
    }
}
