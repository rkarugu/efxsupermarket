<div>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Banks </h3>
                <button class="btn btn-primary pull-right"  data-toggle="modal" data-target="#exampleModal">Create New Cheque Bank</button>
            </div>
            <div class="box-body">

                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Bank Code</th>
                        <th>Bounce Penalty</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($chequeBanks as $chequeBank)
                        <tr>
                            <td>{{ $chequeBank->bank }}</td>
                            <td>{{ $chequeBank->bank_code }}</td>
                            <td>{{ $chequeBank->bounce_penalty }}</td>
                            <td>
                                <button wire:click="edit({{ $chequeBank->id }})" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal">Edit</button>
                                <button wire:click="delete({{ $chequeBank->id }})"  wire:confirm="Are you sure you want to delete this Record?" class="btn btn-danger btn-sm">Delete</button>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>


    <!-- Modal -->
    <div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        @if($chequeBankId)
                            Edit Cheque Bank
                        @else
                            Create New Cheque Bank
                        @endif
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" wire:model="name">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="bank_code">Bank Code</label>
                            <input type="text" class="form-control" id="bank_code" wire:model="bank_code">
                            @error('bank_code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="bounce_penalty">Bounce Penalty</label>
                            <input type="text" class="form-control" id="bounce_penalty" wire:model="bounce_penalty">
                            @error('bounce_penalty') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" wire:click="closeModal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="store">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>



        document.addEventListener('livewire:init', () => {
            // $('#create_datatable').DataTable().destroy();
            // $('#create_datatable').DataTable();
            // console.log('lalala')
            Livewire.on('close', (event) => {
                $(event).modal('hide');
                $('.close').click();
            });

            Livewire.on('show', (event) => {
                $(event).modal('show');
            });
        });
    </script>


@endpush