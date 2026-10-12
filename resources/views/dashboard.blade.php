@extends('layouts.app')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

<div class="dashboard-page">
<div class="dash-header d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
        <h2><i class="fa-solid fa-chart-line me-2"></i> Admin Dashboard</h2>
        <p class="dash-intro">Welcome back! Here is the current financial and operational status.</p>
    </div>
    <div class="d-flex gap-2 no-print">
        <a href="{{ route('bookings.create') }}" class="btn-brand">
            <i class="fa-solid fa-plus me-1"></i> New Booking
        </a>
        <a href="{{ route('dashboard.report') }}" class="btn-brand-outline">
            <i class="fa-solid fa-file-invoice-dollar me-1"></i> View Full Report
        </a>
    </div>
</div>

<!-- ROW 1: Financial Analytics -->
<div class="row g-3 mb-4 dashboard-financial-row">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon icon-teal"><i class="fa-solid fa-peso-sign"></i></div>
            <div class="stat-details">
                <h5>Today's Revenue</h5>
                <h3>&#8369; {{ number_format($dailyRevenue, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon icon-teal"><i class="fa-solid fa-calendar-week"></i></div>
            <div class="stat-details">
                <h5>This Week</h5>
                <h3>&#8369; {{ number_format($weeklyRevenue, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon icon-teal"><i class="fa-solid fa-calendar-days"></i></div>
            <div class="stat-details">
                <h5>This Month</h5>
                <h3>&#8369; {{ number_format($monthlyRevenue, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon icon-gold"><i class="fa-solid fa-vault"></i></div>
            <div class="stat-details">
                <h5>All-Time Revenue</h5>
                <h3>&#8369; {{ number_format($totalRevenue, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- ROW 2: System Operations -->
<div class="row g-3 mb-4 dashboard-operations-row">
    <div class="col-md-4">
        <div class="stat-card stat-card-compact {{ $pendingApprovals > 0 ? 'stat-card-alert' : '' }}">
            <div class="stat-details">
                <h5>Pending Approvals</h5>
                <h3 class="stat-primary-value">{{ $pendingApprovals }} <span class="stat-action-text">Action Required</span></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card-compact">
            <div class="stat-details">
                <h5>Available Courts</h5>
                <h3 class="stat-primary-value">{{ $availableCourts }} / {{ $totalCourts }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card-compact">
            <div class="stat-details">
                <h5>Active Athletes</h5>
                <h3 class="stat-primary-value">{{ $activeUsers }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- ROW 3: Tables & Breakdowns -->
<div class="row g-4 dashboard-grid">
    <!-- Left Column: Primary Tables -->
    <div class="col-lg-8 dashboard-main-column">

        @if($pendingApprovals > 0)
        <!-- ACTION REQUIRED: Pending Approvals -->
        <div class="dash-panel dash-panel-alert approvals-panel">
            <div class="dash-panel-header">
                <div><i class="fa-solid fa-triangle-exclamation me-2"></i>Pending GCash Approvals</div>
            </div>
            <div class="table-responsive p-3 dash-panel-scroll-body">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Athlete</th>
                            <th>Court</th>
                            <th>Schedule</th>
                            <th class="text-center no-print">Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingBookings as $booking)
                        <tr class="dash-table-row">
                            <td class="fw-bold dash-danger-text">{{ $booking->user->name ?? 'Unknown' }}</td>
                            <td>{{ $booking->court->name ?? 'Unknown' }}</td>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</div>
                                <div class="dash-schedule-time">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                                </div>
                            </td>
                            <td class="text-center no-print">
                                <a href="{{ route('bookings.edit', $booking->id) }}" class="action-btn action-btn-danger" title="Review Receipt">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

    <!-- Right Column: Smart Sales Analytics -->
    <div class="col-lg-4 dashboard-side-column">
        
        <!-- Revenue by Facility -->
        <div class="dash-panel facility-panel">
            <div class="dash-panel-header">
                <div><i class="fa-solid fa-trophy me-2"></i>Revenue by Facility</div>
            </div>
            @php
                $maxSales = (float) $revenueByFacility->max('total_sales');
                $maxSales = $maxSales > 0 ? $maxSales : 1;
            @endphp
            <ul class="facility-list dash-panel-scroll-body">
                @forelse($revenueByFacility as $facility)
                    @php
                        $percentage = ($facility->total_sales / $maxSales) * 100;
                    @endphp
                    <li>
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <span class="facility-name">{{ $facility->name }}</span>
                            <span class="facility-sales">&#8369; {{ number_format($facility->total_sales, 2) }}</span>
                        </div>
                        <progress class="facility-progress" max="100" value="{{ $percentage }}" aria-label="{{ $facility->name }} revenue"></progress>
                    </li>
                @empty
                    <li class="justify-content-center text-muted text-center py-4">No facilities found.</li>
                @endforelse
            </ul>
        </div>

        <!-- NEW: Equipment Revenue Breakdown -->
        <div class="dash-panel equipment-panel">
            <div class="dash-panel-header">
                <div><i class="fa-solid fa-dumbbell me-2"></i>Equipment Revenue</div>
            </div>
            <ul class="facility-list dash-panel-scroll-body">
                @forelse($equipmentStats as $item)
                    <li>
                        <div class="d-flex justify-content-between align-items-end mb-1">
                            <span class="facility-name">{{ $item->name }}</span>
                            <span class="facility-sales">&#8369; {{ number_format($item->total_revenue, 2) }}</span>
                        </div>
                        <div class="equipment-rentals">Total Rentals: {{ $item->total_rented }}</div>
                    </li>
                @empty
                    <li class="justify-content-center text-muted text-center py-4">No equipment rentals yet.</li>
                @endforelse
            </ul>
        </div>

    </div>
</div>
</div>
@endsection
