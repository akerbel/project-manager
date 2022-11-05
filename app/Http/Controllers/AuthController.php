<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registration.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $post_data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|min:8',
            ]);

            $user = new User([
                'name' => $post_data['name'],
                'email' => $post_data['email'],
                'password' => Hash::make($post_data['password']),
            ]);
            $user->save();
            return response()->json();
        }
        catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the authenticated user's API token.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                throw new \Exception('Login information is invalid.', 401);
            }
            $user = User::where('email', $request['email'])->firstOrFail();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], !empty($e->getCode()) ? $e->getCode() : 500);
        }

    }

}
