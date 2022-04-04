<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponser
{
    /**
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     * @return array
     */
    public function parseGivenData($data = [], $statusCode = 200, $headers = [])
    {
        $responseStructure = [
            'success' => $data['success'],
            'status' => $data['status'],
            'message' => $data['message'] ?? null,
            'data' => $data['data'] ?? null,
        ];
        if (isset($data['links'])) {
            $responseStructure['links'] = $data['links'];
        }
        if (isset($data['meta'])) {
            $responseStructure['meta'] = $data['meta'];
        }

        if (isset($data['errors'])) {
            $responseStructure['errors'] = $data['errors'];
        }

        if (isset($data['exception']) && ($data['exception'] instanceof \Error || $data['exception'] instanceof \Exception)) {
            if (config('app.env') !== 'production') {
                $responseStructure['exception'] = [
                    'message' => $data['exception']->getMessage(),
                    'file' => $data['exception']->getFile(),
                    'line' => $data['exception']->getLine(),
                    'code' => $data['exception']->getCode(),
                    'trace' => $data['exception']->getTrace(),
                ];
            }

            if ($statusCode === 200) {
                $statusCode = 500;
            }
        }
        if ($data['success'] === false) {
            if (isset($data['error_code'])) {
                $responseStructure['error_code'] = $data['error_code'];
            } else {
                $responseStructure['error_code'] = 1;
            }
        }

        return ['content' => $responseStructure, 'statusCode' => $statusCode, 'headers' => $headers];
    }

    /**
     * Return generic json response with the given data.
     *
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function apiResponse($data = [], $statusCode = 200, $headers = [])
    {
        $result = $this->parseGivenData($data, $statusCode, $headers);

        return response()->json(
            $result['content'],
            $result['statusCode'],
            $result['headers']
        );
    }

    /**
     * @param JsonResource $resource
     * @param null $message
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function responseWithResource(JsonResource $resource, string $message = null, $statusCode = 200, $headers = [])
    {
        return $this->apiResponse(
            [
                'success' => true,
                'status' => 'success',
                'data' => $resource,
                'message' => $message,
            ],
            $statusCode,
            $headers
        );
    }

    /**
     * @param ResourceCollection $resourceCollection
     * @param null $message
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function responseWithResourceCollection(ResourceCollection $resourceCollection, string $message = null, $statusCode = 200, $headers = [])
    {
        return $this->apiResponse(
            [
                'success' => true,
                'status' => 'success',
                'data' => $resourceCollection->response()->getData()->data,
                'message' => $message,
                'links' => $resourceCollection->response()->getData()->links ?? null,
                'meta' => $resourceCollection->response()->getData()->meta ?? null,
            ],
            $statusCode,
            $headers
        );
    }

    protected function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse($message, $code = 400)
    {
        return response()->json([
            'success' => false,
            'status' => 'error',
            'message' => $message,
            'data' => null,
        ], $code);
    }

    protected function notFoundResponse($data = '')
    {
        return response()->json([
            'success' => false,
            'status' => 'not_found',
            'message' => 'Data '.$data.' tidak ditemukan !',
            'data' => null,
        ], 404);
    }
}
