
@extends('layouts.admin.admin')

@section('content')

<style type="text/css">
    .panel-heading .accordion-toggle:after {
        /* symbol for "opening" panels */
        font-family: 'Glyphicons Halflings';  /* essential for enabling glyphicon */
        content: "\e114";    /* adjust as needed, taken from bootstrap.css */
        float: left;        /* adjust as needed */
        color: grey;         /* adjust as needed */
        position: relative;
    }
    .panel-heading .accordion-toggle.collapsed:after {
        /* symbol for "collapsed" panels */
        content: "\e080";    /* adjust as needed, taken from bootstrap.css */
       
    }

    .fixed_icon{
        position: absolute;
        right: 21px;
        top: 14px;
    }
    .fixed_panel_headding{
         position: relative;
    }
</style>

<section class="content">
    @include('message')
    <div class="box box-primary">
        <div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> {!! $title !!} </h3>
        <div>&nbsp;</div>
        <div class="card tabbable">
                    <form class="validate form-horizontal"   role="form" method="POST" action="{{ route($model.'.store_items') }}" enctype = "multipart/form-data">
                            {{ csrf_field() }}
                      <div class="col-md-4 " style="padding:20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); ">
                            <ul class="nav nav-pills nav-stacked">
                                <li class="active "><a href="#details" data-bs-target="#v-pills-home" data-toggle="tab"> Inspection Items</a></li>
                                <li><a href="{{route($model.'.edit.vehicle_schedule',base64_encode($form_id))}}">Vehicle & Schedule</a></li>
                                <li><a href="#Lifecycle" data-toggle="tab">Workflow</a></li>
                                
                            </ul>
                             <!-- <div class="tab-content col-md-9">
                                <div class="tab-pane active" id="a">Lorem ipsum dolor sit amet, charetra varius rci quis tortor imperdiet venenatis quam sit amet vulputate. Quisque mauris augue, molestie tincidunt condimentum vitae, gravida a libero.</div>
                                <div class="tab-pane" id="b">Secondo sed ac orci quis tortor imperdiet venenatis. Duis elementum auctor accumsan. Aliquam in felis sit amet augue.</div>
                                <div class="tab-pane" id="c">Thirdamuno, ipsum dolor sit amet, consectetur adipiscing elit. Duis elementum auctor accumsan. Duis pharetra
                                varius quam sit amet vulputate. Quisque mauris augue, molestie tincidunt condimentum vitae. </div>
                            </div>  -->
                      </div>
                      <div class="col-md-8 tab-content">
                            
                            
                        <div class="dropdown ">
                            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-plus"></i> &nbsp; Add Inspection Form
                            <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                @if($item_types->count()>0)
                                    @foreach($item_types as $type)
                                        <li data-type="{{$type->slug}}" class="click_item" ><a href="javascript:void(0)"><i class="fa fa-{{$type->icon}}"  aria-hidden="true"></i> {{$type->name}}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        
                        
                            
                            
                        <div class="item_div">
                            @foreach($items as $item)

                                <div class="panel panel-default item_box" style="margin-top:20px;">
                                    <div class="panel-heading fixed_panel_headding">
                                        <h4 class="panel-title">
                                            &nbsp;
                                            
                                                
                                            <input type="text" value="{{ $item->title }}" class=" m-bot15" name="title[{{$item->id}}][]" required="true">
                                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#{{$item->id}}">    
                                                <label for="exampleInputPassword1">Label <span style="color:#ccc;"> ({{@$item->item_type->name}})</span></label>
                                            </a>
                                        </h4>
                                        <i class="fa fa-trash fixed_icon text-right delete_item" ></i>

                                    </div>
                                    <div id="{{$item->id}}" class="panel-collapse collapse">
                                      <div class="panel-body form-div" style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">
                                            

                                            <div class="form-group">
                                                <input type="hidden" class="form-control  m-bot15" name="inspection_from_type_id[{{ $item->id }}][]" value="{{$item->inspection_from_type_id}}" required="true">
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Short Description</label>
                                                <input type="text" class="form-control category_list m-bot15" value="{{ $item->short_description }}" name="short_description[{{ $item->id }}][]">
                                            </div>

                                            @if(!in_array($item->inspection_from_type_id,[2,3]))
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Instructions</label>
                                                <textarea class="form-control" rows="4" id="exampleInputPassword1"  name="instructions[{{$item->id}}][]" placeholder="Description">{{ $item->instructions }}</textarea>
                                            </div>
                                            @endif
                                            @if(in_array($item->inspection_from_type_id,[1]))
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Pass Label</label>
                                                <input type="text" class="form-control category_list m-bot15" value="{{$item->pass_label}}" name="pass_label[{{$item->id}}][]">
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Fail Label</label>
                                                <input type="text" class="form-control category_list m-bot15" value="{{$item->fail_label}}" name="fail_label[{{$item->id}}][]">
                                            </div>

                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Enable for submission</label>
                                                <input type="checkbox" class="m-bot15" <?php if($item->enable_for_submission == 1){echo "checked";}?> value="1" name="enable_for_submission[{{$item->id}}][]" >
                                                
                                            </div>
                                            @endif

                                            @if(in_array($item->inspection_from_type_id,[1,5,7]))                                
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Require photo or comment for "Pass"</label>
                                                    <input type="checkbox" class="m-bot15" value="1" name="require_photo_or_comment_for_pass[{{$item->id}}][]" {{($item->require_photo_or_comment_for_pass == 1)?'checked':''}}>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Require photo or comment for "Fail"</label>
                                                    <input type="checkbox" class="m-bot15" value="1"  name="require_photo_or_comment_for_fail[{{$item->id}}][]" {{($item->require_photo_or_comment_for_fail == 1)?'checked':''}}>
                                                </div>
                                            @endif

                                            @if(in_array($item->inspection_from_type_id,[2]))
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Require Secondary Meter?</label>
                                                <input type="checkbox" class="m-bot15" value="1" {{($item->require_secondary_meter == 1)?'checked':''}} name="require_secondary_meter[{{$item->id}}][]" >
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Require Photo Verification</label>
                                                <input type="checkbox" class="m-bot15" {{($item->require_photo_verification == 1)?'checked':''}} value="1" name="require_photo_verification[{{$item->id}}][]">
                                            </div>
                                            @endif

                                            @if(in_array($item->inspection_from_type_id,[7]))
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Passing Range</label>
                                                    <input type="number" value="{{ $item->passing_range_from }}" class="form-control category_list m-bot15" name="passing_range_from[{{$item->id}}][]">
                                                    To
                                                    <input type="number" value="{{ $item->passing_range_to }}" class="form-control category_list m-bot15" name="passing_range_to[{{$item->id}}][]" >
                                                </div>
                                            @endif

                                            @if(in_array($item->inspection_from_type_id,[8]))
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Date</label>
                                                    <input type="date" value="{{$item->date}}" class="form-control category_list m-bot15" name="date[{{$item->id}}][]" >
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Date and time</label>
                                                    <input type="date" value="{{$item->date_time}}" class="form-control category_list m-bot15" name="date_time[{{$item->id}}][]" >
                                                </div>
                                            @endif
                                      </div>
                                  </div>
                                </div>

                                <!-- <div class="col-sm-12 item_box form-div " style="padding:50px; margin-top:20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">
                                    <div class="text-right">
                                        <i class="fa fa-trash fixed_icon delete_item" ></i>
                                    </div>
                                    

                                </div>   -->  
                            @endforeach
                        </div>
                        
                        
                        
                        <div class="item_append_div item_div">
                            

                        </div>
                        
                        <div class="col-sm-12 form-div tab-pane " style="padding:30px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);" id="maintenance">
                                    <h3>Vehicle & Schedule</h3>
                        </div>

                        <div class="col-sm-12 form-div tab-pane " style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);" id="Lifecycle">
                            <h3>Work Flow</h3>
                        </div>

                       

                        <div class="btn-block">
                             <div class="btn-group">
                                  <br>
                                  <input  name="inspection_form_id" type="hidden" value="{{base64_encode($form_id)}}" />
                                  <button type="submit" class="btn btn-primary">Save</button>
                              </div>
                          </div>
                      </div>
                    </form>
        </div>
    </div>
  </div>
