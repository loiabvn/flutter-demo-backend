<?php

namespace App\Http\Controllers;

use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        # By default we are using here auth:api middleware
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Handle POST /api/auth/login
     *
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return $this->sendError('Unauthorized', 401);
        }

        $user = auth()->user();

        # If all credentials are correct - we are going to generate a new access token and send it back on response
        return $this->sendResponse([
            'name' => $user['name'],
            'email' => $user['email'],
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 # This is expiration time of access token
        ], 'Successfully logged in.');
    }

    /**
     * Handle POST /api/auth/register
     *
     * Register a new user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register()
    {
        # Here we are going to create a new user
        $data = request([
            'name',
            'email',
            'password'
        ]);

        $user = User::create($data);

        # And then we are going to generate a new access token for this user
        $token = auth()->login($user);

        return $this->sendResponse([
            'name' => $user['name'],
            'email' => $user['email'],
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 'Successfully registered.');
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        # Here we just get information about current user
        $user = auth()->user();
        return $this->sendResponse($user, 'User retrieved successfully.');
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout(); # This is just logout function that will destroy access token of current user

        return $this->sendResponse([], 'Successfully logged out.');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        # When access token will be expired, we are going to generate a new one wit this function
        # and return it here in response
        $token = auth()->refresh();
        return $this->sendResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 'Successfully refreshed.');
    }
}
