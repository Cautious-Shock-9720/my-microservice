<?php

namespace App\Http\Controllers;

use App\Models\User;
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

		$user = new User;

		$user->email     = $request->email;
		$user->firstName = $request->firstName;
		$user->lastName  = $request->lastName;
		$user->password  = '12345678';

		$user->save();

		return response()->json( ['message' => 'User created successfully'], 201 );
	}

}
