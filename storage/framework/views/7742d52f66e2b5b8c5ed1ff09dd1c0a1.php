<?php $__env->startSection('content'); ?>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <?php if(!env("USE_OTP")): ?>
                    <?php if(isset($permission[$pmodule.'___invoices-create']) || $permission == 'superadmin'): ?>
                        <?php if(isset($permission[$pmodule.'___invoices-create']) || $permission == 'superadmin'): ?>
                            <div align="right"><a href="<?php echo route($model.'.create'); ?>" class="btn btn-success">Add New Sales Invoice</a></div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if(isset($permission[$pmodule.'___invoices-create']) || $permission == 'superadmin'): ?>
                        <div align="right">
                            <button id="addNewInvoiceBtn" class="btn btn-success">Add New Sales Invoice</button>
                        </div>

                        <!-- OTP Modal -->
                        <div id="otpModal" class="modal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">OTP Verification</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Please enter the OTP sent to the admin:</p>
                                        <input type="text" id="otpInput" class="form-control" placeholder="Enter OTP">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="verifyOtpBtn">Verify OTP</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>




                    <br>
                <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="col-md-12 no-padding-h">
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">From</label>
                                    <input type="date" name="from" id="start-date" class="form-control" value="<?php echo e(request()->input('from') ?? date('Y-m-d')); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">To</label>
                                    <input type="date" name="to" id="end-date" class="form-control"  value="<?php echo e(request()->input('to') ?? date('Y-m-d')); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <button type="submit" id="filter" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th width="5%">S.No.</th>

                            <th width="10%">Invoice No</th>
                            <th width="10%">Invoice Date</th>
                            
                            <th width="15%">Route</th>
                            <th width="15%">Salesman Name</th>
                            <th width="15%">Customer</th>
                            <th width="10%">Status</th>


                            <th width="15%" class="noneedtoshort">Action</th>

                            <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                        </tr>
                        </thead>
                        <tbody>
                        <?php if(isset($lists) && !empty($lists)): ?>

                            <?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <tr>
                                    <td><?php echo e($loop ->iteration); ?></td>

                                    <td><?php echo $list->requisition_no; ?></td>
                                    <td><?php echo $list->requisition_date; ?></td>
                                    <td><?php echo $list->route; ?></td>
                                    <td>
                                        <?php if($list->getrelatedEmployee): ?>
                                            <?php echo $list->getrelatedEmployee->name; ?>

                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($list->name); ?>

                                    </td>
                                    <td><?php echo $list->status; ?></td>
                                    <td class="action_crud">

                                        <?php if($list->status == 'UNAPPROVED'): ?>
                                            <?php if(isset($permission['sales-invoice___edit-invoice']) || $permission == 'superadmin'): ?>
                                                <span>
                                                    <a title="Edit" class="btn btn-primary btn-sm" href="<?php echo e(route($model.'.edit', $list->slug)); ?>"><i class="fa fa-pencil" aria-hidden="true"></i>
                                                    </a>
                                                    </span>
                                            <?php endif; ?>
                                            <span>
                                                    <form title="Trash" action="<?php echo e(URL::route($model.'.destroy', $list->slug)); ?>" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                                    <button class="btn btn-danger btn-sm" style="float:left"><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    </form>
                                                    </span>

                                        <?php else: ?>
                                            <span>
                                                    <a title="View" class="btn btn-warning btn-sm" href="<?php echo e(route($model.'.show', $list->slug)); ?>"><i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    </span>
                                        <?php endif; ?>

                                        <?php if($list->status == 'APPROVED'): ?>

                                            <?php if( (!isset($user_permission['sales-invoice___confirm-invoice-r']) &&  isset($permission['sales-invoice___confirm-invoice'])) || $permission == 'superadmin'): ?>
                                                <span>
                                                          <a title="Confirm Invoice" class="btn btn-primary btn-sm" href="<?php echo e(route('confirm-invoice.show', $list->slug)); ?>"><i
                                                                      class="fa fa-check-circle"></i></i>
                                                          </a>
                                                        </span>
                                            <?php endif; ?>

                                        <?php endif; ?>

                                        <?php if($list -> status == 'COMPLETED'): ?>
                                                <?php if(isset($permission['print-invoice-delivery-note___pdf']) || $permission == 'superadmin'): ?>
                                                    <?php if(!$list->esd_details): ?>
                                                        <button title="Not Signed successfully."
                                                                class="not-signed btn btn-sm btn-biz-purplish">
                                                            <i aria-hidden="true" class="fa fa-file-pdf"></i>
                                                        </button>
                                                    <?php else: ?>

                                                        <a title="Export To Pdf" class="btn btn-sm btn-biz-purplish"
                                                           href="<?php echo e(route($model . '.exportToPdf', $list->requisition_no)); ?>">
                                                            <i aria-hidden="true" class="fa fa-file-pdf"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <?php if(isset($permission['print-invoice-delivery-note___print']) || $permission == 'superadmin'): ?>
                                                    <a title="Print" class="btn btn-sm btn-biz-greenish"
                                                       href="javascript:void(0)"
                                                       onClick="printgrn('<?php echo $list->requisition_no; ?>')">
                                                        <i aria-hidden="true" class="fa fa-print"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if(isset($permission['print-invoice-delivery-note___return']) || $permission == 'superadmin'): ?>
                                                    <a title="Return" class="btn btn-sm btn-primary"
                                                       href="<?php echo e(route('transfers.return_show', $list->requisition_no)); ?>"
                                                       target="_blank">
                                                        <i class="fa fa-refresh" aria-hidden="true"></i>
                                                    </a>
                                                <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        function printMe(url, data, type) {
            let isConfirm = confirm('Do you want to print this Invoice?');
            if (isConfirm) {
                jQuery.ajax({
                    url: url,
                    async: false, //NOTE THIS
                    type: type,
                    data: data,
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var divContents = response;
                        var printWindow = window.open('', '', 'width=600');
                        printWindow.document.write(divContents);
                        printWindow.document.close();
                        printWindow.print();
                        printWindow.close();
                    }
                });
            }
        }

        function printgrn(transfer_no) {
            printMe('<?php echo e(route('transfers.print')); ?>', {
                transfer_no: transfer_no
            }, 'POST');
        }

        function print_invoice(input) {
            var postData = $(input).parents('form').serialize() + '&request=PRINT';
            var url = $(input).parents('form').attr('action');
            // postData.append('request','PRINT');
            printMe(url, postData, 'GET');
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#addNewInvoiceBtn').click(function() {
                $.post("<?php echo e(route('credit.sales.otp')); ?>", {
                    _token: '<?php echo e(csrf_token()); ?>'
                }, function(response) {
                    if (response.message) {
                        $('#otpModal').modal('show');
                    }
                });
            });

            $('#verifyOtpBtn').click(function() {
                const otp = $('#otpInput').val();

                $.post("<?php echo e(route('credit.sales.verify.otp')); ?>", {
                    _token: '<?php echo e(csrf_token()); ?>',
                    otp: otp
                }, function(response) {
                    if (response.success) {
                        window.location.href = "<?php echo e(route($model.'.create')); ?>";
                    } else {
                        alert(response.message);
                    }
                });
            });
        });

    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/internalrequisition/index.blade.php ENDPATH**/ ?>