@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/equipment-form.css') }}">
@endpush

@section('content')
<section class="equipment-create-page equipment-edit-page">
    <header class="equipment-create-heading">
        <div><span class="equipment-create-eyebrow">EQUIPMENT MANAGEMENT</span><h1><i class="fa-solid fa-pen-to-square"></i> Edit equipment</h1><p>Update rental pricing, stock quantity, availability, and image.</p></div>
        <a href="{{ route('equipment.index') }}" class="equipment-create-back"><i class="fa-solid fa-arrow-left"></i> Back to equipment</a>
    </header>

    <div class="form-panel equipment-form-panel equipment-create-card">
        <div class="form-panel-header equipment-create-card-header"><span class="equipment-create-icon"><i class="fa-solid fa-dumbbell"></i></span><div><h2>{{ $equipment->name }}</h2><p>Update the equipment information below.</p></div></div>
        <div class="form-panel-body">
            @if ($errors->any())
                <div class="equipment-form-error" role="alert"><strong>Please review the information.</strong><ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <form id="equipmentEditForm" action="{{ route('equipment.update', $equipment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="equipment-create-fields">
                    <div class="equipment-create-field"><label for="name">Equipment name <span>*</span></label><input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $equipment->name) }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="equipment-create-field"><label for="status">Availability <span>*</span></label><select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required><option value="available" {{ old('status', $equipment->status) === 'available' ? 'selected' : '' }}>Available for rent</option><option value="maintenance" {{ old('status', $equipment->status) === 'maintenance' ? 'selected' : '' }}>Under maintenance</option></select>@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="equipment-create-field"><label for="price">Rental price per hour <span>*</span></label><div class="equipment-rate-input"><span>&#8369;</span><input id="price" type="number" name="price" class="form-control @error('price') is-invalid @enderror" min="0" step="0.01" value="{{ old('price', $equipment->price) }}" required></div>@error('price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="equipment-create-field"><label for="stock">Stock quantity <span>*</span></label><div class="equipment-stock-input"><i class="fa-solid fa-boxes-stacked"></i><input id="stock" type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" min="0" step="1" value="{{ old('stock', $equipment->stock) }}" required></div><small>Total number of items owned.</small>@error('stock')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="equipment-create-field equipment-image-field">
                        <label for="image">Equipment image <span class="equipment-optional">Optional</span></label>
                        @if($equipment->image)<div class="equipment-edit-image-wrap"><img src="{{ asset('storage/' . $equipment->image) }}" alt="{{ $equipment->name }}" class="equipment-edit-image"><span>Current equipment image</span></div>@endif
                        <input id="image" type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">
                        <small>JPG, PNG, or GIF up to 2 MB. Leave empty to keep the current image.</small>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <aside class="equipment-create-tip"><i class="fa-solid fa-circle-info"></i><span>Update total stock to match your inventory. Current rentals are managed separately by bookings.</span></aside>
                <footer class="equipment-create-actions"><a href="{{ route('equipment.index') }}" class="btn-brand-outline">Cancel</a><button type="submit" class="btn-brand equipment-submit" id="equipmentUpdateButton"><i class="fa-solid fa-floppy-disk equipment-update-icon"></i><span id="equipmentUpdateText">Save changes</span><span class="spinner-border spinner-border-sm d-none" id="equipmentUpdateSpinner" role="status" aria-label="Saving equipment"></span></button></footer>
            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    const equipmentEditForm = document.getElementById('equipmentEditForm');
    if (equipmentEditForm) equipmentEditForm.addEventListener('submit', function (event) {
        if (!equipmentEditForm.reportValidity()) { event.preventDefault(); return; }
        document.getElementById('equipmentUpdateButton').disabled = true;
        document.querySelector('.equipment-update-icon').classList.add('d-none');
        document.getElementById('equipmentUpdateSpinner').classList.remove('d-none');
        document.getElementById('equipmentUpdateText').textContent = 'Saving changes…';
    });
</script>
@endsection
