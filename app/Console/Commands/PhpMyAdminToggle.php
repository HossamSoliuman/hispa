<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PhpMyAdminToggle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phpmyadmin:toggle';

    protected $description = 'Enable or disable phpMyAdmin via Nginx config based on .env setting ALLOW_PHPMYADMIN';

    public function handle()
    {
        $allowed = env('ALLOW_PHPMYADMIN', 'false');

        if ($allowed == 'true') {
            exec('sudo ln -sf /etc/nginx/snippets/phpmyadmin_enabled.conf /etc/nginx/snippets/phpmyadmin.conf');
            $this->info('phpMyAdmin enabled');
        } else {
            exec('sudo ln -sf /etc/nginx/snippets/phpmyadmin_disabled.conf /etc/nginx/snippets/phpmyadmin.conf');
            $this->info('phpMyAdmin disabled');
        }

        exec('sudo systemctl reload nginx');
        $this->info('Nginx reloaded');
    }
}
