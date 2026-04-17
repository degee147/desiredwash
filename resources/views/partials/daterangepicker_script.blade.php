<script type="text/javascript">
    $(function () {


        //"Y-m-d H:i:s"

        // var start = moment().subtract(29, 'days');
        // var end = moment();
        var label = "<?= !empty($label) ? $label : '' ?>";

        function getDateToShow(type) {

            if (type == "start") {
                if (label == "Today") {
                    return moment();
                }
                if (label == "Yesterday") {
                    return moment().subtract('days', 1);
                }
                if (label == "Last 7 Days") {
                    return moment().subtract('days', 6);
                }
                if (label == "Last 30 Days") {
                    return moment().subtract('days', 29);
                }
                if (label == "Last 90 Days") {
                    return moment().subtract('days', 89);
                }
                if (label == "This Month") {
                    return moment().startOf('month');
                }
                if (label == "Last Month") {
                    return moment().subtract('month', 1).startOf('month');
                }
                if (label == "Last 3 Months") {
                    return moment().subtract('month', 3).startOf('month');
                }
                if (label == "Last 6 Months") {
                    return moment().subtract('month', 6).startOf('month');
                }
                if (label == "Last 9 Months") {
                    return moment().subtract('month', 9).startOf('month');
                }

                <?php if (!empty($startDate)): ?>
                    if (label == "Custom Range") {
                        var sDate = moment("<?= $startDate ?>");
                        return sDate;
                        // return sDate.format('MM/DD/YYYY');
                    }
                    if (label == "All Time") {
                        var sDate = moment("<?= $startDate ?>");
                        return sDate;
                        // return sDate.format('MM/DD/YYYY');
                    }
                <?php endif; ?>
                return moment("01/01/2017");
            }
            if (type == "end") {
                if (label == "Today") {
                    return moment();
                }
                if (label == "Yesterday") {
                    return moment().subtract('days', 1);
                }
                if (label == "Last 7 Days") {
                    return moment();
                }
                if (label == "Last 30 Days") {
                    return moment();
                }
                if (label == "Last 90 Days") {
                    return moment();
                }
                if (label == "Last 3 Months") {
                    return moment();
                }
                if (label == "Last 6 Months") {
                    return moment();
                }
                if (label == "Last 9 Months") {
                    return moment();
                }
                if (label == "This Month") {
                    return moment().endOf('month');
                }
                if (label == "Last Month") {
                    return moment().subtract('month', 1).endOf('month');
                }
                <?php if (!empty($endDate)): ?>
                    if (label == "Custom Range") {
                        var eDate = moment("<?= $endDate ?>");
                        return eDate;
                        // return eDate.format('MM/DD/YYYY');
                    }
                    if (label == "All Time") {
                        var eDate = moment("<?= $endDate ?>");
                        return eDate;
                        // return eDate.format('MM/DD/YYYY');
                    }
                <?php endif; ?>
                return moment("01/01/2050");
            }
        }

        // var start = moment("<?= $startDate ?>").format('MM/DD/YYYY');
        // var end = moment("<?= $endDate ?>").format('MM/DD/YYYY');

        function cb(start, end, label) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $("input[name=startDate]").val(start.format("YYYY-MM-DD HH:mm:ss"));
            $("input[name=endDate]").val(end.format("YYYY-MM-DD HH:mm:ss"));
            $("input[name=label]").val(label);
            $("#momentForm").submit();
        }

        $('#reportrange').daterangepicker({
            startDate: getDateToShow("start"),
            endDate: getDateToShow("end"),
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last 3 Months': [moment().subtract('month', 3).startOf('month'), moment()],
                'Last 6 Months': [moment().subtract('month', 6).startOf('month'), moment()],
                'Last 9 Months': [moment().subtract('month', 9).startOf('month'), moment()],
            }
        }, cb);
        $('#reportrange span').html(getDateToShow("start").format('MMMM D, YYYY') + ' - ' + getDateToShow("end").format('MMMM D, YYYY'));

        // cb(getDateToShow("start"), getDateToShow("end"));

    });
</script>
