<li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell"></i>
        @if ($notifications->count())
            <span class="label label-warning">{{ $notifications->count() }}</span>
        @endif
    </a>
    <ul class="dropdown-menu">
        <li class="header">You have {{ $notifications->count() }} notifications</li>
        <li>
            <ul class="menu">
                @foreach ($notifications as $notification)
                    <li>
                        <a href="{{ $notification->data['url'].'?notification='.$notification->id }}">
                            <i class="{{ $notification->data['icon_class'] }}"></i> {{ $notification->data['message'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
        <li class="footer"><a href="{{ route('notifications.index') }}">View all</a></li>
    </ul>
</li>
