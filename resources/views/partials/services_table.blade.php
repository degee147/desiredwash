<div class="table-responsive col-sm-12">
    <table class="table table-striped table-bordered file-export" id="services_table">
        <thead>
            <tr>
                <th>SN</th>
                <th>Emoji</th>
                <th>Name</th>
                <th>Price</th>
                @if ($currentUser->isAdmin())
                    <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            {{-- DataTables will populate this --}}
        </tbody>
    </table>
</div>

@php
    $datatableOptions = [
        'record_name' => $record_name ?? 'Services',
        'specific_id' => 'services_table',
        'ajax_table' => true,
        'searching' => true,
        'export' => false,
        'ordering' => $currentUser->isAdmin(),
        'autoreload' => false,
        'stateSave' => true,
        'ajax_url' => route('tables.services', [
            'userid' => isset($userid) ? $userid : $currentUser->id,
        ]),
    ];
@endphp

@include('partials.datatable_options', $datatableOptions)
