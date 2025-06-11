<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-3">{{ $shop->name }}</h2>
        <h4 class="text-center mb-3">Sales Rep TEL: {{ $user->phone_number }}</h4>
        <h4 class="text-center mb-3">{{ $allItems->created_at }}</h4>


        <div class="mt-5">

            <h4 class="text-center mb-3">-- FISCAL RECEIPT --</h4>
            <h4 class="text-center mb-3">{{ $allItems->requisition_no }}</h4>

        </div>

        <table class="table table-bordered">
            <thead>
                <tr class="">
                    <th scope="col">Product</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Pack Size</th>
                    <th scope="col">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($allItems->getRelatedItem ?? '' as $data)
                    <tr>
                        <th scope="row">
                            {{ $data->item_name }}
                            {{ $data->item_name }}
                        </th>
                        <td>{{ $data->quantity }}</td>
                        <td>{{ $data->pack_size }}</td>
                        <td>{{ $data->total_cost }}</td>
                    </tr>
                @endforeach

                <tr>
                    <th scope="row">
                        Totals
                    </th>
                    <td></td>
                    <td></td>
                    <td>{{ $allItems->totalOrderAmount }}</td>
                </tr>
            </tbody>
        </table>

        <div class="" style="grid-template-columns: repeat(4, 1fr);">
            <div class="">Code</div>
            <div class="">Rate {{ $allItems->vat_rate }}</div>
            <div class="">Vatable AMT</div>
            <div class="">VAT {{ $allItems->vatSum }}</div>
        </div>

        <div class="row mt-5">
            <div class="col-9">You were served by</div>
            <div class="col-3" style="margin-left: 150px">{{ $user->name }}</div>
        </div>

        <div class="mt-5">
            <p class="text-center">thank you for shopping with us. provided by retailpay</p>

        </div>

        <div class="mt-5">
            <p class="text-center">Control Unit Info</p>

        </div>

        <div class="row mt-4">
            <div class="col-4">Invoice Number</div>
            <div class="col-8" style="margin-left: 150px">{{ $allItems->requisition_no }}</div>
        </div>

        <div class="row">
            <div class="col-4">Serial NO</div>
            <div class="col-8" style="margin-left: 100px">{{ $allItems->requisition_no }}</div>
        </div>

        <div class="row">
            <div class="col-4">Date Time</div>
            <div class="col-8" style="margin-left: 100px">{{ $allItems->created_at }}</div>
        </div>
    </div>
    <script src="{{ asset('js/app.js') }}" type="text/js"></script>
</body>

</html>
