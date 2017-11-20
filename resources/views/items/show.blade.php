@extends('template.theme')
@section('css')
<link href='{{ asset("assets/datatable/jquery.dataTables.min.css") }}' rel="stylesheet">

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
</style>
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
	<div class="col-lg-12">
		<h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
				<li class="breadcrumb-item ">Item</li>
				<li class="breadcrumb-item active">{{$items->item_name}}</li>
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
						<div class="col-md-6">
							<h3>{{$items->item_name}} <small>Transactions</small></h3>
						</div>
						<div class="col-md-6 pull-right">
						<h3 class="pull-right">Balance : {{number_format($asset_val_total,2)}}</h3></div>	
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">

			            <div class="table-responsive">
			            	<div class="panel panel-default">
							    <div class="panel-heading">
							        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
							    </div>
							    <div class="panel-body">
							        
							        {!! Form::open(['action'=>['ItemController@search',$items->id],'class'=>'form-inline','id'=>'search-form']) !!}

							            <div class="form-group">
							                <label for="name">Date Range</label>
							                <input type="text" class="form-control daterange" name="date" id="name" placeholder="date" autocomplete="off">
							            </div>

							             <button type="submit" id="sub" class="btn btn-primary">Search</button> 
							        </form>
							    </div>
							</div>
							<table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
								<thead>
	       				          	<tr>
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
					    		<tbody>
						    		@foreach($transaction_data as $data)
						    			
							    		<tr>
											<td>{{ $data->type }}</td>
											<td>{{ $data->date }}</td>
											<td>{{ $data->name}}</td>
											<td>{{ $data->ref}}</td>
											<td>{{ $data->qty}}</td>
											<td>{{ $data->cost}}</td>
											<td>{{ $data->onhand}}</td>
											<td>{{ $data->um}}</td>
											<td>{{ $data->avgcost}}</td>
											<td>{{ $data->assetval}}</td>
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
@endsection

@section('script')
<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script>

<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
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
		$('table').DataTable({ 
			dom: "<'row'<'col-xs-12'<'col-xs-6'l><'col-xs-6'p>>r>"+
			"<'row'<'col-xs-12't>>"+
			"<'row'<'col-xs-12'<'col-xs-6'i><'col-xs-6'p>>>",

			"ordering": false
		});
	});
</script>
@endsection