<!Doctype>
<html>
	 <meta name="viewport" content="width=device-width; initial-scale=1.0">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

<head>
<style type="text/css">
	*{
		margin: 0px;
		padding: 0px;
		    background: #000;
    color: #fff
	}
	hgroup h1{
font-size:24px;
color: #EB2329;
margin-bottom: 10px;
	}
		hgroup p{
font-size:16px;
	}
	hgroup {
		margin: 10px 0px;
	}
	.content{
		padding: 0px 15px;
		max-width: 1170px;
		margin: 0 auto;
	}
	

</style>
</head>
	<body>
	</body>
	@if($row)

	@if($row->image)
<div class="banner">
	<img src="{{ asset('uploads/app_custom_pages/'.$row->image) }}" style="max-width:100%;">
</div>

@endif
<div class="content">
	
	<?php 
            $description = $row->description;
            $description = json_decode($description);
           
            ?>
   @foreach($description as $desc_data)         
<hgroup>
	<h1>{!! strtoupper($desc_data->heading) !!}</h1>
	<p>{!! ucfirst($desc_data->description) !!}</p>
</hgroup>
@endforeach

</div>


@else
<hgroup>
	
	<p>No page found</p>
</hgroup>
@endif

	</html>