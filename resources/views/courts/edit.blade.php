@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/courts-form.css') }}">
@endpush

@section('content')
<section class="courts-create-page courts-edit-page">
    <header class="courts-create-heading">
        <div><span class="courts-create-eyebrow">FACILITY MANAGEMENT</span><h1><i class="fa-solid fa-pen-to-square"></i> Edit court</h1><p>Update facility details, pricing, availability, and its display image.</p></div>
        <a href="{{ route('courts.index') }}" class="courts-create-back"><i class="fa-solid fa-arrow-left"></i> Back to courts</a>
    </header>

    <div class="form-panel courts-form-panel courts-create-card">
        <div class="form-panel-header courts-create-card-header"><span class="courts-create-icon"><i class="fa-solid fa-basketball"></i></span><div><h2>{{ $court->name }}</h2><p>Update the court information below.</p></div></div>
        <div class="form-panel-body">
            @if ($errors->any())
                <div class="courts-form-error" role="alert"><strong>Please review the information.</strong><ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <form id="courtEditForm" action="{{ route('courts.update', $court->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="courts-create-fields">
                    <div class="courts-create-field"><label for="name">Court name <span>*</span></label><input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $court->name) }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="courts-create-field"><label for="sport_type">Sport or activity <span>*</span></label><select id="sport_type" name="sport_type" class="form-select @error('sport_type') is-invalid @enderror" required>
                        @foreach(['Basketball', 'Pickleball', 'Badminton', 'Weight Training', 'Circuit Training', 'Boxing', 'Muay Thai', 'Taekwondo', 'Jiu Jitsu', 'Zumba', 'Yoga'] as $sport)<option value="{{ $sport }}" {{ old('sport_type', $court->sport_type) === $sport ? 'selected' : '' }}>{{ $sport }}</option>@endforeach
                    </select>@error('sport_type')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="courts-create-field"><label for="hourly_rate">Hourly rate <span>*</span></label><div class="courts-rate-input"><span>&#8369;</span><input id="hourly_rate" type="number" name="hourly_rate" class="form-control @error('hourly_rate') is-invalid @enderror" min="0" step="0.01" value="{{ old('hourly_rate', $court->hourly_rate) }}" required></div>@error('hourly_rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="courts-create-field"><label for="status">Availability <span>*</span></label><select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required><option value="available" {{ old('status', $court->status) === 'available' ? 'selected' : '' }}>Available for booking</option><option value="maintenance" {{ old('status', $court->status) === 'maintenance' ? 'selected' : '' }}>Under maintenance</option></select>@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="courts-create-field courts-image-field">
                        <label for="image">Court image <span class="courts-optional">Optional</span></label>
                        @if($court->image)<div class="court-edit-image-wrap"><img src="{{ asset('storage/' . $court->image) }}" alt="Current image of {{ $court->name }}" class="current-image-preview"><span>Current court image</span></div>@endif
                        <input id="image" type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">
                        <small>Choose a new JPG, PNG, or GIF up to 2 MB. Leave empty to keep the existing image.</small>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <footer class="courts-create-actions"><a href="{{ route('courts.index') }}" class="btn-brand-outline">Cancel</a><button type="submit" class="btn-brand courts-submit" id="courtUpdateButton"><i class="fa-solid fa-floppy-disk court-update-icon"></i><span id="courtUpdateText">Save changes</span><span class="spinner-border spinner-border-sm d-none" id="courtUpdateSpinner" role="status" aria-label="Saving court"></span></button></footer>
            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    const courtEditForm = document.getElementById('courtEditForm');
    if (courtEditForm) courtEditForm.addEventListener('submit', function (event) {
        if (!courtEditForm.reportValidity()) { event.preventDefault(); return; }
        document.getElementById('courtUpdateButton').disabled = true;
        document.querySelector('.court-update-icon').classList.add('d-none');
        document.getElementById('courtUpdateSpinner').classList.remove('d-none');
        document.getElementById('courtUpdateText').textContent = 'Saving changes…';
    });
</script>
@endsection
