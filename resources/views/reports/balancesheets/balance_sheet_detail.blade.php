@extends('template.theme')
@section('css')

<link href='https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css' rel="stylesheet">
<link href='https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css' rel="stylesheet">
<link href='https://cdn.datatables.net/fixedcolumns/3.2.3/css/fixedColumns.dataTables.min.css' rel="stylesheet">

<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

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
        .nav > li.active, .nav > li:focus {
		    background: none;
		    background: none; 
		   
		}
		tr.total-border td:nth-child(7),tr.total-border td:nth-child(10),tr.total-border td:nth-child(11) {
	    
	   
	    font-weight: 900;
		}

		tr.total-border1 td:nth-child(7),tr.total-border1 td:nth-child(8){
	    
	   
	    font-weight: 900;
		}

		.text-bold {
			 font-weight: 900;
		}

		th, td { white-space: nowrap; }
	    div.dataTables_wrapper {
	        width: 100%;
	        margin: 0 auto;
	    }
	    table.dataTable td {
		    box-sizing: content-box;
		    /* margin: 0px!important; */
		    padding: 0px 0px 0px 20px!important;
		}


</style>
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
	<div class="col-lg-12">
		<h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Reports</a></li>
				<li class="breadcrumb-item active">Balance Sheet Details</li>
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
						<h5>Balance Sheet Details Report</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif

						
				     
				
					<div class="ibox-content" id="ibox_form">
						<div class="form-group">
							<a class="btn btn-primary" href="{{action('ReportsController@balance_sheet_view')}}">Balance Sheet</a>
							<a class="btn btn-danger" href="{{action('ReportsController@balance_sheet_detail_view')}}">Balance Sheet Details</a>
						</div>
						<div class="panel panel-default">

						    <div class="panel-heading">
						        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
						    </div>
						    <div class="panel-body">
						     
						        {!! Form::open(['class'=>'form-inline','id'=>'search-form']) !!}

						            <div class="form-group">
						                <label for="name">Search</label>
						                <input type="text" class="form-control" name="item_name" id="name" placeholder="Search" autocomplete="off">
						            </div>
						         

						           	<button type="submit" id="sub" class="btn btn-primary">Search</button>
						       	<!-- 	<input type="checkbox" name="checkbox" id="checkbox" value="1"  data-toggle="toggle" data-on="Collapse" data-off="Expand" data-onstyle="success" data-offstyle="danger"> -->
						       		
						        {!! Form::close() !!}
						    </div>
						</div>
					
						
					    <table id="balancesheet" class="stripe row-border order-column" cellspacing="0" width="100%">
							<thead>
								<th>Account</th>
								<th >Type</th>
								<th >Date</th>
								<th >Ref</th>
								<th >Name</th>
								<th >Memo</th>
								<th >Dept</th>
								<th >Coa</th>
								<th >Amount</th>
								<th >Balance</th>
							</thead>
							<tbody>
								
							</tbody>
						</table>		
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
<script type="text/javascript" src='https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js'></script>
<script type="text/javascript" src='https://cdn.datatables.net/fixedcolumns/3.2.3/js/dataTables.fixedColumns.min.js'></script>


<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script>

<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
 
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>

