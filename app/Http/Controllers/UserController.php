<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class UserController extends Controller
{
    /**
     * Endpoint to check in a user
     *
     * @param int $id
     * @param UserService $userService
     * @return JsonResponse|Response
     */
    public function checkin(int $id, UserService $userService): JsonResponse|Response
    {
        try {
            $userService->checkinUser($id);

            return response()->noContent();
        } catch (Throwable $e) {
            return $this->errorResponse(statusCode: 400, throwable: $e);
        }
    }
}
