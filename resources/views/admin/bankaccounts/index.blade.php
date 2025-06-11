
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                            <div align = "right"> <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add {!! $title !!}</a></div>
                             @endif
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="5%">S.No.</th>

                                          <th width="8%"  > GL Code</th>
                                           <th width="7%"  > Currency</th>
                                       
                                        <th width="15%"  >Name</th>
                                          <th width="8%"  >Code</th>
                                           <th width="15%"  > Number</th>
                                           <th width="15%"  > Balance</th>

                                          <th  width="16%" class="noneedtoshort" >Action</th>
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php   $total_account_balance = []; ?>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;
                                         $account_codes =  getChartsOfaccountarrayWithID();
                                         //echo "<pre>"; print_r($lists); die;

                                        ?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>

                                                  <td>{!! isset($account_codes[$list->bank_account_gl_code_id]) ? $account_codes[$list->bank_account_gl_code_id] : '' !!}</td>

                                                   <td>{!! $list->currency !!}</td>
                                                

                                                <td>{!! $list->account_name !!}</td>
                                                 <td>{!! $list->account_code !!}</td>
                                                  <td>{!! $list->account_number !!}</td>
                                                  <?php 
                                                 $amount_balance =  getBankAccountBalanceByCode(isset($account_codes[$list->bank_account_gl_code_id]) ? $account_codes[$list->bank_account_gl_code_id] : '');
                                                 $total_account_balance[] = $amount_balance;
                                                  ?>
                                                   <td>{!! manageAmountFormat($amount_balance) !!}</td>
                                                 

                                                      
                                                  
                                                
                                                <td class = "action_crud">
                                               
                                                 @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')

                                                   <span>
                                                    <a title="Debtor Trans" href="{{ route($model.'.account-inquiry', ['slug'=>$list->slug,'from'=>date('Y-m-d'),'to'=>date('Y-m-d')]) }}" ><i class="fa fa-list"></i>
                                                    </a>
                                                    </span>
                                                    
                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

                                                    <span>
                                                        <a title="Edit" href="{{ route('bank-accounts.assignUsers',$list->id) }}" >
                                                            <i class="fa fa-lock"></i>
                                                        </a>
                                                        </span>

                                                    @endif

                                                    

                                                   @if(1==2 && (isset($permission[$pmodule.'___delete']) || $permission == 'superadmin'))

                                                    <span>
                                                    <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button  style="float:left"><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    </form>
                                                    </span>
                                                     @endif

                                                       


                                                    





                                                </td>
                                                
											
                                            </tr>
                                           <?php $b++; ?>
                                        @endforeach
                                    @endif


                                    </tbody>
                                    <tfoot>
                                    
                                    <tr>
                                    <td></td>
                                     <td></td>
                                      <td></td>
                                       <td></td>
                                        <td></td>
                                      <td style="font-weight: bold;">Total</td>
                                        <td style="font-weight: bold;">{{ manageAmountFormat(array_sum($total_account_balance)) }}</td>
                                         <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection
