<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessUserRegistered;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller {
	public function store( Request $request ) {

		if (
			empty( $request->email )
			|| empty( $request->firstName )
			|| empty( $request->lastName )
		) {
			return response()->json( ['message' => 'Missing payload'], 400 );
		}

		if (  ! filter_var( $request->email, FILTER_VALIDATE_EMAIL ) ) {
			return response()->json( ['message' => 'Invalid email'], 400 );
		}

		$user = new User;

		$user->email     = $this->sanitizeFilterString( $request->email );
		$user->firstName = $this->sanitizeFilterString( $request->firstName );
		$user->lastName  = $this->sanitizeFilterString( $request->lastName );
		$user->password  = '12345678';

		$user->save();

		// dispatch job
		ProcessUserRegistered::dispatch( $user );

		return response()->json( ['message' => 'User created successfully'], 201 );
	}

	private static function sanitizeFilterString( $value ): string {
		// Strip the tags
		$value = strip_tags( $value );

		// strip further
		$value = filter_var( $value, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// Run the replacement for FILTER_SANITIZE_STRING
		$value = htmlspecialchars( $value );

		// Fix that HTML entities are converted to entity numbers instead of entity name (e.g. ' -> &#34; and not ' -> &quote;)
		// https://stackoverflow.com/questions/64083440/use-php-htmlentities-to-convert-special-characters-to-their-entity-number-rather
		$value = str_replace( ["&quot;", "&#039;"], ["&#34;", "&#39;"], $value );

		return $value;
	}

}
