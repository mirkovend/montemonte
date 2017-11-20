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
				<li class="breadcrumb-item active">Deposit</li>
			</ol>
		</h2>
	</div>
</div>
<div class="wrapper wrapper-content">
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">

			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Deposit Lists</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">
					<div class="pull-right">
		            	<div class="form-group">
		            	<a class="btn btn-md btn-primary" href="{{ action('InventoryController@create')}}"><span class="fa fa-plus"></span> Create Entry</a>
		            	</div>
		            </div>
		            <div class="table-responsive">
						<table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
				    		<thead>	
				    			<tr>
				                    <th>Date</th>
				                    <th>Adjustment Item</th>
				                    <th>Ref No</th>
				                    <th>Amount Cost</th>
				    				<th width = "20%" class="text-center">Action</th>
				    			</tr>
				    		</thead>
				    		<tbody>
				            @foreach($inventories as $inventory)
				                <tr>
				                    <td>{{Carbon\Carbon::parse($inventory->dt)->toFormattedDateString()}}</td>
				                    <td>{{$inventory->item_account->item_name}}</td>
				                    <td>{{$inventory->ref_no}}</td>
				                    <td>{{number_format($inventory->amount_cost,2)}}</td>
				                    <td  class="text-center">
				                        <div class="btn-group">
				                            <button type="button" class="btn btn-md btn-primary dropdown-toggle" data-toggle="dropdown">
				                                <span class="icon-gear"></span> <span class="caret"></span>
				                            </button>
				                            <ul class="dropdown-menu dropdown-menu-arrow" role="menu" style="text-align:left;text-transform:uppercase;">
				                                <li><a href="#"><span class="icon-remove"></span> Remove Entry</a></li>
				                                <li><a href="{{ action('InventoryController@edit',$inventory->id)}}"><span class="icon-remove"></span> Update Entry</a></li>
				                                <li><a href="#chEdit{{ $inventory->id }}" data-toggle="modal"><span class="icon-edit"></span> View Item</a></li>
				                            </ul>
				                        </div>
				                    </td>
				                </tr>
				            @endforeach

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

@foreach($inventories as $inventory)
<div class="modal fade" id="chEdit{{ $inventory->id }}"  role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="icon-plus"></i> Add Supplier</h4>
            </div>
            <div class="modal-body">
				<div class="row">
	                <div class="col-md-12">
	                    <table class="table">
	                        <thead>
	                            <tr>
	                                <th>Item Name</th>
	                                <th>Quantity</th>
	                                <th>Price</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        @foreach($inventory->inv_items as $item)
	                            <tr>
	                                <td>{{$item->item_account->item_name}}</td>
	                                <td>{{$item->credit_quantity}}</td>
	                                <td>{{$item->credit_quantity}}</td>
	                            </tr>
	                        @endforeach
	                        </tbody>
	                    </table>
	                </div>
            	</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">CLOSE</button>
            </div>
         
        </div>
    </div>
</div>
@endforeach

@endsection

@section('script')
<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('table').DataTable();
	});
</script>
@endsection