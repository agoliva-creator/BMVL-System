@extends('layouts.app')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/courts.css') }}">
@endpush


<div class="courts-page">
<div class="row courts-heading align-items-center">
    <div class="col-md-6">
        <h2 class="courts-title"><i class="fa-solid fa-map-location-dot me-2"></i> Manage Courts</h2>
        <p class="courts-subtitle">View, add, and edit facility spaces.</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('courts.create') }}" class="btn-brand shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Add new court
            </a>
        @endif
    </div>
</div>

<div class="dash-panel courts-panel">
    <div class="table-responsive courts-table-wrap">
        <table class="table dash-table align-middle" id="courtsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Court name</th>
                    <th>Sport type</th>
                    <th>Rate / hour</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- BYPASSING BLADE COMPILER WITH RAW PHP -->
                <?php foreach($courts as$court): ?>
                <tr>
                    <td>{{ $court->id }}</td>
                    <td>
                        @if($court->image)
                            <img src="{{ asset('storage/' . $court->image) }}" alt="{{ $court->name }}" class="court-thumb" data-full="{{ asset('storage/' . $court->image) }}">
                        @else
                            <div class="court-thumb-placeholder">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        @endif
                    </td>
                    <td class="court-name">{{ $court->name }}</td>
                    <td>{{ $court->sport_type }}</td>
                    <td>&#8369; {{ number_format($court->hourly_rate, 2) }}</td>
                    <td>
                        @if($court->status == 'available')
                            <span class="dash-badge dash-badge-available">Available</span>
                        @else
                            <span class="dash-badge dash-badge-maintenance">Maintenance</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-2">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('courts.edit', $court->id) }}" class="action-btn action-btn-edit" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                
                                <form action="{{ route('courts.destroy', $court->id) }}" method="POST" class="d-inline delete-court-form" data-court-name="{{ $court->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn-delete" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <!-- STAFF ONLY SEES THIS -->
                                <span class="dash-badge dash-badge-readonly">
                                    <i class="fa-solid fa-lock me-1 readonly-icon"></i> Read-Only
                                </span>
                            @endif
                        </div>
                    </td>
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
        const initializeCourtsTable = function () {
            if ($.fn.DataTable.isDataTable('#courtsTable')) {
                $('#courtsTable').DataTable().destroy();
            }

            $('#courtsTable').DataTable({
                pageLength: 10,
                paging: true,
                lengthChange: false,
                info: true,
                ordering: true,
                scrollX: window.matchMedia('(max-width: 1199.98px)').matches,
                scrollY: Math.max(160, $('.courts-panel').innerHeight() - 145) + 'px',
                scrollCollapse: true,
                language: { search: 'Quick Search:' }
            });
        };

        initializeCourtsTable();

        let courtsResizeTimer;
        $(window).on('resize', function () {
            clearTimeout(courtsResizeTimer);
            courtsResizeTimer = setTimeout(function () {
                initializeCourtsTable();
            }, 150);
        });

        $(document).on('click', '.court-thumb', function () {
            Swal.fire({
                imageUrl: $(this).data('full'),
                imageAlt: 'Court photo',
                showConfirmButton: false,
                showCloseButton: true,
                background: 'var(--panel)',
                width: 'auto'
            });
        });

        $(document).on('submit', '.delete-court-form', function (e) {
            e.preventDefault();
            const form = this;
            const courtName = $(form).data('court-name');

            Swal.fire({
                title: 'Delete this court?',
                html: `<strong>${courtName}</strong> and its image will be permanently removed. This can't be undone.`,
                icon: 'warning',
                iconColor: '#A6392F',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete court',
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

