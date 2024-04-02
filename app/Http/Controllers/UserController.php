<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Jobs\ProcessUserRegistered;
use Illuminate\Http\Request;

class UserController extends Controller {
	public function store( Request $request ) {
		if (
			empty( $request->email )
			|| empty( $request->firstName )
			|| empty( $request->lastName )
		) {
			return response( ['message' => 'missing payload'], 400 );
		}

		if ( ! filter_var( $request->email, FILTER_VALIDATE_EMAIL ) ) {
			return response( ['message' => 'invalid email'], 400 );
		}

		$user = new User;

		$user->email     = $request->email;
		$user->firstName = $request->firstName;
		$user->lastName  = $request->lastName;
		$user->password  = '12345678';

		$user->save();

		// dispatch job
		ProcessUserRegistered::dispatch( $user );

		return response()->json( ['message' => 'User created successfully'], 201 );
	}

}
