<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase {
	use RefreshDatabase;

	public function test_can_write_user() {
		$this->withoutExceptionHandling();

		$userData = [
			'firstName'         => fake()->firstName(),
			'lastName'          => fake()->lastName(),
			'email'             => fake()->unique()->safeEmail(),
		];

		$response = $this->postJson( '/users', $userData );

		$response->assertStatus( 201 );

		$this->assertDatabaseHas( 'users', $userData );
	}

	public function test_can_not_write_user() {
		$this->withoutExceptionHandling();

		$userData = [
			'lastName'          => fake()->lastName(),
			'email'             => fake()->unique()->safeEmail(),
		];

		$response = $this->postJson( '/users', $userData );

		$response->assertStatus( 400 );
	}
}
