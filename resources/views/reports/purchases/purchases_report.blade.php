@extends('template.theme')
@section('css')
<link href='https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css' rel="stylesheet">
<link href='https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css' rel="stylesheet">
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
				<li class="breadcrumb-item active">Purchases</li>
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
						<h5>Purchases Report</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">
					

						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active text-info"><a href="#Idetails" aria-controls="Idetails" role="tab" data-toggle="tab">Item Details</a></li>
							<li role="presentation" id="Itemsummary"><a href="#Isummary" aria-controls="Isummary" role="tab" data-toggle="tab">Item Summary</a></li>
							<li role="presentation" id="vendor_details"><a href="#Vdetails" aria-controls="Vdetails" role="tab" data-toggle="tab">Vendor Details</a></li>
							<li role="presentation"><a href="#Vsummary" aria-controls="Vsummary" role="tab" data-toggle="tab">Vendor Summary</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="Idetails">
								<div class="table-responsive">
									<div class="panel panel-default">
									    <div class="panel-heading">
									        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
									    </div>
									    <div class="panel-body">
									        
									        {!! Form::open(['action'=>'ReportsController@inventory_report_store','class'=>'form-inline','id'=>'search-form']) !!}

									            <div class="form-group">
									                <label for="name">Item Name</label>
									                <input type="text" class="form-control" name="item_name" id="name" placeholder="search name" autocomplete="off">
									            </div>

									            <!-- <button type="submit" id="sub" class="btn btn-primary">Search</button> -->
									        </form>
									    </div>
									</div>
				            		<table class="table table-hover" id="tablez" style="width:100%">
							            <thead class="text-uppercase">
								            <tr>
								                <th width="15%">Item</th>
								                <th>Type</th>
								                <th>Date</th>
								                <th>Ref</th>
								                <th>Memo</th>
								                <th>Source</th>
								                <th>Qty</th>
								                <th>U/M</th>
								                <th>Avg Cost</th>
								                <th>Amount</th>
								                <th>Balance</th>
								            </tr>
							            </thead>
							            <tbody>
							            	
							            </tbody>
							        </table>

								
								</div>
							</div>
							<div role="tabpanel" class="tab-pane" id="Isummary">
								<div class="table-responsive">
									<div class="panel panel-default">
									    <div class="panel-heading">
									        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
									    </div>
									    <div class="panel-body">
									        
									        {!! Form::open(['class'=>'form-inline','id'=>'search-form1']) !!}

									            <div class="form-group">
									                <label for="name">Item Name</label>
									                <input type="text" class="form-control" name="item_name1" id="name" placeholder="search name" autocomplete="off">
									            </div>

									            <!-- <button type="submit" id="sub" class="btn btn-primary">Search</button> -->
									        </form>
									    </div>
									</div>
				            		<table class="table table-hover" id="tablez-summary" style="width:100%">
							            <thead class="text-uppercase">
								            <tr>
								                <th width="15%">Item</th>
								                <th>Qty</th>
								                <th>Amount</th>
								            </tr>
							            </thead>
							            <tbody>
							            	
							            </tbody>
							        </table>

								
								</div>

							</div>
							<div role="tabpanel" class="tab-pane" id="Vdetails">
								<div class="table-responsive">
									<div class="panel panel-default">
									    <div class="panel-heading">
									        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
									    </div>
									    <div class="panel-body">
									        
									        {!! Form::open(['class'=>'form-inline','id'=>'search-form2']) !!}

									            <div class="form-group">
									                <label for="name">Item Name</label>
									                <input type="text" class="form-control" name="item_name2" id="name" placeholder="search name" autocomplete="off">
									            </div>

									            <!-- <button type="submit" id="sub" class="btn btn-primary">Search</button> -->
									        </form>
									    </div>
									</div>
									<table class="table table-hover" id="tablezvendor" style="width:100%">
										<thead class="text-uppercase">
											<tr>
												<th width="15%">Vendor Name</th>
												<th>Type</th>
												<th>Date</th>
												<th>Ref</th>
												<th>Memo</th>
												<th>ITEM</th>
												<th>Qty</th>
												<th>U/M</th>
												<th>Avg Cost</th>
												<th>Amount</th>
												<th>Balance</th>
											</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane" id="Vsummary">
								<div class="table-responsive">
									<div class="panel panel-default">
									    <div class="panel-heading">
									        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
									    </div>
									    <div class="panel-body">
									        
									        {!! Form::open(['class'=>'form-inline','id'=>'search-form3']) !!}

									            <div class="form-group">
									                <label for="name">Item Name</label>
									                <input type="text" class="form-control" name="item_name3" id="name" placeholder="search name" autocomplete="off">
									            </div>

									            <!-- <button type="submit" id="sub" class="btn btn-primary">Search</button> -->
									        </form>
									    </div>
									</div>
				            		<table class="table table-hover" id="vendorsummary" style="width:100%">
							            <thead class="text-uppercase">
								            <tr>
								                <th width="15%">Item</th>
								                <th>Amount</th>
								            </tr>
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
<script type="text/javascript">
	// $(document).ready(function(){
	// 	// $('table').DataTable({ "ordering": false});
	// 	var $table = $('#tablez');
	//     $table.floatThead({
	// 		responsiveContainer: function($table){
	// 			return $table.closest('.table-responsive');
	// 		}
	//     });
	//     $table.floatThead('reflow');


	// });

	var oTable;
	var tablesummary;
	var vendordetail;
	var vendorsummary;
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // var table = $('#example').DataTable( {
    // 	dom: "<'row'<'col-xs-12'<'col-xs-6'l><'col-xs-6'p>>r>"+
    //         "<'row'<'col-xs-12't>>"+
    //         "<'row'<'col-xs-12'<'col-xs-6'i><'col-xs-6'p>>>",
    // 	"ordering": false,
    // 	"bPaginate": false,
	   //  "bLengthChange": false,
	   //  "bFilter": true,
	   //  "bInfo": false,
        // fixedHeader: {
        //     header: true,
        //     footer: true
        // }
    // } ).draw();
 	 var tables = $('#tablez');
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
            url: '{{action("ReportsController@purchases_report_data")}}',
            data: function (d) {
                d.item_name = $('input[name=item_name]').val();
            }
        },
        columns: [
            {data: 'item_name', name: 'item_name'},
            {data: 'type', name: 'type'},
            {data: 'date', name: 'date'},
            {data: 'ref', name: 'ref'},
            {data: 'memo', name: 'memo'},
            {data: 'source', name: 'source'},
            {data: 'qty', name: 'qty'},
            {data: 'um', name: 'um'},
            {data: 'avgcost', name: 'avgcost'},
            {data: 'amount', name: 'onHand'},
            {data: 'balance', name: 'assetval'},

        ]
    });
    $('#Idetails #search-form').on('keyup', function(e) {
        oTable.draw();
        e.preventDefault();
    });


    tables.floatThead({
		responsiveContainer: function(tables){
			return tables.closest('.table-responsive');
		}
    });
    tables.floatThead('reflow');

    //item details
	$('#Itemsummary').click(function(){
		if (!$.fn.dataTable.isDataTable('#tablez-summary')) {  
			var tables1 = $('#tablez-summary');
		    tablesummary = tables1.DataTable({
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
			    fixedHeader: {
		            header: true,
		            footer: true
		        },
			    responsive: true,
		        ajax: {
		            url: '{{action("ReportsController@item_summary")}}',
		            data: function (d) {
		                d.item_name = $('input[name=item_name1]').val();
		            }
		        },
		        columns: [
		            {data: 'item_name', name: 'item_name'},
		            {data: 'qty', name: 'qty'},
		            {data: 'amount', name: 'amount'},
		        ]
		    });
		    $('#Isummary #search-form1').on('keyup', function(e) {
		        tablesummary.draw();
		        e.preventDefault();
		    });


		  //   tables1.floatThead({
				// responsiveContainer: function(tables){
				// 	return tables1.closest('.table-responsive');
				// }
		  //   });
		  //   tables1.floatThead('reflow');

		}
		

	});

	
			var tables2 = $('#tablezvendor');
		    vendordetail = tables2.DataTable({
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
		            url: '{{action("ReportsController@purchase_vendor_detail")}}',
		            data: function (d) {
		                d.item_name = $('input[name=item_name2]').val();
		            }
		        },
		        columns: [
		            {data: 'supplier', name: 'supplier'},
		            {data: 'type', name: 'type'},
		            {data: 'date', name: 'date'},
		            {data: 'ref', name: 'ref'},
		            {data: 'memo', name: 'memo'},
		            {data: 'item', name: 'item'},
		            {data: 'qty', name: 'qty'},
		            {data: 'um', name: 'um'},
		            {data: 'cost', name: 'cost'},
		            {data: 'amount', name: 'amount'},
		            {data: 'balance', name: 'balance'},


		        ]
		    });
		    $('#Vdetails #search-form2').on('keyup', function(e) {
		        vendordetail.draw();
		        e.preventDefault();
		    });


		    tables2.floatThead({
				responsiveContainer: function(tables){
					return tables2.closest('.table-responsive');
				}
		    });
		    tables2.floatThead('reflow');

		    //

		    var tables3 = $('#vendorsummary');
		    vendorsummary = tables3.DataTable({
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
		            url: '{{action("ReportsController@vendor_summary")}}',
		            data: function (d) {
		                d.item_name = $('input[name=item_name3]').val();
		            }
		        },
		        columns: [
		            {data: 'supplier', name: 'supplier'},
		            {data: 'balance', name: 'balance'},


		        ]
		    });

		    $('#Vsummary #search-form2').on('keyup', function(e) {
		        vendorsummary.draw();
		        e.preventDefault();
		    });

   			tables3.floatThead({
				responsiveContainer: function(tables){
					return tables3.closest('.table-responsive');
				}
		    });
		    tables3.floatThead('reflow');
   

} );
</script>
@endsection