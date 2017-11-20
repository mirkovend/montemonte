@extends('template.theme')
@section('css')
<link href='https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css' rel="stylesheet">
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
				<li class="breadcrumb-item active">Item</li>
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
						<h5>Item Lists</h5>
					</div>
					

					<div class="ibox-content" id="ibox_form">
					
					   

					<div class="pull-right">
					<!-- Button trigger modal -->

		            	<div class="form-group">
			            	<!-- <button id="modal_button" type="button" class="btn btn-md btn-primary" data-toggle="modal" data-target="#myModal">
							  View Category
							</button> -->
							<a class="btn btn-md btn-primary" href="{{ action('CategoryController@create')}}"><span class="fa fa-plus"></span> Create Category</a>
			            	<a class="btn btn-md btn-primary" href="{{ action('ItemController@create')}}"><span class="fa fa-plus"></span> Create Entry</a>
		            	</div>
		            </div>
		            <div class="table-responsive">
						<table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
				    		<thead>	
				    			<tr>
				    				<th>Item Name</th>
				    				<th>Category</th>
				    				<th>Item Type</th>
				                    <th>Chart of Account</th>
				                    <th>Balance</th>
				    				<th width = 80 class="text-center">Action</th>
				    			</tr>
				    		</thead>
				    		<tbody>
				    			{!! $items !!}
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
<script type="text/javascript" src='https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script>
<script type="text/javascript">
	var oTable;
	$(document).ready(function(){
		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	    });
	    $("#item").select2();
		$(".subitem").hide();
		$("#save-edit").hide();
		$("#edit-form").hide();
		

		$(".isSubitem").click(function() {
            if($(this).is(":checked")) {
                $(".subitem").show();
            } else {
                $(".subitem").hide();
            }
        }); 


        function printErrorMsg (msg) {
			$(".print-error-msg").find("ul").html('');
			$(".print-error-msg").css('display','block');
			$.each( msg, function( key, value ) {
				$(".print-error-msg").find("ul").append('<li>'+value+'</li>');
			});
		}
		$('#tableSortable').DataTable({ 

			"ordering": false

		});
		
		

	});
</script>
@endsection