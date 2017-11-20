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
				<li class="breadcrumb-item active">Cash Invoice</li>
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
						<h5>Cash Invoice Lists</h5>
					</div>
					<div class="ibox-content" id="ibox_form">
					<div class="pull-right">
		            	<div class="form-group">
		            	<a class="btn btn-md btn-primary" href="{{ action('CashinvoiceController@create')}}"><span class="fa fa-plus"></span> Create Entry</a>
		            	</div>
		            </div>
		            <div class="table-responsive">
						<table class="table table-bordered sys_table responsive" id="tableSortable" style="text-transform:uppercase;">
							<thead>	
								<tr>
				    				<th>Date</th>
				    				<th>Cash Invoice Number</th>
				    				<th>Customer Name</th>
				    				<th>Total Amount</th>
				                    <th>Department</th>
				    				<th>Status</th>
				    				<th width = 80 class="text-center">Action</th>
				    			</tr>
							</thead>
							<tbody>
								@foreach($cashinvoice as $data)
					    			<tr>
					    				<td>{{ \Carbon\Carbon::parse($data->dt)->toFormattedDateString() }}</td>
					    				<td>{{ $data->cash_invoice_number }}</td>
					                    @if($data->cash_invoice_to != 0)
					    				<td>{{ $data->customers->customer_name }}</td>
					                    @else
					                    <td></td>
					                    @endif
					    				<td>P {{ number_format($data->invoice->where('cash_invoice_number',$data->cash_invoice_number)->sum('amount'),2) }}</td>
					                    <td>{{ $data->dept }}</td>
										@if($data->status == 0)
					    				<td>RECEIVED</td>
					    				@else
					    				<td>CANCELLED</td>
					    				@endif
					    				<td width = 80 class="text-center">
					    					<div class="btn-group">
					                              <button type="button" class="btn btn-lg btn-primary dropdown-toggle" data-toggle="dropdown">
					                                  <span class="icon-gear"></span> <span class="caret"></span>
					                              </button>
					                              <ul class="dropdown-menu dropdown-menu-arrow" role="menu" style="text-align:left;text-transform:uppercase;">
					                                  <li><a href="{{ action('CashinvoiceController@edit',$data->id) }}"><span class="icon-edit"></span> Edit Entry</a></li>
					                                  @if($data->status == 0)
					                                  <li><a href="{{ action('CashinvoiceController@cancel_invoice',$data->cash_invoice_number) }}"><span class="icon-remove"></span> Cancel Invoice</a></li>
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

