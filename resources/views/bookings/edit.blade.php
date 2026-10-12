@extends('layouts.app')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/booking-edit.css') }}">
@endpush

<div class="booking-edit-page">
    <article class="booking-edit-card">
        <header class="booking-edit-header">
            <div class="booking-edit-heading">
                <span class="booking-edit-eyebrow">BOOKING MANAGEMENT</span>
                <h1><i class="fa-solid fa-file-invoice"></i> Booking details</h1>
                <p>Review the reservation, payment information, and equipment.</p>
            </div>
            @php
                $displayStatus = $booking->status === 'confirmed' && $booking->is_completed
                    ? 'completed'
                    : ($booking->status === 'cancelled' && $booking->is_completed ? 'expired' : $booking->status);
            @endphp
            <span class="booking-status-pill status-{{ $displayStatus }}">
                <span class="booking-status-dot"></span>
                {{ ucfirst($displayStatus) }}
            </span>
        </header>

        <div class="booking-edit-body">
            <section class="booking-overview-grid" aria-label="Booking summary">
                <div class="booking-overview-item">
                    <span class="booking-overview-icon"><i class="fa-solid fa-user"></i></span>
                    <div><span class="booking-overview-label">Athlete</span><strong>{{ $booking->user->name ?? 'Unknown User' }}</strong></div>
                </div>
                <div class="booking-overview-item">
                    <span class="booking-overview-icon"><i class="fa-solid fa-building"></i></span>
                    <div><span class="booking-overview-label">Facility</span><strong>{{ $booking->court->name ?? 'Unknown Court' }}</strong></div>
                </div>
                <div class="booking-overview-item">
                    <span class="booking-overview-icon"><i class="fa-regular fa-calendar"></i></span>
                    <div>
                        <span class="booking-overview-label">Schedule</span>
                        <strong>
                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                            @if($booking->end_date && $booking->end_date != $booking->booking_date)
                                – {{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}
                            @endif
                        </strong>
                        <small>{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</small>
                    </div>
                </div>
                <div class="booking-overview-item booking-overview-total">
                    <span class="booking-overview-icon"><i class="fa-solid fa-peso-sign"></i></span>
                    <div><span class="booking-overview-label">Total amount</span><strong>₱ {{ number_format($booking->total_price, 2) }}</strong></div>
                </div>
            </section>

            <div class="booking-edit-content-grid">
                <div class="booking-edit-main">
                    <section class="booking-section-card">
                        <div class="booking-section-heading">
                            <span class="booking-section-icon"><i class="fa-solid fa-dumbbell"></i></span>
                            <div><h2>Rented equipment</h2><p>Items reserved with this booking</p></div>
                        </div>
                        @if($booking->equipment && $booking->equipment->count() > 0)
                            <div class="booking-equipment-list">
                                @foreach($booking->equipment as $eq)
                                    <div class="booking-equipment-row">
                                        <span class="booking-equipment-symbol"><i class="fa-solid fa-box"></i></span>
                                        <div class="booking-equipment-description">
                                            <strong>{{ $eq->name }}</strong>
                                            <small>₱ {{ number_format($eq->pivot->price, 2) }} each</small>
                                        </div>
                                        <span class="booking-equipment-quantity">Qty {{ $eq->pivot->quantity }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="booking-empty-equipment"><i class="fa-regular fa-circle-check"></i> No equipment was included.</div>
                        @endif
                    </section>

                    <section class="booking-section-card booking-payment-card">
                        <div class="booking-section-heading">
                            <span class="booking-section-icon booking-payment-icon"><i class="fa-solid fa-wallet"></i></span>
                            <div><h2>Payment</h2><p>Payment method and verification details</p></div>
                        </div>
                        <div class="booking-payment-method">
                            @if($booking->payment_method === 'gcash')
                                <span class="booking-payment-method-icon booking-gcash-icon"><i class="fa-solid fa-mobile-screen"></i></span>
                                <div><strong>GCash transfer</strong><small>Review the uploaded proof before approval.</small></div>
                            @else
                                <span class="booking-payment-method-icon booking-cash-icon"><i class="fa-solid fa-money-bill-wave"></i></span>
                                <div><strong>Cash at front desk</strong><small>Payment is handled at the facility.</small></div>
                            @endif
                        </div>
                        @if($booking->payment_method === 'gcash')
                            <div class="booking-payment-proof">
                                <div class="booking-proof-heading"><i class="fa-regular fa-image"></i> Payment proof</div>
                                @if($booking->payment_receipt)
                                    <a href="{{ asset('storage/' . $booking->payment_receipt) }}" target="_blank" rel="noopener" class="booking-receipt-link">
                                        <img src="{{ asset('storage/' . $booking->payment_receipt) }}" class="booking-payment-receipt" alt="GCash payment receipt">
                                        <span>Open full image <i class="fa-solid fa-arrow-up-right-from-square"></i></span>
                                    </a>
                                @else
                                    <div class="booking-no-receipt"><i class="fa-solid fa-triangle-exclamation"></i> No receipt was uploaded.</div>
                                @endif
                            </div>
                        @endif
                    </section>
                </div>

                <aside class="booking-action-card">
                    <div class="booking-action-heading">
                        <span class="booking-action-icon"><i class="fa-solid fa-clipboard-check"></i></span>
                        <div><h2>Booking action</h2><p>Current status: {{ ucfirst($displayStatus) }}</p></div>
                    </div>

                    <form action="{{ route('bookings.update', $booking->id) }}" method="POST" class="booking-action-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="source" value="{{ request('source') }}">

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                            @if($booking->status === 'pending')
                                <div class="booking-action-message booking-message-pending">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    <span>Check the reservation details and verify payment before approving.</span>
                                </div>
                                <button type="submit" name="status" value="confirmed" class="booking-approve-button">
                                    <i class="fa-solid fa-circle-check"></i> Approve &amp; confirm
                                </button>
                                <button type="submit" name="status" value="cancelled" class="booking-reject-button" onclick="return confirm('Reject this booking request?')">
                                    <i class="fa-solid fa-circle-xmark"></i> Reject request
                                </button>
                            @elseif($booking->status === 'confirmed' && !$booking->is_completed)
                                <div class="booking-action-message booking-message-confirmed">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>Payment has been approved and the reservation is confirmed.</span>
                                </div>
                                <button type="submit" name="status" value="cancelled" class="booking-reject-button" onclick="return confirm('Cancel this confirmed booking and release its equipment?')">
                                    <i class="fa-solid fa-ban"></i> Cancel booking
                                </button>
                            @elseif($booking->status === 'cancelled')
                                <div class="booking-action-message booking-message-closed">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    <span>{{ $booking->is_completed ? 'This request expired after its scheduled time. Reserved equipment has been released.' : 'This booking was cancelled and its reserved equipment was released.' }}</span>
                                </div>
                            @else
                                <div class="booking-action-message booking-message-confirmed">
                                    <i class="fa-solid fa-flag-checkered"></i>
                                    <span>The booking time has ended and rented equipment has been returned to stock.</span>
                                </div>
                            @endif
                        @elseif($booking->status === 'pending')
                            <input type="hidden" name="status" value="cancelled">
                            <div class="booking-action-message booking-message-pending">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>Pending requests cannot be edited. Cancel this request and submit a new one if you need to make changes.</span>
                            </div>
                            <button type="submit" class="booking-reject-button" onclick="return confirm('Cancel your booking request?')">
                                <i class="fa-solid fa-ban"></i> Cancel my request
                            </button>
                        @else
                            <div class="booking-action-message booking-message-confirmed">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>This booking is {{ $displayStatus }}. No further action is available.</span>
                            </div>
                        @endif
                    </form>

                    <a href="{{ request('source') == 'calendar' ? route('bookings.calendar') : route('bookings.index') }}" class="booking-back-button">
                        <i class="fa-solid fa-arrow-left"></i> Back to bookings
                    </a>
                </aside>
            </div>
        </div>
    </article>
</div>
@endsection