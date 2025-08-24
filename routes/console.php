<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the report generation command
// This will run every hour to check for due reports
Schedule::command('reports:generate-scheduled')
    ->hourly()
    ->appendOutputTo(storage_path('logs/scheduled-reports.log'));

// Daily cleanup command for old report files
Schedule::call(function () {
    // Clean up temporary report files older than 7 days
    $tempPath = storage_path('app/temp/reports');
    if (is_dir($tempPath)) {
        $files = glob($tempPath . '/*');
        $now = time();
        foreach ($files as $file) {
            if (is_file($file) && $now - filemtime($file) >= 7 * 24 * 3600) {
                unlink($file);
            }
        }
    }
})->daily()->at('02:00');
