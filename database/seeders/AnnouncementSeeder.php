<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Welcome to BMVL Sports Center',
                'content' => 'Welcome to BMVL Sports Center. Use your account to view facilities, request court bookings, and check the latest updates from our team.',
                'target_audience' => 'all',
                'pinned' => true,
            ],
            [
                'title' => 'Booking Requests Require Confirmation',
                'content' => 'After submitting a booking request, check its status in the Bookings section. Your reservation is confirmed after it has been reviewed by our staff.',
                'target_audience' => 'all',
                'pinned' => true,
            ],
            [
                'title' => 'Paying Through GCash',
                'content' => 'If you choose GCash when making a booking request, upload a clear image of your payment receipt. Staff will review the receipt before confirming the booking.',
                'target_audience' => 'all',
                'pinned' => false,
            ],
            [
                'title' => 'Cash Payments at the Front Desk',
                'content' => 'For bookings paid in cash, please settle the amount at the front desk. Keep your booking details available so staff can verify your reservation.',
                'target_audience' => 'all',
                'pinned' => false,
            ],
            [
                'title' => 'Check Facility Availability Before Booking',
                'content' => 'Review the booking calendar and available court list before sending a request. Availability can change as requests are reviewed.',
                'target_audience' => 'athlete',
                'pinned' => false,
            ],
            [
                'title' => 'Equipment Rentals and Stock',
                'content' => 'Equipment rental options and quantities are shown on the booking form. The available quantity may change as bookings are submitted and processed.',
                'target_audience' => 'all',
                'pinned' => false,
            ],
            [
                'title' => 'Review Your Booking Details',
                'content' => 'Before submitting a request, check the facility, dates, time slot, payment method, and any selected equipment to make sure everything is correct.',
                'target_audience' => 'athlete',
                'pinned' => false,
            ],
            [
                'title' => 'Please Arrive on Time for Your Reservation',
                'content' => 'Please arrive with enough time to prepare for your scheduled session. This helps everyone make the most of their reserved court time.',
                'target_audience' => 'all',
                'pinned' => false,
            ],
            [
                'title' => 'Help Keep Our Facilities Clean',
                'content' => 'Please dispose of rubbish properly and leave courts and shared areas tidy after use. Let staff know if you notice a maintenance or safety issue.',
                'target_audience' => 'all',
                'pinned' => false,
            ],
            [
                'title' => 'Keep Your Contact Details Up to Date',
                'content' => 'Please check that your profile name, email address, and phone number are current so staff can contact you about your account or booking requests.',
                'target_audience' => 'all',
                'pinned' => false,
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::updateOrCreate(
                ['title' => $announcement['title']],
                [
                    'content' => $announcement['content'],
                    'status' => 'active',
                    'target_audience' => $announcement['target_audience'],
                    'pinned' => $announcement['pinned'],
                ],
            );
        }
    }
}
