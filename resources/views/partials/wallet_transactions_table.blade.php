<div class="table-responsive col-sm-12">
    <table class="table table-striped table-bordered file-export" id="wallet_transactions_table">
        <thead>
            <tr>
                <th>SN</th>
                <th>Ref</th>
                @if (isset($viewpage) && $viewpage)
                @else
                    <th>Customer</th>
                @endif
                <th>Type</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Status</th>
                <th>Processed</th>
                <th>Date</th>
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
        'specific_id' => 'wallet_transactions_table',
        'ajax_table' => true,
        'searching' => $currentUser->isAdmin(),
        'export' => false,
        'ordering' => $currentUser->isAdmin(),
        'autoreload' => false,
        'stateSave' => true,
        'ajax_url' => route('tables.wallet_transactions', [
            'userid' => $userid ?? null,
        ]),
    ];
@endphp

@include('partials.datatable_options', $datatableOptions)
