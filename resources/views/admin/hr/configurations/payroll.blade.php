@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="admin-app">
        <payroll-configuration
            user-role="{{ $user->role_id }}" 
            user-permissions="{{ json_encode($user->permissions) }}"
        />
    </section>
@endsection

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('uniquepagescript')
    @vite('resources/js/admin-app.js')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
@endsection
