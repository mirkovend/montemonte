@extends('template.theme')
<?php

	use Carbon\Carbon;
	$debit_total = 0;
	$credit_total = 0;
	$asset_total = 0;
	$current_asset_total = 0;
	$non_current_asset_total = 0;
	$liability_equit_total = 0;
	$equity_total = 0; 
	$expense = 0;
	$revenue = 0;
	$withdrawal = 0;
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
			<li class="breadcrumb-item">Balance Sheet as of <strong>{{$dt}}</strong></li>
		</ol>
    </h2>
  </div>
</div>



<div class="wrapper wrapper-content animated fadeIn">
	<div class="row">
        <div class="col-md-12">

            <div class="panel panel-default">
                <div class="panel-body">
                {!! Form::open(['action'=>'ReportController@balance_sheet_post','class'=>'form-inline'])!!}
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
	    <div class="col-lg-6 col-md-6 col-sm-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title">
	                <h5>ASSETS</h5>
	            </div>
	            <div class="ibox-content">
		            <div class="row">
		            	<div class="col-md-12">
			                <div class="table-responsive">
			                    <table class="table table-bordered sys_table" id="">
							        
							        <tbody>
							   
							      	@foreach($type->where('category','Assets') as $acc_type)
					                    @unless(!$acc_type->coa()->first())
					                    <?php $total = 0;?>
					                    <tr>
					                        <th colspan="2">{{$acc_type->name}}</th>
					                    </tr>
						                    @foreach($acc_type->coa as $coa)
						                    	@unless(!$coa->coa_items()->first())
						                    	<?php 
						                    		$total += $coa->coa_items()->sum('debit')-$coa->coa_items()->sum('credit');

						                    	?>
						                    	<tr >
							                        <td>{{$coa->account_title}}</td>
							                        <td>{{number_format($coa->coa_items()->sum('debit')-$coa->coa_items()->sum('credit'),2)}}</td>
							                    </tr>
							                    @endunless
							                    <?php 
							                    	$recItem = App\recurringItem::where('chart_of_account_id',$coa->id)
								                    	->whereBetween('date',['1990-20-02',Carbon::now()])
								                    	->get();

							                    ?>
							                    @unless(!$recItem->first())
						                    	<?php 
						                    		$total += $recItem->sum('debit')-$recItem->sum('credit');
						                    	?>
						                    	<tr >
							                        <td>{{$coa->account_title}}</td>
							                        <td>{{number_format($recItem->sum('debit')-$recItem->sum('credit'),2)}}</td>
							                    </tr>
							                    @endunless

							                    @unless(!$coa->subaccount)
							                    	@foreach($coa->subaccount as $sub)
							                    		@unless(!$sub->coa_items()->first())
							                    		<?php 
							                    			$total += $sub->coa_items()
							                    				->sum('debit')-$sub->coa_items()
							                    				->sum('credit');
						                    			?>
								                    	<tr >
									                        <td style="text-indent: 10px;">&rarr; {{$sub->account_title}}</td>
									                        <td>
									                        	{{number_format($sub->coa_items()->sum('debit')-$sub->coa_items()->sum('credit'),2)}}
									                        </td>
									                    </tr>
									                    @endunless
							                    	@endforeach
							                    @endunless
						                    @endforeach
						                    <?php $asset_total += $total; ?>
						                <tr >
					                        <th>TOTAL</th>
					                        <th>{{number_format($total,2)}}</th>
					                    </tr>
					                    <tr >
					                        <th colspan="2" ></th>
					                    </tr>
							      		@endunless
							      		
							      	@endforeach
							      		<tr style="background-color: #e7eaec ">
							      			<td>TOTAL ASSETS</td>
							      			<td class="text-right">₱ {{number_format($asset_total ,2)}}</td>
							      		</tr>


							        </tbody>
							    </table>
			                </div>
			               
		                </div>
		                
		            </div>	
	            </div>
	        </div>
	    </div>
	    
	    <div class="col-lg-6 col-md-6 col-sm-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title">
	                <h5>LIABILITIES AND EQUITY</h5>
	            </div>
	            <div class="ibox-content">
		            <div class="row">
		            	<div class="col-md-12">
			                <div class="table-responsive">
			                    <table class="table table-bordered sys_table" id="">
							        
							        <tbody>
							   		
							      	@foreach($type_liab_equity as $acc_type)
					                    @unless(!$acc_type->coa()->first())
					                    <?php $total = 0;?>
					                    <tr>
					                        <th colspan="2">{{$acc_type->name}}</th>
					                    </tr>
						                    @foreach($acc_type->coa as $coa)
						                    	@unless(!$coa->coa_items()->first())
							                    	<?php $total += $coa->coa_items()->sum('credit')-$coa->coa_items()->sum('debit');

							                    	?>
							                    	<tr >
								                        <td>{{$coa->account_title}}</td>
								                        <td>{{number_format($coa->coa_items()->sum('credit')-$coa->coa_items()->sum('debit'),2)}}</td>
								                    </tr>
								                    @if($coa->category == 'Equity' && $retain_earnings != 0)
									                    <?php $total +=$retain_earnings;?>
									                    <tr >
									                        <td>Retain Earnings</td>
									                        <td>{{number_format($retain_earnings,2)}}</td>
									                    </tr>
								                    @endif
							                    @endunless
						                    @endforeach
						                    <?php $liability_equit_total += $total; ?>
						                <tr >
					                        <th>TOTAL</th>
					                        <th>{{number_format($total,2)}}</th>
					                    </tr>
					                    <tr >
					                        <th colspan="2" ></th>
					                    </tr>
							      		@endunless
							      		
							      	@endforeach
							      		<tr style="background-color: #e7eaec ">
							      			<td>TOTAL LIABILITIES AND EQUITY</td>
							      			<td class="text-right">₱ {{number_format($liability_equit_total ,2)}}</td>
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