<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use TryCatchTrait;

    /**
     * Get model name of the controller.
     *
     * @return string
     */
    protected function getModelName(): string
    {
        return 'user';
    }

    /**
     * Create a user.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function post(Request $request): JsonResponse
    {
        return $this->tryCatch(function () use ($request) {
            $this->authorize('create', User::class);

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
                'message' => 'Verification email was sent to user`s email address.'
            ]);
        });
    }

    /**
     * Update the user.
     *
     * @param Request $request
     * @param int|null $id
     *
     * @return JsonResponse
     */
    public function patch(Request $request, int $id = null): JsonResponse
    {
        return $this->tryCatch(function () use ($request, $id) {
            if (!empty($id)) {
                $user = User::findOrFail($id);
            }
            else {
                $user = Auth::user();
            }
            $this->authorize('update', $user);

            $post_data = $request->validate([
                'name' => 'string',
                'email' => 'string|unique:users,email',
                'password' => 'min:8',
            ]);

            if (!empty($post_data['name'])) {
                $user->name = $post_data['name'];
            }
            if (!empty($post_data['email'])) {
                $user->setEmail($post_data['email']);
            }
            if (!empty($post_data['password'])) {
                $user->setPassword($post_data['password']);
            }

            $user->save();

            if (!empty($post_data['email'])) {
                event(new Registered($user));
                $result = [
                    'message' => 'Verification email was sent to user`s email address.'
                ];
            }

            return response()->json($result ?? []);
        });
    }

    /**
     * Delete user.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        return $this->tryCatch(function() use ($id) {
            $user = User::findOrFail($id);
            $this->authorize('delete', $user);
            User::destroy($id);
            return response()->json();
        });
    }

    /**
     * Get the user.
     *
     * @param int|null $id
     *
     * @return JsonResponse
     */
    public function get(int $id = null): JsonResponse {
        return $this->tryCatch(function() use ($id) {
            if (!empty($id)) {
                $user = User::findOrFail($id);
            }
            else {
                $user = Auth::user();
            }
            $this->authorize('view', $user);
            return response()->json($user->toArray());
        });
    }

    /**
     * Get all users.
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        return $this->tryCatch(function() {
            $this->authorize('viewAny', User::class);
            $result = User::all();
            return response()->json($result);
        });
    }

}
