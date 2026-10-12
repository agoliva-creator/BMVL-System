@extends('layouts.app')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/equipment.css') }}">
@endpush


<div class="equipment-page">
<div class="row equipment-heading align-items-center">
    <div class="col-md-6">
        <h2 class="equipment-title"><i class="fa-solid fa-dumbbell me-2"></i> Manage Equipment</h2>
        <p class="equipment-subtitle">Track facility gear, inventory, and rental rates.</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <!-- BOTH STAFF AND ADMIN CAN ADD EQUIPMENT -->
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
        <a href="{{ route('equipment.create') }}" class="btn-brand shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> Add New Equipment
        </a>
        @endif
    </div>
</div>

<div class="dash-panel equipment-panel">
    <div class="table-responsive equipment-table-wrap">
        <table class="table dash-table align-middle" id="equipmentTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Equipment Name</th>
                    <th>Rental Price / Hour</th>
                    <th>Stock</th>
                    <th>Status</th>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                    <th class="text-center">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <!-- BYPASSING BLADE COMPILER WITH RAW PHP -->
                <?php foreach($equipment as$item): ?>
                <tr>
                    <td>{{ $item->id }}</td>
                    
                    <td>
                        @if($item->image)
                            <!-- Clickable image triggering the modal -->
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="equip-thumb" data-bs-toggle="modal" data-bs-target="#imageModal{{ $item->id }}">

                            <!-- Bootstrap Modal for this specific image -->
                            <div class="modal fade" id="imageModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content equipment-modal-content">
                                        <div class="modal-header border-0 pb-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center pb-4 pt-0">
                                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="img-fluid rounded shadow-sm equipment-modal-image">
                                            <h5 class="mt-3 mb-0 equipment-modal-title">{{ $item->name }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="equip-thumb-placeholder">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        @endif
                    </td>

                    <td class="equipment-name">{{ $item->name }}</td>
                    <td>₱ {{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->stock }}</td>
                    <td>
                        @if($item->status == 'available')
                            <span class="dash-badge dash-badge-available">Available</span>
                        @else
                            <span class="dash-badge dash-badge-maintenance">Maintenance</span>
                        @endif
                    </td>
                    
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                    <td class="text-center">
                        <div class="d-inline-flex gap-2">
                            <!-- BOTH STAFF AND ADMIN CAN EDIT -->
                            <a href="{{ route('equipment.edit', $item->id) }}" class="action-btn action-btn-edit" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            
                            <!-- ONLY ADMINS CAN DELETE -->
                            @if(auth()->user()->role === 'admin')
                                <form action="{{ route('equipment.destroy', $item->id) }}" method="POST" class="d-inline delete-equip-form" data-equip-name="{{ $item->name }}">
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
        const initializeEquipmentTable = function () {
            if ($.fn.DataTable.isDataTable('#equipmentTable')) {
                $('#equipmentTable').DataTable().destroy();
            }

            $('#equipmentTable').DataTable({
                pageLength: 10,
                paging: true,
                lengthChange: false,
                info: true,
                ordering: true,
                scrollX: window.matchMedia('(max-width: 1199.98px)').matches,
                scrollY: Math.max(160, $('.equipment-panel').innerHeight() - 145) + 'px',
                scrollCollapse: true,
                language: { search: 'Quick Search:' }
            });
        };

        initializeEquipmentTable();
        let equipmentResizeTimer;
        $(window).on('resize', function () {
            clearTimeout(equipmentResizeTimer);
            equipmentResizeTimer = setTimeout(initializeEquipmentTable, 150);
        });

        // Themed confirmation modal before deleting equipment
        $(document).on('submit', '.delete-equip-form', function (e) {
            e.preventDefault();
            const form = this;
            const equipName = $(form).data('equip-name');

            Swal.fire({
                title: 'Delete this equipment?',
                html: `<strong>${equipName}</strong> will be permanently removed. This can't be undone.`,
                icon: 'warning',
                iconColor: '#A6392F',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete equipment',
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
