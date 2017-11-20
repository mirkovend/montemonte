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

		tr.total-border1 td:nth-child(7),tr.total-border1 td:nth-child(8){
	    
	   
	    font-weight: 900;
		}

		.text-bold {
			 font-weight: 900;
		}
		.widthFix{
		  width:auto !important;
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
				<li class="breadcrumb-item active">Account Receivable</li>
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
						<h5>Account Receivable Report</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active text-info"><a href="#CBdetails" aria-controls="CBdetails" role="tab" data-toggle="tab">Customer Balance Details</a></li>
							<li role="presentation" id="CustSumm"><a href="#Cdetails" aria-controls="Cdetails" role="tab" data-toggle="tab">Customer Balance Summary</a></li>
							<li role="presentation" id="aging_detail"><a href="#agingdetail" aria-controls="agingdetail" role="tab" data-toggle="tab">Aging Detail</a></li>
							<li role="presentation" id="aging_summary"><a href="#agingsummary" aria-controls="agingsummary" role="tab" data-toggle="tab">Aging Summary</a></li>
						</ul>
						<div class="tab-content">

							<div role="tabpanel" class="tab-pane active" id="CBdetails">
								<div class="table-responsive">
									<div class="panel panel-default">
									    <div class="panel-heading">
									        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
									    </div>
									    <div class="panel-body">
									        
									        {!! Form::open(['class'=>'form-inline','id'=>'search-form']) !!}

									            <div class="form-group">
									                <label for="name">Search</label>
									                <input type="text" class="form-control" name="item_name" id="name" placeholder="search " autocomplete="off">
									            </div>
									            <div class="form-group">
									                <label for="name">Date Range</label>
									                <input type="text" class="form-control daterange" name="daterange" id="name" value="">
									            </div>
									            <button type="submit" id="sub" class="btn btn-primary">Search</button>
									            <!-- <button type="submit" id="sub" class="btn btn-primary">Search</button> -->
									        </form>
									    </div>
									</div>
								    <table class="table table-hover" id="customer-balance-detail" style="width:100%">
										<thead>
											<th>Name</th>
											<th>Type</th>
											<th>Date</th>
											<th>Ref</th>
											<th>Account</th>
											<th>Class</th>
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
									                <label for="name">Search</label>
									                <input type="text" class="form-control" name="item_name1" id="name" placeholder="search" autocomplete="off">
									            </div>
									            <div class="form-group">
									                <label for="name">Date Range</label>
									                <input type="text" class="form-control daterange" name="daterange1" id="name" value="">
									            </div>


									            <button type="submit" id="sub" class="btn btn-primary">Search</button>
									        {!! Form::close() !!}
									    </div>
									</div>
								    <table class="table table-hover" id="customer-balance-summary" style="width:100%">
										<thead>
										  <th>Name</th>
										  <th>Amount</th>
										</thead>
										<tbody>
											
										</tbody>
									</table>								
								</div>

							</div>
	
							<div role="tabpanel" class="tab-pane" id="agingdetail">
								<div class="table-responsive">
									<div class="panel panel-default">
									    <div class="panel-heading">
									        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
									    </div>
									    <div class="panel-body">
									        
									        {!! Form::open(['class'=>'form-inline','id'=>'search-form2']) !!}

									            <div class="form-group">
									                <label for="name">Search</label>
									                <input type="text" class="form-control" name="item_name2" id="name" placeholder="Search" autocomplete="off">
									            </div>
									            <div class="form-group">
									                <label for="name">Date Range</label>
									                <input type="text" class="form-control daterange" name="daterange2" id="name" value="">
									            </div>

									           <button type="submit" id="sub" class="btn btn-primary">Search</button>
									        {!! Form::close() !!}
									    </div>
									</div>
								    <table class="table table-hover" id="aging-detail" style="width:100%">
										<thead>
										  <th>Aging Type</th>
										  <th>Type</th>
										  <th>Date</th>
										  <th>Ref</th>
										  <th>P.O #</th>
										  <th>Name</th>
										  <th>Terms</th>
										  <th>Due Date</th>
										  <th>Class</th>
										  <th>Aging</th>
										  <th>Balance</th>
										</thead>
										<tbody>
											
										</tbody>
									</table>								
								</div>

							</div>

							<div role="tabpanel" class="tab-pane" id="agingsummary">
								<div class="table-responsive">
									<div class="panel panel-default">
									    <div class="panel-heading">
									        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
									    </div>
									    <div class="panel-body">
									        
									        {!! Form::open(['class'=>'form-inline','id'=>'search-form3']) !!}

									            <div class="form-group">
									                <label for="name">Search</label>
									                <input type="text" class="form-control" name="item_name3" id="name" placeholder="Search" autocomplete="off">
									            </div>
									            <div class="form-group">
									                <label for="name">Date Range</label>
									                <input type="text" class="form-control daterange" name="daterange3" id="name" value="">
									            </div>

									           <button type="submit" id="sub" class="btn btn-primary">Search</button>
									        {!! Form::close() !!}
									    </div>
									</div>
								    <table class="table table-hover" id="aging-summary" style="width:100%">
										<thead>
										  <th></th>
										  <th>Current</th>
										  <th>1 - 30</th>
										  <th>31 - 60</th>
										  <th>61 - 90</th>
										  <th>> 90</th>
										  <th>Total</th>
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
	var $from,$to;
	var agingsum;
	$(document).ready(function(){
		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	    });
	  
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


	var tables = $('#customer-balance-detail');
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


        ajax: {
            url: '{{action("ReportsController@customer_balance_detail")}}',
            data: function (d) {
                d.item_name = $('input[name=item_name]').val();
				d.daterange = $('input[name=daterange]').val();
            }
        },
        columns: [
            {data: 'cust_name', name: 'cust_name'},
            {data: 'type', name: 'type'},
            {data: 'date', name: 'date'},
            {data: 'ref', name: 'ref'},
            {data: 'account', name: 'account'},
            {data: 'class', name: 'class'},
            {data: 'amount', name: 'amount'},
            {data: 'balance', name: 'balance'},

        ]
    });
    $('#search-form').on('submit', function(e) {
        oTable.draw();
        e.preventDefault();
    });
    $('.floatThead-container').addClass('widthFix');
    tables.floatThead({
		responsiveContainer: function(tables){
			return tables.closest('.table-responsive');
		}
    });
    
    
    tables.floatThead('reflow');

    $('#navbar-minimalize').on('click',function(){
    	tables.floatThead('reflow');
    });
    $('#CustSumm').click(function(){
        if (!$.fn.dataTable.isDataTable('#customer-balance-summary')) {  
        	var tables1 = $('#customer-balance-summary');
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

			  
		        ajax: {
		            url: '{{action("ReportsController@customer_balance_summary")}}',
		            data: function (d) {
		                d.item_name = $('input[name=item_name1]').val();
		                d.daterange = $('input[name=daterange1]').val();
		            }
		        },
		        columns: [
		 			{data: 'cust_name', name: 'cust_name'},
		            {data: 'balance', name: 'balance'},

		        ]
		    });
		    $('#search-form1').on('submit', function(e) {
		        cTable.draw();
		        e.preventDefault();
		    });
		    tables1.floatThead({
				responsiveContainer: function(tables1){
					return tables1.closest('.table-responsive');
				}
		    });
		    tables1.floatThead('reflow');

       	}
    });
    
    $('#aging_detail').click(function(){
        if (!$.fn.dataTable.isDataTable('#aging-detail')) {  
        	var tables2 = $('#aging-detail');
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

		        ajax: {
		            url: '{{action("ReportsController@customer_aging_detail")}}',
		            data: function (d) {
		                d.item_name = $('input[name=item_name2]').val();
		               d.daterange = $('input[name=daterange2]').val();
		            }
		        },
		        columns: [
		            {data: 'age', name: 'age'},
		            {data: 'type', name: 'type'},
		            {data: 'date', name: 'date'},
		            {data: 'ref', name: 'ref'},
		            {data: 'po', name: 'po'},
		            {data: 'name', name: 'name'},
		            {data: 'terms', name: 'terms'},

		            {data: 'duedate', name: 'duedate'},
		            {data: 'class', name: 'class'},
		            {data: 'aging', name: 'aging'},
		            {data: 'balance', name: 'balance'},
		        ]
		    });
		    $('#search-form2').on('submit', function(e) {
		        sTable.draw();
		        e.preventDefault();
		    });
		   	tables2.floatThead({
				responsiveContainer: function(tables2){
					return tables2.closest('.table-responsive');
				}
		    });
		    tables2.floatThead('reflow');

      	}
    });
    $('#aging_summary').click(function(){
        if (!$.fn.dataTable.isDataTable('#aging-summary')) {
        	var tables3= $('#aging-summary');
		    agingsum = tables3.DataTable({
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

		        ajax: {
		            url: '{{action("ReportsController@customer_aging_summary")}}',
		            data: function (d) {
		                d.item_name = $('input[name=item_name3]').val();
		               	d.daterange = $('input[name=daterange3]').val();
		       
		            }
		        },
		        columns: [
		            {data: 'name', name: 'name'},
		            {data: 'current', name: 'current'},
		            {data: 'aging1_30', name: 'aging1_30'},
		            {data: 'aging31_60', name: 'aging31_60'},
		            {data: 'aging61_90', name: 'aging61_90'},
		            {data: 'aging90', name: 'aging90'},
		            {data: 'total', name: 'total'},
		        ]
		    });
		    $('#search-form3').on('submit', function(e) {
		        agingsum.draw();
		        e.preventDefault();
		    });
		   	tables3.floatThead({
				responsiveContainer: function(tables3){
					return tables3.closest('.table-responsive');
				}
		    });
		    tables3.floatThead('reflow');

        	
      	}
    });



	});
</script>
@endsection