<div class="table-responsive col-sm-12">
    <table class="table table-striped table-bordered file-export" id="packages_table">
        <thead>
            <tr>
                <th>SN</th>
                <th>Name</th>
                <th>Subtitle</th>
                <th>Price</th>
                <th>Featured</th>
                <th>Status</th>
                <th>Sort</th>
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
        'record_name' => $record_name ?? 'Packages',
        'specific_id' => 'packages_table',
        'ajax_table' => true,
        'searching' => true,
        'export' => false,
        'ordering' => $currentUser->isAdmin(),
        'autoreload' => false,
        'stateSave' => true,
        'ajax_url' => route('tables.packages', [
            'userid' => isset($userid) ? $userid : $currentUser->id,
        ]),
    ];
@endphp

@include('partials.datatable_options', $datatableOptions)
