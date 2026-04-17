<script>
    let {{ $specific_id }}_table;

    jQuery(document).ready(function() {

        $.fn.dataTable.ext.errMode = function(settings, helpPage, message) {
            // console.log(message);
            // Optionally redirect on error
            // window.location.href = "{{ request()->getRequestUri() }}";
        };

        if (!$.fn.DataTable.isDataTable("#{{ $specific_id }}")) {

            {{ $specific_id }}_table = $("#{{ $specific_id }}").DataTable({
                // dom: 'Blfrtip',
                // "responsive": true,

                @if (!empty($roiordering))
                    "order": [
                        [6, "desc"]
                    ],
                @endif
                @if (!empty($resetordering))
                    "order": [],
                @endif
                @if (!empty($ajax_table))
                    "processing": true,
                    "serverSide": true,
                    "searchDelay": 3500,
                    "ajax": {
                        'url': "{{ $ajax_url }}",
                        "beforeSend": function(xhr) {
                            showLoadingData("{{ $record_name }}");
                            dataTableInstances.push(xhr);
                        },
                        'data': function(data) {
                            if (typeof selections !== 'undefined' && selections !== null) {
                                if (selections) {
                                    data.selections = JSON.stringify(selections);
                                }
                            }
                            data.played = played;
                            if ($('#highest_roi').length) {
                                data.highest_roi = $('#highest_roi').val();
                            }
                            data.table_filter = $("#table_filter option:selected").text() || '';
                            data.campaign_filters = $("#campaign_filters option:selected").text() ||
                                '';
                            data.priority = $("input[name=priority]").val() || '';
                            data.rate = $("input[name=rate]").val() || '';
                            data.min_odd = $("input[name=min_odd]").val() || '';
                            if ($('#filter_options').length) {
                                data.filter_options = $('#filter_options').val();
                            }
                            if ($('#trade_filters').length) {
                                data.trade_filters = $('#trade_filters').val();
                            }
                            if ($('#submission_filters').length) {
                                data.submission_filters = $('#submission_filters').val();
                            }
                            // Indicators
                            if ($('#ema').length) {
                                data.ema = $('#ema').val();
                            }
                            if ($('#rsi').length) {
                                data.rsi = $('#rsi').val();
                            }
                            if ($('#bb').length) {
                                data.bb = $('#bb').val();
                            }
                            if ($('#macd').length) {
                                data.macd = $('#macd').val();
                            }
                            if ($('#psar').length) {
                                data.psar = $('#psar').val();
                            }
                            if ($('#stoch').length) {
                                data.stoch = $('#stoch').val();
                            }
                            if ($('#obv').length) {
                                data.obv = $('#obv').val();
                            }
                            if ($('#mfi').length) {
                                data.mfi = $('#mfi').val();
                            }
                            if ($('#sma').length) {
                                data.sma = $('#sma').val();
                            }
                            if ($('#combo_id').length) {
                                data.combo_id = $('#combo_id').val();
                            }
                            if ($('#suggestion').length) {
                                data.suggestion = $('#suggestion').val();
                            }
                        },
                        "dataSrc": function(json) {
                            showLoadedData("{{ $record_name }}");
                            if (parseFloat(json.available)) {
                                $('#navbalance').html(json.available);
                                $('#navonorder').html(json.onorder);
                            }
                            if (parseFloat(json.accumulator)) {
                                $('#accumulator').html(json.accumulator);
                                $('#accm').fadeIn();
                            }
                            return json.data;
                        }
                    },
                    "rowCallback": function(row, data) {
                        if (typeof selections !== 'undefined' && selections !== null) {
                            if (selections.includes($(data[0]).attr('itemid'))) {
                                $(row).addClass('selected');
                                $(row).find('input[type="checkbox"]').prop("checked", true);
                            }
                        }
                    },
                @endif
                @if (!empty($stateSave))
                    "stateSave": true,
                @endif
                @if (isset($ordering) && $ordering === false)
                    "ordering": false,
                @endif
                @if (isset($ordering) && $ordering === true)
                    "order": [
                        [0, "desc"]
                    ],
                @endif
                @if (!empty($searching) && $searching === false)
                    "searching": false,
                @endif

                @if (!empty($paging_and_search))
                    'select': {
                        'style': 'single'
                    },
                    dom: "<'row'<'col-md-3'f><'col-md-3'B><'col-md-3'>>rt<'row'<'col-md-6'i><'col-md-6'p>>",
                @elseif (!empty($paging_and_search_multiple))
                    columnDefs: [{
                            orderable: false,
                            targets: 0,
                            checkboxes: {
                                selectRow: true
                            }
                        }],
                        select: {
                            style: 'multi'
                        },
                        dom:
                        "<'row'<'col-md-3'><'col-md-3'B><'col-md-3'>>rt<'row'<'col-md-3'f><'col-md-6'ip>>",
                        @if (isset($pagination) && $pagination === false)
                            "paging": false,
                        @else
                            "pagingType": "full_numbers",
                            "paging": true,
                        @endif
                @elseif (isset($paging) && $paging === false)
                    dom: "<'row'<'col-md-4'><'col-md-4'B><'col-md-4'f>>rt<'row'<'col-md-6'><'col-md-6'>>",
                @else
                    dom: "<'row'<'col-md-3'lt><'col-md-4'iB><'col-md-5'fp>>rt<'row'<'col-md-5'i><'col-md-7'p>>",
                @endif
                "lengthMenu": [5, 10, 15, 20, 30, 50, 75, 100, 200, 300, 500, 1000],
                "pageLength": parseInt("{{ !empty($selection_count) ? $selection_count : 5 }}"),
                @if (isset($export) && $export === false)
                    buttons: [],
                @else
                    buttons: ['copy', 'csv', 'pdf', 'print'],
                @endif
                "language": {
                    "emptyTable": "Not {{ !empty($record_count) ? singularise($record_name, $record_count) : '' }} available at the moment",
                    "decimal": "",
                    @if (!empty($no_info))
                        "info": "Showing _TOTAL_ {{ $record_name }}",
                        "infoEmpty": "",
                    @else
                        "info": "Showing _START_ to _END_ of _TOTAL_ {{ $record_name }}",
                        "infoEmpty": "Showing 0 to 0 of 0 {{ $record_name }}",
                    @endif
                    "infoFiltered": "(filtered from _MAX_ total {{ !empty($record_count) ? singularise($record_name, $record_count) : '' }})",
                    "infoPostFix": "",
                    "thousands": ",",
                    "select": {
                        rows: {
                            _: " |  Selected %d {{ $record_name }}",
                            0: "Click on a row to select it",
                            1: "Selected 1 {{ singularise($record_name, 1) }}"
                        }
                    },
                    "lengthMenu": "Show _MENU_ {{ $record_name }}",
                    "loadingRecords": "Loading...",
                    "processing": "Loading {{ $record_name }}...",
                    "search": "Search:",
                    "zeroRecords": "No matching {{ !empty($record_count) ? singularise($record_name, $record_count) : '' }} records found",
                    "initComplete": function(settings, json) {}
                }
            });

            $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass(
                'btn btn-outline-primary mr-1');

            $('#played').change(function() {
                played = $(this).is(":checked") ? "1" : "0";
                {{ $specific_id }}_table.draw();
            });
            $('#campaign_filters').change(function() {
                {{ $specific_id }}_table.draw();
            });
            $('#priority').change(function() {
                /* Optionally: {{ $specific_id }}_table.draw(); */
            });
            $('#rate').change(function() {
                /* Optionally: {{ $specific_id }}_table.draw(); */
            });
            $('#min_odd').change(function() {
                /* Optionally: {{ $specific_id }}_table.draw(); */
            });
            $('#table_filter').change(function() {
                {{ $specific_id }}_table.draw();
            });
            $('.refresh_tables').click(function(event) {
                event.preventDefault();
                cancelDataTablesRequests();
                setTimeout(() => {
                    {{ $specific_id }}_table.draw();
                }, 1000);
            });
            $('#filter_options').change(function() {
                {{ $specific_id }}_table.draw();
            });
            $('#highest_roi').change(function() {
                {{ $specific_id }}_table.draw();
            });
            $('#trade_filters').change(function() {
                {{ $specific_id }}_table.draw();
            });
            $('#submission_filters').change(function() {
                {{ $specific_id }}_table.draw();
            });
            $('.indicator').change(function() {
                {{ $specific_id }}_table.draw();
            });

            @if (!empty($autoreload))
                setInterval(function() {
                    {{ $specific_id }}_table.ajax.reload(null, false);
                }, 20000);
            @endif
            @if (!empty($games_autoreload))
                setInterval(function() {
                    /* For games: {{ $specific_id }}_table.ajax.reload(null, false); */
                }, 120000);
            @endif
        }
    });
</script>
