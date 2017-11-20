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
</style>
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
	<div class="col-lg-12">
		<h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
				<li class="breadcrumb-item active">Inventory Report</li>
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
						<h5>Inventory Info</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">

		            <div class="table-responsive">
					<!-- 	<table class="table table-bordered" id="tablez">
					      </table> -->
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
					      <table id="example" class="display" cellspacing="0" width="100%">
       				        <thead>
       				          	<tr>
									<th>Item</th>
									<th>Type</th>
									<th>Date</th>
									<th>Name</th>
									<th>Ref</th>
									<th>Qty</th>
									<th>Cost</th>
									<th>On Hand</th>
									<th>U/M</th>
									<th>Avg Cost</th>
									<th>Asset Value</th>
								</tr>
				          	</thead>
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
<script type="text/javascript" src='https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js'></script>
<script type="text/javascript" src='https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js'></script>

<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script> -->
<script type="text/javascript">
var oTable;
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
    //     fixedHeader: {
    //         header: true,
    //         footer: true
    //     }
    // } ).draw();

     oTable = $('#example').DataTable({
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
            url: '{{action("ReportsController@anydata")}}',
            data: function (d) {
                d.item_name = $('input[name=item_name]').val();
            }
        },
        columns: [
            {data: 'item_name', name: 'item_name'},
            {data: 'type', name: 'type'},
            {data: 'date', name: 'date'},
            {data: 'name', name: 'name'},
            {data: 'ref', name: 'ref'},
            {data: 'qty', name: 'qty'},
            {data: 'cost', name: 'cost'},
            {data: 'onHand', name: 'onHand'},
            {data: 'um', name: 'um'},
            {data: 'avgcost', name: 'avgcost'},
            {data: 'assetval', name: 'assetval'},

        ]
    });
    $('#search-form').on('keyup', function(e) {
        oTable.draw();
        e.preventDefault();
    });

} );
   
</script>
@endsection

