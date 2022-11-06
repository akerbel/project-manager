<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Provides tryCatch() method to avoid duplication of long "catch" code.
 */
trait TryCatchTrait
{

    /**
     * Get model name of the controller.
     *
     * @return string
     */
    abstract protected function getModelName(): string;

    /**
     * Try code and catch typical exceptions.
     *
     * @param callable $func
     *
     * @return JsonResponse
     */
    protected function tryCatch(callable $func): JsonResponse {
        try {
            return $func();
        }
        catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => ucfirst($this->getModelName()) . ' is not found'], 404);
        }
        catch (AuthorizationException $e) {
            return response()->json(['error' => 'Access is forbidden.'], 403);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
