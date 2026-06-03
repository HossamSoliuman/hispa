<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshSettingsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the cached settings data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cache::forget('site_settings');
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        Cache::forever('site_settings', $settings);
        $this->info('Settings cache refreshed!');
    }
}
