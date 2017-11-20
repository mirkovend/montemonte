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
				<li class="breadcrumb-item active">Customer</li>
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
						<h5>Customer Lists</h5>
					</div>
					<div class="ibox-content" id="ibox_form">
					<div class="pull-right">
		            	<div class="form-group">
		            	<a class="btn btn-md btn-primary" href="{{ action('CustomerController@create')}}"><span class="fa fa-plus"></span> Create Customer</a>
		            	</div>
		            </div>
		            <div class="table-responsive">
						<table class="table table-bordered sys_table responsive" id="tableSortable" style="text-transform:uppercase;">
							<thead>	
								<tr>
									<th>Customer Name</th>
									<th>Customer Address</th>
									<th>Customer Contact</th>
									<th>Balance</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								@foreach($customers as $data)
								<?php 
									$balance = $data->beginning_bal;
									foreach ($data->invoice as $key => $transaction_invoice) {
										$balance += $transaction_invoice->invoice->sum('amount');
									};
									foreach ($data->payments as $key => $transaction_payment) {
										$balance += -$transaction_payment->payment;
									};
								?>
								<tr>
									<td>{{ $data->customer_name }}</td>
									<td>{{ $data->customer_address }}</td>
									<td>{{ $data->customer_contact }}</td>
									<td>{{ number_format($balance,2) }}</td>
									<td class="text-center">
					        			<div class="btn-group">
										  <a  class="btn btn-sm btn-primary" href="{{ action('CustomerController@view_jacket',$data->id) }}"><i class="fa fa-share" aria-hidden="true"></i> View</a>
										  <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										    <span class="caret"></span>
										    <span class="sr-only">Toggle Dropdown</span>
										  </button>
										  <ul class="dropdown-menu">
										    <li><a href="{{ action('CustomerController@edit',$data->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a></li>
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