</section>


<style>


    .same-btn{
    margin-right: 10px !important;
    border-radius: 3px !important; 
    border: 1px solid #c7c7c7;
    color: #000;
    }

    .btn-block{
        display: flex;
        justify-content: end;
    }
    .main-box-ul{
        border-radius: 4px;
        background-color: #c7c7c7;
        padding: 10px;
        background-color: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 10%)
    }
    .form-div .same-form{
        background-color: #fff !important;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 10%) !important;
        padding: 10px 12px !important;
        margin: 10px 0;
    }
    .btn-group .green-btn{
        background-color: #44ace9 !important;
    }
</style>
    
   
@endsection

@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}">
</script>

<script type="text/javascript">
    //var pass_fail_html={{$pass_fail_html}};
    

    /*<div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            &nbsp;
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
              Collapsible Group Item #1
            </a>
          </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in">
          <div class="panel-body">
          </div>
      </div>
    </div>*/


    var pass_fail_html='\n\
        <div class="col-sm-12 item_box form-div " style="padding:50px; margin-top:20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">  \n\
            <div class="text-right">\n\
                <i class="fa fa-trash fixed_icon delete_item" ></i>\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Label <span style="color:#ccc;">(Pass / Fail)</span></label>\n\
                <input type="text" class="form-control  m-bot15" name="title[1][]" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <input type="hidden" class="form-control  m-bot15" name="inspection_from_type_id[1][]" value="1" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Short Description</label>\n\
                <input type="text" class="form-control category_list m-bot15" name="short_description[1][]">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Instructions</label>\n\
                <textarea class="form-control" rows="4" id="exampleInputPassword1"  name="instructions[1][]" placeholder="Description"></textarea>\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Pass Label</label>\n\
                <input type="text" class="form-control category_list m-bot15" name="pass_label[1][]">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Fail Label</label>\n\
                <input type="text" class="form-control category_list m-bot15" name="fail_label[1][]">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Enable for submission</label>\n\
                <input type="checkbox" class="m-bot15" value="1" name="enable_for_submission[1][]" >\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Require photo or comment for "Pass"</label>\n\
                <input type="checkbox" class="m-bot15" value="1" name="require_photo_or_comment_for_pass[1][]" >\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Require photo or comment for "Fail"</label>\n\
                <input type="checkbox" class="m-bot15" value="1" name="require_photo_or_comment_for_fail[1][]">\n\
            </div>\n\
        </div>';

    var meter_entry_html='\n\
        <div class="col-sm-12 item_box form-div " style="padding:50px; margin-top:20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">  \n\
            <div class="text-right">\n\
                <i class="fa fa-trash fixed_icon delete_item" ></i>\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Label <span style="color:#ccc;">(Meter Entry)</span></label>\n\
                <input type="text" class="form-control  m-bot15" name="title[2][]">\n\
            </div>\n\
            <div class="form-group">\n\
                <input type="hidden" class="form-control  m-bot15" name="inspection_from_type_id[2][]" value="2" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Short Description</label>\n\
                <input type="text" class="form-control category_list m-bot15" name="short_description[2][]" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Require Secondary Meter?</label>\n\
                <input type="checkbox" class="m-bot15" value="1" name="require_secondary_meter[2][]" >\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Require Photo Verification</label>\n\
                <input type="checkbox" class="m-bot15" value="1" name="require_photo_verification[2][]">\n\
            </div>\n\
        </div>';  


    var signature_html='\n\
        <div class="col-sm-12 item_box form-div " style="padding:50px; margin-top:20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">  \n\
            <div class="text-right">\n\
                <i class="fa fa-trash fixed_icon delete_item" ></i>\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Label <span style="color:#ccc;">(Signature)</span></label>\n\
                <input type="text" class="form-control  m-bot15" name="title[3][]" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <input type="hidden" class="form-control  m-bot15" name="inspection_from_type_id[3][]" value="3" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Short Description</label>\n\
                <input type="text" class="form-control category_list m-bot15" name="short_description[3][]" required="true">\n\
            </div>\n\
        </div>';  


    var free_text_html='\n\
        <div class="col-sm-12 item_box form-div " style="padding:50px; margin-top:20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">  \n\
            <div class="text-right">\n\
                <i class="fa fa-trash fixed_icon delete_item" ></i>\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Label <span style="color:#ccc;">(Free Text)</span></label>\n\
                <input type="text" class="form-control  m-bot15" name="title[4][]" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <input type="hidden" class="form-control  m-bot15" name="inspection_from_type_id[4][]" value="4" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Short Description</label>\n\
                <input type="text" class="form-control category_list m-bot15" name="short_description[4][]" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Instructions</label>\n\
                <textarea class="form-control" rows="4" id="exampleInputPassword1"  name="instructions[4][]" placeholder="Description"></textarea>\n\
            </div>\n\
        </div>'; 


    var dropdown_html='\n\
        <div class="col-sm-12 item_box form-div " style="padding:50px; margin-top:20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);"> \n\
            <div class="text-right">\n\
                <i class="fa fa-trash fixed_icon delete_item" ></i>\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Label <span style="color:#ccc;">(Dropdown)</span></label>\n\
                <input type="text" class="form-control  m-bot15" name="title[5][]" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <input type="hidden" class="form-control  m-bot15" name="inspection_from_type_id[5][]" value="5" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Short Description</label>\n\
                <input type="text" class="form-control category_list m-bot15" name="short_description[5][]" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Instructions</label>\n\
                <textarea class="form-control" rows="4" id="exampleInputPassword1"  name="instructions[5][]" placeholder="Description"></textarea>\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Choices</label>\n\
                <input type="text" class="form-control category_list m-bot15">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Require photo or comment for "Pass"</label>\n\
                <input type="checkbox" class="m-bot15" name="require_photo_or_comment_for_pass[5][]" value="1">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Require photo or comment for "Fail"</label>\n\
                <input type="checkbox" class="m-bot15" value="1" name="require_photo_or_comment_for_fail[5][]">\n\
            </div>\n\
        </div>'; 

        var photo_html='\n\
        <div class="col-sm-12 item_box form-div " style="padding:50px; margin-top:20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">  \n\
            <div class="text-right">\n\
                <i class="fa fa-trash fixed_icon delete_item" ></i>\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Label <span style="color:#ccc;">(Photo)</span></label>\n\
                <input type="text" class="form-control  m-bot15" name="title[6][]" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <input type="hidden" class="form-control  m-bot15" name="inspection_from_type_id[6][]" value="6" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Short Description</label>\n\
                <input type="text" class="form-control category_list m-bot15" name="short_description[6][]" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Instructions</label>\n\
                <textarea class="form-control" rows="4" id="exampleInputPassword1"  name="instructions[6][]" placeholder="Description"></textarea>\n\
            </div>\n\
        </div>';    


        var number_html='\n\
        <div class="col-sm-12 item_box form-div " style="padding:50px; margin-top:20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">  \n\
            <div class="text-right">\n\
                <i class="fa fa-trash fixed_icon delete_item" ></i>\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Label <span style="color:#ccc;">(Number)</span></label>\n\
                <input type="text" class="form-control  m-bot15" name="title[7][]" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <input type="hidden" class="form-control  m-bot15" name="inspection_from_type_id[7][]" value="7" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Short Description</label>\n\
                <input type="text" class="form-control category_list m-bot15" name="short_description[7][]" >\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Instructions</label>\n\
                <textarea class="form-control" rows="4" id="exampleInputPassword1"  name="instructions[7][]" placeholder="Description"></textarea>\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Passing Range</label>\n\
                <input type="number" class="form-control category_list m-bot15" name="passing_range_from[7][]">\n\
                To \n\
                <input type="number" class="form-control category_list m-bot15" name="passing_range_to[7][]" >\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Require photo or comment for "Pass"</label>\n\
                <input type="checkbox" class="m-bot15" value="1" name="require_photo_or_comment_for_pass[7][]">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Require photo or comment for "Fail"</label>\n\
                <input type="checkbox" class="m-bot15" value="1" name="require_photo_or_comment_for_fail[7][]">\n\
            </div>\n\
        </div>'; 


        var datetime_html='\n\
        <div class="col-sm-12 item_box form-div " style="padding:50px; margin-top:20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">  \n\
            <div class="text-right">\n\
                <i class="fa fa-trash fixed_icon delete_item" ></i>\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Label <span style="color:#ccc;">(Datetime)</span></label>\n\
                <input type="text" class="form-control  m-bot15" name="title[8][]" required="true">\n\
            </div>\n\
            <div class="form-group">\n\
                <input type="hidden" class="form-control  m-bot15" name="inspection_from_type_id[8][]" value="8" >\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Short Description</label>\n\
                <input type="text" class="form-control category_list m-bot15" name="short_description[8][]">\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Instructions</label>\n\
                <textarea class="form-control" rows="4" id="exampleInputPassword1"  name="instructions[8][]" placeholder="Description"></textarea>\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Date</label>\n\
                <input type="date" class="form-control category_list m-bot15" name="date[8][]" >\n\
            </div>\n\
            <div class="form-group">\n\
                <label for="exampleInputPassword1">Date and time</label>\n\
                <input type="date" class="form-control category_list m-bot15" name="date_time[8][]" >\n\
            </div>\n\
        </div>';    

    $(document).on('click','.click_item',function(){
        var item_type=$(this).data('type');
        if(item_type=="pass_fail"){$('.item_append_div').append(pass_fail_html);}
        if(item_type=="meter_entry"){$('.item_append_div').append(meter_entry_html);}
        if(item_type=="signature"){$('.item_append_div').append(signature_html);}
        if(item_type=="free_text"){$('.item_append_div').append(free_text_html);}
        if(item_type=="dropdown"){$('.item_append_div').append(dropdown_html);}
        if(item_type=="photo"){$('.item_append_div').append(photo_html);}
        if(item_type=="number"){$('.item_append_div').append(number_html);}
        if(item_type=="date_time"){$('.item_append_div').append(datetime_html);}
        
    });


    $('.item_div').on('click','.item_box .delete_item',function(){

        Swal.fire({
          title: 'Are you sure want to remove this Item?',
          showCancelButton: true,
          confirmButtonColor: '#252525',
          cancelButtonColor: 'red',
          confirmButtonText: 'Yes, I Confirm',
          cancelButtonText: `No, Cancel It`,
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).parent('div').parent('.item_box').remove();
            }
        })

    });


</script>

@endsection