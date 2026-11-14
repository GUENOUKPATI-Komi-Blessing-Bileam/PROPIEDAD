<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
	public function test_the_application_returns_a_successful_response(): void
	{
		$this->get('/')->assertOk();
	}

	public function test_asset_service_pages_are_accessible(): void
	{
		foreach (['/assets', '/assets/create', '/assets/INV-2026-0482', '/movements/create', '/inventories/2026'] as $path) {
			$this->get($path)->assertOk();
		}
	}
}
