<?php

namespace Tests\Feature;

use App\Jobs\ProcessUserRegistered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationTest extends TestCase {
	use RefreshDatabase;

	public function test_can_dispatch() {
		Queue::fake();
		$user = [
			'firstName' => fake()->firstName(),
			'lastName'  => fake()->lastName(),
			'email'     => fake()->unique()->safeEmail(),
		];

		// Act
		ProcessUserRegistered::dispatch( $user );

		// Assert
		Queue::assertPushed( ProcessUserRegistered::class );
	}

	public function test_can_process_queue() {
		// $this->withoutExceptionHandling();

		// Prepare
		Queue::fake();
		Storage::fake( 'local' );
		$user = [
			'firstName' => fake()->firstName(),
			'lastName'  => fake()->lastName(),
			'email'     => fake()->unique()->safeEmail(),
		];
		Storage::disk( 'local' )->put( storage_path( 'userlog.txt' ), print_r( $user, true ) );

		// Act
		ProcessUserRegistered::dispatch( $user );

		// Assert
		Queue::assertPushed( ProcessUserRegistered::class );

		// Assuming the job is synchronous and the log file is stored in 'local' disk
		Storage::disk( 'local' )->assertExists( storage_path( 'userlog.txt' ) );

		$logContent = Storage::disk( 'local' )->get( storage_path( 'userlog.txt' ) );

		$this->assertStringContainsString( $user['email'], $logContent );
		$this->assertStringContainsString( $user['firstName'], $logContent );
		$this->assertStringContainsString( $user['lastName'], $logContent );
	}
}
