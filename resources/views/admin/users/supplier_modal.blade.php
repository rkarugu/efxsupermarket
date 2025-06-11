<div class="modal fade" id="assign-supplier" tabindex="-1" role="dialog" aria-labelledby="assignSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{route('admin.users.assign_user_suppliers')}}" method="POST" class="addExpense">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="assignSupplierModalLabel">Employee: <span id="user_name"></span></h4>
                </div>
                <div class="modal-body" style="overflow:auto; height:450px">
                    <div class="row">
                        <div class="col-sm-6">
                            <ul class="list-group" id="list-suppliers">
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Suppliers to be assigned</label>
                                <select class="form-control select_supplier mlselec6t" placeholder="Input field">
                                    <option value="" selected disabled>Select Supplier</option>
                                    <option value="Select All">Select All</option>
                                    @foreach(getSuppliers() as $key => $supplier)
                                        <option value="{{$key}}">{{$supplier}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-default" onclick="unassginSuppliers()">Remove All Assigned Suppliers</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary assignBtn">Assign Suppliers</button>
                </div>
            </form>
        </div>
    </div>
</div> 