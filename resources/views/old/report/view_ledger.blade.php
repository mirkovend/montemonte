@extends('template.theme')
<?php

	use Carbon\Carbon;

	$balance1 = 0;

?>
@section('css')
<link href='{{ asset("assets/datatable/jquery.dataTables.min.css") }}' rel="stylesheet">
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
  <div class="col-lg-12">
    <h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
    	<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#" style="color:#2196f3">General Ledger</a></li>
			<li class="breadcrumb-item">{{$ledger->account_title}}</li>
		</ol>
    </h2>
  </div>
</div>



<div class="wrapper wrapper-content animated fadeIn">
	<div class="row">
	    <div class="col-lg-12 col-md-12 col-sm-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title">
	                <h5>{{$ledger->account_title}}</h5>

	            </div>
	            <div class="ibox-content">
	                <div class="table-responsive">
	                    <table class="table table-bordered sys_table" id="">
					        <thead>
					            <tr>
			                        <th>Date</th>
			                        <th class="">Ref #</th>
			                        <th>Debit</th>
			                        <th>Credit</th>
			                        <th>Balance</th>
			                    </tr>
					        </thead>
					        <tbody>
					      	@foreach($ledgers as $data)

					      	<?php $balance1 += $data->debit - $data->credit;?>
					        	<tr>
					        		<td>{{carbon::parse($data->created_at)->toFormattedDateString()}}</td>
					        		<td>{{$data->reference}}</td>
					        		<td class="text-right">{{($data->debit == 0) ? "--" : number_format($data->debit,2)}}</td>
					        		<td class="text-right">{{($data->credit == 0) ? "--" : number_format($data->credit,2)}}</td>
					        		<td class="text-right">{{number_format($balance1,2)}}</td>
					        	</tr>

					    	@endforeach
					        </tbody>
					    </table>
	                </div>
	            </div>
	        </div>
	        
	    </div>

	   <!-- Widget-1 end-->

	    <!-- Widget-2 end-->
	</div> <!-- Row end-->


	<!-- Modal -->
	
<!-- dataTables_paginate paging_simple_numbers -->
<!-- Row end-->


<!-- Row end-->

	<div id="ajax-modal" class="modal container fade-scale" tabindex="-1" style="display: none;"></div>
</div>

@stop


@section('script')

<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script type="text/javascript">
	$(document).ready(function() {
	    $('#disbursement').DataTable();
	    $('.amount').autoNumeric('init', {

		    aSign: '₱ ',
		    dGroup: 3,
		    aPad: true,
		    pSign: 'p',
		    aDec: '.',
		    aSep: ','

		});
	} );
</script>
@stop