<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\BookingSeeder;

class GenerateFinanceTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:generate-test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate realistic booking and payment test data for the finance dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating realistic finance test data...');

        $seeder = new BookingSeeder();
        $seeder->setCommand($this);
        $seeder->run();

        $this->info('✅ Finance test data generated successfully!');
        $this->info('You can now view the updated finances dashboard with realistic data.');

        return Command::SUCCESS;
    }
}
