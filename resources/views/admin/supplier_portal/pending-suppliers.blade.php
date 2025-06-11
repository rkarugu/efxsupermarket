@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-header-flex">
                    <div class="d-flex flex-column">
                        <h3 class="box-title"> Pending Suppliers </h3>
                        <span class="box-title-tagline"> A list of suppliers who are yet to be invited to the portal. </span>
                    </div>

                    <button class="btn btn-primary"> Invite All </button>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table" id="create_datatable_10">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Supplier</th>
                            <th>Code</th>
                            <th>Service Type</th>
                            <th>Supplier Since</th>
                            <th>Telephone</th>
                            <th>Email Address</th>
                            <th>Physical Address</th>
                            <th> Actions </th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($pendingSuppliers as $index => $supplier)
                          <tr>
                              <th style="width: 3%;" scope="row">{{ $index + 1 }}</th>
                              <td>{{ $supplier->name }}</td>
                              <td>{{ $supplier->supplier_code }}</td>
                              <td>{{ ucwords(str_replace('_', ' ', $supplier->service_type)) }}</td>
                              <td>{{ \Carbon\Carbon::parse($supplier->supplier_since)->toFormattedDayDateString() }}</td>
                              <td>{{ $supplier->telephone }}</td>
                              <td>{{ $supplier->email }}</td>
                              <td>{{ $supplier->address }}</td>
                              <td>
                                  <div class="action-button-div">
                                      <button title="Invite" data-toggle="modal" data-target="#confirm-invite-modal" data-backdrop="static"
                                              data-id="{{ $supplier->id }}" data-name="{{ $supplier->name }}">
                                          <i class="fas fa-envelope text-primary fa-lg"></i>
                                      </button>
                                  </div>
                              </td>
                          </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirm-invite-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Invite Supplier </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <p style="font-size: 16px;"> Are you sure you want to invite <span id="supplier-name"></span> into the supplier portal? </p>
                        <form action="{!! route("supplier-portal.invite-supplier")  !!} " method="post" id="invite-supplier-form">
                            {{ csrf_field() }}

                            <input type="hidden" id="supplier-id" name="supplier_id">
                        </form>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="confirmInviteSupplier();">Yes, Invite</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script type="text/javascript">
        function confirmInviteSupplier() {
            $("#invite-supplier-form").submit();
        }

        $('#confirm-invite-modal').on('show.bs.modal', function (event) {
            let triggeringButton = $(event.relatedTarget);
            let idValue = triggeringButton.data('id');
            let nameValue = triggeringButton.data('name');

            $("#supplier-id").val(idValue);
            $("#supplier-name").text(nameValue);
        })
    </script>


@endsection
