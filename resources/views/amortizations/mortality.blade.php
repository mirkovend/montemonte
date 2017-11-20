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
				<li class="breadcrumb-item active">Table of Amortization</li>
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
						<h5>Table of Amortization Lists</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">
					<div class="pull-right">
		            	<div class="form-group">
		            	<a class="btn btn-md btn-primary" href="{{ action('AmortizationController@create_mortality',$id)}}"><span class="fa fa-plus"></span> Create Entry</a>
		            	</div>
		            </div>
		            <div class="table-responsive">
						<table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
				            <thead> 
				                <tr>
				                    <th>Month</th>
				                    <th>Age</th>
				                    <th>BV Factor</th>
				                    <th>Mortality</th>
				                    <th>Heads Balance</th>
				                    <th>Amortization</th>
				                    <th>Mort. Exp.</th>
				                    <th>PreOp Bal</th>
				                    <th>BV/Bird</th>
				                </tr>
				            </thead>
				            <tbody>
				                <tr>
				                    <td>Beginning</td>
				                    <td></td>
				                    <td>{{ number_format($batchno->total_cost / $batchno->number_heads,2) }}</td>
				                    <td></td>
				                    <td>{{ number_format($batchno->number_heads,0) }}</td>
				                    <td></td>
				                    <td></td>
				                    <td>{{ number_format($batchno->total_cost,2) }}</td>
				                    <td></td>
				                </tr>
				                @foreach($mortality as $row)
				                    <tr>
				                        <td>{{ $row->month }} / {{ $row->year }}</td>
				                        <td>{{ $row->weeksFrom }} - {{ $row->daysFrom }} to {{ $row->weeksTo }} - {{ $row->daysTo }}</td>
				                        <td>{{ $row->bv }}</td>
				                        <td>{{ $row->mortality }}</td>
				                        <td>{{ number_format($row->heads_balance,0) }}</td>
				                        <td>{{ number_format($row->amort,0) }}</td>
				                        <td>{{ number_format($row->mortality_exp,0) }}</td>
				                        <td>{{ number_format($row->preOP,2) }}</td>
				                        <td>{{ number_format($row->bvBird,2) }}</td>
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
		// $('table').DataTable();
	});
</script>
@endsection