<div class="table-responsive col-sm-12">
    <table class="table table-striped table-bordered file-export" id="notifications_table">
        <thead>
            <tr>
                <th>SN</th>
                <th>Title</th>
                <th>Body</th>
                <th>Type</th>
                <th>User</th>
                <th>Read</th>
                <th>Date</th>
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
        'record_name' => $record_name ?? 'Notifications',
        'specific_id' => 'notifications_table',
        'ajax_table' => true,
        'searching' => true,
        'export' => false,
        'ordering' => $currentUser->isAdmin(),
        'autoreload' => false,
        'stateSave' => true,
        'ajax_url' => route('tables.notifications', [
            'userid' => isset($userid) ? $userid : $currentUser->id,
        ]),
    ];
@endphp

@include('partials.datatable_options', $datatableOptions)
