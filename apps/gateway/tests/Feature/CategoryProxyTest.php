<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CategoryProxyTest extends TestCase
{
    public function test_category_list_is_forwarded_to_the_asset_service(): void
    {
        config(['gateway.upstreams.asset' => 'http://asset.internal']);
        Http::preventStrayRequests();
        Http::fake([
            'http://asset.internal/api/v1/categories' => Http::response([
                'data' => [['id' => 'category-id', 'label' => 'Mobilier']],
            ]),
        ]);

        $this->withToken('test-token')
            ->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonPath('data.0.label', 'Mobilier');

        Http::assertSent(fn (ClientRequest $request): bool => $request->url() === 'http://asset.internal/api/v1/categories'
            && $request->method() === 'GET'
            && $request->hasHeader('Authorization', 'Bearer test-token'));
    }

    public function test_category_creation_is_forwarded_to_the_asset_service(): void
    {
        config(['gateway.upstreams.asset' => 'http://asset.internal']);
        Http::preventStrayRequests();
        Http::fake([
            'http://asset.internal/api/v1/categories' => Http::response([
                'id' => 'category-id',
                'label' => 'Mobilier',
                'status' => 'Active',
            ], 201),
        ]);

        $this->withToken('test-token')
            ->postJson('/api/v1/categories', ['label' => 'Mobilier'])
            ->assertCreated()
            ->assertJsonPath('label', 'Mobilier');

        Http::assertSent(fn (ClientRequest $request): bool => $request->url() === 'http://asset.internal/api/v1/categories'
            && $request->method() === 'POST'
            && $request['label'] === 'Mobilier'
            && $request->hasHeader('Authorization', 'Bearer test-token'));
    }
}
