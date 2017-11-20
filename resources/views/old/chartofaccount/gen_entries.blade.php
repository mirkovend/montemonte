@extends('template.theme')
<?php use Carbon\Carbon;?>
@section('css')
<link href='{{ asset("assets/datatable/jquery.dataTables.min.css") }}' rel="stylesheet">
<style type="text/css">
	.select2-container {
    width: 100% !important;
    padding: 0;
}
</style>
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
  <div class="col-lg-12">
    <h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
    	<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Chart of Accounts</a></li>
			<li class="breadcrumb-item">General Journal Entries</li>
			
		</ol>
    </h2>
  </div>
</div>



<div class="wrapper wrapper-content animated fadeIn">
	<div class="row">
	    <div class="col-lg-12 col-md-12 col-sm-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title">
	                <h5>General Journal Entries</h5>

	            </div>
	             {!! Form::open(["action"=>"ChartOfAccountController@store_gje"])!!}
	            <div class="ibox-content">
	            	<div class="row">
			            <div class="col-md-3">
			            	<div class="form-group">
			            		<input type="date" name="date" value="{{ Carbon::now()->toDateString()}}" class="form-control">
			            	</div>
			            </div>
		            </div>
	                <div class="table-responsive">
	               
	                    	<table class="table" id="journal">
				      			<thead>
				      				<th width="30%">Account</th>
				      				<th>Debit</th>
				      				<th>Credit</th>
				      				<th>Memo</th>
				      				<th>Payee</th>
				      				<th></th>
				      			</thead>
				      			
				      			<tbody>
				      				<tr>
				      					<td>{!! Form::select('account[]',$charts,null,['class'=>'form-control select account','id'=>'account','width'=>'100%',"placeholder" => "Select Account"]) !!}</td>
				      					<td>{!! Form::text('debit[]',null,['class'=>'form-control amount'])!!}</td>
				      					<td>{!! Form::text('credit[]',null,['class'=>'form-control amount'])!!}</td>
				      					<td>{!! Form::text('memo[]',null,['class'=>'form-control'])!!}</td>
				      					<td>{!! Form::select('payee[]',$account,null,['class'=>'form-control select payee','id'=>'accounttype','width'=>'100%',"placeholder" => "Select Payee"]) !!}</td>
				      					<td><button class="btn btn-primary" id="addmore"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>
				      				</tr>
				      			</tbody>
				      			
				      		</table>
				      		 <button type="submit" class="btn btn-primary">Save Entry</button>
	                {!! Form::close() !!}
	                </div>
	               
	            </div>
	        </div>
	        
	    </div>

	   <!-- Widget-1 end-->

	    <!-- Widget-2 end-->
	</div>

@stop


@section('script')

<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script type="text/javascript">
	$(document).ready(function() {
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});
		
		$('.payee').select2({
	    	theme: "bootstrap",
	    	placeholder: "Select Payee",
	    });
		$('.account').select2({
	    	theme: "bootstrap",
	    	placeholder: "Select a Account",
	    });
		$('.amount').autoNumeric('init', {

			    dGroup: 3,
			    aPad: true,
			    pSign: 'p',
			    aDec: '.',
			    aSep: ','

			});
	    $('#journal').on('click','#addmore',function(e){
			e.preventDefault();
			var data = '<tr>';
			data +='<td>{!! Form::select("account[]", $charts, null, ["placeholder" => "Select Account","class"=>"form-control account1"])!!}</td>';
			data += '<td><input type="text" class="form-control amount" id="amount" name="debit[]"></td>';
			data += '<td><input type="text" class="form-control amount" id="amount" name="credit[]"></td>';

			data += '<td><input type="text" class="form-control amount" id="amount" name="memo[]"></td>';
			data +='<td>{!! Form::select("payee[]", $account, null, ["placeholder" => "Select Payee","class"=>"form-control account payee1"])!!}</td>';
			data += '<td><button class="btn btn-danger" id="remove"><i class="fa fa-minus-circle" aria-hidden="true"></i></button></td>'
			$('#journal tbody').append(data);
			$(".account1").select2({
			  theme: "bootstrap",
			  
	    	placeholder: "Select a Account",
			});
			$(".payee1").select2({
			  theme: "bootstrap",


	    		placeholder: "Select Payee",
			});
			$('.amount').autoNumeric('init', {

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