@extends('layouts.app')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/booking-create.css') }}">
@endpush


<div class="booking-create-page">
    <div class="booking-page-heading">
        <div>
            <span class="booking-eyebrow">BMVL SPORTS CENTER</span>
            <h2>Make a reservation</h2>
            <p>Choose a court, schedule, and any equipment you need.</p>
        </div>
        <a href="{{ request('source') == 'calendar' ? route('bookings.calendar') : route('bookings.index') }}" class="booking-back-link">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to bookings
        </a>
    </div>

<div class="form-panel booking-form-panel">
    <div class="form-panel-header">
        <div class="booking-form-title-icon"><i class="fa-regular fa-calendar-check"></i></div>
        <div>
            <h4>Booking details</h4>
            <span>Complete the information below to continue.</span>
        </div>
    </div>
    <div class="form-panel-body booking-form-body">

        @if ($errors->any())
            <div class="alert-brand-error">
                <ul class="mb-0">
                    <?php foreach ($errors->all() as$error): ?>
                        <li>{{ $error }}</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert-brand-error">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
            </div>
        @endif

        <form id="bookingCreateForm" action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="source" value="{{ request('source') }}">

            <div class="booking-form-grid">
            <section class="booking-main-fields" aria-label="Reservation details">

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                <div class="booking-field">
                    <label class="form-label">Select Athlete</label>
                    <select name="user_id" class="form-select" required>
                        <option value="" disabled selected>Choose the athlete...</option>
                        <?php foreach($users as$user): ?>
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{$user->email }})
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            @endif

            <div class="booking-field">
                <label class="form-label">Select Court</label>
                <select name="court_id" id="courtSelect" class="form-select" required>
                    <option value="" disabled selected data-rate="0">Choose an available court...</option>
                    <?php foreach($courts as$court): ?>
                        <option value="{{ $court->id }}" data-rate="{{ $court->hourly_rate }}" {{ old('court_id') == $court->id ? 'selected' : '' }}>
                            {{ $court->name }} - &#8369;{{ number_format($court->hourly_rate, 2) }}/hr
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="booking-date-time-grid">
                <div class="booking-field">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="booking_date" id="startDate" class="form-control" min="{{ date('Y-m-d') }}" value="{{ request('date', old('booking_date', date('Y-m-d'))) }}" required>
                </div>
                <div class="booking-field">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" id="endDate" class="form-control" min="{{ date('Y-m-d') }}" value="{{ request('date', old('end_date', date('Y-m-d'))) }}" required>
                    <div class="form-text">Choose the same date for a single-day booking.</div>
                </div>
            </div>

            <div class="booking-date-time-grid">
                <div class="booking-field">
                    <label class="form-label">Daily Start Time</label>
                    <input type="time" name="start_time" id="startTime" class="form-control" value="{{ old('start_time') }}" required>
                </div>
                <div class="booking-field">
                    <label class="form-label">Daily End Time</label>
                    <input type="time" name="end_time" id="endTime" class="form-control" value="{{ old('end_time') }}" required>
                </div>
            </div>

            </section>
            <section class="booking-side-fields" aria-label="Equipment and payment">

            <!-- ADD-ON EQUIPMENT SECTION -->
            @if(isset($equipmentList) &&$equipmentList->isNotEmpty())
            <div class="booking-field">
                <label class="form-label">Add-on Equipment (Optional)</label>
                <div class="equipment-list">
                    @foreach($equipmentList as $index =>$eq)
                        <div class="equipment-option {{ $loop->last ? 'equipment-option-last' : '' }}">
                            <div class="form-check mb-0">
                                <input class="form-check-input equipment-checkbox" type="checkbox" name="equipment[{{ $index }}][id]" value="{{ $eq->id }}" id="eq_{{ $eq->id }}" data-price="{{ $eq->price }}">
                                <label class="form-check-label equipment-label" for="eq_{{ $eq->id }}">
                                    {{ $eq->name }} 
                                    <br>
                                    <span class="equipment-price">&#8369;{{ number_format($eq->price, 2) }} each &#124; {{ $eq->stock }} available</span>
                                </label>
                            </div>
                            <div class="equipment-quantity-wrap">
                                <label class="equipment-quantity-label">Qty</label>
                                <input type="number" name="equipment[{{ $index }}][quantity]" class="form-control equipment-qty" min="1" max="{{ $eq->stock }}" value="1" disabled data-eq-id="{{ $eq->id }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="booking-payment-grid">
            <div class="booking-field">
                <label class="form-label">Payment Method</label>
                <select name="payment_method" id="paymentMethodSelect" class="form-select" required>
                    <option value="" disabled selected>Select how you will pay...</option>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash (Pay at Front Desk)</option>
                    <option value="gcash" {{ old('payment_method') == 'gcash' ? 'selected' : '' }}>GCash (Online Transfer)</option>
                </select>
            </div>

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                <div class="booking-field">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed (Walk-In)</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending (Unpaid)</option>
                    </select>
                </div>
            @else
                <input type="hidden" name="status" value="pending">
                <div class="booking-field">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control booking-status-readonly" value="Pending (Awaiting Approval)" disabled>
                    <div class="form-text">Your request will be reviewed by facility staff.</div>
                </div>
            @endif
            </div>

            <div id="gcashReceiptBox" class="gcash-receipt-box">
                <label class="form-label mb-1 gcash-receipt-label"><i class="fa-solid fa-image me-2"></i>Upload GCash Screenshot</label>
                <p class="gcash-receipt-help">Please transfer the exact amount to <strong>0912-345-6789</strong> and upload the screenshot here.</p>
                <input type="file" name="payment_receipt" class="form-control" accept="image/png, image/jpeg, image/jpg">
            </div>

            <div class="price-display-box">
                <span class="price-display-label">Estimated total</span>
                <h3 id="displayPrice">&#8369; 0.00</h3>
                <input type="hidden" name="total_price" id="totalPriceInput" value="0">
            </div>

            </section>
            </div>

            <div class="booking-form-actions">
                <a href="{{ request('source') == 'calendar' ? route('bookings.calendar') : route('bookings.index') }}" class="btn-brand-outline">Cancel</a>
                <button type="submit" class="btn-brand booking-submit" id="bookingSubmitButton">
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                        <span id="bookingSubmitText">Save Booking</span>
                    @else
                        <span id="bookingSubmitText">Submit Request</span>
                    @endif
                    <span class="spinner-border spinner-border-sm d-none" id="bookingSubmitSpinner" role="status" aria-label="Submitting booking"></span>
                </button>
            </div>
        </form>

    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const courtSelect = document.getElementById('courtSelect');
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const startTimeInput = document.getElementById('startTime');
        const endTimeInput = document.getElementById('endTime');
        const displayPrice = document.getElementById('displayPrice');
        const totalPriceInput = document.getElementById('totalPriceInput');

        // Dynamic warning message for invalid times
        const timeWarning = document.createElement('div');
        timeWarning.style.color = '#A6392F';
        timeWarning.style.fontSize = '0.85rem';
        timeWarning.style.marginTop = '6px';
        timeWarning.style.fontWeight = '600';
        timeWarning.style.display = 'none';
        timeWarning.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> End time must be after start time.';
        endTimeInput.parentNode.appendChild(timeWarning);

        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            if(endDateInput.value < this.value) {
                endDateInput.value = this.value;
            }
            calculatePrice();
        });

        // Equipment Elements
        const eqCheckboxes = document.querySelectorAll('.equipment-checkbox');
        const eqQtyInputs = document.querySelectorAll('.equipment-qty');

        function calculatePrice() {
            let courtTotal = 0;
            let equipmentTotal = 0;

            const selectedOption = courtSelect.options[courtSelect.selectedIndex];
            const hourlyRate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;

            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;

            // 1. Calculate Court Price
            if (hourlyRate > 0 && startDate && endDate && startTime && endTime) {
                const startD = new Date(startDate);
                const endD = new Date(endDate);
                let daysMultiplier = 0;

                if (endD >= startD) {
                    const diffTime = Math.abs(endD - startD);
                    daysMultiplier = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                }

                const startT = new Date(`1970-01-01T${startTime}:00`);
                const endT = new Date(`1970-01-01T${endTime}:00`);
                let diffInMs = endT - startT;

                if (diffInMs <= 0) {
                    timeWarning.style.display = 'block';
                    courtTotal = 0;
                } else {
                    timeWarning.style.display = 'none';
                    const diffInHours = diffInMs / (1000 * 60 * 60);
                    if (daysMultiplier > 0) {
                        courtTotal = diffInHours * hourlyRate * daysMultiplier;
                    }
                }
            } else {
                timeWarning.style.display = 'none';
            }

            // 2. Calculate Equipment Price
            const checkboxes = document.querySelectorAll('.equipment-checkbox:checked');
            checkboxes.forEach(function(cb) {
                const price = parseFloat(cb.getAttribute('data-price')) || 0;
                const qtyInput = document.querySelector(`input.equipment-qty[data-eq-id="${cb.value}"]`);
                const qty = parseInt(qtyInput.value) || 1;
                equipmentTotal += (price * qty);
            });

            // 3. Final Output
            const grandTotal = courtTotal + equipmentTotal;
            
            // Only show total if valid times are selected (prevents checking gear and booking 0 hour courts)
            if (courtTotal > 0) {
                displayPrice.innerHTML = `&#8369; ${grandTotal.toFixed(2)}`;
                totalPriceInput.value = grandTotal.toFixed(2);
            } else {
                displayPrice.innerHTML = '&#8369; 0.00';
                totalPriceInput.value = 0;
            }
        }

        // Event Listeners for Court & Time
        courtSelect.addEventListener('change', calculatePrice);
        endDateInput.addEventListener('change', calculatePrice);
        startTimeInput.addEventListener('change', calculatePrice);
        endTimeInput.addEventListener('change', calculatePrice);

        // Event Listeners for Equipment Options
        eqCheckboxes.forEach(function(cb) {
            cb.addEventListener('change', function() {
                const qtyInput = document.querySelector(`input.equipment-qty[data-eq-id="${this.value}"]`);
                qtyInput.disabled = !this.checked;
                calculatePrice();
            });
        });

        eqQtyInputs.forEach(function(input) {
            input.addEventListener('change', calculatePrice);
            input.addEventListener('keyup', calculatePrice);
        });
        
        // Toggle GCash Upload Box
        const paymentMethodSelect = document.getElementById('paymentMethodSelect');
        const gcashReceiptBox = document.getElementById('gcashReceiptBox');

        function toggleReceiptBox() {
            gcashReceiptBox.classList.toggle('is-visible', paymentMethodSelect.value === 'gcash');
        }

        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', toggleReceiptBox);
            toggleReceiptBox(); 
        }

        const bookingForm = document.getElementById('bookingCreateForm');
        bookingForm.addEventListener('submit', function(event) {
            if (!bookingForm.reportValidity()) {
                event.preventDefault();
                return;
            }

            const submitButton = document.getElementById('bookingSubmitButton');
            submitButton.disabled = true;
            document.getElementById('bookingSubmitSpinner').classList.remove('d-none');
            document.getElementById('bookingSubmitText').textContent = 'Submitting…';
        });
    });
</script>
@endsection

