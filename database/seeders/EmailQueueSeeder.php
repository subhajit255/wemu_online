<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmailQueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Locate the users_old_data.sql file
        $sqlFilePath = base_path('users_old_data.sql');

        if (!file_exists($sqlFilePath)) {
            $sqlFilePath = database_path('users_old_data.sql');
        }

        if (!file_exists($sqlFilePath)) {
            $sqlFilePath = database_path('seeders/users_old_data.sql');
        }

        if (!file_exists($sqlFilePath)) {
            if ($this->command) {
                $this->command->error("Could not find 'users_old_data.sql' in base_path or database directory.");
            }
            return;
        }

        // 2. Check if email_queues table exists
        if (!Schema::hasTable('email_queues')) {
            if ($this->command) {
                $this->command->error("The 'email_queues' table does not exist. Please run 'php artisan migrate' first.");
            }
            return;
        }

        if ($this->command) {
            $this->command->info("Reading SQL dump from: {$sqlFilePath}");
        }

        // 3. Read and parse lines memory-efficiently
        $handle = fopen($sqlFilePath, 'r');
        if (!$handle) {
            if ($this->command) {
                $this->command->error("Unable to open '{$sqlFilePath}' for reading.");
            }
            return;
        }

        $totalRowsScanned = 0;
        $emptyCount = 0;
        $invalidCount = 0;
        $duplicateInDumpCount = 0;

        $uniqueEmails = []; // key: lowercased email => value: original trimmed email

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            // Match lines that represent data rows: starts with ( and ends with ), or );
            if (str_starts_with($line, '(') && (str_ends_with($line, '),') || str_ends_with($line, ');'))) {
                $totalRowsScanned++;

                // Match 3rd column: (id, username, email, ...
                if (preg_match('/^\(\s*\d+\s*,\s*(?:\'(?:[^\'\\\\]|\\\\.)*\'|"(?:[^"\\\\]|\\\\.)*"|NULL)\s*,\s*(\'(?:[^\'\\\\]|\\\\.)*\'|"(?:[^"\\\\]|\\\\.)*"|NULL)/i', $line, $matches)) {
                    $rawEmail = $matches[1];

                    // Skip NULL value
                    if (strtoupper($rawEmail) === 'NULL') {
                        $emptyCount++;
                        continue;
                    }

                    // Strip wrapping quotes and unescape
                    $email = stripslashes(substr($rawEmail, 1, -1));
                    $email = trim($email);

                    // Skip empty strings
                    if ($email === '') {
                        $emptyCount++;
                        continue;
                    }

                    // Validate email format
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $invalidCount++;
                        continue;
                    }

                    // Deduplicate case-insensitively
                    $lower = strtolower($email);
                    if (isset($uniqueEmails[$lower])) {
                        $duplicateInDumpCount++;
                    } else {
                        $uniqueEmails[$lower] = $email;
                    }
                }
            }
        }

        fclose($handle);

        if ($this->command) {
            $this->command->info("Scanned {$totalRowsScanned} user rows from SQL dump.");
            $this->command->info("Found " . count($uniqueEmails) . " unique valid email addresses.");
            if ($emptyCount > 0) {
                $this->command->line("Skipped {$emptyCount} empty/NULL email entries.");
            }
            if ($invalidCount > 0) {
                $this->command->line("Skipped {$invalidCount} invalid email formats (e.g. phone numbers).");
            }
            if ($duplicateInDumpCount > 0) {
                $this->command->line("Skipped {$duplicateInDumpCount} duplicate emails within the SQL dump.");
            }
        }

        // 4. Check against already existing emails in email_queues table to ensure idempotency
        $existingEmails = DB::table('email_queues')
            ->pluck('email')
            ->filter()
            ->map(fn($e) => strtolower(trim($e)))
            ->flip()
            ->toArray();

        $emailsToInsert = [];
        $alreadyInDbCount = 0;

        foreach ($uniqueEmails as $lower => $email) {
            if (isset($existingEmails[$lower])) {
                $alreadyInDbCount++;
            } else {
                $emailsToInsert[] = $email;
            }
        }

        if ($alreadyInDbCount > 0 && $this->command) {
            $this->command->line("Skipped {$alreadyInDbCount} emails that already exist in 'email_queues' table.");
        }

        // 5. Bulk insert in chunks
        $now = now();
        $records = [];
        foreach ($emailsToInsert as $email) {
            $records[] = [
                'email' => $email,
                'status' => 0, // 0 for pending
                'error_message' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $insertedCount = count($records);
        $chunkSize = 1000;

        foreach (array_chunk($records, $chunkSize) as $chunk) {
            DB::table('email_queues')->insert($chunk);
        }

        if ($this->command) {
            $this->command->info("Successfully inserted {$insertedCount} emails into 'email_queues' table.");
        }
    }
}
