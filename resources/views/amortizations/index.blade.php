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
				<li class="breadcrumb-item active">Table of Amortization</li>
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
						<h5>Table of Amortization Lists</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">
					<div class="pull-right">
		            	<div class="form-group">
		            	<a class="btn btn-md btn-primary" href="{{ action('AmortizationController@create')}}"><span class="fa fa-plus"></span> Create Entry</a>
		            	</div>
		            </div>
		            <div class="table-responsive">
						<table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
				    		<thead>	
				    			<tr>
				    				<th>Date</th>
				                    <th>Batch Number</th>
				                    <th>No. of Heads</th>
				                    <th>Pullet Price</th>
				                    <th>Total Cost</th>
				    				<th width = 80 class="text-center">Action</th>
				    			</tr>
				    		</thead>
				    		<tbody>
				    			@foreach($batch as $data)
				    			<tr>
				    				<td>{{ \Carbon\Carbon::parse($data->dt)->toFormattedDateString() }}</td>
				                    <td>{{ $data->batch_number }}</td>
				                    <td>{{ number_format($data->number_heads,2) }}</td>
				                    <td>P {{ $data->pullet_price }}</td>
				                    <td>P {{ number_format($data->total_cost,2) }}</td>
				    				<td width = 80 class="text-center">
				    					<div class="btn-group">
				                            <button type="button" class="btn btn-md btn-primary dropdown-toggle" data-toggle="dropdown">
				                                <span class="icon-gear"></span> <span class="caret"></span>
				                            </button>
				                            <ul class="dropdown-menu dropdown-menu-arrow" role="menu" style="text-align:left;text-transform:uppercase;">
				                                <li><a href="{{ action('AmortizationController@edit',$data->id) }}"><span class="icon-edit"></span> Edit Entry</a></li>
				                                <li><a href="{{ action('AmortizationController@delete_item',$data->id) }}"><span class="icon-remove"></span> Delete Entry</a></li>
				                                <li><a href="{{ action('AmortizationController@print_report',$data->id) }}"><span class="icon-print"></span> Print Batch</a></li>
				                                <li><a href="{{ action('AmortizationController@mortality_index',$data->id) }}"><span class="icon-print"></span> Mortality Table</a></li>
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