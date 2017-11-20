@extends('template.theme')
<?php

	use Carbon\Carbon;
	$totalexpense = 0;
	$totalincome = 0;

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
			<li class="breadcrumb-item">Income Statement </li>
		</ol>
    </h2>
  </div>
</div>



<div class="wrapper wrapper-content animated fadeIn">
	<div class="row">
        <div class="col-md-7">

            <div class="panel panel-default">
                <div class="panel-body">
                {!! Form::open(['action'=>'ReportController@income_statement_post','class'=>'form-inline'])!!}
					<div class="form-group">
						<label for="">From</label>
						<input type="text" class="form-control" id="idate" name="from" datepicker="" data-date-format="yyyy-mm-dd" data-auto-close="true" value="">
					</div>
					<div class="form-group">
						<label for="">To</label>
						<input type="text" class="form-control" id="idate" name="to" datepicker="" data-date-format="yyyy-mm-dd" data-auto-close="true" value="">
					</div>
					<button type="submit" class="btn btn-primary">Submit</button>
				{!! Form::close()!!}
                </div>
            </div>
        </div>
    </div>
	<div class="row">
	    <div class="col-lg-7 col-md-7 col-sm-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title">
	                <h5>{{$title}}</h5>

	            </div>
	            <div class="ibox-content">
		            <div class="row">
		            	<div class="col-md-12">
			                <div class="table-responsive">
			                    <table class="table table-bordered sys_table" id="">
							        
							        <tbody>
							   		
							      	@foreach($type->where('category','Revenue') as $acc_type)
					                    @unless(!$acc_type->coa()->first())
					                    <?php $total = 0;?>
					                    <tr>
					                        <th colspan="2">{{$acc_type->category}}</th>
					                    </tr>
						                    @foreach($acc_type->coa as $coa)
						                    	@unless(!$coa->coa_items()->first())
							                    	<?php $total += $coa->coa_items()->sum('credit')-$coa->coa_items()->sum('debit');
							                    		
							                    	?>
							                    	<tr >
								                        <td>{{$coa->account_title}}</td>
								                        <td>{{number_format($coa->coa_items()->sum('credit')-$coa->coa_items()->sum('debit'),2)}}</td>
								                    </tr>
							                    @endunless
						                    @endforeach
						                    <?php $totalincome += $total;?>
						                <tr >
					                        <th>TOTAL</th>
					                        <th>{{number_format($total,2)}}</th>
					                    </tr>
					                    <tr >
					                        <th colspan="2" ></th>
					                    </tr>
							      		@endunless
							      		
							      	@endforeach


							      	@foreach($type->where('category','Expenses') as $acc_type)
					                    @unless(!$acc_type->coa()->first())
					                    <?php $total = 0;?>
						                    <tr>
						                        <th colspan="2">{{$acc_type->name}}</th>
						                    </tr>
						                    @foreach($acc_type->coa as $coa)
						                    	@unless(!$coa->coa_items()->first())
							                    	<?php $total += $coa->coa_items()->sum('debit')-$coa->coa_items()->sum('credit');
							                    	?>
							                    	<tr >
								                        <td>{{$coa->account_title}}</td>
								                        <td>{{number_format($coa->coa_items()->sum('debit')-$coa->coa_items()->sum('credit'),2)}}</td>
								                    </tr>
							                    @endunless
						                    @endforeach
						                    <?php $totalexpense += $total;?>
							                <tr>
						                        <th>TOTAL</th>
						                        <th>{{number_format($total,2)}}</th>
						                    </tr>
						                    <tr >
						                        <th colspan="2" ></th>
						                    </tr>
							      		@endunless
							      		
							      	@endforeach
							      		<tr style="background-color: #e7eaec ">
							      			<th>Net Income</th>
							      			<td class="text-right">₱ {{number_format($totalincome-$totalexpense,2)}}</td>
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