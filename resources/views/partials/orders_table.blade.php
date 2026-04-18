<div class="table-responsive col-sm-12">
    <table class="table table-striped table-bordered file-export" id="orders_table">
        <thead>
            <tr>
                <th>SN</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Zone</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Date</th>
                @if ($currentUser->isSupport())
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
        'record_name' => $record_name ?? 'Orders',
        'specific_id' => 'orders_table',
        'ajax_table' => true,
        'searching' => $currentUser->isSupport(),
        'export' => false,
        'ordering' => $currentUser->isSupport(),
        'autoreload' => false,
        'stateSave' => true,
        'ajax_url' => route('tables.orders', [
            'userid' => isset($userid) ? $userid : $currentUser->id,
        ]),
    ];
@endphp

@include('partials.datatable_options', $datatableOptions)
