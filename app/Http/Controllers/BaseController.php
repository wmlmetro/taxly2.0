<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[
    OA\Info(
        title: 'Taxly API',
        version: '1.0.0',
        description: 'API documentation for Taxly platform'
    ),
    OA\Server(
        url: 'https://taxly.test',
        description: 'Local Server'
    ),
    OA\Server(
        url: 'https://dev.taxly.ng',
        description: 'Development Server'
    ),
    OA\Server(
        url: 'https://taxly.ng',
        description: 'Production Server'
    ),
    OA\SecurityScheme(
        securityScheme: 'sanctum',
        type: 'http',
        scheme: 'bearer',
        bearerFormat: 'JWT',
        description: 'Use the token from login'
    ),
    OA\SecurityScheme(
        securityScheme: 'apiKey',
        type: 'apiKey',
        in: 'header',
        name: 'X-API-KEY',
        description: 'API key for authentication'
    )
]
class BaseController extends Controller
{
    public function sendResponse($result, $message, $code = 200, $flatten = false)
    {
        if ($flatten) {
            return response()->json(array_merge(
                ['message' => $message],
                $result
            ), $code);
        }

        $response = [
            'success' => true,
            'data'    => empty($result) ? new \stdClass() : $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function sendValidationError($validator)
    {
        return $this->sendError('Validation Error', $validator->errors(), 422);
    }

    public function sendUnauthorizedError($message = 'Unauthorized')
    {
        return $this->sendError($message, [], 401);
    }

    public function sendNotFoundError($message = 'Resource not found')
    {
        return $this->sendError($message, [], 404);
    }

    public function sendSuccess($message = 'Operation successful')
    {
        return $this->sendResponse([], $message);
    }
}
