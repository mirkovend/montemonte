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

	#profitloss td,th{
		border-left: 1px solid;
		border-right: 1px solid;
	}
	#profitloss tr td:first-child,th:first-child,
	#profitloss tr td:last-child,th:last-child{
	    border-left: none;
		border-right: none;
	}

	.material-switch > input[type="checkbox"] {
    	display: none;   
	}

	.material-switch > label {
	    cursor: pointer;
	    height: 0px;
	    position: relative; 
	    width: 40px;  
	}

	.material-switch > label::before {
	    background: rgb(0, 0, 0);
	    box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
	    border-radius: 8px;
	    content: '';
	    height: 16px;
	    margin-top: -8px;
	    position:absolute;
	    opacity: 0.3;
	    transition: all 0.4s ease-in-out;
	    width: 40px;
	}
	.material-switch > label::after {
	    background: rgb(255, 255, 255);
	    border-radius: 16px;
	    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
	    content: '';
	    height: 24px;
	    left: -4px;
	    margin-top: -8px;
	    position: absolute;
	    top: -4px;
	    transition: all 0.3s ease-in-out;
	    width: 24px;
	}
	.material-switch > input[type="checkbox"]:checked + label::before {
	    background: inherit;
	    opacity: 0.5;
	}
	.material-switch > input[type="checkbox"]:checked + label::after {
	    background: inherit;
	    left: 20px;
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
				<li class="breadcrumb-item active">Balance Sheet</li>
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
						<h5>Balance Sheet Report</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">
						<profit></profit>
					</div>
				</div>


			</div>

		</div>

	</div>
</div>
</div><!-- end wrapper -->

<template id="profit">
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
	                <input type="text" class="form-control" name="search" id="name" placeholder="Search" autocomplete="off">
	            </div>
	         

	           	<button type="submit" id="sub" class="btn btn-primary">Search</button>
	       		<!-- <input type="checkbox" name="checkbox" id="checkbox" value="1"  data-toggle="toggle" data-on="Collapse" data-off="Expand" data-onstyle="success" data-offstyle="danger"> -->
	       		
	        {!! Form::close() !!}
	    </div>
	</div>
	<div id="" style="overflow-x:scroll;">
		<div style="padding:8px;">
			<div class="material-switch">
		        <input id="someSwitchOptionPrimary" name="someSwitchOption001" type="checkbox" v-model="checked" @change="collapse"/>
		        <label for="someSwitchOptionPrimary" class="label-primary"></label>
		    </div>
		
		</div>
	    <table id="profitloss" class="table table-hover" cellspacing="0" width="100%">
			<thead>
			  <th width="10%">Account Title</th>
			  <!-- <th  colspan="2" class="text-center" style="border:none">Broiler</th> -->
			  <th  colspan="2" class="text-center" style="border:none">Farm Admin</th>
			  <th  colspan="2" class="text-center" style="border:none">General</th>
			  <th  colspan="2" class="text-center" style="border:none">Hatchery</th>
			  <th  colspan="2" class="text-center" style="border:none">Layer</th>
			  <!-- <th  colspan="2" class="text-center" style="border:none">Total</br>Unclassified</th> -->
			  <th  colspan="2" class="text-center" style="border:none">Total</th>
			</thead>
			<thead>
			  <th></th>
			  <!-- <th class="text-center">jan</th> -->
			  <!-- <th class="text-center">feb</th> -->

			  <th class="text-center">jan</th>
			  <th class="text-center">feb</th>

			  <th class="text-center">jan</th>
			  <th class="text-center">feb</th>

			  <th class="text-center">jan</th>
			  <th class="text-center">feb</th>

			  <th class="text-center">jan</th>
			  <th class="text-center">feb</th>

			  <!-- <th class="text-center">jan</th> -->
			  <!-- <th class="text-center">feb</th> -->

			  <th class="text-center">jan</th>
			  <th class="text-center">feb</th>

			</thead>
			<tbody>
				<tr v-show="loading">
					<td colspan="15" ><h4 class="text-center">Loading</h4></td>
				</tr>
				<tr v-for="coa in list" track-by="$index" v-if="loading == false">
					<td v-if="coa.body == 'header'" :style="{'padding-left':coa.space + 'em'}">
						@{{coa.name}}
					</td>
					<td v-else :style="{'padding-left':coa.space + 'em'}">
						<strong>@{{coa.name}}</strong>
					</td>
					<!-- <td class="text-right" v-if="coa.body != 'footer'">@{{coa.broiler_amount}}</td> -->
					<!-- <td class="text-right"  v-if="coa.body != 'footer'">@{{coa.broiler_amount2}}</td> -->
					<td class="text-right"  v-if="coa.body != 'footer'">@{{coa.admin_amount}}</td>
					<td class="text-right"  v-if="coa.body != 'footer'">@{{coa.admin_amount2}}</td>
					<td class="text-right"  v-if="coa.body != 'footer'">@{{coa.general_amount}}</td>
					<td class="text-right"  v-if="coa.body != 'footer'">@{{coa.general_amount2}}</td>
					<td class="text-right"  v-if="coa.body != 'footer'">@{{coa.hatchery_amount}}</td>
					<td class="text-right"  v-if="coa.body != 'footer'">@{{coa.hatchery_amount}}</td>
					<td class="text-right"  v-if="coa.body != 'footer'">@{{coa.layer_amount}}</td>
					<td class="text-right"  v-if="coa.body != 'footer'">@{{coa.layer_amount2}}</td>
					<!-- <td class="text-right" 	v-if="coa.body != 'footer'">@{{coa.unclassified_amount}}</td> -->
					<!-- <td class="text-right"  v-if="coa.body != 'footer'">@{{coa.unclassified_amount2}}</td> -->
					<td class="text-right"  v-if="coa.body != 'footer'">@{{coa.total_amount}}</td>
					<td class="text-right"  v-if="coa.body != 'footer'">@{{coa.total_amount2}}</td>


					<!-- <td class="text-right" v-if="coa.body == 'footer'"><strong>@{{coa.broiler_amount}}</strong></td> -->
					<!-- <td class="text-right"  v-if="coa.body == 'footer'"><strong>@{{coa.broiler_amount2}}</strong></td> -->
					<td class="text-right"  v-if="coa.body == 'footer'"><strong>@{{coa.admin_amount}}</strong></td>
					<td class="text-right"  v-if="coa.body == 'footer'"><strong>@{{coa.admin_amount2}}</strong></td>
					<td class="text-right"  v-if="coa.body == 'footer'"><strong>@{{coa.general_amount}}</strong></td>
					<td class="text-right"  v-if="coa.body == 'footer'"><strong>@{{coa.general_amount2}}</strong></td>
					<td class="text-right"  v-if="coa.body == 'footer'"><strong>@{{coa.hatchery_amount}}</strong></td>
					<td class="text-right"  v-if="coa.body == 'footer'"><strong>@{{coa.hatchery_amount}}</strong></td>
					<td class="text-right"  v-if="coa.body == 'footer'"><strong>@{{coa.layer_amount}}</strong></td>
					<td class="text-right"  v-if="coa.body == 'footer'"><strong>@{{coa.layer_amount2}}</strong></td>
					<!-- <td class="text-right" 	v-if="coa.body == 'footer'" ><strong>@{{coa.unclassified_amount}}</strong></td> -->
					<!-- <td class="text-right"  v-if="coa.body == 'footer'"><strong>@{{coa.unclassified_amount2}}</strong></td> -->
					<td class="text-right"  v-if="coa.body == 'footer'"><strong>@{{coa.total_amount}}</strong></td>
					<td class="text-right"  v-if="coa.body == 'footer'"><strong>@{{coa.total_amount2}}</strong></td>
				</tr>
			</tbody>
		</table>	
	</div>		   	       
</template>
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
<script type="text/javascript" src='{{ asset("assets/js/components/profit.js") }}'></script>

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

		

	});
</script>
@endsection
