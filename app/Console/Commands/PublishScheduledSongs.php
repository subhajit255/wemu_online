<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Song;

class PublishScheduledSongs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'songs:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish songs that are in draft mode and have reached their published_at date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDateTime = date('Y-m-d H:i:s');
        $tenMinutesAgo = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        
        $updatedCount = Song::where('status', 0)
            ->whereNotNull('published_at')
            ->whereBetween('published_at', [$tenMinutesAgo, $currentDateTime])
            ->update(['status' => 1]);

        if ($updatedCount > 0) {
            logger()->info("Successfully published {$updatedCount} scheduled songs.");
        }
    }
}
