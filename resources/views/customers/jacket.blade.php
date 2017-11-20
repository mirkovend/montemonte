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
				<li class="breadcrumb-item "><a href="#" style="color:#2196f3">Customer</a></li>
				<li class="breadcrumb-item active">{{$customer->customer_name}}</li>
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
						<div class="col-md-6"><h3>{{$customer->customer_name}}</h5></div>
						<div class="col-md-6"><h3 class="pull-right">Balance: {{number_format($balance,2)}}</h5></div>
					</div>
					<div class="ibox-content" id="ibox_form">
					
						<table class="table table-bordered sys_table responsive" id="tableSortable" style="text-transform:uppercase;">
				    		<thead>	
				    			<tr>
				    				<th>Date</th>
				                    <th>Type</th>
				                    <th>Ref</th>
				                    <th>Amount</th>
				                    <th>Charge</th>
				    			</tr>
				    		</thead>
				            <tbody>
				            	@if($customer->beginning_bal !=0)
				                <tr>
				                    <td></td>
				                    <td>Beginning Balance</td>
				                    <td></td>
				                    <td>{{number_format($customer->beginning_bal,2)}}</td>
				                    <td>{{number_format($customer->beginning_bal,2)}}</td>
				                </tr>
				                @endif
				                @foreach($trans_array as $transaction)
				                <tr>
				                    <td>{{ $transaction->date }}</td>
			                    	<td>{{ $transaction->type }}</td>
			                    	<td>{{ $transaction->ref }}</td>
			                    	<td>{{ number_format($transaction->amount,2) }}</td>
				                  	<td>{{ number_format($transaction->charge,2) }}</td>
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
</div><!-- end wrapper -->
@endsection

