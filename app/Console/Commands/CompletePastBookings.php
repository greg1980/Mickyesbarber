<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class CompletePastBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:complete-past-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $affected = Booking::where(function ($q) use ($now) {
            $q->where('status', '!=', 'completed')
              ->where('status', '!=', 'cancelled')
              ->whereRaw("STR_TO_DATE(CONCAT(booking_date, ' ', booking_time), '%Y-%m-%d %H:%i:%s') < ?", [$now]);
        })->update(['status' => 'completed']);

        $this->info("Marked {$affected} bookings as completed.");
    }
}
