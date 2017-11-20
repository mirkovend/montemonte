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
			<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Disbursement</a></li>
			<li class="breadcrumb-item active">Add Check Voucher</li>
		</ol>
	</h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
	<div class="row">
	    <div class="col-lg-12 col-md-12 col-sm-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title">
	                <h5>Check Voucher Info</h5>

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
	            	{!! Form::open()!!}
		                <div class="row">
		                	<div class="col-md-4">
			                	<div class="form-group">
			                		<label>Payee</label>
			                		{!! Form::select("supplier_id", $account, null, ["placeholder" => "Select Account","class"=>"form-control js-example-basic-single "])!!}
								   
								</div>
		                	</div>
		                	<div class="col-md-4">
		                		<div class="form-group">
			                		<label>Reference #</label>
			                		{!! Form::text('reference',null,['class'=>'form-control'])!!}
								   
								</div>
		                	</div>
		                	<div class="clearfix"></div>
		                	<hr>
		                	<div class="col-md-3">
		                		<div class="form-group">
									<label>Date</label>
								    <input type="text" class="form-control" value="" name="date" id="date" datepicker="" data-date-format="yyyy-mm-dd" data-auto-close="true" >
								</div>
		                	</div>
		                	<div class="col-md-3">
		                		<div class="form-group">
									<label>Voucher Number</label>
								    <input type="text" class="form-control" value="" name="voucher_number" >
								</div>
		                	</div>
		                	<div class="col-md-3">
		                		<div class="form-group">
									<label>Due Date</label>
								    <input type="text" class="form-control" value=""  datepicker="" data-date-format="yyyy-mm-dd" name="due_date">
								</div>
		                	</div>
		                	<div class="col-md-3">
		                		<div class="form-group">
									<label>Amount</label>
								    <input type="text" class="form-control amount" id="amount" name="amount">
								</div>
		                	</div>
		                	<div class="col-md-12">
		                		<div class="form-group">
									<label>Explanation</label>
								    <textarea class="form-control" name="explanation"></textarea>
								   
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
									    			{!! Form::select("chart_of_account_id[]", $chart, null, ["placeholder" => "Select Account","class"=>"form-control account"])!!}
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
	    $(".js-example-basic-single").select2({
	    	 theme: "bootstrap"
	    });

	    $('[data-toggle="datepicker"]').datepicker();
	    $('.amount').autoNumeric('init', {

		    aSign: '₱ ',
		    dGroup: 3,
		    aPad: true,
		    pSign: 'p',
		    aDec: '.',
		    aSep: ','

		});

		$(".account").select2({
		  theme: "bootstrap"
		});

		$('#journal').on('click','#addmore',function(e){
			e.preventDefault();
			var data = '<tr>';
			data +='<td>{!! Form::select("chart_of_account_id[]", $chart, null, ["placeholder" => "Select Account","class"=>"form-control account"])!!}</td>';
			data += '<td><input type="text" class="form-control amount" id="amount" name="debit[]"></td>';
			data += '<td><input type="text" class="form-control amount" id="amount" name="credit[]"></td>';
			data += '<td><button class="btn btn-danger" id="remove"><i class="fa fa-minus-circle" aria-hidden="true"></i></button></td>'
			$('#journal tbody').append(data);
			$(".account").select2({
			  theme: "bootstrap"
			});
			$('.amount').autoNumeric('init', {

			    aSign: '₱ ',
			    dGroup: 3,
			    aPad: true,
			    pSign: 'p',
			    aDec: '.',
			    aSep: ','

			});
		});

		$('#journal').on('click','#remove',function(e){
			e.preventDefault();
			$(this).parents('tr').remove();
		});
	});
</script>
@stop