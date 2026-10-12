@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/courts-form.css') }}">
@endpush

@section('content')
<section class="courts-create-page">
    <header class="courts-create-heading">
        <div>
            <span class="courts-create-eyebrow">FACILITY MANAGEMENT</span>
            <h1><i class="fa-solid fa-circle-plus"></i> Add a court</h1>
            <p>Add a facility to the booking system and set its hourly rate and availability.</p>
        </div>
        <a href="{{ route('courts.index') }}" class="courts-create-back"><i class="fa-solid fa-arrow-left"></i> Back to courts</a>
    </header>

    <div class="form-panel courts-form-panel courts-create-card">
        <div class="form-panel-header courts-create-card-header">
            <span class="courts-create-icon"><i class="fa-solid fa-basketball"></i></span>
            <div><h2>Court details</h2><p>Fields marked with * are required.</p></div>
        </div>
        <div class="form-panel-body">
            @if ($errors->any())
                <div class="courts-form-error" role="alert">
                    <strong>Please review the information.</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form id="courtCreateForm" action="{{ route('courts.store') }}" method="POST" enctype="multipart/form-data" class="courts-create-form">
                @csrf
                <div class="courts-create-fields">
                    <div class="courts-create-field">
                        <label for="name">Court name <span>*</span></label>
                        <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Badminton Court 2" value="{{ old('name') }}" required autofocus>
                        <small>Use a clear name that players can recognize.</small>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="courts-create-field">
                        <label for="sport_type">Sport or activity <span>*</span></label>
                        <select id="sport_type" name="sport_type" class="form-select @error('sport_type') is-invalid @enderror" required>
                            <option value="" disabled {{ old('sport_type') ? '' : 'selected' }}>Choose an activity</option>
                            @foreach(['Basketball', 'Pickleball', 'Badminton', 'Weight Training', 'Circuit Training', 'Boxing', 'Muay Thai', 'Taekwondo', 'Jiu Jitsu', 'Zumba', 'Yoga'] as $sport)
                                <option value="{{ $sport }}" {{ old('sport_type') === $sport ? 'selected' : '' }}>{{ $sport }}</option>
                            @endforeach
                        </select>
                        @error('sport_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="courts-create-field">
                        <label for="hourly_rate">Hourly rate <span>*</span></label>
                        <div class="courts-rate-input"><span>&#8369;</span><input id="hourly_rate" type="number" name="hourly_rate" class="form-control @error('hourly_rate') is-invalid @enderror" min="0" step="0.01" placeholder="0.00" value="{{ old('hourly_rate') }}" required></div>
                        <small>Price charged per hour in Philippine pesos.</small>
                        @error('hourly_rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="courts-create-field">
                        <label for="status">Availability <span>*</span></label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Available for booking</option>
                            <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Under maintenance</option>
                        </select>
                        <small>You can change this later from the courts list.</small>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="courts-create-field courts-image-field">
                        <label for="image">Court image <span class="courts-optional">Optional</span></label>
                        <input id="image" type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">
                        <small>JPG, PNG, or GIF. Maximum file size: 2 MB.</small>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <aside class="courts-create-tip"><i class="fa-solid fa-circle-info"></i><span>Accurate rates and availability help prevent booking conflicts and make facility choices clear to athletes.</span></aside>

                <footer class="courts-create-actions">
                    <a href="{{ route('courts.index') }}" class="btn-brand-outline">Cancel</a>
                    <button type="submit" class="btn-brand courts-submit" id="courtSubmitButton">
                        <i class="fa-solid fa-floppy-disk court-submit-icon"></i>
                        <span id="courtSubmitText">Save court</span>
                        <span class="spinner-border spinner-border-sm d-none" id="courtSubmitSpinner" role="status" aria-label="Saving court"></span>
                    </button>
                </footer>
            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    const courtCreateForm = document.getElementById('courtCreateForm');

    if (courtCreateForm) {
        courtCreateForm.addEventListener('submit', function (event) {
            if (!courtCreateForm.reportValidity()) {
                event.preventDefault();
                return;
            }

            const submitButton = document.getElementById('courtSubmitButton');
            submitButton.disabled = true;
            document.getElementById('courtSubmitSpinner').classList.remove('d-none');
            document.querySelector('.court-submit-icon').classList.add('d-none');
            document.getElementById('courtSubmitText').textContent = 'Saving court…';
        });
    }
</script>
@endsection
