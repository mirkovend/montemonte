@extends('template.theme')
<?php use Carbon\Carbon;?>
@section('css')
<link href='{{ asset("assets/datatable/jquery.dataTables.min.css") }}' rel="stylesheet">
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
  <div class="col-lg-12">
    <h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
    	<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
			<li class="breadcrumb-item">Check Voucher Lists</li>
		</ol>
    </h2>
  </div>
</div>



<div class="wrapper wrapper-content animated fadeIn">
	<div class="row">
	    <div class="col-lg-12 col-md-12 col-sm-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title">
	                <h5>Check Voucher Lists </h5>

	            </div>
	            <div class="ibox-content">
	                <div class="table-responsive">
	                    <table class="table table-bordered sys_table responsive" id="disbursement">
					        <thead>
					            <tr>
					            	<th>Date</th>
					            	<th>Reference</th>
			                        <th>Voucher Number</th>
			                        <th>Payee</th>
			                        <th>Due Date</th>
			                        <th class="text-right">Amount</th>
			                        <th>Status</th>
			                        <th>Manage</th>
			                    </tr>
					        </thead>
					        <tbody>
					        	@foreach($disbursements as $disbursement)
						        	<tr>
						        		<td>
						        			{{Carbon::parse($disbursement->date)->ToFormattedDateString()}}
						        		</td>
						        		<td>
						        			{{$disbursement->reference}}
						        		</td>
						        		<td>
						        			{{$disbursement->voucher_number}}
						        		</td>
						        		<td>
						        			{{ $disbursement->supplier->lname }}, {{ $disbursement->supplier->fname }}
						        		</td>
						        		<td>
						        			{{Carbon::parse($disbursement->due_date)->ToFormattedDateString()}}
						        		</td>
						        		<td>
						        			{{number_format($disbursement->amount,2)}}
						        		</td>
						        		<td>
						        			{{$disbursement->status}}
						        		</td>
						        		<td>
						        			<div class="btn-group">
											  <a  class="btn btn-sm btn-primary" href="#"><i class="fa fa-share" aria-hidden="true"></i> Release</a>
											  <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											    <span class="caret"></span>
											    <span class="sr-only">Toggle Dropdown</span>
											  </button>
											  <ul class="dropdown-menu">
											  	<li><a href="#"><i class="fa fa-info-circle" aria-hidden="true"></i> view</a></li>
											    <li><a href="#"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a></li>
											    <li><a href="#"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a></li>
											    <li role="separator" class="divider"></li>
											    <li><a href="#"><i class="fa fa-print" aria-hidden="true"></i> Print</a></li>
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

	   <!-- Widget-1 end-->

	    <!-- Widget-2 end-->
	</div> <!-- Row end-->


<!-- Row end-->


<!-- Row end-->

	<div id="ajax-modal" class="modal container fade-scale" tabindex="-1" style="display: none;"></div>
</div>
@stop


@section('script')

<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>
<script type="text/javascript">
	$(document).ready(function() {
	    $('#disbursement').DataTable();
	} );
</script>
@stop