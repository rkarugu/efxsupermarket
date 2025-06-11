@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <h4 class="box-title">Delivery Slots</h4>
            </div>
            <div class="box-header with-border">
                <form action="{!! route($model . '.show_booked_slots') !!}" method="GET">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="date" name="date" value="{{ request()->date ?? date('Y-m-d') }}"
                                    id="date" class="form-control" placeholder="Delivery Date">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <x-filters.store />
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa fa-filter"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-body">
                <div id="calendar"></div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="modelId" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <form action="{{ route('order-delivery-slots.book_lpo_slot') }}" method="post" class="submitMe">
            @csrf
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Slot to LPO</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Date</label>
                            <input type="date" name="date" id="date" class="form-control" placeholder=""
                                aria-describedby="helpId">
                        </div>
                        <div class="form-group">
                            <label for="">LPO</label>
                            <select name="lpo" class="form-control select2_input">
                                <option value="" selected disabled>-- Select LPO --</option>
                                @foreach ($lpos as $lpo)
                                    <option value="{{ $lpo->id }}">{{ $lpo->purchase_no }} -
                                        {{ $lpo->getSupplier->name }} ({{ $lpo->getSupplier->supplier_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Time Slot</label>
                            <select name="time_slot" class="form-control select2_input">
                                <option value="" selected disabled>-- Select Slot --</option>
                                @for ($i = 0; $i < 24; $i++)
                                    <option value="{{ $i }}">
                                        {{ date('H:i A', strtotime(date('Y-m-d ' . $i . ':0:0'))) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Proces</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}"
        media="print">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .fc-row .fc-bg {
            z-index: 99999999999999;
        }

        .select2 {
            width: 100% !important;
        }
    </style>
@endsection
@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/fullcalendar/dist/fullcalendar.min.js') }}"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(function() {
            $('.select2_input').select2();
            $(document).on('click', '.add-event-btn', function(e) {
                e.preventDefault();
                $('.modal-body #date').val($(this).data('date'));
                $('.modal').modal('show');
            });

            var colors = ['#00a65a', '#00c0ef', '#0073b7', '#f39c12', '#f56954', '#3c8dbc'];

            function shuffle(array) {
                let currentIndex = array.length;

                // While there remain elements to shuffle...
                while (currentIndex != 0) {

                    // Pick a remaining element...
                    let randomIndex = Math.floor(Math.random() * currentIndex);
                    currentIndex--;

                    // And swap it with the current element.
                    [array[currentIndex], array[randomIndex]] = [
                        array[randomIndex], array[currentIndex]
                    ];
                }
            }

            var slots = {!! json_encode($slots) !!}

            function get_slots() {
                data = [];
                $.each(slots, function(i, v) {
                    var ms = Date.parse(v.delivery_slot_date)
                    let d = new Date(ms)
                    shuffle(colors);
                    data.push({
                        title: ` - ${v.business_name}`,
                        start: new Date(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours() -
                            1, d.getMinutes()),
                        end: new Date(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours(), d
                            .getMinutes()),
                        backgroundColor: colors[0],
                        borderColor: colors[0]
                    })
                });
                return data;
            }
            /* initialize the external events
             -----------------------------------------------------------------*/
            function init_events(ele) {
                ele.each(function() {

                    // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                    // it doesn't need to have a start or end
                    var eventObject = {
                        title: $.trim($(this).text()) // use the element's text as the event title
                    }

                    // store the Event Object in the DOM element so we can get to it later
                    $(this).data('eventObject', eventObject)

                    // make the event draggable using jQuery UI
                    $(this).draggable({
                        zIndex: 1070,
                        revert: true, // will cause the event to go back to its
                        revertDuration: 0 //  original position after the drag
                    })

                })
            }

            init_events($('#external-events div.external-event'))

            /* initialize the calendar
             -----------------------------------------------------------------*/
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
                events: get_slots(),
                timeFormat: 'h(:mm) A', // 12-hour format with optional minutes
                eventRender: function(event, element) {
                    let endTime = moment(event.end).format('h:mm A');
                    element.find('.fc-title').prepend(`<b>- ${endTime} </b>`);
                },
                dayRender: function(date, cell) {
                    // Create a "+" button and append it to the day cell
                    var t_date = moment(); // Get today's date as a moment object
                    console.log(t_date, date, t_date.isSameOrAfter(date, 'day'))
                    // Compare only the date part, ignoring the time
                    if (date.isSameOrAfter(t_date, 'day')) {
                        let addButton = $('<a class="add-event-btn" data-date="' + date.format() +
                            '">+</a>');
                        $(cell).css('position', 'relative'); // Ensure the cell has relative positioning
                        $(cell).append(addButton);

                        // Style the button to be in the top-right corner
                        addButton.css({
                            position: 'absolute',
                            top: '2px',
                            left: '5px',
                            zIndex: 1000,
                            backgroundColor: '#007eff',
                            borderRadius: '50%',
                            color: 'white',
                            width: '20px',
                            height: '20px',
                            textAlign: 'center',
                            cursor: 'pointer'
                        });
                    }

                },
                // Ensure that all days are rendered correctly
                // droppable: true
            });
            date = '{{ date('Y-m-01', strtotime(request()->date ?? date('Y-m-d'))) }}';
            $('#calendar').fullCalendar('gotoDate', date);

        })
    </script>
@endsection
