@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/equipment-form.css') }}">
@endpush

@section('content')
<section class="equipment-create-page">
    <header class="equipment-create-heading">
        <div>
            <span class="equipment-create-eyebrow">EQUIPMENT MANAGEMENT</span>
            <h1><i class="fa-solid fa-circle-plus"></i> Add equipment</h1>
            <p>Add a rental item, set its hourly price, and record how many are in stock.</p>
        </div>
        <a href="{{ route('equipment.index') }}" class="equipment-create-back"><i class="fa-solid fa-arrow-left"></i> Back to equipment</a>
    </header>

    <div class="form-panel equipment-form-panel equipment-create-card">
        <div class="form-panel-header equipment-create-card-header">
            <span class="equipment-create-icon"><i class="fa-solid fa-dumbbell"></i></span>
            <div><h2>Equipment details</h2><p>Fields marked with * are required.</p></div>
        </div>
        <div class="form-panel-body">
            @if ($errors->any())
                <div class="equipment-form-error" role="alert">
                    <strong>Please review the information.</strong>
                    <ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form id="equipmentCreateForm" action="{{ route('equipment.store') }}" method="POST" enctype="multipart/form-data" class="equipment-create-form">
                @csrf
                <div class="equipment-create-fields">
                    <div class="equipment-create-field">
                        <label for="name">Equipment name <span>*</span></label>
                        <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Mikasa Basketball" value="{{ old('name') }}" required autofocus>
                        <small>Use the name players will recognize when renting.</small>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="equipment-create-field">
                        <label for="status">Availability <span>*</span></label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Available for rent</option>
                            <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Under maintenance</option>
                        </select>
                        <small>Set whether this item can currently be rented.</small>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="equipment-create-field">
                        <label for="price">Rental price per hour <span>*</span></label>
                        <div class="equipment-rate-input"><span>&#8369;</span><input id="price" type="number" name="price" class="form-control @error('price') is-invalid @enderror" min="0" step="0.01" placeholder="0.00" value="{{ old('price') }}" required></div>
                        <small>Price in Philippine pesos for each rental hour.</small>
                        @error('price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="equipment-create-field">
                        <label for="stock">Stock quantity <span>*</span></label>
                        <div class="equipment-stock-input"><i class="fa-solid fa-boxes-stacked"></i><input id="stock" type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" min="0" step="1" placeholder="0" value="{{ old('stock') }}" required></div>
                        <small>Enter the total number of items owned.</small>
                        @error('stock')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="equipment-create-field equipment-image-field">
                        <label for="image">Equipment image <span class="equipment-optional">Optional</span></label>
                        <input id="image" type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">
                        <small>JPG, PNG, or GIF. Maximum file size: 2 MB.</small>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <aside class="equipment-create-tip"><i class="fa-solid fa-circle-info"></i><span>Stock sets the total quantity available. Items currently rented are tracked separately by the booking system.</span></aside>

                <footer class="equipment-create-actions">
                    <a href="{{ route('equipment.index') }}" class="btn-brand-outline">Cancel</a>
                    <button type="submit" class="btn-brand equipment-submit" id="equipmentSubmitButton">
                        <i class="fa-solid fa-floppy-disk equipment-submit-icon"></i>
                        <span id="equipmentSubmitText">Save equipment</span>
                        <span class="spinner-border spinner-border-sm d-none" id="equipmentSubmitSpinner" role="status" aria-label="Saving equipment"></span>
                    </button>
                </footer>
            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    const equipmentCreateForm = document.getElementById('equipmentCreateForm');

    if (equipmentCreateForm) {
        equipmentCreateForm.addEventListener('submit', function (event) {
            if (!equipmentCreateForm.reportValidity()) {
                event.preventDefault();
                return;
            }

            const submitButton = document.getElementById('equipmentSubmitButton');
            submitButton.disabled = true;
            document.getElementById('equipmentSubmitSpinner').classList.remove('d-none');
            document.querySelector('.equipment-submit-icon').classList.add('d-none');
            document.getElementById('equipmentSubmitText').textContent = 'Saving equipment…';
        });
    }
</script>
@endsection
