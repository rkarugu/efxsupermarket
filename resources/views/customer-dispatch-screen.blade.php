<!DOCTYPE html>
<html lang="en">
<head>
    <title>Ticket List</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/admin/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet"
          href="{{ asset('assets/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

    <style>
        .dataTables_wrapper tr.ready > td {
            background-color: white !important;
            color: black !important;
        }

        .header-section {
            background-color: red;
            padding: 10px;
            color: white;
            display: flex;
            justify-content: space-between;
            width: 100%; /* Add this line to make sure it spans the whole width */
        }

        .logout {
            background-color: red;
            color: white;
            border-radius: 4%;
        }

        .logout_img {
            margin: 0 auto;
            width: 30%;
            float: right;
        }

        .table > thead > tr > th,
        .table > tbody > tr > td {
            width: auto;
            /*background-color: black;*/
            color: white;
            padding-top: 15px; /* Increase padding for wider rows */
            padding-bottom: 15px; /* Increase padding for wider rows */
            /*border: none; !* Remove cell borders *!*/
            font-size: 25px;
        }

        .table.dataTable {
            margin-top: 0 !important;
            border-color: black;
        }

        .dataTables_processing {
            display: none !important;
        }

        .container-fluid {
            overflow-x: hidden; /* Hide horizontal overflow */
        }

        .dispatching-bins {
            display: flex;
            align-items: center;
        }

        .carousel-item.active,
        .carousel-item-next,
        .carousel-item-prev {
            display: block;
            color: white;
        }
    </style>
    <style>
        .animated-list {
            position: relative;
        }

        .item {
            position: absolute;
            opacity: 0;
            animation: fadeInOut 6s infinite;
        }

        .single-bin {
            opacity: 1;
            position: relative;
        }

        @keyframes fadeInOut {
            0% {
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            30% {
                opacity: 1;
            }
            40% {
                opacity: 0;
            }
            100% {
                opacity: 0;
            }
        }
    </style>

    <style>
        /* Basic styles for modal */
        #fullscreen-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        #fullscreen-modal button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container-fluid" id="fullscreen-div" style="background-color: black; min-height: 100vh;">
    <div class="row">
        <button class="btn btn-sm pull-right xyz" id="fullscreen-button"><i class="fa fa-desktop"></i></button>
        <div class="row" style="padding-left: 3%; padding-right: 3%">
            <div class="col-md-6">
                <h2 class="text-center text-white" style="color: white">ORDER DISPATCH ON {{ date('Y-m-d') }}</h2>
            </div>

            <div class="col-md-6">
                <h2 style="color: white">PENDING ORDERS: <span id="dispatchingItemsCount"></span> &nbsp;&nbsp;&nbsp;READY ORDERS: <span id="readyItemsCount"></span></h2>
            </div>

        </div>

        <div class="col-12" style="padding-left: 3%">
            <table id="orderTable" class="table">
                <thead>
                <tr>
                    <th>Time</th>
                    <th>Cash Sale</th>
                    <th>Customer</th>
                    <th>Items Count</th>
                    <th>Dispatched Stores</th>
                    <th>Pending Stores</th>
                    <th>State</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

</div>

<div id="fullscreen-modal">
    <button id="enter-fullscreen-button" class="btn btn-danger">Click to Enter Fullscreen</button>
</div>

<script src="{{ asset('assets/admin/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script>
    var branch = '{{\Illuminate\Support\Facades\Auth::user()->restaurant_id }}';
    var readyOrderIDs = [];



    function reloadTable() {
        $('#orderTable').DataTable().ajax.reload();
    }

    setInterval(reloadTable, 20000);

    function convertToWords(number) {
        // Define an array to hold words for each digit
        const words = ["zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine"];

        // Convert the number to a string to access individual digits
        const numString = number.toString();

        // Iterate over each digit and convert it to its word representation
        const wordArray = numString.split('').map(digit => words[parseInt(digit)]);

        // Join the word representations with a space
        return wordArray.join(', ');
    }

    function play(order) {
        return new Promise((resolve, reject) => {

            const speakButton = document.getElementById('speakButton');
            const words = convertToWords(order.id);
            // const message = 'Order Number, ' + order.sale + ', by, ' + order.name + ', is Ready for Collection';
            const message = 'Order Number, ' + order.sale + ', is Ready for Collection';
            // const message = 'Order Number C,S, ' + order.id + ', by, '+ order.name+', is Ready for Collection';

            const utterance = new SpeechSynthesisUtterance();
            utterance.text = message;
            utterance.rate = 0.7;
            // const voices = window.speechSynthesis.getVoices();
            // console.log(voices)
            // if (selectedVoice) {
            //     utterance.voice = selectedVoice;
            // } else {
            //     console.warn('Selected voice not found, using default voice');
            // }

            utterance.onend = function () {
                resolve(); // Resolve the promise once speech synthesis is complete
            };

            if (window.speechSynthesis) {
                window.speechSynthesis.speak(utterance);
            } else {
                alert('Your browser does not support the Speech Synthesis.');
                reject('Speech Synthesis not supported');
            }
        });
    }


    function requestFullscreen(element) {
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.mozRequestFullScreen) { /* Firefox */
            element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
            element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) { /* IE/Edge */
            element.msRequestFullscreen();
        }
    }

    $(document).ready(function () {

        $(document).on('keydown', function (e) {
            if (e.which === 121) { //F10
                e.preventDefault();
                if (isFullscreen()) {
                    exitFullscreen();
                } else {
                    enterFullscreen(document.documentElement);
                }
            }
        });
        $('#enter-fullscreen-button').on('click', function () {
            const modal = document.getElementById('fullscreen-modal');
            if (isFullscreen()) {
                 exitFullscreen();
            } else {
                modal.style.display = 'none';
                enterFullscreen(document.documentElement);
            }
        });
        $('#fullscreen-button').on('click', function () {
            if (isFullscreen()) {
                 exitFullscreen();
            } else {
                enterFullscreen(document.documentElement);
            }
        });

        // Function to request fullscreen
        function enterFullscreen(element) {
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.mozRequestFullScreen) { /* Firefox */
                element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
                element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) { /* IE/Edge */
                element.msRequestFullscreen();
            }
            $('#fullscreen-button').hide();
        }

        // Function to exit fullscreen
        function exitFullscreen() {
            $('#fullscreen-button').show();
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) { /* Firefox */
                $('#fullscreen-button').show();
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
                $('#fullscreen-button').show();
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { /* IE/Edge */
                $('#fullscreen-button').show();
                document.msExitFullscreen();
            }
        }

        // Function to check if currently in fullscreen
        function isFullscreen() {
            return !!(document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement);
        }

        document.addEventListener('fullscreenchange', function () {
            if (document.fullscreenElement) {
                console.log("Page entered fullscreen mode");
            } else {
                $('#fullscreen-button').show();
                const modal = document.getElementById('fullscreen-modal');
                modal.style.display = 'flex';
            }
        });
    });



    $(document).ready(function () {

        var table = $('#orderTable').DataTable({
            searching: false,
            bLengthChange: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: false,
            info: false,
            paging: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route('pos-cash-sales.customer-view.unguarded') !!}',
                data: function (data) {
                    var from = $('#start_date').val();
                    var to = $('#end_date').val();
                    data.restaurant_id = branch;
                    data.from = from;
                    data.to = to;
                }
            },
            columns: [
                { data: "time", name: "time" },
                { data: "sales_no", name: "sales_no" },
                { data: "customer", name: "customer" },
                { data: "items_count", name: "items_count", searchable: false },
                { data: "bins_count_dispatched", name: "bins_count_dispatched", searchable: false },
                { data: "pending_bin_titles", name: "pending_bin_titles", searchable: false },
                { data: "order_status", name: "order_status", searchable: false, visible: false },
                { data: "state", name: "state", searchable: false },
            ],
            fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {

                if (aData['order_status'] === 'Ready') {
                    $(nRow).addClass('ready');
                }

                // Update and animate the cell content for bins
                var binsCountCell = $(nRow).find('td').eq(4); // Adjust index if necessary
                var pendingBinsCell = $(nRow).find('td').eq(5); // Adjust index if necessary

                // Get the data
                var binsCount = aData['bins_count_dispatched'];
                var pendingBins = aData['pending_bin_titles'];

                // Set the initial state
                binsCountCell.html(binsCount);
                pendingBinsCell.html('');

                if (pendingBins) {
                    // Handle single or multiple items
                    var binsArray = pendingBins.split(',').map(item => item.trim());
                    if (binsArray.length === 1) {
                        // If only one bin, keep it visible without animation
                        pendingBinsCell.html(`<div class="single-bin">${binsArray[0]}</div>`);
                    } else {
                        // Wrap items in spans with animation for multiple bins
                        var formattedBins = binsArray.map((item, index) => `<span class="item" style="animation-delay: ${index * 2}s">${item}</span>`).join('');
                        setTimeout(function () {
                            pendingBinsCell.html('<div class="animated-list">' + formattedBins + '</div>');
                        }, 100); // Adjust timing if necessary
                    }
                } else {
                    pendingBinsCell.html('<div class="no-pending-bins">-</div>');
                }

                return nRow;
            },

        });


        table.on('draw.dt', function () {
            var allData = table.rows().data().toArray();

            // Calculate total items, ready items, and dispatching items
            var totalItems = allData.length;
            var readyItems = allData.filter(order => order.order_status === 'Ready').length;
            var dispatchingItems = allData.filter(order => order.order_status === 'Dispatching').length;

            $('#totalItemsCount').text(totalItems);
            $('#readyItemsCount').text(readyItems);
            $('#dispatchingItemsCount').text(dispatchingItems);

            var message = readyItems + ' Ready Orders ' + dispatchingItems + ' Pending Orders ' + totalItems + ' All Orders '

            $('#itemsCount').text(message);

            const uniqueReadyOrderIds = new Set(readyOrderIDs.map(order => order.id));

            const newReadyOrders = allData
                .filter(order => order.order_status === 'Ready')
                .filter(order => !uniqueReadyOrderIds.has(order.id)) // Check for unique ID
                .filter(order => {
                    // Ensure last_dispatch_time is more than 30 seconds ago
                    const lastDispatchTime = new Date(order.last_dispatch_time);
                    const thirtySecondsAgo = new Date(Date.now() - 5000);
                    return lastDispatchTime <= thirtySecondsAgo;
                })
                .map(order => ({id: order.id, sale: order.sales_no, name: order.customer, sent: 'no'}));

            // Add unique new orders to readyOrderIDs
            newReadyOrders.forEach(order => {
                readyOrderIDs.push(order);
            });
            sendCalloutForReadyOrders(readyOrderIDs);
        });

    });

    async function sendCalloutForReadyOrders(readyOrderIDs) {
        for (const orderId of readyOrderIDs) {
            if (orderId.sent === 'no') {
                orderId.sent = 'yes';
                await play(orderId);

            }
        }
    }


</script>
</body>
</html>
