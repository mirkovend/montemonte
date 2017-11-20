@extends('template.theme')
<?php

	use Carbon\Carbon;

	$credit_total = 0;
	$debit_total = 0;
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
			<li class="breadcrumb-item">Trial Balance </li>
		</ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
	<div class="row">
        <div class="col-md-12">

            <div class="panel panel-default">
                <div class="panel-body">
                {!! Form::open(['action'=>'ReportController@trial_balance_post','class'=>'form-inline'])!!}
				<!-- 	<div class="form-group">
						<label for="">From</label>
						<input type="text" class="form-control" id="idate" name="from" datepicker="" data-date-format="yyyy-mm-dd" data-auto-close="true" value="">
					</div> -->
					<div class="form-group">
						<label for="">Select Date</label>
						<input type="text" class="form-control" id="idate" name="to" datepicker="" data-date-format="yyyy-mm-dd" data-auto-close="true" value="">
					</div>
					<button type="submit" class="btn btn-primary">Submit</button>
				{!! Form::close()!!}
                </div>
            </div>
        </div>
    </div>
	<div class="row">
	    <div class="col-lg-12 col-md-12 col-sm-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title">
	                <h5>Trial Balance as of {{$dt}}</h5>

	            </div>
	            <div class="ibox-content">
		            <div class="row">
		            	<div class="col-md-8">
			                <div class="table-responsive">
			                    <table class="table table-bordered sys_table" id="">
							        <thead>
							            <tr>
					                        <th></th>
					                        <th class="text-center">Debit</th>
					                        <th class="text-center">Credit</th>
					                    </tr>
							        </thead>
							        <tbody>

							      	@foreach($coas as $coa)
							  			@if($coa->debit - $coa->credit != 0 && $coa->credit - $coa->debit != 0)
							  			<?php 
							  			$debit_total += $coa->debit;
							  			$credit_total += $coa->credit;
							  			?>
								      		<tr>
								      			<td>{{$coa->account_title}}</td>
								      			<td class="text-right">{{ ($coa->debit == 0)? "--" : number_format( $coa->debit,2)}}</td>
								      			<td class="text-right">{{ ($coa->credit == 0)? "--" : number_format($coa->credit,2)}}</td>
									      		
								      		</tr>
								      	@endif
							      	@endforeach
							      		<tr style="background-color: #e7eaec ">
							      			<td>TOTAL</td>
							      			<td class="text-right">₱ {{number_format($debit_total,2)}}</td>
							      			<td class="text-right">₱ {{number_format($credit_total,2)}}</td>
							      		</tr>
							        </tbody>
							    </table>
			                </div>
		                </div>
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