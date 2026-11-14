<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_the_history_page_is_available_at_its_public_route(): void
    {
        $this->get('/history')->assertRedirect('/');
        $this->get('/')->assertOk()->assertSee('Journal d’audit');
    }
}
