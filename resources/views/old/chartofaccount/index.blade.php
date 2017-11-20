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
</style>
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
  <div class="col-lg-12">
    <h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
    	<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
			<li class="breadcrumb-item">Chart of Accounts</li>
		</ol>
    </h2>
  </div>
</div>



<div class="wrapper wrapper-content animated fadeIn">
	<div class="row">
	    <div class="col-lg-12 col-md-12 col-sm-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title">
	                <h5>Chart of Accounts </h5>

	            </div>
	            <div class="ibox-content">
		            <div class="pull-right">
		            	<div class="form-group">
		            	<a class="btn btn-primary" href="#">Make Recurring Entries</a>
		            	<a class="btn btn-primary" href="#">Make General Journal Entries</a>
		            		<a class="btn btn-primary" href="#" data-toggle="modal" data-target="#myModal">Add Account</a>
		            	</div>
		            </div>
	                <div class="table-responsive">
	                    <table class="table table-bordered sys_table" id="tablez">
					        <thead>
					            <tr>
			                        <th width="50%">Name</th>
			                        <th>Type</th>
			                        <th class="">Balance Total</th>
			                        <th>Manage</th>
			                    </tr>
					        </thead>
					        <tbody>
					        
					        
					       
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



@stop


@section('script')

<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});
		
		$('#subaccount').val([]);
	    $('#disbursement').DataTable();
	    $('#accounttype').val([]).select2({
	    	theme: "bootstrap",
	    	placeholder: "Select a Account Type",
  			allowClear: true,
  			
	    }).on('change',function(){
	   		if($('#accounttype option:selected').text()=="Bank"){
	   			$('#bank').append('<label for="account_number">Bank Account #</label>{!! Form::text("bank_number",null,["class"=>"form-control"]) !!}')
	   		}else{
	   			$('#bank').html("");
	   		}

	   		var id = $(this).val();

	   		$.ajax({
				url: "{{ action('ChartOfAccountController@subtype') }}",
				type:"get",
				dataType:'json',   
                data: { id : id },
                success: function(response){
                	$("#subaccount").html('');
                 	$.each(response,function(index,value){
                 	
                   		$("#subaccount").append('<option value="'+value.id+'">'+value.account_title+'</option>');
                   		$.each(value.sub_account,function(index1,value1){
                   			$("#subaccount").append('<option value="'+value1.id+'">   --> '+value1.account_title+'</option>');
                   		});
                   	});
                   
                }	
				
			});
	   });


	   $('#check').on('click',function(){
		   
		   	if ($('#subaccount').prop('disabled')) {
            $('#subaccount').prop('disabled', false);
            $('#subaccount').val([]).select2({
		    	theme: "bootstrap",
		    	placeholder: "Select a Sub Account",
	  			allowClear: true,
	  			
		    });
	        } else {
	            $('#subaccount').prop('disabled', true);
	            $('#subaccount').val([]).select2({
			    	theme: "bootstrap",
			    	placeholder: "Select a Sub Account",
		  			allowClear: true,
		  			
		    	});
	        }
	   });

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
<script type="text/javascript">
    $(document).ready(function () {
      
      var $table = $('#tablez');
      $table.floatThead({
          responsiveContainer: function($table){
              return $table.closest('.table-responsive');
          }
      });
      $table.floatThead('reflow');

    });

</script>
@stop