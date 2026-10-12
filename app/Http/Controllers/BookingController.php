<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Court;
use App\Models\User;
use App\Models\Equipment; // ADDED EQUIPMENT MODEL
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\BookingReceipt;
use Carbon\Carbon;
use Throwable;

class BookingController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin' || Auth::user()->role === 'staff') {
            $bookings = Booking::with(['court', 'user'])->orderBy('id', 'desc')->get();
        } else {
            $bookings = Booking::with(['court', 'user'])
                ->where('user_id', Auth::id())
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $courts = Court::where('status', 'available')->get();
        $users = [];
        
        // NEW: Fetch all available equipment that has stock > 0
        $equipmentList = Equipment::where('status', 'available')->where('stock', '>', 0)->get();

        if (Auth::user()->role === 'admin' || Auth::user()->role === 'staff') {
            $users = User::where('role', 'athlete')->orderBy('name', 'asc')->get();
        }

        return view('bookings.create', compact('courts', 'users', 'equipmentList')); // PASSED EQUIPMENT TO VIEW
    }

    public function store(Request $request)
    {
        $rules = [
            'court_id' => 'required|exists:courts,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:booking_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'payment_method' => 'required|in:cash,gcash', 
            'payment_receipt' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:pending,confirmed',
            // NEW: Validation for equipment array
            'equipment' => 'nullable|array',
            'equipment.*.id' => 'required_with:equipment|exists:equipment,id',
            'equipment.*.quantity' => 'required_with:equipment|integer|min:1',
        ];

        if (Auth::user()->role === 'admin' || Auth::user()->role === 'staff') {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        // Double-Booking Prevention Logic
        $isBooked = Booking::where('court_id', $request->court_id)
            ->where('status', '!=', 'cancelled') 
            ->where('booking_date', '<=', $request->end_date)
            ->where('end_date', '>=', $request->booking_date)
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->exists();

        if ($isBooked) {
            return back()->with('error', 'Sorry, this court is already booked during that date and time slot.')
                         ->withInput(); 
        }

        // Handle the Image Upload
        $receiptPath = null;
        if ($request->hasFile('payment_receipt')) {
            $receiptPath = $request->file('payment_receipt')->store('receipts', 'public');
        }

        // NEW: Calculate Equipment Price and check stock before saving
        $equipmentTotalCost = 0;
        $equipmentDataToAttach = [];

        if ($request->has('equipment')) {
            foreach ($request->equipment as $item) {
                if (isset($item['id']) && isset($item['quantity'])) {
                    $eq = Equipment::findOrFail($item['id']);
                    
                    // Failsafe stock check
                    if ($eq->stock < $item['quantity']) {
                        return back()->with('error', 'Sorry, we do not have enough stock for ' . $eq->name . '. Only ' . $eq->stock . ' left.')
                                     ->withInput();
                    }

                    $equipmentTotalCost += ($eq->price * $item['quantity']);
                    $equipmentDataToAttach[$item['id']] = [
                        'quantity' => $item['quantity'],
                        'price' => $eq->price
                    ];
                }
            }
        }

        // Calculate Court Price
        $court = Court::findOrFail($request->court_id);
        $startDate = Carbon::parse($request->booking_date);
        $endDate = Carbon::parse($request->end_date);
        $days = $startDate->diffInDays($endDate) + 1; 

        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $durationInHours = $startTime->diffInMinutes($endTime) / 60;

        // NEW: Add equipment total to the court total
        $totalPrice = ($days * $durationInHours * $court->hourly_rate) + $equipmentTotalCost;

        $bookingUserId = (Auth::user()->role === 'admin' || Auth::user()->role === 'staff') ? $request->user_id : Auth::id();

        // Create the booking
        $booking = Booking::create([
            'user_id' => $bookingUserId,
            'court_id' => $request->court_id,
            'booking_date' => $request->booking_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_price' => $totalPrice,
            'payment_method' => $request->payment_method,
            'payment_receipt' => $receiptPath,
            'status' => $request->status,
        ]);

        // NEW: Attach Equipment to Pivot Table and Deduct Stock
        if (!empty($equipmentDataToAttach)) {
            $booking->equipment()->attach($equipmentDataToAttach);
            
            foreach ($equipmentDataToAttach as $id => $data) {
                $eq = Equipment::find($id);
                $eq->decrement('stock', $data['quantity']);
            }
        }

        $receiptEmailFailed = false;
        if ($booking->user && $booking->user->email) {
            try {
                Mail::to($booking->user->email)->send(new BookingReceipt($booking));
            } catch (Throwable) {
                $receiptEmailFailed = true;
                Log::warning('Booking receipt email could not be sent.', ['booking_id' => $booking->id]);
            }
        }

        $successMessage = $receiptEmailFailed
            ? 'Booking successfully saved, but the receipt email could not be sent. Please check the mail settings.'
            : 'Booking successfully saved!';

        if ($request->source === 'calendar') {
            return redirect()->route('bookings.calendar')->with('success', $successMessage);
        }

        return redirect()->route('bookings.index')->with('success', $successMessage);
    }

    public function edit($id)
    {
        $booking = Booking::findOrFail($id);

        if (Auth::user()->role === 'athlete') {
            if ($booking->user_id !== Auth::id() || $booking->status !== 'pending') {
                abort(403, 'Unauthorized action. You can only cancel your own pending requests.');
            }
        }

        return view('bookings.edit', compact('booking'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $newStatus = $request->status;
        $result = DB::transaction(function () use ($id, $newStatus) {
            $booking = Booking::with('equipment')->lockForUpdate()->findOrFail($id);

            if (Auth::user()->role === 'athlete'
                && ($booking->user_id !== Auth::id() || $newStatus !== 'cancelled' || $booking->status !== 'pending')) {
                abort(403, 'You are only allowed to cancel your own pending booking requests.');
            }

            if ($booking->is_completed && $newStatus !== $booking->status) {
                return 'This booking is already completed and cannot be changed.';
            }

            if ($booking->status === 'cancelled' && $newStatus !== 'cancelled') {
                return 'A cancelled booking cannot be reopened. Please create a new booking request.';
            }

            if ($booking->status === 'confirmed' && $newStatus === 'pending') {
                return 'A confirmed booking cannot be moved back to pending.';
            }

            if ($newStatus === 'cancelled' && $booking->status !== 'cancelled') {
                foreach ($booking->equipment as $item) {
                    $item->increment('stock', $item->pivot->quantity);
                }
            }

            $booking->update(['status' => $newStatus]);

            return null;
        });

        if ($result !== null) {
            return back()->with('error', $result);
        }

        $successMessage = match ($newStatus) {
            'confirmed' => 'Payment approved and booking confirmed.',
            'cancelled' => 'Booking cancelled and reserved equipment released.',
            default => 'Booking status updated.',
        };

        if ($request->source === 'calendar') {
            return redirect()->route('bookings.calendar')->with('success', $successMessage);
        }

        return redirect()->route('bookings.index')->with('success', $successMessage);
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action. Only Admins can permanently delete a booking record.');
        }

        DB::transaction(function () use ($id) {
            $booking = Booking::with('equipment')->lockForUpdate()->findOrFail($id);

            // Completed bookings have already returned their stock via the scheduler.
            if ($booking->status !== 'cancelled' && !$booking->is_completed) {
                foreach ($booking->equipment as $item) {
                    $item->increment('stock', $item->pivot->quantity);
                }
            }

            $booking->delete();
        });

        return redirect()->route('bookings.index')->with('success', 'Booking record permanently deleted.');
    }

    public function print(Booking $booking)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'staff' && $booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('bookings.print', compact('booking'));
    }

    public function calendar()
    {
        $bookings = Booking::with(['court', 'user'])->where('status', '!=', 'cancelled')->get();
        $events = [];

        foreach ($bookings as $booking) {
            $startDate = Carbon::parse($booking->booking_date);
            $endDate = $booking->end_date ? Carbon::parse($booking->end_date) : $startDate->copy();
            
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                if (Auth::user()->role === 'admin' || Auth::user()->role === 'staff') {
                    $title = ($booking->court->name ?? 'Court') . ' - ' . ($booking->user->name ?? 'User');
                    $url = route('bookings.edit', $booking->id) . '?source=calendar';
                } else {
                    if ($booking->user_id === Auth::id()) {
                        $title = 'My Booking: ' . ($booking->court->name ?? 'Court');
                        $url = route('bookings.edit', $booking->id) . '?source=calendar';
                    } else {
                        $title = 'Booked: ' . ($booking->court->name ?? 'Court');
                        $url = ''; 
                    }
                }

                $events[] = [
                    'title'   => $title,
                    'start'   => $date->format('Y-m-d') . 'T' . $booking->start_time,
                    'end'     => $date->format('Y-m-d') . 'T' . $booking->end_time,
                    'color'   => $booking->status === 'confirmed' ? '#0E4B4E' : '#D4AF37', 
                    'display' => 'block', 
                    'url'     => $url, 
                ];
            }
        }

        return view('bookings.calendar', compact('events'));
    }
}
