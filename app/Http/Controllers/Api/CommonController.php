<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CommonController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/get-error",
     *     summary="Process and return multi-line error details with optional storage cleanup",
     *     tags={"Common"},
     *     @OA\RequestBody(
     *         required=false,
     *         description="Error text",
     *         @OA\JsonContent(
     *             @OA\Property(property="err", type="string", description="Full error text with multiple lines")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error details returned without truncation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="response_code", type="integer", example=422),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="err", type="string", description="Full error text with multiple lines")
     *             )
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getError(Request $request)
    {
        // 1. Storage cleanup if file path exists
        $path = $request->query('path');
        $fileDeleted = false;

        if (!empty($path)) {
            if (Storage::exists($path)) {
                $fileDeleted = Storage::delete($path);
            } elseif (Storage::disk('public')->exists($path)) {
                $fileDeleted = Storage::disk('public')->delete($path);
            }
        }

        // 2. Default error representation
        $defaultError = "This photo is not a valid representation of the night sky.Please upload another photo";

        // 3. Extract error content from body (only 'err')
        $errorInput = null;

        if ($request->isJson() && !empty($request->getContent())) {
            $rawJson = json_decode($request->getContent(), true);
            if (is_array($rawJson) && array_key_exists('err', $rawJson)) {
                $errorInput = $rawJson['err'];
            }
        }

        if ($errorInput === null) {
            if ($request->has('err')) {
                $errorInput = $request->input('err');
            } elseif ($request->query('err')) {
                $errorInput = $request->query('err');
            }
        }

        // Fallback to default error message if none provided or empty
        if ($errorInput === null || (is_string($errorInput) && trim($errorInput) === '')) {
            $errorInput = $defaultError;
        }

        // 4. Preserve all lines in the exact sequential order without cutting, trimming, deduplication, or truncation
        if (is_array($errorInput)) {
            $rawErrorText = implode("\n", array_map(function ($line) {
                return is_scalar($line) ? (string) $line : json_encode($line, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }, $errorInput));
        } else {
            $rawErrorText = (string) $errorInput;
        }

        // Status and response code configuration
        $responseCode = (int) ($request->input('response_code', $request->query('response_code', 422)));
        $status = $request->has('status') ? filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN) : false;

        // 5. Plain text response option if requested (Accept: text/plain or format=text)
        if ($request->wantsJson() === false && ($request->header('Accept') === 'text/plain' || $request->query('format') === 'text' || $request->boolean('raw'))) {
            return response($rawErrorText, $responseCode, [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ]);
        }

        // 6. Return standard BaseController JSON response with only 'err' in data
        return $this->responseJson(
            $status,
            $responseCode,
            "error message",
            [
                'err' => $rawErrorText,
            ]
        );
    }
}
