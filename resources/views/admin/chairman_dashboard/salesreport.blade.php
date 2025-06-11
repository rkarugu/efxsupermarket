@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="admin-app">
        <chairman-general-dashboard-sales-report
        branches="{{ $branches }}" 
        branch-id="{{ $branchId }}"
    />

    </section>
@endsection

@section('uniquepagescript')
    @vite('resources/js/admin-app.js')
@endsection

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $('body').addClass('sidebar-collapse');
        });
    </script>
@endpush