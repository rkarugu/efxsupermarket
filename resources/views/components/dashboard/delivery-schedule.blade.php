<div id="calendar" style="min-height: 350px; width:100%" class="d-flex align-items-center justify-content-center">
    <div class="loading">
        <h4><i class="fa fa-spinner fa-spin"></i> Loading...</h4>
    </div>
</div>
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}"
        media="print">
@endpush
@push('scripts')
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src="{{ asset('assets/admin/bower_components/fullcalendar/dist/fullcalendar.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('procurement-dashboard.delivery-schedule') }}",
                data: {
                    location: $("#store").val()
                },
                success: function(response) {
                    $("#calendar").removeClass('d-flex');
                    $("#calendar .loading").hide();
                    let slots = response.slots;
                    data = get_slots(slots);
                    renderCalendar(data);
                }
            })
        })

        function renderCalendar(data) {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                buttonText: {
                    today: 'today',
                    month: 'month',
                    week: 'week',
                    day: 'day'
                },
                events: data,
                timeFormat: 'h(:mm) A', // 12-hour format with optional minutes
                eventRender: function(event, element) {
                    let endTime = moment(event.end).format('h:mm A');
                    element.find('.fc-title').prepend(`<b>- ${endTime} </b>`);
                },
                dayRender: function(date, cell) {

                },
                // Ensure that all days are rendered correctly
                // droppable: true
            });

            date = '{{ date('Y-m-01', strtotime(request()->date ?? date('Y-m-d'))) }}';
            $('#calendar').fullCalendar('gotoDate', date);
        }

        function get_slots(slots) {
            let colors  = ['#00a65a','#00c0ef','#0073b7', '#f39c12', '#f56954', '#3c8dbc'];
            data = [];
            $.each(slots, function(i, v) {
                var ms = Date.parse(v.delivery_slot_date)
                let d = new Date(ms)
                shuffle(colors);
                data.push({
                    title: ` - ${v.business_name}`,
                    start: new Date(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours() - 1, d
                        .getMinutes()),
                    end: new Date(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours(), d.getMinutes()),
                    backgroundColor: colors[0],
                    borderColor: colors[0]
                })
            });

            return data;
        }

        function shuffle(array) {
            let currentIndex = array.length;

            while (currentIndex != 0) {
                let randomIndex = Math.floor(Math.random() * currentIndex);
                currentIndex--;
                [array[currentIndex], array[randomIndex]] = [
                    array[randomIndex], array[currentIndex]
                ];
            }
        }
    </script>
@endpush
