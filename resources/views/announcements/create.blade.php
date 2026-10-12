@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/announcement-form.css') }}">
@endpush

@section('content')
<section class="announcement-create-page">
    <header class="announcement-create-heading">
        <div>
            <span class="announcement-create-eyebrow">COMMUNICATIONS</span>
            <h1><i class="fa-solid fa-bullhorn"></i> Create announcement</h1>
            <p>Share updates with your community and choose who can see them.</p>
        </div>
        <a href="{{ route('announcements.index') }}" class="announcement-create-back"><i class="fa-solid fa-arrow-left"></i> Back to announcements</a>
    </header>

    <div class="form-panel announcement-form-panel announcement-create-card">
        <div class="form-panel-header announcement-create-card-header">
            <span class="announcement-create-icon"><i class="fa-regular fa-pen-to-square"></i></span>
            <div><h2>Announcement details</h2><p>Fields marked with * are required.</p></div>
        </div>
        <div class="form-panel-body">
            @if ($errors->any())
                <div class="announcement-form-error" role="alert">
                    <strong>Please review the announcement details.</strong>
                    <ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form id="announcementCreateForm" action="{{ route('announcements.store') }}" method="POST" class="announcement-create-form">
                @csrf
                <div class="announcement-create-grid">
                    <div class="announcement-compose-fields">
                        <div class="announcement-create-field">
                            <label for="title">Announcement title <span>*</span></label>
                            <input id="title" type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Write a short, clear title" value="{{ old('title') }}" maxlength="255" required autofocus>
                            <small>A concise title helps readers understand the update quickly.</small>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="announcement-create-field announcement-content-field">
                            <label for="content">Message <span>*</span></label>
                            <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="8" placeholder="Write the announcement message..." required>{{ old('content') }}</textarea>
                            <small>Include the details and any action readers need to take.</small>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <aside class="announcement-settings-panel">
                        <div class="announcement-settings-heading"><i class="fa-solid fa-sliders"></i><div><strong>Publishing settings</strong><span>Choose the audience and visibility.</span></div></div>

                        <div class="announcement-create-field">
                            <label for="target_audience">Target audience <span>*</span></label>
                            <select id="target_audience" name="target_audience" class="form-select @error('target_audience') is-invalid @enderror" required>
                                <option value="all" {{ old('target_audience', 'all') === 'all' ? 'selected' : '' }}>Everyone</option>
                                <option value="athlete" {{ old('target_audience') === 'athlete' ? 'selected' : '' }}>Athletes only</option>
                                <option value="staff" {{ old('target_audience') === 'staff' ? 'selected' : '' }}>Staff only</option>
                            </select>
                            @error('target_audience')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="announcement-create-field">
                            <label for="status">Visibility <span>*</span></label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active · visible now</option>
                                <option value="hidden" {{ old('status') === 'hidden' ? 'selected' : '' }}>Hidden · save as draft</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <label class="announcement-pin-option" for="pinned">
                            <input type="checkbox" name="pinned" id="pinned" value="1" {{ old('pinned') ? 'checked' : '' }}>
                            <span class="announcement-pin-copy"><strong><i class="fa-solid fa-thumbtack"></i> Pin announcement</strong><small>Keep it at the top of the list.</small></span>
                        </label>
                    </aside>
                </div>

                <footer class="announcement-create-actions">
                    <a href="{{ route('announcements.index') }}" class="btn-brand-outline">Cancel</a>
                    <button type="submit" class="btn-brand announcement-submit" id="announcementSubmitButton">
                        <i class="fa-solid fa-paper-plane announcement-submit-icon"></i>
                        <span id="announcementSubmitText">Publish announcement</span>
                        <span class="spinner-border spinner-border-sm d-none" id="announcementSubmitSpinner" role="status" aria-label="Publishing announcement"></span>
                    </button>
                </footer>
            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    const announcementCreateForm = document.getElementById('announcementCreateForm');

    if (announcementCreateForm) {
        announcementCreateForm.addEventListener('submit', function (event) {
            if (!announcementCreateForm.reportValidity()) {
                event.preventDefault();
                return;
            }

            const submitButton = document.getElementById('announcementSubmitButton');
            submitButton.disabled = true;
            document.getElementById('announcementSubmitSpinner').classList.remove('d-none');
            document.querySelector('.announcement-submit-icon').classList.add('d-none');
            document.getElementById('announcementSubmitText').textContent = 'Publishing…';
        });
    }
</script>
@endsection
