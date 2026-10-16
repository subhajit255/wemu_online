<?php

namespace App\Console\Commands;

use App\Mail\AppUpdateAnnouncementMail;
use App\Models\EmailQueue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessEmailQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-queue:process 
                            {--chunk=30 : Number of emails to process in this run} 
                            {--sleep=150000 : Delay in microseconds between each email (150ms)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending emails from the email_queues table and send the new app update announcement';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $startTime = microtime(true);
        $chunkSize = max(1, (int) $this->option('chunk'));
        $sleepMicroseconds = max(0, (int) $this->option('sleep'));

        $this->info("=== Starting Email Queue Worker at " . Carbon::now()->toDateTimeString() . " ===");
        $this->info("Chunk size: {$chunkSize} | Rate-limit delay: " . ($sleepMicroseconds / 1000) . "ms");

        // 1. Fetch the next chunk of pending emails
        $queueItems = EmailQueue::pending()
            ->take($chunkSize)
            ->get();

        if ($queueItems->isEmpty()) {
            $this->info("No pending emails found in the 'email_queues' table.");
            return Command::SUCCESS;
        }

        $this->info("Processing {$queueItems->count()} pending emails...");

        $sentCount = 0;
        $alreadyRegisteredCount = 0;
        $failedCount = 0;

        foreach ($queueItems as $queueItem) {
            $email = trim((string) $queueItem->email);

            // Validate email format
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $queueItem->update([
                    'status' => EmailQueue::STATUS_FAILED,
                    'error_message' => 'Invalid email address format',
                ]);
                $failedCount++;
                $this->warn("[INVALID] ID #{$queueItem->id} - Skipped invalid email: '{$email}'");
                continue;
            }

            // Check if user has already registered in the application
            $isAlreadyRegistered = User::where('email', $email)->exists();

            if ($isAlreadyRegistered) {
                // Ignore sending if already registered and mark as processed (status = 1)
                $queueItem->update([
                    'status' => EmailQueue::STATUS_SENT,
                    'error_message' => 'User already registered; email sending skipped.',
                ]);
                $alreadyRegisteredCount++;
                $this->line("[IGNORED] {$email} - Already registered in users table.");
                continue;
            }

            // Attempt to send email
            try {
                Mail::to($email)->send(new AppUpdateAnnouncementMail($email));

                $queueItem->update([
                    'status' => EmailQueue::STATUS_SENT,
                    'error_message' => null,
                ]);

                $sentCount++;
                $this->info("[SENT] Successfully sent to {$email}");
            } catch (\Throwable $e) {
                $errorMsg = mb_substr($e->getMessage(), 0, 1000);

                $queueItem->update([
                    'status' => EmailQueue::STATUS_FAILED,
                    'error_message' => $errorMsg,
                ]);

                $failedCount++;
                Log::error("EmailQueue failed for {$email}: " . $e->getMessage());
                $this->error("[FAILED] {$email} - {$errorMsg}");
            }

            // Micro-sleep to prevent SMTP provider throttling (e.g. Office365)
            if ($sleepMicroseconds > 0) {
                usleep($sleepMicroseconds);
            }
        }

        $duration = round(microtime(true) - $startTime, 2);
        $remainingCount = EmailQueue::pending()->count();

        $summary = "Batch finished in {$duration}s | Sent: {$sentCount} | Already Registered (Skipped): {$alreadyRegisteredCount} | Failed: {$failedCount} | Remaining Pending: {$remainingCount}";
        $this->info("=== {$summary} ===");
        Log::info("EmailQueueWorker: {$summary}");

        return Command::SUCCESS;
    }
}
