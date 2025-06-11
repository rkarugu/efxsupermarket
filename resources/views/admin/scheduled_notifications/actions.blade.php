@if (can('edit', 'scheduled-notifications'))
    <a href="#" data-toggle="modal" data-target="#editNotificationScheduleModal"
        data-action="{{ route('scheduled-notifications.update', $notification) }}"
        data-model="{{ $notification }}">
        <i class="fa fa-edit"></i>
    </a>
@endif
@if (can('delete', 'scheduled-notifications'))
    <form action="{{ route('scheduled-notifications.destroy', $notification) }}" method="post"
        style="display: inline-block">
        @csrf
        @method('delete')
        <button type="submit" style="background: none; border:none">
            <i class="fa fa-trash"></i>
        </button>
    </form>
@endif
