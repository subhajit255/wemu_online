<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Album;

class PublishScheduledAlbums extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'albums:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish albums that are in draft mode and have reached their release_date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDate = date('Y-m-d');
        
        $albumCount = Album::where('status', 0)
            ->whereNotNull('release_date')
            ->where('release_date', '<=', $currentDate)
            ->update(['status' => 1]);

        if ($albumCount > 0) {
            logger()->info("Successfully published {$albumCount} scheduled albums.");
        }
    }
}
