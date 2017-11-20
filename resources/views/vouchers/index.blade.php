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
				<li class="breadcrumb-item active">Voucher</li>
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
						<h5>Voucher Lists</h5>
					</div>
					<div class="ibox-content" id="ibox_form">
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="pull-right">
		            	<div class="form-group">
		            	<a class="btn btn-md btn-primary" href="{{ action('VoucherController@create')}}"><span class="fa fa-plus"></span> Create Entry</a>
		            	</div>
		            </div>

		            <div class="table-responsive">
						<table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
				    		<thead>	
				    			<tr>
				                    <th>Date</th>
				    				<th>Voucher Number</th>
				                    <th>Payee</th>
				                    <th>Voucher Type</th>
				                    <th>Amount</th>
				    				<th width = 80 class="text-center">Action</th>
				    			</tr>
				    		</thead>
				    		<tbody>
				    			@foreach($voucher as $data)
				    			<tr>
				    				<td>{{ \Carbon\Carbon::parse($data->dt)->toFormattedDateString() }}</td>
				                    <td>{{ $data->voucher_number }}</td>
				                    <td>{{ $data->supplier->first()->supplier_name }}</td>
				                    <td>{{ $data->voucher_type }}</td>
				                    <td>{{ number_format($data->amount,2) }}</td>
				    				<td width = 80 class="text-center">
				    					<div class="btn-group">
				                            <button type="button" class="btn btn-md btn-primary dropdown-toggle" data-toggle="dropdown">
				                                <span class="icon-gear"></span> <span class="caret"></span>
				                            </button>
				                            <ul class="dropdown-menu dropdown-menu-arrow" role="menu" style="text-align:left;text-transform:uppercase;">
				                                <li><a href="{{ action('VoucherController@edit',$data->id) }}"><span class="icon-edit"></span> Edit Entry</a></li>
				                                <li><a href="{{ action('VoucherController@delete_item',$data->id) }}"><span class="icon-remove"></span> Delete Entry</a></li>
				                                <li><a href="{{ action('VoucherController@print_voucher',$data->id) }}"><span class="icon-print"></span> Print Voucher</a></li>
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