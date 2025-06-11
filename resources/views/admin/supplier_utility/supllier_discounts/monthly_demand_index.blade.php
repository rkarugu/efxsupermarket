@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-header-flex">
                    <div class="d-flex flex-column">
                        <h3 class="box-title"> Supplier Monthly Demand </h3>
                    </div>
                    
                    <a href="{{route('suppliers-utilities.supplier-montly-demand.generate')}}" class="btn btn-primary"> Generate </a>
                </div>
                
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table" id="create_datatable_10">
                        <thead>
                       
                        </thead>

                        <tbody>
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('uniquepagescript')
    
@endsection
