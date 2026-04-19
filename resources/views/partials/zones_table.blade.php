<div class="table-responsive col-sm-12">
    <table class="table table-striped table-bordered file-export" id="zones_table">
        <thead>
            <tr>
                <th>SN</th>
                <th>ID</th>
                <th>Name</th>
                <th>Area</th>
                <th>Delivery fee</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {{-- DataTables will populate this --}}
        </tbody>
    </table>
</div>

@php
    $datatableOptions = [
        'record_name' => 'Zones',
        'specific_id' => 'zones_table',
        'ajax_table' => true,
        'searching' => true,
        'export' => false,
        'ordering' => true,
        'autoreload' => false,
        'stateSave' => true,
        'ajax_url' => route('tables.zones', [
            'userid' => $currentUser->id,
        ]),
    ];
@endphp

@include('partials.datatable_options', $datatableOptions)