<script type="text/javascript">
	var oTable;
	var cTable;
	var sTable;
	var $from,$to;
	var agingsum;
	var dates,dates2;
	$(document).ready(function(){
		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	    });
	  	$('#wew').bootstrapToggle();
		$('.daterange').daterangepicker({
		 		"autoApply":true,
			autoUpdateInput: false,
			locale: {
				cancelLabel: 'Clear',
				format: 'YYYY/MM/DD',
			},
			singleDatePicker: true,
        	showDropdowns: true,
		});

		$('.dates').daterangepicker({
		 		"autoApply":false,
			autoUpdateInput: false,
			locale: {
				cancelLabel: 'Clear',
				format: 'YYYY/MM/DD',
			},
			singleDatePicker: true,
        	showDropdowns: true,
		});
		$('#firstdate').on('apply.daterangepicker', function(ev, picker) {
			$(this).text(picker.startDate.format('MMMM D, YYYY'));
			dates = picker.startDate.format('YYYY/MM/DD');
			$('#search-form').submit();
		});

		$('#firstdate').on('cancel.daterangepicker', function(ev, picker) {
		    $(this).val('');
		});

		$('#seconddate').on('apply.daterangepicker', function(ev, picker) {
			$(this).text(picker.startDate.format('MMMM D, YYYY'));
			dates2 = picker.startDate.format('YYYY/MM/DD');
			$('#search-form').submit();
		});

		$('#seconddate').on('cancel.daterangepicker', function(ev, picker) {
		    $(this).val('');
		});


		$('#balancesheet2 #firstdate').on('apply.daterangepicker', function(ev, picker) {
			$(this).text(picker.startDate.format('MMMM D, YYYY'));
			dates = picker.startDate.format('YYYY/MM/DD');
			$('#search-form1').submit();
		});

		$('#balancesheet2 #firstdate').on('cancel.daterangepicker', function(ev, picker) {
		    $(this).val('');
		});

		$('#balancesheet2 #seconddate').on('apply.daterangepicker', function(ev, picker) {
			$(this).text(picker.startDate.format('MMMM D, YYYY'));
			dates2 = picker.startDate.format('YYYY/MM/DD');
			$('#search-form1').submit();
		});

		$('#balancesheet2 #seconddate').on('cancel.daterangepicker', function(ev, picker) {
		    $(this).val('');
		});





		var tables = $('#balancesheet');
	    oTable = tables.DataTable({
	        processing: true,
	        serverSide: true,
			dom: "<'row'<'col-xs-12'<'col-xs-6'l><'col-xs-6'p>>r>"+
			"<'row'<'col-xs-12't>>"+
			"<'row'<'col-xs-12'<'col-xs-6'i><'col-xs-6'p>>>",
	        "ordering": false,
	    	"bPaginate": false,
	        "bLengthChange": false,
		    "bFilter": true,
		    "bInfo": false,
	 	scrollY:        "500px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
        fixedColumns:   {
            leftColumns: 1,
            rightColumns: 0
        },
        responsive:true,
       	columnDefs: [
            { width: '2%', targets: 0 }
        ],
		    ajax: {
	            url: '{{action("ReportsController@balance_sheet_detail_data")}}',
	            data: function (d) {
	                d.item_name = $('input[name=item_name]').val();
	               	d.daterange = dates;
	               	d.daterange2 = dates2;
	               	d.checkbox = $('input[name=checkbox]:checked').val();
	            }
	        },
	        "columns": [
	            { "data": "account" },
	            { "data": "type" },
	            { "data": "date" },
	            { "data": "ref" },
	            { "data": "name" },
	            { "data": "memo" },
	            { "data": "dept" },
	            { "data": "coa" },
	            { "data": "amount" },
	            { "data": "balance" },
        	]
	    });
	    $('#search-form').on('submit', function(e) {
	        oTable.draw();
	        e.preventDefault();
	    });

	  //   var tables1 = $('#balancesheet2');
	  //   cTable = tables1.DataTable({
	  //       processing: true,
	  //       serverSide: true,
			// dom: "<'row'<'col-xs-12'<'col-xs-6'l><'col-xs-6'p>>r>"+
			// "<'row'<'col-xs-12't>>"+
			// "<'row'<'col-xs-12'<'col-xs-6'i><'col-xs-6'p>>>",
	  //       "ordering": false,
	  //   	"bPaginate": false,
	  //       "bLengthChange": false,
		 //    "bFilter": true,
		 //    "bInfo": false,
	 	// 	scrollY:        "300px",
   //      scrollX:        true,
   //      scrollCollapse: true,
   //      paging:         false,
   //      fixedColumns:   {
   //          leftColumns: 2
   //      },
		 //    ajax: {
	  //           url: '{{action("ReportsController@balance_sheet_detail_data")}}',
	  //           data: function (d) {
	  //               d.item_name = $('input[name=item_name1]').val();
	  //              	d.daterange = dates;
	  //              	d.daterange2 = dates2;
	  //           }
	  //       },
	        // "columns": [
	        //     { "data": "account" },
	        //     { "data": "type" },
	        //     { "data": "date" },
	        //     { "data": "name" },
   //      	]
	  //   });
	  //   $('#search-form1').on('submit', function(e) {
	  //       cTable.draw();
	  //       e.preventDefault();
	  //   });

	   
	    	
	   	$('#checkbox').change(function() {
	      $('#search-form').submit();
	    })

	});
</script>
@endsection