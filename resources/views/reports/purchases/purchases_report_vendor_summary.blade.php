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
	<div class="col-lg-6">
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

			<div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Purchases Report</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">
		            	<div class="row">
					        <div class="col-md-12" style="margin:6px 0;">
						        {!! Form::open(['method'=>'POST','action'=>'ReportController@purchases_type','class'=>'form-inline'])!!}
						            <div class="form-group">
						             {!! Form::select('detail_type',[1=>'Item Details',2=>'Item Summary',3=>'Vendor Details',4=>'Vendor Summary'],null,['class'=>'form-control'])!!}
						            </div>
						           <!--  <div class="form-group">
						              <label>From</label>
						              {!! Form::date('dt',\Carbon\Carbon::now()->toDateString(),['class'=>'form-control dt']) !!}
						            </div>
						            <div class="form-group">
						              <label>To</label>
						              {!! Form::date('dt',\Carbon\Carbon::now()->toDateString(),['class'=>'form-control dt']) !!}
						            </div> -->
						            <button type="submit" class="btn btn-primary">Submit</button>
						        </form>
						    </div>
					    </div>
			            <div class="table-responsive">
							<table class="table table-hover" id="tablez">
					            <thead class="text-uppercase">
					                <th >Supplier Name</th>
					                <th>Amount</th>
					            </thead>
					            <tbody>
					              <?php $totals = 0;?>
					              @foreach($suppliers as $supplier)
					               	<tr>
					               		<td>{{$supplier->supplier_name}}</td>
					               		<td></td>
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
<script type="text/javascript">
	$(document).ready(function(){
		// $('table').DataTable({ "ordering": false});
		var $table = $('#tablez');
	    $table.floatThead({
			responsiveContainer: function($table){
				return $table.closest('.table-responsive');
			}
	    });
	    $table.floatThead('reflow');
	});
</script>
@endsection