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
     *     @OA\Parameter(
     *         name="path",
     *         in="query",
     *         description="File path in storage to delete if present",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         description="Multi-line error text or array of error lines",
     *         @OA\JsonContent(
     *             @OA\Property(property="err", type="string", description="Full error text with multiple lines"),
     *             @OA\Property(property="error", type="string", description="Full error text with multiple lines"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), description="List of error lines in exact order"),
     *             @OA\Property(property="path", type="string", description="Storage file path to delete")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error details returned without truncation"
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getError(Request $request)
    {
        // 1. Storage cleanup if file path exists
        $path = $request->input('path', $request->query('path'));
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

        // 3. Extract error content from various possible inputs without losing anything
        $errorInput = null;

        if ($request->hasFile('file')) {
            $errorInput = file_get_contents($request->file('file')->getRealPath());
        } elseif ($request->hasFile('error_file')) {
            $errorInput = file_get_contents($request->file('error_file')->getRealPath());
        } else {
            if ($request->isJson() && !empty($request->getContent())) {
                $rawJson = json_decode($request->getContent(), true);
                if (is_array($rawJson)) {
                    if (array_key_exists('err', $rawJson)) {
                        $errorInput = $rawJson['err'];
                    } elseif (array_key_exists('error', $rawJson)) {
                        $errorInput = $rawJson['error'];
                    } elseif (array_key_exists('errors', $rawJson)) {
                        $errorInput = $rawJson['errors'];
                    }
                }
            }

            if ($errorInput === null) {
                if ($request->has('err')) {
                    $errorInput = $request->input('err');
                } elseif ($request->has('error')) {
                    $errorInput = $request->input('error');
                } elseif ($request->has('errors')) {
                    $errorInput = $request->input('errors');
                } elseif (!empty($request->getContent()) && in_array(strtoupper($request->method()), ['POST', 'PUT', 'PATCH'])) {
                    $errorInput = $request->getContent();
                } elseif ($request->query('err')) {
                    $errorInput = $request->query('err');
                } elseif ($request->query('error')) {
                    $errorInput = $request->query('error');
                } elseif ($request->query('errors')) {
                    $errorInput = $request->query('errors');
                }
            }
        }

        // Fallback to default error message if none provided or empty
        if ($errorInput === null || (is_string($errorInput) && trim($errorInput) === '')) {
            $errorInput = $defaultError;
        }

        // 4. Preserve all lines in the exact sequential order without cutting, trimming, deduplication, or truncation
        if (is_array($errorInput)) {
            $lines = array_values($errorInput);
            $rawErrorText = implode("\n", array_map(function ($line) {
                return is_scalar($line) ? (string) $line : json_encode($line, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }, $lines));
        } else {
            $rawErrorText = (string) $errorInput;
            // Split by CRLF, LF, or CR into lines; do NOT drop empty lines or whitespace
            $lines = preg_split("/\r\n|\n|\r/", $rawErrorText);
        }

        // Status and response code configuration
        $responseCode = (int) ($request->input('response_code', $request->query('response_code', 422)));
        $status = $request->has('status') ? filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN) : false;

        // 5. Plain text response option if requested (Accept: text/plain or format=text)
        if ($request->wantsJson() === false && ($request->header('Accept') === 'text/plain' || $request->query('format') === 'text' || $request->boolean('raw'))) {
            return response($rawErrorText, $responseCode, [
                'Content-Type'   => 'text/plain; charset=UTF-8',
                'X-Path-Deleted' => $fileDeleted ? 'true' : 'false',
                'X-Total-Lines'  => (string) count($lines),
            ]);
        }

        // 6. Return standard BaseController JSON response with full fidelity
        return $this->responseJson(
            $status,
            $responseCode,
            $rawErrorText,
            [
                'err'          => $rawErrorText,
                'error'        => $rawErrorText,
                'error_text'   => $rawErrorText,
                'errors'       => $lines,
                'lines'        => $lines,
                'total_lines'  => count($lines),
                'path'         => $path,
                'file_deleted' => $fileDeleted,
            ]
        );
    }
}
