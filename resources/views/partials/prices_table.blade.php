<div class="table-responsive col-sm-12">
    <table class="table table-striped table-bordered file-export" id="prices_table">
        <thead>
            <tr>
                <th>SN</th>
                <th>Item</th>
                <th>Category</th>
                <th>Service Type</th>
                <th>Regular Price</th>
                <th>Express Price</th>
                <th>Status</th>
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
        'record_name' => $record_name ?? 'Prices',
        'specific_id' => 'prices_table',
        'ajax_table' => true,
        'searching' => true,
        'export' => false,
        'ordering' => $currentUser->isAdmin(),
        'autoreload' => false,
        'stateSave' => true,
        'ajax_url' => route('tables.prices', [
            'userid' => isset($userid) ? $userid : $currentUser->id,
            'category' => $category ?? '',
        ]),
    ];
@endphp

@include('partials.datatable_options', $datatableOptions)
