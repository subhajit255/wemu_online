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
                    'file_deleted' => true,
                    'path' => 'test_uploads/star_photo.jpg',
                    'total_lines' => 1,
                    'errors' => [
                        'This photo is not a valid representation of the night sky.Please upload another photo'
                    ]
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
            'error' => $largeErrorText
        ]);

        $response->assertStatus(422);

        $responseData = $response->json('data');
        $this->assertCount(500, $responseData['errors']);
        $this->assertEquals(500, $responseData['total_lines']);

        // Check exact match and order of all 500 lines
        $this->assertSame($expectedLines, $responseData['errors']);
        $this->assertSame($largeErrorText, $responseData['error_text']);
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
            'errors' => $inputLines
        ]);

        $response->assertStatus(422);
        $this->assertSame($inputLines, $response->json('data.errors'));
    }

    /**
     * Test raw text/plain body format response.
     */
    public function test_raw_text_format_response(): void
    {
        $rawError = "Line A\nLine B\nLine C";

        $response = $this->call('POST', '/api/get-error?format=text', [], [], [], [
            'HTTP_ACCEPT' => 'text/plain',
            'CONTENT_TYPE' => 'text/plain',
        ], $rawError);

        $response->assertStatus(422);
        $this->assertSame($rawError, $response->getContent());
        $this->assertSame('text/plain; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    /**
     * Test uploading an error log file.
     */
    public function test_uploaded_file_error_lines_preserved(): void
    {
        $logContent = "Log 1: Initializing\nLog 2: Error calculating asterism\nLog 3: Finished with error";
        $file = UploadedFile::fake()->createWithContent('error.log', $logContent);

        $response = $this->post('/api/get-error', [
            'file' => $file
        ], [
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'data' => [
                    'errors' => [
                        'Log 1: Initializing',
                        'Log 2: Error calculating asterism',
                        'Log 3: Finished with error'
                    ],
                    'total_lines' => 3
                ]
            ]);
    }

    /**
     * Test custom response code and status override.
     */
    public function test_custom_response_code_and_status(): void
    {
        $response = $this->postJson('/api/get-error', [
            'error' => "Fatal error occurred",
            'response_code' => 500,
            'status' => false,
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'status' => false,
                'response_code' => 500,
                'message' => 'Fatal error occurred',
            ]);
    }

    /**
     * Test GET request with error query param.
     */
    public function test_get_request_with_query_param_error(): void
    {
        $response = $this->getJson('/api/get-error?error=Custom+error+message');

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'message' => 'Custom error message',
                'data' => [
                    'total_lines' => 1,
                    'errors' => ['Custom error message']
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
                    'total_lines' => 3,
                    'errors' => [
                        'Line 1: this is error text',
                        'Line 2: sky invalid',
                        'Line 3: please upload another photo'
                    ]
                ]
            ]);
    }
}
