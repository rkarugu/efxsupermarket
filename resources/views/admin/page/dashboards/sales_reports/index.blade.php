@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="admin-app">
        <chairman-dashboard 
            branches="{{ $branches }}" 
            branch-id="{{ $branchId }}" 
            {{-- monthly-sales="{{ $monthlySales }}"
            debtor-balances="{{ $debtorBalances }}" --}}
            page-route="{{ $pageRoute }}"
            detailed-branch-performance-route="{{ $detailedBranchPerformanceRoute }}"
            detailed-route-sales-performance-route="{{ $detailedRouteSalesPerformanceRoute }}"
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
@endpush