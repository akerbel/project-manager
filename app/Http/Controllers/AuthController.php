<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use VerifiesEmails;

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
            ]);
            $user->setEmail($post_data['email']);
            $user->setPassword($post_data['password']);
            $user->save();

            event(new Registered($user));

            return response()->json([
                'message' => 'Verification email was sent to your email address.'
            ]);
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

    /**
     * Show notice, that email verification link was sent.
     *
     * @return JsonResponse
     */
    public function emailVerificationNotice(): JsonResponse
    {
        return response()->json(['message' => 'Verification link sent!']);
    }

    /**
     * Email verification link.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function emailVerify(Request $request): Response
    {
        try {
            $request->setUserResolver(function () use ($request) {
                return User::findOrFail($request->route('id'));
            });
            $this->redirectTo = '/?verified=1';
            return $this->verify($request);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Resend email verification link.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function emailVerifySend(Request $request): JsonResponse {
        try {
            $request->user()->sendEmailVerificationNotification();
            return response()->json(['message' => 'Verification link sent!']);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
