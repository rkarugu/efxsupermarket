@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Reconciliation for {{ request()->customer_code }}</h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route('maintain-customers.recon.upload', request()->customer_code) }}" method="post" enctype='multipart/form-data'>
                    {{ @csrf_field() }}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="upload_file">Select upload file</label>
                                <input type="file" class="form-control" name="upload_file" id="upload_file" required>
                                <label class="custom-file-label" for="customFile"></label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="display: block;">&nbsp;</label>
                                <input type="submit" class="btn btn-primary" name="intent" value="UPLOAD">
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                @if($uploadedData)
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Uploaded Data</h3>
                        </div>

                        <div class="box-body">
                            <form action="{{ route('maintain-customers.recon.confirm', request()->customer_code) }}" method="post">
                                {{ @csrf_field() }}

                                <input type="hidden" value="{{ json_encode($uploadedData) }}" name="records">

                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="create_datatable_10">
                                        <thead>
                                        <tr>
                                            <th style="width: 3%;">#</th>
                                            <th>DATE</th>
                                            <th>DOC NO</th>
                                            <th>REFERENCE</th>
                                            <th>CHANNEL</th>
                                            <th>USER</th>
                                            <th>TOTAL</th>
                                            <th>FLAGGED</th>
                                            <th>REASON</th>
                                            <th>IS BATCH</th>
                                            <th>COUNT</th>
                                            <th>ROUTE</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($uploadedData as $index => $row)
                                            @if($index != 0)
                                                <tr>
{{--                                                    <input type="hidden" name="date[]" value="{{ $row[0] }}">--}}
{{--                                                    <input type="hidden" name="document_no[]" value="{{ $row[1] }}">--}}
{{--                                                    <input type="hidden" name="reference[]" value="{{ $row[2] }}">--}}
{{--                                                    <input type="hidden" name="channel[]" value="{{ $row[3] }}">--}}
{{--                                                    <input type="hidden" name="user[]" value="{{ $row[4] }}">--}}
{{--                                                    <input type="hidden" name="total[]" value="{{ $row[5] }}">--}}
{{--                                                    <input type="hidden" name="flagged[]" value="{{ $row[6] }}">--}}
{{--                                                    <input type="hidden" name="reason[]" value="{{ $row[7] }}">--}}
{{--                                                    <input type="hidden" name="batch[]" value="{{ $row[8] }}">--}}
{{--                                                    <input type="hidden" name="count[]" value="{{ $row[9] }}">--}}

                                                    <th style="width: 3%" scope="row">{{ $index }}</th>
                                                    <td>{{ $row[0] }}</td>
                                                    <td>{{ $row[1] }}</td>
                                                    <td>{{ $row[2] }}</td>
                                                    <td>{{ $row[3] }}</td>
                                                    <td>{{ $row[4] }}</td>
                                                    <td>{{ $row[5] }}</td>
                                                    <td>{{ $row[6] == 1 ? 'Yes' : 'No' }}</td>
                                                    <td>{{ $row[7] }}</td>
                                                    <td>{{ $row[8] == 1 ? 'Yes' : 'No' }}</td>
                                                    <td>{{ $row[9] }}</td>
                                                    <td>{{ $row[10] }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>

                                    <div class="d-flex justify-content-end">
                                        <input type="submit" value="Confirm Upload" class="btn btn-primary">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script>
        $('#upload_file').on('change',function(){
            let fileName = $(this).val();
            $(this).next('.custom-file-label').text(fileName);
        })
    </script>
@endsection