<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OptimizePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:performance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all performance optimizations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running performance optimizations...');

        // Clear all caches first
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('route:clear');

        // Cache configurations
        $this->call('config:cache');
        $this->call('view:cache');
        $this->call('route:cache');

        // Optimize Composer autoloader
        $this->info('Optimizing Composer autoloader...');
        exec('composer dump-autoload --optimize');

        // Build frontend assets
        $this->info('Building frontend assets...');
        exec('npm run build');

        $this->info('Performance optimizations completed!');
    }
}
