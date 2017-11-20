@extends('template.theme')
@section('css')

<link href="{{asset('assets/bootstrap-fileinput-master/css/fileinput.min.css')}}" media="all" rel="stylesheet">
<style type="text/css">.kv-avatar .file-input {
    display: table-cell;
    max-width: 220px;
}</style>
@endsection


@section('content')
<div class="row wrapper white-bg page-heading">
	<div class="col-lg-12">
		<h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
				<li class="breadcrumb-item active">Delivery Receipt</li>
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
						<h5>Delivery Receipt Lists</h5>
					</div>
					<div class="ibox-content" id="ibox_form">
					<div class="pull-right">
		            	<div class="form-group">
		            	<a class="btn btn-md btn-primary" href="{{ action('DeliveryreceiptController@create')}}"><span class="fa fa-plus"></span> Create Entry</a>
		            	</div>
		            </div>
		            <div class="table-responsive">
						<table class="table table-bordered sys_table responsive" id="tableSortable" style="text-transform:uppercase;">
							<thead>	
				    			<tr>
				    				<th>Date</th>
				    				<th>Delivery Receipt Number</th>
				    				<th>Delivered To</th>
				    				<th>Total Quantity</th>
				    				<th width = 80 class="text-center">Action</th>
				    			</tr>
							</thead>
							<tbody>
								@foreach($deliveryreceipt as $data)
					    			<tr>
					    				<td>{{ \Carbon\Carbon::parse($data->dt)->toFormattedDateString() }}</td>
					    				<td>{{ $data->delivery_receipt_number }}</td>
					    				<td>{{ $data->delivery_receipt_to }}</td>
					    				<td>{{ $data->invoice->where('delivery_receipt_number',$data->delivery_receipt_number)->sum('delivery_receipt_qty') }}</td>
					    				<td width = 80 class="text-center">
					    					<div class="btn-group">
					                            <button type="button" class="btn btn-md btn-primary dropdown-toggle" data-toggle="dropdown">
					                                <span class="icon-gear"></span> <span class="caret"></span>
					                            </button>
					                            <ul class="dropdown-menu dropdown-menu-arrow" role="menu" style="text-align:left;text-transform:uppercase;">
					                                <li><a href="{{ action('DeliveryreceiptController@edit',$data->id) }}"><span class="icon-edit"></span> Edit Entry</a></li>
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

