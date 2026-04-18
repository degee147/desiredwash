<div class="table-responsive col-sm-12">
    <table class="table table-striped table-bordered file-export" id="transactions_table">
        <thead>
            <tr>
                <th>SN</th>
                <th>Ref</th>
                <th>User</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Status</th>
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
        'record_name' => $record_name ?? 'Transactions',
        'specific_id' => 'transactions_table',
        'ajax_table' => true,
        'searching' => $currentUser->isAdmin(),
        'export' => false,
        'ordering' => $currentUser->isAdmin(),
        'autoreload' => false,
        'stateSave' => true,
        'ajax_url' => route('tables.transactions', [
            'userid' => isset($userid) ? $userid : $currentUser->id,
        ]),
    ];
@endphp

@include('partials.datatable_options', $datatableOptions)
