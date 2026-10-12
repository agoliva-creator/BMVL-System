@extends('layouts.app')

@section('content')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/booking-calendar.css') }}">
@endpush



<div class="booking-calendar-page">
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="booking-list-title"><i class="fa-regular fa-calendar-days me-2 booking-title-icon"></i> Booking Calendar</h2>
        <p class="booking-list-subtitle">Click any available time slot or date to add a new booking.</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end align-items-center gap-3">
        <!-- Visual Legend -->
        <div class="calendar-legend">
            <div class="legend-item"><div class="legend-dot legend-dot-confirmed"></div> Confirmed</div>
            <div class="legend-item"><div class="legend-dot legend-dot-pending"></div> Pending</div>
        </div>

        <a href="{{ route('bookings.index') }}" class="btn-brand-outline">
            <i class="fa-solid fa-list me-1"></i> List View
        </a>
    </div>
</div>

<div class="dash-panel shadow-sm booking-calendar-panel">
    <div id="calendar"></div>
</div>
</div>
@endsection

@section('scripts')
<!-- FullCalendar JS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var bookingEvents = @json($events);

        var compactCalendar = window.matchMedia('(max-width: 991.98px)').matches;
        var savedView = localStorage.getItem('bmvl_calendar_view') || 'dayGridMonth';
        var savedDate = localStorage.getItem('bmvl_calendar_date') || null;

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: compactCalendar ? 'dayGridMonth' : savedView,
            initialDate: savedDate ? savedDate : undefined, 
            
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: bookingEvents,
            slotMinTime: "06:00:00", 
            slotMaxTime: "23:00:00", 
            
            // THE FIX: Force the display of the end time and use standard AM/PM format
            displayEventEnd: true,
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            },
            height: compactCalendar ? 'auto' : '100%',
            expandRows: true,

            datesSet: function(info) {
                localStorage.setItem('bmvl_calendar_view', info.view.type);
                localStorage.setItem('bmvl_calendar_date', info.view.currentStart.toISOString());
            },
            
            dateClick: function(info) {
                let clickedDate = new Date(info.dateStr.split('T')[0]);
                let today = new Date();
                today.setHours(0, 0, 0, 0); 

                if (clickedDate < today) {
                    return; 
                }

                let url = "{{ route('bookings.create') }}?date=" + info.dateStr.split('T')[0] + "&source=calendar";
                
                if(info.dateStr.includes('T')) {
                    let time = info.dateStr.split('T')[1].substring(0, 5); 
                    url += "&time=" + time;
                }
                window.location.href = url;
            }
        });

        calendar.render();
        let calendarResizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(calendarResizeTimer);
            calendarResizeTimer = setTimeout(function() {
                calendar.updateSize();
            }, 120);
        });
    });
</script>
@endsection
