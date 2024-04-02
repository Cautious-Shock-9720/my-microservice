<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUserRegistered implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $user;

	public function __construct( $user ) {
		$this->user = $user;
	}

	public function handle() {
		if (  ! isset( $this->user['email'] ) || ! isset( $this->user['firstName'] ) || ! isset( $this->user['lastName'] ) ) {
			Log::error('Missing required user parameter', ['user' => $this->user]);
			throw new Exception( 'Missing required user parameter' );
		}

		file_put_contents( storage_path( 'userlog.txt' ), print_r( $this->user, true ) );
	}

}
