<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-demo-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear demo data and reset database to fresh state';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! app()->environment('production')) {
            $this->error('Demo refresh blocked: not in demo environment.');

            return Command::FAILURE;
        }

        $this->info('Refreshing demo database...');

        $this->call('migrate:refresh', [
            '--force' => true,
            '--seed' => true,
        ]);

        $this->info('Demo database refreshed.');

        return Command::SUCCESS;
    }
}
