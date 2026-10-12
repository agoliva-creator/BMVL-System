@extends('layouts.app')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/announcements.css') }}">
@endpush


<div class="announcements-page">
<div class="row announcements-heading align-items-center">
    <div class="col-md-6">
        <h2 class="announcements-title"><i class="fa-solid fa-bullhorn me-2"></i> Announcements</h2>
        <p class="announcements-subtitle">Manage facility news and updates.</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <!-- BOTH STAFF AND ADMIN CAN ADD ANNOUNCEMENTS -->
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
        <a href="{{ route('announcements.create') }}" class="btn-brand shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> Post Announcement
        </a>
        @endif
    </div>
</div>

<div class="dash-panel announcements-panel {{ in_array(auth()->user()->role, ['admin', 'staff'], true) ? 'announcements-has-actions' : '' }}">
    <div class="table-responsive announcements-table-wrap">
        <table class="table dash-table align-middle" id="announcementsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Audience</th>
                    <th>Status</th>
                    <th>Date Posted</th>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                    <th class="text-center">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <!-- BYPASSING BLADE COMPILER WITH RAW PHP -->
                <?php foreach($announcements as$post): ?>
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>
                        <!-- Clicking the title or icon opens a full content modal -->
                        <span class="announcement-title-link view-content-btn" data-title="{{ $post->title }}" data-content="{{ $post->content }}" data-date="{{ $post->created_at->format('M d, Y') }}">
                            @if($post->pinned)
                                <i class="fa-solid fa-thumbtack pin-icon" title="Pinned"></i>
                            @endif
                            {{ $post->title }}
                        </span>
                    </td>
                    <td>
                        @if($post->target_audience == 'athlete')
                            <span class="dash-badge dash-badge-audience-athlete">Athletes</span>
                        @elseif($post->target_audience == 'staff')
                            <span class="dash-badge dash-badge-audience-staff">Staff</span>
                        @else
                            <span class="dash-badge dash-badge-audience-all">Everyone</span>
                        @endif
                    </td>
                    <td>
                        @if($post->status == 'active')
                            <span class="dash-badge dash-badge-active">Active</span>
                        @else
                            <span class="dash-badge dash-badge-hidden">Hidden</span>
                        @endif
                    </td>
                    <td>{{ $post->created_at->format('M d, Y') }}</td>
                    
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                    <td class="text-center">
                        <div class="d-inline-flex gap-2">
                            <!-- BOTH STAFF AND ADMIN CAN EDIT -->
                            <a href="{{ route('announcements.edit', $post->id) }}" class="action-btn action-btn-edit" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <!-- ONLY ADMINS CAN DELETE -->
                            @if(auth()->user()->role === 'admin')
                                <form action="{{ route('announcements.destroy', $post->id) }}" method="POST" class="d-inline delete-announcement-form" data-title="{{ $post->title }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn-delete" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const initializeAnnouncementsTable = function () {
            if ($.fn.DataTable.isDataTable('#announcementsTable')) {
                $('#announcementsTable').DataTable().destroy();
            }

            $('#announcementsTable').DataTable({
                pageLength: 10,
                paging: true,
                lengthChange: false,
                info: true,
                ordering: true,
                scrollX: window.matchMedia('(max-width: 1199.98px)').matches,
                scrollY: Math.max(160, $('.announcements-panel').innerHeight() - 145) + 'px',
                scrollCollapse: true,
                // Keep the order sent by the server (pinned first, then ID ascending).
                order: [],
                language: { search: 'Quick Search:' }
            });
        };

        initializeAnnouncementsTable();
        let announcementsResizeTimer;
        $(window).on('resize', function () {
            clearTimeout(announcementsResizeTimer);
            announcementsResizeTimer = setTimeout(initializeAnnouncementsTable, 150);
        });

        // Click title to view full announcement content in SweetAlert
        $(document).on('click', '.view-content-btn', function() {
            const title = $(this).data('title');
            const content = $(this).data('content');
            const date = $(this).data('date');

            Swal.fire({
                title: `<span class="announcement-modal-title">${title}</span>`,
                html: `
                    <div class="announcement-modal-content">
                        <p class="announcement-modal-date"><i class="fa-regular fa-calendar me-1"></i> Posted on ${date}</p>
                        <div class="announcement-modal-body">${content}</div>
                    </div>
                `,
                confirmButtonText: 'Close',
                confirmButtonColor: 'var(--teal)',
                background: 'var(--panel)',
                width: '550px'
            });
        });

        // Themed confirmation modal before deleting an announcement
        $(document).on('submit', '.delete-announcement-form', function (e) {
            e.preventDefault();
            const form = this;
            const title = $(form).data('title');

            Swal.fire({
                title: 'Delete this announcement?',
                html: `<strong>${title}</strong> will be permanently removed. This can't be undone.`,
                icon: 'warning',
                iconColor: '#A6392F',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete announcement',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#A6392F',
                cancelButtonColor: '#57635F',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
