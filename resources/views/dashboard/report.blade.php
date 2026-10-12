@extends('layouts.app')

@section('title', 'Financial & Operations Report - BMVL Sports')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-report.css') }}">
@endpush

@section('content')
<div class="report-page">
<div class="report-toolbar no-print">
    <div class="report-toolbar-copy"><span>MANAGEMENT TOOLS</span><strong>Review or print the current report</strong></div>
    <div class="report-toolbar-actions">
        <button onclick="window.print()" class="report-action report-action-primary"><i class="fa-solid fa-print"></i> Print report</button>
        <a href="{{ route('dashboard') }}" class="report-action"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
    </div>
</div>

<div class="report-document">
    <div class="report-header">
        <div class="report-brand-lockup">
            <img src="{{ asset('images/bmvl-logo.jpg') }}" alt="BMVL Sports Center logo" class="report-logo">
            <div>
            <span class="report-kicker">OFFICIAL MANAGEMENT REPORT</span>
            <h1 class="brand-title">BMVL Sports Center</h1>
            <p class="report-subtitle">Executive Revenue &amp; Operations</p>
            </div>
        </div>
        <div class="report-meta">
            <span><i class="fa-regular fa-clock"></i> Generated {{ \Carbon\Carbon::now()->format('M d, Y · g:i A') }}</span>
            <span><i class="fa-regular fa-user"></i> {{ auth()->user()->name }}</span>
        </div>
    </div>

    <h3 class="section-title"><span class="section-icon"><i class="fa-solid fa-chart-line"></i></span> Financial overview</h3>
    <div class="fin-grid">
        <div class="fin-box">
            <span class="fin-icon"><i class="fa-regular fa-sun"></i></span><h6>Daily revenue</h6>
            <h3>&#8369; {{ number_format($dailyRevenue, 2) }}</h3>
        </div>
        <div class="fin-box">
            <span class="fin-icon"><i class="fa-regular fa-calendar"></i></span><h6>Weekly revenue</h6>
            <h3>&#8369; {{ number_format($weeklyRevenue, 2) }}</h3>
        </div>
        <div class="fin-box">
            <span class="fin-icon"><i class="fa-regular fa-calendar-days"></i></span><h6>Monthly revenue</h6>
            <h3>&#8369; {{ number_format($monthlyRevenue, 2) }}</h3>
        </div>
        <div class="fin-box fin-box-total">
            <span class="fin-icon"><i class="fa-solid fa-coins"></i></span><h6>All-time revenue</h6>
            <h3>&#8369; {{ number_format($totalRevenue, 2) }}</h3>
        </div>
    </div>

    <h3 class="section-title"><span class="section-icon"><i class="fa-solid fa-lightbulb"></i></span> Business analysis</h3>
    <div class="insights-box">
        <div class="insights-heading"><i class="fa-solid fa-chart-pie"></i><strong>System analytics summary</strong><span>As of {{ \Carbon\Carbon::now()->format('F d, Y') }}</span></div>
        <div class="insights-stats">
            <div><strong>{{ number_format($totalConfirmedBookings) }}</strong><span>confirmed reservations</span></div>
            <div><strong>{{ number_format($uniqueAthletes) }}</strong><span>active athletes</span></div>
            <div><strong>{{ $topCourt->name ?? 'N/A' }}</strong><span>top activity</span></div>
            <div><strong>&#8369; {{ number_format($topCourt->total_sales ?? 0, 2) }}</strong><span>top activity revenue · {{ $topCourt->booking_count ?? 0 }} sessions</span></div>
        </div>
    </div>

    <h3 class="section-title"><span class="section-icon"><i class="fa-solid fa-table-list"></i></span> Sport &amp; activity breakdown</h3>
    <div class="table-responsive report-table-wrap">
    <table class="table table-bordered mb-5">
        <thead class="table-light">
            <tr>
                <th>Sport / Activity</th>
                <th class="text-center">Total Sessions</th>
                <th class="text-center">Unique Athletes</th>
                <th class="text-end">Total Revenue Generated</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueByFacility as $facility)
            <tr>
                <td>{{ $facility->name }}</td>
                <td class="text-center">{{ $facility->booking_count }}</td>
                <td class="text-center">{{ $facility->unique_athletes }}</td>
                <td class="text-end row-total">&#8369; {{ number_format($facility->total_sales, 2) }}</td>
            </tr>
            @endforeach
            @if($revenueByFacility->isEmpty())
            <tr><td colspan="4" class="report-empty">No facility revenue data is available for this period.</td></tr>
            @endif
        </tbody>
    </table>
    </div>

    <h3 class="section-title mt-4"><span class="section-icon"><i class="fa-solid fa-dumbbell"></i></span> Equipment rental breakdown</h3>
    <div class="table-responsive report-table-wrap">
    <table class="table table-bordered mb-5">
        <thead class="table-light">
            <tr>
                <th>Equipment Item</th>
                <th class="text-center">Total Items Rented</th>
                <th class="text-end">Total Revenue Generated</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipmentStats as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td class="text-center">{{ $item->total_rented }}</td>
                <td class="text-end row-total">&#8369; {{ number_format($item->total_revenue, 2) }}</td>
            </tr>
            @endforeach
            @if($equipmentStats->isEmpty())
            <tr><td colspan="3" class="report-empty">No equipment rental data is available.</td></tr>
            @endif
        </tbody>
    </table>
    </div>

    <div class="text-center text-muted small mt-5 pt-3 report-confidential">
        CONFIDENTIAL - internal financial document for BMVL Management and Administration only.
    </div>
</div>
@endsection
