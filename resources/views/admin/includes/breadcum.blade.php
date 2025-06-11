
      <ol class="breadcrumb">
        <li><a href="{!! route('admin.dashboard')!!}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        @if(isset($breadcum) && is_array($breadcum) && count($breadcum)>0)
        <?php  $br=1; $total_br=count($breadcum); ?>
       		@foreach($breadcum as $breadcum_key=>$breadcum_url)
       			@if($total_br == $br)
       			 <li class="active"> {!! ucfirst($breadcum_key) !!} </li>
       			 @else
       			 <li><a href="{!! $breadcum_url !!}"> {!! ucfirst($breadcum_key) !!}</a></li>
       			@endif
       			<?php $br++; ?>
        	@endforeach
        @endif
      </ol>