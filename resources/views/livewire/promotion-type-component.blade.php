<div>

    <div>
        <!-- Add Button -->
        <div class="text-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addServiceModal">Add Service</button>
        </div>

        <!-- Search Field -->
        <div class="form-group">
            <input type="text" class="form-control" id="searchInput" placeholder="Search...">
        </div>

        <!-- Table with Pagination -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody id="serviceTableBody">
                <tr>
                    @foreach($items as $item)
                        <td>{{ $item -> name }}</td>
                        <td>{{ $item -> description }}</td>
                    @endforeach
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <ul class="pagination">
            <!-- Pagination links will be dynamically populated -->
        </ul>

        <div wire:ignore.self id="addServiceModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content -->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add Service</h4>
                    </div>
                    <div class="modal-body">
                        <form wire:submit="save" id="addServiceForm">
                            <div class="col-lg-12">
                                <div>
                                    <label for="name" class="form-label">Name</label>
                                    <input wire:model.defer="name" type="text" id="name" class="form-control" placeholder="" required />
                                    @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div>
                                    <label for="name" class="form-label">Description</label>
                                    <textarea wire:model.defer="description"  class="form-control"></textarea>
                                    @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div wire:ignore.self id="deleteModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content -->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add Service</h4>
                    </div>
                    <div class="modal-body">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                        <div class="mt-4 text-center">
                            <h4 class="fs-semibold">You are about to do a delete Action !</h4>
                            <p class="text-muted fs-14 mb-4 pt-1">Deleting an item is irreversible.</p>
                            <div class="hstack gap-2 justify-content-center remove">
                                <button class="btn btn-link link-success fw-medium text-decoration-none" data-bs-dismiss="modal">
                                    <i class="ri-close-line me-1 align-middle"></i> Close
                                </button>
                                <button wire:click="delete()" class="btn btn-danger" id="delete-record">Yes, Delete It!!</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
