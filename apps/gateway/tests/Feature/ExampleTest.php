<?php

namespace Tests\Feature;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_the_frontend_is_not_served_by_the_api_gateway(): void
    {
        $this->get('/')->assertNotFound();
    }

    public function test_auth_requests_are_forwarded_to_the_configured_service(): void
    {
        config(['gateway.upstreams.auth' => 'http://auth.internal']);
        Http::fake([
            'http://auth.internal/api/v1/auth/login' => Http::response(['access_token' => 'sample-token'], 200),
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'manager@example.gov',
            'password' => 'example-password',
        ])->assertOk()->assertJsonPath('access_token', 'sample-token');

        Http::assertSent(fn (ClientRequest $request): bool => $request->url() === 'http://auth.internal/api/v1/auth/login'
            && $request->method() === 'POST'
            && $request['email'] === 'manager@example.gov'
            && $request->hasHeader('Accept', 'application/json')
            && $request->hasHeader('X-Request-Id')
        );
    }

    public function test_asset_search_forwards_query_and_authorization_headers(): void
    {
        config(['gateway.upstreams.asset' => 'http://asset.internal']);
        Http::fake([
            'http://asset.internal/api/v1/assets*' => Http::response(['data' => []], 200),
        ]);

        $this->withHeader('Authorization', 'Bearer example-token')
            ->getJson('/api/v1/assets?q=ordinateur&page=2')
            ->assertOk()
            ->assertHeader('X-Request-Id');

        Http::assertSent(function (ClientRequest $request): bool {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return $request->url() === 'http://asset.internal/api/v1/assets?q=ordinateur&page=2'
                && $request->hasHeader('Authorization', 'Bearer example-token')
                && $query === ['q' => 'ordinateur', 'page' => '2'];
        });
    }

    public function test_unavailable_upstream_returns_a_problem_response(): void
    {
        config(['gateway.upstreams.history' => 'http://history.internal']);
        Http::fake(fn () => throw new ConnectionException('upstream unavailable'));

        $this->getJson('/api/v1/history')
            ->assertServiceUnavailable()
            ->assertHeader('Content-Type', 'application/problem+json')
            ->assertJsonPath('title', 'Service indisponible');
    }
}
