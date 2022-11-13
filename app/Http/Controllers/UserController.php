<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi as OA;

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
     *
     * @OA\Post(
     *      path="/user",
     *      tags={"Users"},
     *      summary="Create user",
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Authorization token",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string",
     *              example="Bearer 1|y6VIcnEFVMpQvOHStKU0dcXD8CKEdJ0nQuW3anF6"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="name",
     *          description="User name",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          description="User email",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="User password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              minLength=8
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *    )
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
     *
     * @OA\Patch(
     *      path="/user/{id}",
     *      tags={"Users"},
     *      summary="Update user details",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Authorization token",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string",
     *              example="Bearer 1|y6VIcnEFVMpQvOHStKU0dcXD8CKEdJ0nQuW3anF6"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="name",
     *          description="User name",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          description="User email",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="User password",
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              minLength=8
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *    )
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
     *
     * @OA\Delete(
     *      path="/user/{id}",
     *      tags={"Users"},
     *      summary="Delete user",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Authorization token",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string",
     *              example="Bearer 1|y6VIcnEFVMpQvOHStKU0dcXD8CKEdJ0nQuW3anF6"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *    )
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
     *
     * @OA\Get(
     *      path="/user/{?id}",
     *      tags={"Users"},
     *      summary="Get user details. If id is not passed returns details of current user.",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Authorization token",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string",
     *              example="Bearer 1|y6VIcnEFVMpQvOHStKU0dcXD8CKEdJ0nQuW3anF6"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *    )
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
     *
     * @OA\Get(
     *      path="/users",
     *      tags={"Users"},
     *      summary="Get all users",
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Authorization token",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string",
     *              example="Bearer 1|y6VIcnEFVMpQvOHStKU0dcXD8CKEdJ0nQuW3anF6"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *    )
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
