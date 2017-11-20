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
        table.dataTable #firsthead th, table.dataTable thead td {
		    padding: 0 18px;
		    border-bottom: none;
		}
		table.dataTable #secondhead th, table.dataTable thead td {
		    padding: 0 18px;
		    border-bottom: 1px solid #111;
		    border-top:none;
		}
</style>
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
	<div class="col-lg-12">
		<h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
				<li class="breadcrumb-item active">Chart of Accounts</li>
			</ol>
		</h2>
	</div>
</div>
<div class="wrapper wrapper-content">
	<div class="wrapper wrapper-content">
		<div class="row">

			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						
					<div class="col-md-6">
		            	<h3>{{$chart->coa_title}}</h3>
		            </div>
		            <div class="col-md-6"><h3 class="pull-right">Balance: {{($chart->typical_balance=="DEBIT")? $chart->balance+$chart->transactions->sum('debit')-$chart->transactions->sum('credit') : $chart->balance+$chart->transactions->sum('credit')-$chart->transactions->sum('debit')}}</h3></div>
		            <hr>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">

		            <div class="table-responsive">
						<table class="table table-condensed" id="tableSortable" style="text-transform:uppercase;">
				    		<thead>	
				    			<tr id="firsthead">
				    				<th>Date</th>
				                    <th>Ref No.</th>
				                    <th>Payee</th>
				                    <th>Memo</th>
				                    <th>Amount</th>
				                    <th>Balance</th>
				    			</tr>
				    			<tr id="secondhead">
				    				<th></th>
				    				<th>Type</th>
				                    <th>Account</th>
				                    <th></th>
				                    <th></th>
				                    <th></th>
				    			</tr>
				    		</thead>
				    		<tbody>
				    		<?php $openBalance = 0;?>
				    			@if($chart->balance > 0)
					    			<tr>
				    					<td>{{\Carbon\Carbon::parse($chart->as_of)->toFormattedDateString()}}</td>
				    					<td></td>
				    					<td></td>
				    					<td></td>
				    					<td>{{$openBalance += $chart->balance}}</td>
				    					<td>{{$openBalance}}</td>
				    				</tr>
				    				<tr>
				    					<td></td>
				    					<td>Deposit</td>
				    					<td>Opening Balance</td>
				    					<td></td>
				    					<td></td>
				    					<td></td>
				    				</tr>
			    				@endif


				    			@foreach($chart->transactions as $coaTransaction)
				    				<?php $openBalance += $coaTransaction->debit - $coaTransaction->credit; ?>
				    					@if($coaTransaction->type =="Sales Receipt")
				    						<tr>
					    						<td>{{$coaTransaction->dt}}</td>
					    						<td>{{$coaTransaction->ref}}</td>
					    						<td>{{$coaTransaction->cashInvoice->customers->customer_name}}</td>
					    						<td>MEMO</td>
					    						<td>{{$coaTransaction->debit - $coaTransaction->credit}}</td>	
						    					<td>{{$openBalance}}</td>
						    				</tr>
						    				<tr>
						    					<td></td>
						    					<td>{{$coaTransaction->type}}</td>
						    					<td>
						    						@foreach($coaTransaction->coa_transaction_link as $key => $coa_link)
						    							{{$coa_link->chartofaccount->coa_title}}
						    							@if((count($coaTransaction->coa_transaction_link) - 1)!= $key)
						    							,
						    							@endif
						    						@endforeach
						    					</td>
						    					<td></td>
						    					<td></td>
						    					<td></td>
						    				</tr>
				    					@elseif($coaTransaction->type =="Invoice")
				    						<tr>
					    						<td>{{$coaTransaction->dt}}</td>
					    						<td>{{$coaTransaction->ref}}</td>
					    						<td>{{$coaTransaction->chargeInvoice->customer_belong->customer_name}}</td>
					    						<td>MEMO</td>
					    						<td>{{$coaTransaction->debit - $coaTransaction->credit}}</td>	
						    					<td>{{$openBalance}}</td>
						    				</tr>
						    				<tr>
						    					<td></td>
						    					<td>{{$coaTransaction->type}}</td>
						    					<td>
						    						@foreach($coaTransaction->coa_transaction_link as $coa_link)
						    							{{$coa_link->chartofaccount->coa_title}}
						    						@endforeach
						    					</td>
						    					<td></td>
						    					<td></td>
						    					<td></td>
						    				</tr>
				    					@elseif($coaTransaction->type == "Payment")
				    						<tr>
					    						<td>{{$coaTransaction->dt}}</td>
					    						<td>{{$coaTransaction->ref}}</td>
												<td>{{$coaTransaction->payment->customer_belong->customer_name}}</td>	
												<td></td>
												<td>{{$coaTransaction->debit - $coaTransaction->credit}}</td>	
						    					<td>{{$openBalance}}</td>
						    				</tr>
						    				<tr>
						    					<td></td>
						    					<td>{{$coaTransaction->type}}</td>
						    					<td>
						    						@foreach($coaTransaction->coa_transaction_link as $coa_link)
						    							{{$coa_link->chartofaccount->coa_title}}
						    						@endforeach
						    					</td>
						    					<td></td>
						    					<td></td>
						    					<td></td>
						    				</tr>
				    					@else
				    						<tr>
						    					<td>{{$coaTransaction->dt}}</td>
						    					<td>{{$coaTransaction->ref}}</td>
					    						<td></td>
					    						<td></td>
					    						<td>{{$coaTransaction->debit - $coaTransaction->credit}}</td>	
						    					<td>{{$openBalance}}</td>
						    				</tr>
						    				<tr>
						    					<td></td>
						    					<td>{{$coaTransaction->type}}</td>
						    					<td>
						    						@foreach($coaTransaction->coa_transaction_link as $coa_link)
						    							{{$coa_link->chartofaccount->coa_title}}
						    						@endforeach
						    					</td>
						    					<td></td>
						    					<td></td>
						    					<td></td>
						    				</tr>
				    					@endif
				    				
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
		$('table').DataTable({ "ordering": false,
	    	"bPaginate": false,
	        "bLengthChange": false,
		    "bFilter": true,
		    "bInfo": false,});
		var $table = $('#tableSortable');
	    $table.floatThead({
			responsiveContainer: function($table){
				return $table.closest('.table-responsive');
			}
	    });
	    $table.floatThead('reflow');
	});
</script>
@endsection