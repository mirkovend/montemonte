@extends('template.theme')

@section('css')
<link href='{{ asset("assets/datatable/jquery.dataTables.min.css") }}' rel="stylesheet">
<link href='{{ asset("assets/ui/lib/dp/dist/datepicker.min.css") }}' rel="stylesheet">

@stop
@section('content')
<div class="row wrapper white-bg page-heading">
  <div class="col-lg-12">
    <h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Chart of Account</a></li>
			<li class="breadcrumb-item active">Add Recurring Entry</li>
		</ol>
	</h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
	<div class="row">
	    <div class="col-lg-12 col-md-12 col-sm-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title">
	                <h5>Recurring Info</h5>

	            </div>
	            <div class="ibox-content">
	            	@if (count($errors) > 0)
					    <div class="alert alert-danger" id="">
					        <ul>
					            @foreach ($errors->all() as $error)
					                <li>{{ $error }}</li>
					            @endforeach
					        </ul>
					    </div>
					@endif
	            	{!! Form::open(['action'=>'RecurringController@store'])!!}
		                <div class="row">
		                	<div class="col-md-4">
			                	<div class="form-group">
			                		<label>Account</label>
			                		{!! Form::select("coa_id", $charts, null, ["placeholder" => "Select Account","class"=>"form-control js-example-basic-single ","id"=>"fixedasset"])!!}
								   
								</div>
		                	</div>
		                	<div class="col-md-4">
		                		<div class="form-group">
			                		<label>Amount</label>
			                		<input type="text" class="form-control" name="amount" id="amount_fa" disabled>
								   	<input type="hidden" class="form-control" name="amount" id="amount_fa1" >
								</div>
		                	</div>
		                	<div class="clearfix"></div>
		                	<hr>
		                	<div class="col-md-4">
		                		<div class="form-group">
									<label>Type</label>
								    {!! Form::select("type", ['monthly'=>'Monthly','yearly'=>'Yearly'], null, ["placeholder" => "Select Type","class"=>"form-control js-example-basic-single "])!!}
								</div>
		                	</div>
		                	<div class="col-md-4">
		                		<div class="form-group">
									<label>Start Date #</label>
			                		<input type="text" class="form-control" value="" name="sdate" id="date" datepicker="" data-date-format="yyyy-mm-dd" data-auto-close="true" >
								   
								</div>
		                	</div>
		                	<div class="col-md-4">
		                		<div class="form-group">
									<label>Cycle Number</label>
								    <input type="number" class="form-control cycle" value="" name="cycle_number" >
								</div>
		                	</div>
		                	<div class="clearfix"></div>
		        			<hr>
				            <div class="col-md-12">
				             <h3>Journal Entry </h5>
				            	<div class="table-responsive">
				                    <table class="table table-bordered responsive sys_table" id="journal">
								        <thead>
								            <tr>
								            	<th width="30%">Account Title</th>
						                        <th>Debit</th>
						                        <th>Credit</th>
						                        <th >Action</th>
						                    </tr>
								        </thead>
								        <tbody>
								        	<tr>
								        		<td>
									    			{!! Form::select("chart_of_account_id[]", $coa_expense_fixed, null, ["placeholder" => "Select Account","class"=>"form-control account"])!!}
												</td>
								        		<td><input type="text" class="form-control amount" id="amount" name="debit[]"></td>
								        		<td><input type="text" class="form-control amount" id="amount" name="credit[]"></td>
								        		<td><button class="btn btn-primary" id="addmore"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>
								        	</tr>
								        </tbody>
								    </table>
				                </div>
				                <div class="form-group">
								    <button class="btn btn-primary">Save Entry</button>
								</div>
				            </div>
		                </div>
	                {!! Form::close()!!}
	            </div>
	        </div>
	        
	    </div>

	   <!-- Widget-1 end-->

	    <!-- Widget-2 end-->
	</div> <!-- Row end-->
	<div id="ajax-modal" class="modal container fade-scale" tabindex="-1" style="display: none;"></div>
</div>
@stop


@section('script')

<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>

        <script type="text/javascript" src='{{ asset("assets/ui/lib/dp/dist/datepicker.min.js") }}'></script>
        <script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
        <script type="text/javascript" src='{{ asset("assets/ui/lib/deposit.js") }}'></script>
<script type="text/javascript">
	$(document).ready(function() {
	    $("#fixedasset").select2({
	    	 theme: "bootstrap",
	    	 placeholder:'Select Account'
	    }).change(function(){
	    	var id = $(this).val();
	    	$.ajax({
				url: "{{ Route('amount') }}",
				type:"get",  
                data: { id : id },
                success: function(response){
                	$("#amount_fa").val(response);
                	$("#amount_fa1").val(response);
                	console.log(response);
                }	
				
			});
	    });

	    $('[data-toggle="datepicker"]').datepicker();
	    $('.amount').autoNumeric('init', {

		    aSign: '₱ ',
		    dGroup: 3,
		    aPad: true,
		    pSign: 'p',
		    aDec: '.',
		    aSep: ',',
		    decimalPlacesOverride: '10',

		});

	    

		$(".account").select2({
		  	theme: "bootstrap",
	    	placeholder:'Select Account'

		});

		$('#journal').on('click','#addmore',function(e){
			e.preventDefault();
			var data = '<tr>';
			data +='<td>{!! Form::select("chart_of_account_id[]", $coa_expense_fixed, null, ["placeholder" => "Select Account","class"=>"form-control account"])!!}</td>';
			data += '<td><input type="text" class="form-control amount" id="amount" name="debit[]"></td>';
			data += '<td><input type="text" class="form-control amount" id="amount" name="credit[]"></td>';
			data += '<td><button class="btn btn-danger" id="remove"><i class="fa fa-minus-circle" aria-hidden="true"></i></button></td>'
			$('#journal tbody').append(data);
			$(".account").select2({
			  	theme: "bootstrap",
	    		placeholder:'Select Account'
			});
			$('.amount').autoNumeric('init', {

			    aSign: '₱ ',
			    dGroup: 3,
			    aPad: true,
			    pSign: 'p',
			    aDec: '.',
			    aSep: ',',
		    	decimalPlacesOverride: '10',


			});
		});

		$('#journal').on('click','#remove',function(e){
			e.preventDefault();
			$(this).parents('tr').remove();
		});
	});
</script>
@stop