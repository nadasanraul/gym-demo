<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Throwable;

class Controller extends BaseController
{
    use ValidatesRequests;

    protected function errorResponse(int $statusCode, Throwable $throwable, bool $debug = false): JsonResponse
    {
        $data = [
            'message' => 'Something went wrong, please try again later',
        ];

        if ($debug) {
            $data['debug'] = [
                'message' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTrace(),
            ];
        }

        return response()->json($data, $statusCode);
    }
}
