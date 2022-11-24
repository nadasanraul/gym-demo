<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Throwable;

class Controller extends BaseController
{
    use ValidatesRequests;

    /**
     * Error JSON response for HTTP requests
     *
     * @param int $statusCode
     * @param Throwable $throwable
     * @param bool $debug
     * @return JsonResponse
     */
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
            ];
        }

        return response()->json($data, $statusCode);
    }
}
