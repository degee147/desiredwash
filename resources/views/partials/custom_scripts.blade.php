<script>
    let played = "0";
    toastr.options = {
        "closeButton": false,
        "escapeHtml": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "2000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // Store references to the DataTables API instances
    var dataTableInstances = [];

    function cancelDataTablesRequests() {
        var count = dataTableInstances.length;
        dataTableInstances.forEach(function(api) {
            api.abort();
            api = null;
        });
    }

    function secTime(val) {
        return val > 9 ? val : "0" + val;
    }

    function showLoadingData(str) {
        @if (isset($showloading) && $showloading == true)
            toastr.info("Loading " + str + " ..");
        @endif
    }

    function showLoadedData(str) {
        @if (isset($showloading) && $showloading == true)
            toastr.success("Loaded " + str);
        @endif
    }

    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    function updateQueryString(key, value, url) {
        if (url instanceof URL) {
            url = url.toString();
        }
        if (!url) url = window.location.href;
        var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
            hash;
        if (re.test(url)) {
            if (typeof value !== 'undefined' && value !== null && value.length > 0) {
                return url.replace(re, '$1' + key + "=" + value + '$2$3');
            } else {
                hash = url.split('#');
                url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
                if (typeof hash[1] !== 'undefined' && hash[1] !== null) url += '#' + hash[1];
                return url;
            }
        } else {
            if (typeof value !== 'undefined' && value !== null && value.length > 0) {
                var separator = url.indexOf('?') !== -1 ? '&' : '?';
                hash = url.split('#');
                url = hash[0] + separator + key + '=' + value;
                if (typeof hash[1] !== 'undefined' && hash[1] !== null) url += '#' + hash[1];
                return url;
            } else {
                return url;
            }
        }
    }

    function showLoading() {
        $.blockUI({
            message: '<h5><img src="{{ asset('busy.gif') }}" /> Just a moment..</h5>',
            css: {
                'border-radius': '20px'
            }
        });
    }

    function hideLoading() {
        $.unblockUI();
    }

    function block(target) {
        if (target) {
            App.blockUI({
                animate: true,
                target: target,
                overlayColor: 'none',
            });
        } else {
            App.blockUI({
                animate: true,
                overlayColor: 'none',
            });
        }
    }

    function unblock(target) {
        if (target) {
            App.unblockUI(target);
        }
        App.unblockUI();
        $.unblockUI();
    }

    function jsNumberFormat(n) {
        var value = n.toLocaleString(
            undefined, {
                minimumFractionDigits: 0
            }
        );
        return value;
    }



    var reloadPage = function() {
        // window.location.reload();
    }

    $(document).ready(function() {
        var waitTime = 200000 //5 minutes
        timer = setTimeout(reloadPage, waitTime);

        document.onclick = function(event) {
            if (event === undefined) event = window.event;
            var target = 'target' in event ? event.target : event.srcElement;
            clearTimeout(timer);
            timer = setTimeout(reloadPage, waitTime);
        };



        $(".datepicker").datepicker({
            format: 'yyyy/mm/dd',
        });

        var state_id = $('select[name^="state_id"]').val();
        if (state_id) {
            getCities(state_id);
        }

        $('.state_select').on('select2:select', function(e) {
            var data = e.params.data;
            getCities(data.id);
        });

        $('.select2').select2({
            placeholder: 'Select an option',
            theme: "classic"
        });

        $('.select2_multiple').select2({
            theme: "classic",
            placeholder: "Select one or more options",
            allowClear: true,
            maximumSelectionLength: 7,
        });

        function copyToClipboard(text) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(text).select();
            document.execCommand("copy");
            $temp.remove();
        }

        jQuery(document).on('click', '.clipcopy', function(e) {
            e.preventDefault();
            var element = $(this);
            copyToClipboard(element.data('cliptext'));
            element.tooltip('show');
            setTimeout(function() {
                element.tooltip('hide');
            }, 1500);
        });

        jQuery(document).on('click', '.view_attachment', function(e) {
            var link = $(this).attr('href');
            var title = $(this).data('title');
            var iframe =
                '<div class="iframe-container"><iframe style="width: 100%;height: 500px;" src="' +
                link + '"></iframe></div>'
            $.createModal({
                title: title,
                message: iframe,
                closeButton: true,
                scrollable: false
            });
            return false;
        });

        jQuery(document).on('change', '#limitBox', function(e) {
            var parentForm = $(this).closest("form");
            parentForm.submit();
        });

        jQuery(document).on('change', '#activeCountrySelect', function(e) {
            var parentForm = $(this).closest("form");
            parentForm.submit();
        });



    });
</script>
