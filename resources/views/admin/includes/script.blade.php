<!-- jQuery -->
<script src="{{ asset('assets/admin/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('assets/admin/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('assets/admin/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- SlimScroll -->
<script src="{{ asset('assets/admin/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('assets/admin/bower_components/fastclick/lib/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/admin/dist/js/adminlte.min.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('assets/admin/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

<!-- AdminLTE for demo purposes -->
<script src="{{ asset('assets/admin/dist/js/demo.js') }}"></script>

<script src="{{ asset('assets/admin/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/admin/validation.js') }}"></script>

<script>
    Number.prototype.formatMoney = function(c, d, t) {
        let n = this,
            cc = isNaN(c = Math.abs(c)) ? 2 : c,
            de = d === undefined ? "." : d,
            th = t === undefined ? "," : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(cc)) + "",
            j = i.length;

        j = (j > 3) ? j % 3 : 0;

        return s + (j ? i.substr(0, j) + th : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + th) + (cc ? de +
            Math.abs(n - i).toFixed(cc).slice(2) : "");
    };

    $("input").on("keydown", function(e) {
        console.log(this.value);
        if (e.which === 32 && e.target.selectionStart === 0) {
            return false;
        }
    });
</script>

<script>
    $(document).ready(function() {
        // Debug script loading
        console.log('jQuery version:', jQuery.fn.jquery);
        console.log('Bootstrap modal available:', typeof $.fn.modal !== 'undefined');
        console.log('AdminLTE available:', typeof $.AdminLTE !== 'undefined');
        console.log('Tree plugin available:', typeof $.fn.tree !== 'undefined');

        // Initialize AdminLTE
        if (typeof $.AdminLTE !== 'undefined') {
            $.AdminLTE.layout.activate();
            $.AdminLTE.tree('.sidebar-menu');
        } else {
            console.error('AdminLTE not available');
        }
        
        // Initialize SlimScroll - with error handling to prevent DOM null errors
        try {
            if (typeof $.fn.slimScroll !== 'undefined' && $('.sidebar').length > 0) {
                setTimeout(function() {
                    $('.sidebar').slimScroll({
                        height: 'auto',
                        size: '3px',
                        color: 'rgba(0,0,0,0.2)',
                        wheelStep: 10,
                        allowPageScroll: true,
                        alwaysVisible: false,
                        railVisible: true,
                        railColor: '#222',
                        railOpacity: 0.3,
                        railClass: 'slimScrollRail',
                        barClass: 'slimScrollBar',
                        wrapperClass: 'slimScrollDiv',
                        scrollBy: '30px',
                        position: 'right',
                        distance: '1px'
                    });
                }, 500); // Delay initialization to ensure DOM is ready
            }
        } catch (e) {
            console.error('SlimScroll initialization error:', e);
        }
        
        // Initialize Push Menu
        if (typeof $.fn.pushMenu !== 'undefined') {
            $('[data-toggle="push-menu"]').pushMenu();
        }
        
        // Initialize Bootstrap components
        if (typeof $.fn.dropdown !== 'undefined') {
            $('.dropdown-toggle').dropdown();
        }
        
        // Initialize modals
        $('.modal').on('show.bs.modal', function () {
            $(this).find('select').each(function() {
                if (typeof $(this).select2 !== 'undefined') {
                    $(this).select2('destroy').select2();
                }
            });
        });

        if ($(".account_receivables").find('.active').length > 0) {
            $(".account_receivables").addClass('active');
        }

        if ($(".point_of_sale").find('.active').length > 0) {
            $(".point_of_sale").addClass('active');
        }

        // Validate form on select
        $('form').on('select2:select', 'select.form-control', function(e) {
            $(this).valid();
        });

        // Validate form
        $("form.validate-form").each(function(index, form) {
            $(form).validate();
        });

        // Initialize DataTables
        $('#sticky_header').DataTable({
            'fixedHeader': true,
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'pageLength': 100,
            'initComplete': function (settings, json) {
                var info = this.api().page.info();
                var total_record = info.recordsTotal;
                if (total_record < 101) {
                    $('.dataTables_paginate').hide();
                }
            },
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': 'noneedtoshort'
            }]
        });

        $('#create_datatable').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'pageLength': 100,
            'initComplete': function (settings, json) {
                var info = this.api().page.info();
                var total_record = info.recordsTotal;
                if (total_record < 101) {
                    $('.dataTables_paginate').hide();
                }
            },
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': 'noneedtoshort'
            }]
        });

        $('#create_datatable_10').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'pageLength': 10,
            'initComplete': function (settings, json) {
                let info = this.api().page.info();
                let total_record = info.recordsTotal;
                if (total_record < 11) {
                    $('.dataTables_paginate').hide();
                }
            },
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': 'noneedtoshort'
            }]
        });

        $('#create_datatable_25').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'pageLength': 25,
            'initComplete': function (settings, json) {
                let info = this.api().page.info();
                let total_record = info.recordsTotal;
                if (total_record < 26) {
                    $('.dataTables_paginate').hide();
                }
            },
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': 'noneedtoshort'
            }]
        });

        $('#create_datatable_50').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'pageLength': 50,
            'initComplete': function (settings, json) {
                var info = this.api().page.info();
                var total_record = info.recordsTotal;
                if (total_record < 51) {
                    $('.dataTables_paginate').hide();
                }
            },
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': 'noneedtoshort'
            }]
        });

        $('#create_datatable_desc').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'pageLength': 100,
            'initComplete': function (settings, json) {
                var info = this.api().page.info();
                var total_record = info.recordsTotal;
                if (total_record < 101) {
                    $('.dataTables_paginate').hide();
                }
            },
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': 'noneedtoshort'
            }],
            "aaSorting": [[0, 'desc']]
        });

        $('.create_multiple_datatable_10').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'pageLength': 10,
            'initComplete': function (settings, json) {
                let info = this.api().page.info();
                let total_record = info.recordsTotal;
                if (total_record < 11) {
                    $('.dataTables_paginate').hide();
                }
            },
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': 'noneedtoshort'
            }]
        });

        $('#create_datatable_no_ordering').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': false,
            'info': true,
            'autoWidth': false,
            'pageLength': 100,
            'initComplete': function (settings, json) {
                var info = this.api().page.info();
                var total_record = info.recordsTotal;
                if (total_record < 101) {
                    $('.dataTables_paginate').hide();
                }
            },
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': 'noneedtoshort'
            }]
        });

        $(".validate").validate();

        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    });
</script>

@yield('uniquepagescriptforchart')
@stack('scripts')

<!-- Signature validation override script -->
<script src="{{ asset('js/signature-override.js') }}"></script>
