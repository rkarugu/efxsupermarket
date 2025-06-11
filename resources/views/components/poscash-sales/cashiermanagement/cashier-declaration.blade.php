@push('styles')
{{--    <style>--}}
{{--        th, td {--}}
{{--            text-align: right;--}}
{{--        }--}}
{{--    </style>--}}
@endpush
<div>
    <div class="box-header with-border">
        <h3 class="box-title">Cashier Declaration</h3>
    </div>
    <div class="box-body">
        <table class="table table-striped mt-3 mb-3">
            <thead>
            <tr>
                <th>Cashier</th>
                <th>CS</th>
                <th>CSR</th>
                <th>INV</th>
                <th>SALES</th>
                <th>PETTY</th>
                @foreach($payMethods as $payMethod)
                    <th>{{ $payMethod->title }}</th>
                @endforeach
                <th>TOTAL DROPPED</th>
                <th>CASH IN HAND</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ $cashier->name }}</td>
                <td>{{ number_format($total_sales, 2) }}</td>
                <td>{{ number_format($returns, 2) }}</td>
                <td>{{ number_format($inv, 2) }}</td>
                <td>{{ number_format($sales, 2) }}</td>
                <td>{{ number_format($petty_cash, 2) }}</td>
                @foreach($payMethods as $payMethod)
                    <td>
                        {{ number_format($payments[$payMethod->id] ?? 0, 2) }}
                    </td>
                @endforeach
                <td>{{ number_format($total_drops, 2) }}</td>
                <td>{{ number_format($net_amount, 2) }}</td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="{{8 + count($payMethods) }}">Done By: {{ Auth::user()->name }}</td>
            </tr>
            </tfoot>
        </table>

    </div>
</div>

@push('styles')
    <style>
        .align_float_right {
            float: right;
        }
        .mt-4 {
            margin-top: 1.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/form.js') }}"></script>
    <script>
        var form = new Form();
        $('#declare').click(function(e){
            e.preventDefault();
            $('#loader-on').show();
            $.ajax({
                url: '{{ route('cashier-management.cashier-declare', $cashier->id) }}',
                // data:postData,
                contentType: false,
                cache: false,
                processData: false,
                method:'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(out){
                    $('#loader-on').hide();
                    console.log(out)
                    if(out.result == 0) {
                        form.errorMessage(out.message);
                    }
                    if(out.result === 1) {
                        form.successMessage(out.message);
                        location.reload();
                    }
                    if(out.result === -1) {
                        form.errorMessage(out.message);
                    }
                },
                error:function(err)
                {
                    $('#loader-on').hide();
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');
                }
            });
        });
    </script>
@endpush