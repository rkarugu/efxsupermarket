@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Notifications</h3>
                @include('message')
            </div>
            <form action="{{ route('notifications.update') }}" method="POST">
                @csrf
                <div class="box-body no-padding">
                    @if ($notifications->count())
                        <div class="mailbox-controls">
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle" data-clicks="false"><i
                                    class="fa fa-square"></i>
                            </button>
                            <div class="btn-group">
                                <button type="submit" name="action" value="delete" class="btn btn-default btn-sm"
                                    data-toggle="tooltip" title="Delete">
                                    <i class="fa fa-trash"></i></button>
                                <button type="submit" name="action" value="read" class="btn btn-default btn-sm"
                                    data-toggle="tooltip" title="Mark as Read">
                                    <i class="fa fa-eye"></i></button>
                            </div>
                        </div>
                    @endif
                    <div class="table-responsive mailbox-messages">
                        <table class="table table-hover table-striped">
                            <tbody>
                                @forelse ($notifications as $notification)
                                    <tr>
                                        <td style="width: 15px"><input type="checkbox" name="notifications[]"
                                                value="{{ $notification->id }}"></td>
                                        <td class="mailbox-subject">
                                            @if (!$notification->read_at)
                                                <a href="{{ $notification->data['url'] . '?notification=' . $notification->id }}"
                                                    style="color:inherit">
                                                    <i class="{{ $notification->data['icon_class'] }}"></i>
                                                    <b>{{ $notification->data['message'] }}</b>
                                                </a>
                                            @else
                                                <a
                                                    href="{{ $notification->data['url'] . '?notification=' . $notification->id }}">
                                                    <i class="{{ $notification->data['icon_class'] }}"></i>
                                                    {{ $notification->data['message'] }}
                                                </a>
                                            @endif
                                        </td>
                                        <td class="mailbox-date" style="width: 200px">
                                            {{ $notification->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">You have 0 Notifications</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
            {{ $notifications->links() }}
        </div>
    </div>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/iCheck/flat/blue.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/plugins/iCheck/icheck.js') }}"></script>
    <script>
        $(function() {
            $('input[type="checkbox"]').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });

            $(".checkbox-toggle").click(function() {
                var clicks = $(this).data('clicks');
                if (clicks) {
                    $("input[type='checkbox']").iCheck("uncheck");
                    $(".fa", this).removeClass("fa-check-square").addClass('fa-square');
                } else {
                    $("input[type='checkbox']").iCheck("check");
                    $(".fa", this).removeClass("fa-square").addClass('fa-check-square');
                }

                $(this).data("clicks", !clicks);
            });
        })
    </script>
@endpush
