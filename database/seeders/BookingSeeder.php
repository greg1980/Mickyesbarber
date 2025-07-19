<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Barber;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users, barbers, and services
        $customers = User::where('role', 'customer')->get();
        $barbers = Barber::where('is_approved', true)->get();
        $services = Service::all();

        // If we don't have enough data, create some
        if ($customers->count() < 5) {
            $customers = User::factory(10)->create(['role' => 'customer']);
        }

        if ($barbers->count() < 2) {
            $barbers = Barber::factory(3)->create(['is_approved' => true]);
        }

        if ($services->count() < 3) {
            $services = collect([
                Service::create(['name' => 'Classic Haircut', 'price' => 25.00, 'duration' => 30]),
                Service::create(['name' => 'Beard Trim', 'price' => 15.00, 'duration' => 20]),
                Service::create(['name' => 'Hair Wash & Style', 'price' => 35.00, 'duration' => 45]),
                Service::create(['name' => 'Full Service', 'price' => 50.00, 'duration' => 60]),
            ]);
        }

        // Clear existing bookings for clean test data (SQLite compatible)
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            Booking::truncate();
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Booking::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $bookings = [];
        $statuses = ['completed', 'confirmed', 'cancelled', 'pending'];
        $paymentStatuses = ['paid', 'partially_paid', 'unpaid'];

        // Generate bookings for the last 6 months and next 2 months
        for ($monthOffset = -2; $monthOffset < 6; $monthOffset++) {
            $targetMonth = $monthOffset < 0 ? Carbon::now()->addMonths(abs($monthOffset)) : Carbon::now()->subMonths($monthOffset);

            // More bookings in recent months
            if ($monthOffset < 0) {
                // Future months - fewer bookings, mostly pending/confirmed
                $bookingsThisMonth = 10;
            } else {
                $bookingsThisMonth = $monthOffset === 0 ? 25 : ($monthOffset === 1 ? 20 : 15);
            }

            for ($i = 0; $i < $bookingsThisMonth; $i++) {
                $customer = $customers->random();
                $barber = $barbers->random();
                $service = $services->random();
                $status = $this->getRandomStatus($monthOffset);

                // Generate realistic booking date/time
                $bookingDate = $targetMonth->copy()->addDays(rand(1, $targetMonth->daysInMonth));
                $bookingTime = $bookingDate->copy()->setTime(rand(9, 17), rand(0, 3) * 15);

                // Calculate payment based on status
                $paymentData = $this->calculatePayment($service->price, $status);

                $bookings[] = [
                    'user_id' => $customer->id,
                    'barber_id' => $barber->id,
                    'service_id' => $service->id,
                    'booking_date' => $bookingDate->toDateString(),
                    'booking_time' => $bookingTime->toDateTimeString(),
                    'service_price' => $service->price,
                    'status' => $status,
                    'amount_paid' => $paymentData['amount_paid'],
                    'deposit_paid' => $paymentData['deposit_paid'],
                    'payment_status' => $paymentData['payment_status'],
                    'notes' => $this->getRandomNotes(),
                    'created_at' => $bookingDate->copy()->subDays(rand(1, 7)),
                    'updated_at' => $bookingDate->copy()->addHours(rand(1, 24)),
                ];
            }
        }

        // Insert all bookings at once for better performance
        DB::table('bookings')->insert($bookings);

        $this->command->info('Created ' . count($bookings) . ' realistic bookings with proper payment data');
        $this->command->info('Revenue summary:');

        // Show summary statistics
        $totalRevenue = collect($bookings)->where('payment_status', 'paid')->sum('amount_paid');
        $thisMonthRevenue = collect($bookings)
            ->where('booking_date', '>=', Carbon::now()->startOfMonth()->toDateString())
            ->where('payment_status', 'paid')
            ->sum('amount_paid');
        $todayRevenue = collect($bookings)
            ->where('booking_date', Carbon::today()->toDateString())
            ->where('payment_status', 'paid')
            ->sum('amount_paid');

        $this->command->info("Total Revenue: $" . number_format($totalRevenue, 2));
        $this->command->info("This Month: $" . number_format($thisMonthRevenue, 2));
        $this->command->info("Today: $" . number_format($todayRevenue, 2));
    }

    private function getRandomStatus($monthOffset)
    {
        // Future months - mostly pending/confirmed
        if ($monthOffset < 0) {
            return collect(['confirmed', 'confirmed', 'confirmed', 'pending', 'pending'])->random();
        }
        // More completed bookings for older months
        elseif ($monthOffset >= 2) {
            return collect(['completed', 'completed', 'completed', 'cancelled'])->random();
        } elseif ($monthOffset === 1) {
            return collect(['completed', 'completed', 'confirmed', 'cancelled'])->random();
        } else {
            // Current month - mix of all statuses
            return collect(['completed', 'confirmed', 'pending', 'cancelled'])->random();
        }
    }

    private function calculatePayment($servicePrice, $status)
    {
        switch ($status) {
            case 'completed':
                // Completed bookings are always fully paid
                return [
                    'amount_paid' => $servicePrice,
                    'deposit_paid' => true,
                    'payment_status' => 'paid'
                ];

            case 'confirmed':
                // Confirmed bookings might be paid or have deposit
                $rand = rand(1, 100);
                if ($rand <= 60) {
                    // 60% fully paid
                    return [
                        'amount_paid' => $servicePrice,
                        'deposit_paid' => true,
                        'payment_status' => 'paid'
                    ];
                } elseif ($rand <= 85) {
                    // 25% deposit paid
                    $deposit = round($servicePrice * 0.2, 2); // 20% deposit
                    return [
                        'amount_paid' => $deposit,
                        'deposit_paid' => true,
                        'payment_status' => 'partially_paid'
                    ];
                } else {
                    // 15% unpaid
                    return [
                        'amount_paid' => 0,
                        'deposit_paid' => false,
                        'payment_status' => 'unpaid'
                    ];
                }

            case 'cancelled':
                // Cancelled bookings - some might have paid deposits
                if (rand(1, 100) <= 30) {
                    $deposit = round($servicePrice * 0.1, 2); // 10% deposit (non-refundable)
                    return [
                        'amount_paid' => $deposit,
                        'deposit_paid' => true,
                        'payment_status' => 'partially_paid'
                    ];
                } else {
                    return [
                        'amount_paid' => 0,
                        'deposit_paid' => false,
                        'payment_status' => 'unpaid'
                    ];
                }

            case 'pending':
            default:
                // Pending bookings are usually unpaid
                return [
                    'amount_paid' => 0,
                    'deposit_paid' => false,
                    'payment_status' => 'unpaid'
                ];
        }
    }

    private function getRandomNotes()
    {
        $notes = [
            'Regular customer',
            'First time visit',
            'Requested specific style',
            'Beard trim included',
            'Wash and style',
            'Special occasion',
            'Wedding preparation',
            'Quick trim',
            'Full service requested',
            null, // Some bookings have no notes
        ];

        return collect($notes)->random();
    }
}
