@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/announcement-form.css') }}">
@endpush

@section('content')
<section class="announcement-create-page announcement-edit-page">
    <header class="announcement-create-heading">
        <div><span class="announcement-create-eyebrow">COMMUNICATIONS</span><h1><i class="fa-solid fa-pen-to-square"></i> Edit announcement</h1><p>Update its message, audience, and publishing settings.</p></div>
        <a href="{{ route('announcements.index') }}" class="announcement-create-back"><i class="fa-solid fa-arrow-left"></i> Back to announcements</a>
    </header>

    <div class="form-panel announcement-form-panel announcement-create-card">
        <div class="form-panel-header announcement-create-card-header"><span class="announcement-create-icon"><i class="fa-solid fa-bullhorn"></i></span><div><h2>{{ $announcement->title }}</h2><p>Changes are applied when you save.</p></div></div>
        <div class="form-panel-body">
            @if ($errors->any())
                <div class="announcement-form-error" role="alert"><strong>Please review the announcement details.</strong><ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <form id="announcementEditForm" action="{{ route('announcements.update', $announcement->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="announcement-create-grid">
                    <div class="announcement-compose-fields">
                        <div class="announcement-create-field"><label for="title">Announcement title <span>*</span></label><input id="title" type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $announcement->title) }}" maxlength="255" required>@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="announcement-create-field announcement-content-field"><label for="content">Message <span>*</span></label><textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="8" required>{{ old('content', $announcement->content) }}</textarea>@error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    </div>

                    <aside class="announcement-settings-panel">
                        <div class="announcement-settings-heading"><i class="fa-solid fa-sliders"></i><div><strong>Publishing settings</strong><span>Choose visibility and audience.</span></div></div>
                        <div class="announcement-create-field"><label for="target_audience">Target audience <span>*</span></label><select id="target_audience" name="target_audience" class="form-select @error('target_audience') is-invalid @enderror" required><option value="all" {{ old('target_audience', $announcement->target_audience) === 'all' ? 'selected' : '' }}>Everyone</option><option value="athlete" {{ old('target_audience', $announcement->target_audience) === 'athlete' ? 'selected' : '' }}>Athletes only</option><option value="staff" {{ old('target_audience', $announcement->target_audience) === 'staff' ? 'selected' : '' }}>Staff only</option></select>@error('target_audience')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="announcement-create-field"><label for="status">Visibility <span>*</span></label><select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required><option value="active" {{ old('status', $announcement->status) === 'active' ? 'selected' : '' }}>Active · visible now</option><option value="hidden" {{ old('status', $announcement->status) === 'hidden' ? 'selected' : '' }}>Hidden · draft</option></select>@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <label class="announcement-pin-option" for="pinned"><input type="checkbox" name="pinned" id="pinned" value="1" {{ old('pinned', $announcement->pinned) ? 'checked' : '' }}><span class="announcement-pin-copy"><strong><i class="fa-solid fa-thumbtack"></i> Pin announcement</strong><small>Keep it at the top of the list.</small></span></label>
                        <div class="announcement-edit-status"><span>Current status</span><strong class="{{ $announcement->status === 'active' ? 'is-active' : 'is-hidden' }}">{{ ucfirst($announcement->status) }}</strong></div>
                    </aside>
                </div>
                <footer class="announcement-create-actions"><a href="{{ route('announcements.index') }}" class="btn-brand-outline">Cancel</a><button type="submit" class="btn-brand announcement-submit" id="announcementUpdateButton"><i class="fa-solid fa-floppy-disk announcement-update-icon"></i><span id="announcementUpdateText">Save changes</span><span class="spinner-border spinner-border-sm d-none" id="announcementUpdateSpinner" role="status" aria-label="Saving announcement"></span></button></footer>
            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    const announcementEditForm = document.getElementById('announcementEditForm');
    if (announcementEditForm) announcementEditForm.addEventListener('submit', function (event) {
        if (!announcementEditForm.reportValidity()) { event.preventDefault(); return; }
        document.getElementById('announcementUpdateButton').disabled = true;
        document.querySelector('.announcement-update-icon').classList.add('d-none');
        document.getElementById('announcementUpdateSpinner').classList.remove('d-none');
        document.getElementById('announcementUpdateText').textContent = 'Saving changes…';
    });
</script>
@endsection
