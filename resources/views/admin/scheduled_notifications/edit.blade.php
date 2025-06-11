<div class="modal fade" role="dialog" id="editNotificationScheduleModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Notification Schedule</h4>
            </div>
            <form method="POST" id="editNotificationScheduleForm">
                @csrf()
                @method('put')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="notification">Notification</label>
                        <select name="notification" id="editNotification" class="form-control">
                            <option value="">Select Option</option>
                            @foreach ($notifications as $notification)
                                <option value="{{ $notification->class_name }}">{{ $notification->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="frequency">Frequency</label>
                        <select name="frequency" id="editFrequency" class="form-control">
                            <option value="">Select Option</option>
                            @foreach ($frequencies as $key => $frequency)
                                <option value="{{ $key }}">{{ $frequency }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="time">Time (Optional)</label>
                        <input type="time" class="form-control" name="time" id="editTime">
                    </div>
                    <em>*Set at least one type of recipient</em>
                    <div class="form-group">
                        <label for="roles">Roles</label>
                        <select class="form-control multiselect" name="roles[]" id="editRoles" multiple>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="roles">Users</label>
                        <select class="form-control multiselect" name="users[]" id="editUsers" multiple>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="emails">Emails</label>
                        <input type="text" class="form-control" name="emails" id="editEmails" data-role="tagsinput">
                    </div>
                    <div class="form-group">
                        <label for="phone_numbers">Phone Numbers</label>
                        <input type="text" class="form-control" name="phone_numbers" id="editPhoneNumbers"
                            data-role="tagsinput">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        &times;
                        Close</button>
                    <button type="button" class="btn btn-primary" id="editBtn">
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
            $("#editNotificationScheduleModal").on("show.bs.modal", function(event) {
                let target = $(event.relatedTarget);
                let url = target.data('url');
                let model = target.data('model');
                let action = target.data('action');

                $("#editNotificationScheduleForm").prop('action', action);

                $("#editNotification").val(model.class_name).trigger('change');
                $("#editFrequency").val(model.frequency).trigger('change');
                $("#editTime").val(model.time).trigger('change');
                $("#editRoles").val(model.roles).trigger('change');
                $("#editUsers").val(model.users).trigger('change');

                $("#editEmails").tagsinput('removeAll');
                if (Array.isArray(model.emails)) {
                    model.emails.forEach(element => {
                        $("#editEmails").tagsinput('add', element);
                    });
                } else {
                    $("#editEmails").tagsinput('add', model.emails)
                }

                $("#editPhoneNumbers").tagsinput('removeAll');
                if (Array.isArray(model.phone_numbers)) {
                    model.phone_numbers.forEach(element => {
                        $("#editPhoneNumbers").tagsinput('add', element);
                    });
                } else {
                    $("#editPhoneNumbers").tagsinput('add', model.phone_numbers);
                }

            });

            $("#editBtn").click(function() {
                $("#editNotificationScheduleForm").submit();
            })
        })
    </script>
@endpush
