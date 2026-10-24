<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommonControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');
    }

    /**
     * Test default error message when no error input is provided.
     */
    public function test_default_error_response_and_file_deletion(): void
    {
        Storage::put('test_uploads/star_photo.jpg', 'fake-binary-content');
        $this->assertTrue(Storage::exists('test_uploads/star_photo.jpg'));

        $response = $this->getJson('/api/get-error?path=test_uploads/star_photo.jpg');

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'response_code' => 422,
                'message' => 'This photo is not a valid representation of the night sky.Please upload another photo',
                'data' => [
                    'err' => 'This photo is not a valid representation of the night sky.Please upload another photo',
                ]
            ]);

        // File must be deleted
        $this->assertFalse(Storage::exists('test_uploads/star_photo.jpg'));
    }

    /**
     * Test large multi-line error preservation with exact ordering, no truncation, no dropped lines.
     */
    public function test_large_number_of_error_lines_preserved_in_exact_order(): void
    {
        // Build 500 lines of error messages with blank lines, repeated lines, indentation
        $expectedLines = [];
        for ($i = 1; $i <= 500; $i++) {
            if ($i % 50 === 0) {
                // Include empty line
                $expectedLines[] = "";
            } elseif ($i % 10 === 0) {
                // Include repeated line
                $expectedLines[] = "Repeated error: star coordinates out of boundary";
            } else {
                $expectedLines[] = "    [Line {$i}] Detailed error at frame #{$i}: star luminosity mismatch detected";
            }
        }
        $largeErrorText = implode("\n", $expectedLines);

        $response = $this->postJson('/api/get-error', [
            'err' => $largeErrorText
        ]);

        $response->assertStatus(422);

        $this->assertSame($largeErrorText, $response->json('data.err'));
        $this->assertSame($largeErrorText, $response->json('message'));
    }

    /**
     * Test array of errors passed directly preserves exact order.
     */
    public function test_array_of_errors_preserves_order(): void
    {
        $inputLines = [
            'Step 1: Point detection failed',
            'Step 2: Sky contrast below minimum',
            'Step 3: Constellation matcher returned 0 candidates',
        ];

        $response = $this->postJson('/api/get-error', [
            'err' => $inputLines
        ]);

        $response->assertStatus(422);
        $this->assertSame(implode("\n", $inputLines), $response->json('data.err'));
    }

    /**
     * Test raw text/plain body format response.
     */
    public function test_raw_text_format_response(): void
    {
        $rawError = "Line A\nLine B\nLine C";

        $response = $this->postJson('/api/get-error?format=text', [
            'err' => $rawError,
        ], [
            'HTTP_ACCEPT' => 'text/plain',
        ]);

        $response->assertStatus(422);
        $this->assertSame($rawError, $response->getContent());
        $this->assertSame('text/plain; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    /**
     * Test custom response code and status override.
     */
    public function test_custom_response_code_and_status(): void
    {
        $response = $this->postJson('/api/get-error', [
            'err' => "Fatal error occurred",
            'response_code' => 500,
            'status' => false,
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'status' => false,
                'response_code' => 500,
                'message' => 'Fatal error occurred',
                'data' => [
                    'err' => 'Fatal error occurred',
                ],
            ]);
    }

    /**
     * Test GET request with error query param.
     */
    public function test_get_request_with_query_param_error(): void
    {
        $response = $this->getJson('/api/get-error?err=Custom+error+message');

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'message' => 'Custom error message',
                'data' => [
                    'err' => 'Custom error message',
                ]
            ]);
    }

    /**
     * Test request body sending "err" key with multiline error text.
     */
    public function test_err_key_in_request_body_is_processed_without_cutting(): void
    {
        $multilineError = "Line 1: this is error text\nLine 2: sky invalid\nLine 3: please upload another photo";

        $response = $this->postJson('/api/get-error', [
            'err' => $multilineError
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'response_code' => 422,
                'message' => $multilineError,
                'data' => [
                    'err' => $multilineError,
                ]
            ]);
    }
}
