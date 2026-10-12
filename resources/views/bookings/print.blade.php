@extends('layouts.app')

@section('title', 'Booking Receipt - BMVL Sports')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/booking-print.css') }}">
@endpush

@section('content')
<div class="receipt-page">
    <div class="receipt-box">

        <!-- Header Section -->
        <div class="receipt-header">
            <div>
                <h1 class="brand-title">BMVL Sports Center</h1>
                <p class="mb-0 text-muted fw-semibold mt-1">Official Booking Receipt</p>
            </div>
            <div class="meta-text">
                <div><strong>Ref ID:</strong> #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div><strong>Issued:</strong> {{ date('M d, Y') }}</div>
            </div>
        </div>

        <!-- Details Table -->
        <table class="table table-borderless receipt-table mb-4">
            <tr>
                <th>Status</th>
                <td>
                    <span class="status-badge status-{{ strtolower($booking->status) }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Athlete Name</th>
                <td>{{ $booking->user->name ?? 'Unknown Athlete' }}</td>
            </tr>
            <tr>
                <th>Facility Booked</th>
                <td>{{ $booking->court->name ?? 'Unknown Court' }}</td>
            </tr>
            <tr>
                <th>Schedule Date</th>
                <td>
                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                    @if($booking->end_date && $booking->end_date != $booking->booking_date)
                        - {{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Daily Time Slot</th>
                <td>
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} -
                    {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                </td>
            </tr>

            <!-- Dynamic Pricing Row -->
            <tr class="price-row">
                <th class="pt-4 pb-4">
                    @if($booking->status == 'confirmed')
                        TOTAL PAID
                    @elseif($booking->status == 'cancelled')
                        AMOUNT CANCELLED
                    @else
                        BALANCE DUE
                    @endif
                </th>
                <td class="pt-4 pb-4">
                    ₱ {{ number_format($booking->total_price, 2) }}
                </td>
            </tr>
        </table>

        <div class="text-center mt-5 mb-2 no-print">
            <p class="text-muted small mb-4">This document is valid as an official receipt/invoice for BMVL Sports Center.</p>
            <a href="{{ route('bookings.index') }}" class="btn-brand-outline me-2">Back to Bookings</a>
            <button onclick="window.print()" class="btn-brand">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer me-2 receipt-print-icon" viewBox="0 0 16 16">
                  <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                  <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                </svg>
                Print Receipt
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent
    <script>
        // Open the browser's print dialog as soon as this receipt is ready.
        window.addEventListener('load', () => {
            window.setTimeout(() => window.print(), 300);
        });
    </script>
@endsection

