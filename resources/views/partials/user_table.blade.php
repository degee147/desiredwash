<div class="table-responsive col-sm-12">
    <table class="table table-striped table-bordered file-export" id="users_table">
        <thead>
            <tr>
                <th>SN</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Balance</th>
                <th>Joined</th>
                @if (!empty($currentUser->sa))
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
    // Set up DataTables options for this table
    $datatableOptions = [
        'record_name' => $record_name ?? 'Users',
        'specific_id' => 'users_table',
        'ajax_table' => true,
        'searching' => !empty($currentUser->sa),
        'export' => false,
        'ordering' => !empty($currentUser->sa),
        'autoreload' => false,
        'stateSave' => true,
        'ajax_url' => route('tables.users', [
            'userid' => $currentUser->id,
            'refcode' => $refcode ?? '',
        ]),
    ];
@endphp

@include('partials.datatable_options', $datatableOptions)
