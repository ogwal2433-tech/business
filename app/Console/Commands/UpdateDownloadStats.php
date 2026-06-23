<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DownloadLog;
use Illuminate\Support\Facades\Cache;

class UpdateDownloadStats extends Command
{
    protected $signature = 'downloads:update-stats';
    protected $description = 'Update download statistics cache';

    public function handle()
    {
        $platforms = ['android', 'ios', 'windows', 'mac'];

        foreach ($platforms as $platform) {
            $count = DownloadLog::byPlatform($platform)->count();
            Cache::forever("total_downloads_{$platform}", $count);

            $todayCount = DownloadLog::byPlatform($platform)->today()->count();
            Cache::put("today_downloads_{$platform}", $todayCount, now()->endOfDay());
        }

        $this->info('Download stats updated successfully!');
    }
}
