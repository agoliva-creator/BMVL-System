<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompleteExpiredBookings extends Command
{
    // The terminal command you will run
    protected $signature = 'bookings:complete';

    protected $description = 'Completes ended bookings and releases equipment reserved by them.';

    public function handle()
    {
        // Booking date/time fields represent Philippine local time.
        $timezone = 'Asia/Manila';
        $now = Carbon::now($timezone);

        $completedCount = 0;
        $expiredPendingCount = 0;

        // Pending requests also reserve equipment, so release them after their slot passes.
        Booking::whereIn('status', ['pending', 'confirmed'])
            ->where('is_completed', false)
            ->orderBy('id')
            ->chunkById(100, function ($bookings) use ($now, $timezone, &$completedCount, &$expiredPendingCount) {
                foreach ($bookings as $candidate) {
                    $endDate = $candidate->end_date ?: $candidate->booking_date;
                    $endDateTime = Carbon::parse($endDate . ' ' . $candidate->end_time, $timezone);

                    if ($now->lt($endDateTime)) {
                        continue;
                    }

                    $result = DB::transaction(function () use ($candidate, $now, $timezone) {
                        $booking = Booking::with('equipment')->lockForUpdate()->find($candidate->id);

                        if (!$booking || $booking->is_completed || !in_array($booking->status, ['pending', 'confirmed'], true)) {
                            return null;
                        }

                        $endDate = $booking->end_date ?: $booking->booking_date;
                        $endDateTime = Carbon::parse($endDate . ' ' . $booking->end_time, $timezone);
                        if ($now->lt($endDateTime)) {
                            return null;
                        }

                        foreach ($booking->equipment as $item) {
                            $item->increment('stock', $item->pivot->quantity);
                        }

                        $wasPending = $booking->status === 'pending';
                        $booking->update([
                            'status' => $wasPending ? 'cancelled' : 'confirmed',
                            'is_completed' => true,
                        ]);

                        return $wasPending ? 'pending' : 'confirmed';
                    });

                    if ($result === 'pending') {
                        $expiredPendingCount++;
                    } elseif ($result === 'confirmed') {
                        $completedCount++;
                    }
                }
            });

        $this->info("Completed {$completedCount} bookings and expired {$expiredPendingCount} unapproved requests. Equipment stock was released.");
    }
}
