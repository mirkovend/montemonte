@extends('template.theme')
@section('css')
<link href='{{ asset("assets/datatable/jquery.dataTables.min.css") }}' rel="stylesheet">
<style type="text/css">
		.select2-container {
		    width: 100% !important;
		    padding: 0;

		}
		
        table.floatThead-table {
            border-top: none;
            border-bottom: none;
            background-color: #eee;
        }
</style>
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
	<div class="col-lg-12">
		<h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
				<li class="breadcrumb-item active">Job Order</li>
			</ol>
		</h2>
	</div>
</div>
<div class="wrapper wrapper-content">
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">

			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Job Order Lists</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">
					<div class="pull-right">
		            	<div class="form-group">
		            	<a class="btn btn-md btn-primary" href="{{ action('JoborderController@create')}}"><span class="fa fa-plus"></span> Create Entry</a>
		            	</div>
		            </div>
		            <div class="table-responsive">
						<table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
				    		<thead>	
				    			<tr>
				    				<th>Date</th>
				    				<th>Job Order Number</th>
				    				<th>Job Order To</th>
				    				<th>Total Amount</th>
				                    <th>Department</th>
				    				<th>Status</th>
				    				<th width = 80 class="text-center">Action</th>
				    			</tr>
				    		</thead>
				    		<tbody>
				    			@foreach($job as $data)
				    			<tr>
				    				<td>{{ \Carbon\Carbon::parse($data->dt)->toFormattedDateString() }}</td>
				    				<td>JO - {{ $data->joborder_number }}</td>
				    				<td>{{ $data->supplier->first()->supplier_name }}</td>
				    				<td>P {{ number_format($data->invoice->where('joborder_number',$data->joborder_number)->sum('amount'),2) }}</td>
				                    <td>{{ $data->dept }}</td>
									@if($data->status == NULL)
				    				<td>OPEN</td>
				    				@else
				    				<td>PAID</td>
				    				@endif
				    				<td width = 80 class="text-center">
				    					<div class="btn-group">
				                            <button type="button" class="btn btn-md btn-primary dropdown-toggle" data-toggle="dropdown">
				                                <span class="icon-gear"></span> <span class="caret"></span>
				                            </button>
				                            <ul class="dropdown-menu dropdown-menu-arrow" role="menu" style="text-align:left;text-transform:uppercase;">
				                                <li><a href="{{ action('JoborderController@edit',$data->id) }}"><span class="icon-edit"></span> Edit Entry</a></li>
				                                @if($data->status == 0)
				                                <li><a href="#"><span class="icon-remove"></span> Cancel Invoice</a></li>
				                                @endif
				                            </ul>
				                        </div>
				    				</td>
				    			</tr>
				    			@endforeach
				    		</tbody>
				    	</table>
					</div>
					</div>


				</div>


			</div>

		</div>

	</div>
</div>
</div><!-- end wrapper -->
@endsection

@section('script')
<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('table').DataTable();
	});
</script>
@endsection