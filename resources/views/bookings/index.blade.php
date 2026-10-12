@extends('layouts.app')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/booking-index.css') }}">
@endpush


<div class="bookings-page">
<div class="row mb-4 align-items-center bookings-heading">
    <div class="col-md-6">
        <!-- DYNAMIC HEADER BASED ON ROLE -->
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
            <h2 class="booking-list-title"><i class="fa-solid fa-clipboard-list me-2 booking-title-icon"></i> Pending Approvals</h2>
            <p class="booking-list-subtitle">Review requests, manage walk-ins, and track facility reservations.</p>
        @else
            <h2 class="booking-list-title"><i class="fa-solid fa-calendar-check me-2 booking-title-icon"></i> My Requests</h2>
            <p class="booking-list-subtitle">Track your court reservations and booking status.</p>
        @endif
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('bookings.calendar') }}" class="btn-brand-outline me-2 shadow-sm">
            <i class="fa-regular fa-calendar-days me-1"></i> Calendar View
        </a>
        <a href="{{ route('bookings.create') }}" class="btn-brand shadow-sm">
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                <i class="fa-solid fa-plus me-1"></i> Create Walk-In Booking
            @else
                <i class="fa-solid fa-plus me-1"></i> Request a Court
            @endif
        </a>
    </div>
</div>

<div class="dash-panel bookings-panel">
    <div class="table-responsive booking-table-wrap p-3">
        <table class="table dash-table align-middle" id="bookingsTable">
            <thead>
                <tr>
                    <!-- ONLY SHOW ATHLETE COLUMN TO STAFF/ADMIN -->
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                        <th>Athlete</th>
                    @endif
                    <th>Facility</th>
                    <th>Schedule</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- BYPASSING BLADE COMPILER WITH RAW PHP -->
                <?php foreach($bookings as$booking): ?>
                <tr>
                    <!-- ONLY SHOW ATHLETE COLUMN TO STAFF/ADMIN -->
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                        <td class="booking-athlete-name">{{ $booking->user->name ?? 'Unknown User' }}</td>
                    @endif

                    <td class="booking-facility-name">{{ $booking->court->name ?? 'Deleted Court' }}</td>

                    <!-- Stacked Date and Time to save horizontal space -->
                    <td>
                        <div class="booking-schedule-date">
                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                            @if($booking->end_date &&$booking->end_date != $booking->booking_date)                                 - {{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}
                            @endif
                        </div>
                        <div class="booking-schedule-time">
                            <i class="fa-regular fa-clock me-1"></i> 
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} -                              {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                        </div>
                    </td>

                    <td class="booking-total-price">&#8369; {{ number_format($booking->total_price, 2) }}</td>

                    <td>
                        @if($booking->status === 'confirmed' && $booking->is_completed)
                            <span class="dash-badge dash-badge-completed">Completed</span>
                        @elseif($booking->status === 'cancelled' && $booking->is_completed)
                            <span class="dash-badge dash-badge-expired">Expired</span>
                        @elseif($booking->status == 'confirmed')
                            <span class="dash-badge dash-badge-confirmed">Confirmed</span>
                        @elseif($booking->status == 'cancelled')
                            <span class="dash-badge dash-badge-cancelled">Cancelled</span>
                        @else
                            <span class="dash-badge dash-badge-pending">Pending</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <div class="d-inline-flex gap-2">
                            <!-- EDIT BUTTON LOGIC -->
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                                <!-- Staff/Admin can always edit to change status -->
                                <a href="{{ route('bookings.edit', $booking->id) }}" class="action-btn" title="Review Request">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                </a>
                            @else
                                <!-- Athletes can ONLY edit/cancel if the booking is still pending -->
                                @if($booking->status === 'pending')
                                    <a href="{{ route('bookings.edit', $booking->id) }}" class="action-btn" title="Cancel Request">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                @endif
                            @endif
                            
                            <a href="{{ route('bookings.print', $booking->id) }}" class="btn-brand-outline booking-print-link" title="Print Receipt">
                                <i class="fa-solid fa-print"></i>
                            </a>

                            <!-- STRICT ADMIN DELETION -->
                            @if(auth()->user()->role === 'admin')
                                <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" class="d-inline delete-booking-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn-delete booking-delete-button" title="Permanently Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const initializeBookingsTable = function () {
            if ($.fn.DataTable.isDataTable('#bookingsTable')) {
                $('#bookingsTable').DataTable().destroy();
            }

            $('#bookingsTable').DataTable({
                pageLength: 10,
                ordering: false,
                scrollX: window.matchMedia('(max-width: 1199.98px)').matches,
                scrollY: Math.max(120, $('.bookings-panel').innerHeight() - 150) + 'px',
                scrollCollapse: true,
                language: { search: 'Quick Search:' }
            });
        };

        initializeBookingsTable();
        let bookingsResizeTimer;
        $(window).on('resize', function () {
            clearTimeout(bookingsResizeTimer);
            bookingsResizeTimer = setTimeout(initializeBookingsTable, 150);
        });

        // Confirmation modal for Admin deletion
        $(document).on('submit', '.delete-booking-form', function (e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: 'Delete this booking record?',
                html: `This will permanently scrub the record from the database. This action cannot be undone.`,
                icon: 'warning',
                iconColor: '#A6392F',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#A6392F',
                cancelButtonColor: '#57635F',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection

