<div class="modal fade" role="dialog" id="createNotificationScheduleModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Notification Schedule</h4>
            </div>
            <form action="{{ route('scheduled-notifications.store') }}" method="POST"
                id="createNotificationScheduleForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="notification">Notification</label>
                        <select name="notification" id="notification" class="form-control">
                            <option value="">Select Option</option>
                            @foreach ($notifications as $notification)
                                <option value="{{ $notification->class_name }}">{{ $notification->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="frequency">Frequency</label>
                        <select name="frequency" id="frequency" class="form-control">
                            <option value="">Select Option</option>
                            @foreach ($frequencies as $key => $frequency)
                                <option value="{{ $key }}">{{ $frequency }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="time">Time (Optional)</label>
                        <input type="time" class="form-control" name="time" id="time">
                    </div>
                    <em>*Set at least one type of recipient</em>
                    <div class="form-group">
                        <label for="roles">Roles</label>
                        <select class="form-control multiselect" name="roles[]" id="roles" multiple>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="roles">Users</label>
                        <select class="form-control multiselect" name="users[]" id="users" multiple>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="emails">Emails</label>
                        <input type="text" class="form-control" name="emails" id="emails" data-role="tagsinput">
                    </div>
                    <div class="form-group">
                        <label for="phone_numbers">Phone Numbers</label>
                        <input type="text" class="form-control" name="phone_numbers" id="phone_numbers"
                            data-role="tagsinput">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        &times;
                        Close</button>
                    <button type="button" class="btn btn-primary" id="createBtn">
                        <i class="fa fa-save"></i>
                        Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#createBtn").click(function() {
                $("#createNotificationScheduleForm").submit()
            })
        })
    </script>
@endpush
