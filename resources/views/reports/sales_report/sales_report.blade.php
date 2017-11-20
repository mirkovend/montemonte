@extends('template.theme')
@section('css')
<link href='https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css' rel="stylesheet">
<link href='https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css' rel="stylesheet">
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

		tr.total-border1 td:nth-child(2){
	    
	   
	    font-weight: 900;
		}

		.text-bold {
			 font-weight: 900;
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
				<li class="breadcrumb-item active">Sales</li>
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
						<h5>Sales Report</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active text-info"><a href="#Idetails" aria-controls="Idetails" role="tab" data-toggle="tab">Item Details</a></li>
							<li role="presentation" id="Custdetails"><a href="#Cdetails" aria-controls="Cdetails" role="tab" data-toggle="tab">Customer Details</a></li>
							<li role="presentation" id="Custsummary"><a href="#Csummary" aria-controls="Csummary" role="tab" data-toggle="tab">Customer Summary</a></li>
						</ul>
						<div class="tab-content">

							<div role="tabpanel" class="tab-pane active" id="Idetails">
								<div class="table-responsive">
									<div class="panel panel-default">
									    <div class="panel-heading">
									        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
									    </div>
									    <div class="panel-body">
									        
									        {!! Form::open(['class'=>'form-inline','id'=>'search-form']) !!}

									            <div class="form-group">
									                <label for="name">Item Name</label>
									                <input type="text" class="form-control" name="item_name" id="name" placeholder="search name" autocomplete="off">
									            </div>
									            <div class="form-group">
									                <label for="name">Date Range</label>
									                <input type="text" class="form-control daterange" name="daterange" id="name" value="">
									            </div>

									            <button type="submit" id="sub" class="btn btn-primary">Search</button>
									        </form>
									    </div>
									</div>
								    <table class="table table-hover" id="sales-report" width="100%">
										<thead>
										  <th>Item</th>
										  <th>Type</th>
										  <th>Date</th>
										  <th>Name</th>
										  <th>Ref</th>
										  <th>Memo</th>
										  <th>Qty</th>
										  <th>U/M</th>
										  <th>Sales Price</th>
										  <th>Amount</th>
										  <th>Balance</th>
										</thead>
										<tbody>
											
										</tbody>
									</table>								
								</div>
							</div>

							<div role="tabpanel" class="tab-pane" id="Cdetails">
								<div class="table-responsive">
									<div class="panel panel-default">
									    <div class="panel-heading">
									        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
									    </div>
									    <div class="panel-body">
									        
									        {!! Form::open(['class'=>'form-inline','id'=>'search-form1']) !!}

									            <div class="form-group">
									                <label for="name">Search Name</label>
									                <input type="text" class="form-control" name="item_name1" id="name" placeholder="search name" autocomplete="off">
									            </div>
									            <div class="form-group">
									                <label for="name">Date Range</label>
									                <input type="text" class="form-control daterange" name="daterange1" id="name" value="">
									            </div>

									            <button type="submit" id="sub" class="btn btn-primary">Search</button>

									            <!-- <button type="submit" id="sub" class="btn btn-primary">Search</button> -->
									        </form>
									    </div>
									</div>
								    <table class="table table-hover" id="cust-sales-report" width="100%">
										<thead>
										  <th>Customer Name</th>
										  <th>Type</th>
										  <th>Date</th>
										  <th>Name</th>
										  <th>Ref</th>
										  <th>Memo</th>
										  <th>Qty</th>
										  <th>U/M</th>
										  <th>Sales Price</th>
										  <th>Amount</th>
										  <th>Balance</th>
										</thead>
										<tbody>
											
										</tbody>
									</table>								
								</div>

							</div>
	
							<div role="tabpanel" class="tab-pane" id="Csummary">
								<div class="table-responsive">
									<div class="panel panel-default">
									    <div class="panel-heading">
									        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
									    </div>
									    <div class="panel-body">
									        
									        {!! Form::open(['class'=>'form-inline','id'=>'search-form2']) !!}

									            <div class="form-group">
									                <label for="name">Search Name</label>
									                <input type="text" class="form-control" name="item_name2" id="name" placeholder="search name" autocomplete="off">
									            </div>
									            <div class="form-group">
									                <label for="name">Date Range</label>
									                <input type="text" class="form-control daterange" name="daterange2" id="name" value="">
									            </div>

									            <button type="submit" id="sub" class="btn btn-primary">Search</button>
									        </form>
									    </div>
									</div>
								    <table class="table table-hover" id="cust-summary-report" width="100%">
										<thead>
										  <th></th>
										  <th>Amount</th>
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

		</div>

	</div>
</div>
</div><!-- end wrapper -->
@endsection

@section('script')
<script type="text/javascript" src='https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js'></script>
<script type="text/javascript" src='https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js'></script>

<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script>

<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
 
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript">
	var oTable;
	var cTable;
	var sTable;
	$(document).ready(function(){
		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	    });
		// // $('table').DataTable({ "ordering": false});
		// var $table = $('#tablez');
	 //    $table.floatThead({
		// 	responsiveContainer: function($table){
		// 		return $table.closest('.table-responsive');
		// 	}
	 //    });
	 //    $table.floatThead('reflow');
	// $('input[name="daterange"]').daterangepicker({
	// 	"autoApply":true,
	// 	autoUpdateInput: false,
	// 	locale: {
	// 		cancelLabel: 'Clear'
	// 	}

	// });
	$('.daterange').daterangepicker({
	 		"autoApply":true,
		autoUpdateInput: false,
		locale: {
			cancelLabel: 'Clear',
			format: 'YYYY/MM/DD',
		}
	});

	$('.daterange').on('apply.daterangepicker', function(ev, picker) {
		$(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
	});

	$('.daterange').on('cancel.daterangepicker', function(ev, picker) {
	    $(this).val('');
	});
	var tables = $('#sales-report');
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
	    responsive: true,

	    fixedHeader: {
            header: true,
            footer: true
        },
        ajax: {
            url: '{{action("ReportsController@sales_report_detail")}}',
            data: function (d) {
                d.item_name = $('input[name=item_name]').val();
                d.daterange = $('input[name="daterange"]').val();
            }
        },
        columns: [
            {data: 'item_name', name: 'item_name'},
            {data: 'type', name: 'type'},
            {data: 'date', name: 'date'},
            {data: 'source', name: 'source'},
            {data: 'ref', name: 'ref'},
            {data: 'memo', name: 'memo'},
            
            {data: 'qty', name: 'qty'},
            {data: 'um', name: 'um'},
            {data: 'price', name: 'price'},
            {data: 'amount', name: 'amount'},
            {data: 'balance', name: 'balance'},

        ]
    });
    $('#search-form').on('submit', function(e) {
        oTable.draw();
        e.preventDefault();
    });


    var tables1 = $('#cust-sales-report');
    cTable = tables1.DataTable({
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
	    responsive: true,

	    fixedHeader: {
            header: true,
            footer: true
        },
        ajax: {
            url: '{{action("ReportsController@sales_report_cust")}}',
            data: function (d) {
                d.item_name = $('input[name=item_name1]').val();
                d.daterange = $('input[name="daterange1"]').val();
            }
        },
        columns: [
            {data: 'cust_name', name: 'cust_name'},
            {data: 'type', name: 'type'},
            {data: 'date', name: 'date'},
            {data: 'source', name: 'source'},
            {data: 'ref', name: 'ref'},
            {data: 'memo', name: 'memo'},
            
            {data: 'qty', name: 'qty'},
            {data: 'um', name: 'um'},
            {data: 'price', name: 'price'},
            {data: 'amount', name: 'amount'},
            {data: 'balance', name: 'balance'},

        ]
    });
    $('#search-form1').on('submit', function(e) {
        cTable.draw();
        e.preventDefault();
    });

        var tables2 = $('#cust-summary-report');
    sTable = tables2.DataTable({
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
	    responsive: true,

	    fixedHeader: {
            header: true,
            footer: true
        },
        ajax: {
            url: '{{action("ReportsController@sales_report_cust_summary")}}',
            data: function (d) {
                d.item_name = $('input[name=item_name2]').val();
                d.daterange = $('input[name="daterange2"]').val();
            }
        },
        columns: [
            {data: 'cust_name', name: 'cust_name'},
            {data: 'balance', name: 'balance'},

        ]
    });
    $('#search-form2').on('submit', function(e) {
        sTable.draw();
        e.preventDefault();
    });
   

	});
</script>
@endsection