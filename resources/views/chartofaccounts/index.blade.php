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
				<li class="breadcrumb-item active">Chart of Accounts</li>
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
						<h5>Account Lists</h5>
					</div>
		            <tasks></tasks>
				</div>
			</div>

		</div>

	</div>
</div>
</div><!-- end wrapper -->

<template id="tasks-template">
	<div class="ibox-content" id="ibox_form">

	<div class="alert alert-success" transition="remove" v-if="remove"> Delete Account Successfully!</div>
		<div class="pull-right">
	    	<div class="form-group">
	    	<a class="btn btn-md btn-primary" href="{{ action('ChartofaccountController@create_journal')}}"><span class="fa fa-plus"></span> Create General Journal</a>
	    	<a class="btn btn-md btn-primary" href="{{ action('ChartofaccountController@create')}}"><span class="fa fa-plus"></span> Create Entry</a>
	    	</div>
	    </div>
	    <div class="table-responsive">
	    	<div class="form-group">
	    		<input @change="fetchDataList()" v-model="query" type="text" class="form-control" placeholder="Search Account">
	    	</div>

			<table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
				<thead>	
					<tr>
						<th>Account Title</th>
						<th>Account Type</th>
						<th>Balance</th>
						<th width = 80 class="text-center">Action</th>
					</tr>
				</thead>
				<tbody>
					
					<tr v-for="coa in list" track-by="$index" v-if="loading == false">
						<td :style="{'padding-left':coa.space + 'em'}">
							@{{coa.coa_title}}
						</td>
						<td>@{{coa.detailtype.detail_type_name}}</td>

						<td>@{{coa.balances}}</td>
						
						<td width = 80 class='text-center'>
	                        <div class='btn-group'>
	                            <button type='button' class='btn btn-sm btn-primary dropdown-toggle' data-toggle='dropdown'>
	                                <span class='icon-gear'></span> <span class='caret'></span>
	                            </button>
	                            <ul class='dropdown-menu dropdown-menu-arrow' role='menu' style='text-align:left;text-transform:uppercase;'>
	                                <li><a href="chartofaccount/@{{coa.id}}"><span class='icon-edit'></span>View History</a></li>
	                                <li><a href="chartofaccount/@{{coa.id}}/edit"><span class='icon-edit'></span> Edit Entry</a></li>
	                                <li><a href="#" @click="deleteCoa(coa)"><span class='icon-remove'></span> Delete Entry</a></li>
	                            </ul>
	                        </div>
                    	</td>

					</tr>
					<tr v-if="loading == true">
						<td colspan="4" ><h4 class="text-center">Loading</h4></td>
					</tr>
					<tr v-else>
						<td colspan="4" v-if="list.length == 0">
							<h4 class="text-center">No data Available</h4>
						</td>
					</tr>
					<!--  -->
					
				</tbody>
			</table>
			<div class="pagination">
			    <button class="btn btn-primary" @click="fetchDataList(pagination.prev_page_url)"
			            :disabled="!pagination.prev_page_url">
			        Previous
			    </button>
			    <span>Page @{{pagination.current_page}} of @{{pagination.last_page}}</span>
			    <button class="btn btn-primary" @click="fetchDataList(pagination.next_page_url)"
			            :disabled="!pagination.next_page_url">Next
			    </button>
			</div>
		</div>
	</div>
</template>
@endsection

@section('script')
<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script>

<!-- <script type="text/javascript" src='https://unpkg.com/vue@1.0.8/dist/vue.js'></script>
<script type="text/javascript" src='http://cdnjs.cloudflare.com/ajax/libs/vue-resource/0.1.17/vue-resource.js'></script> -->
<script type="text/javascript" src='{{ asset("assets/js/apps.js") }}'></script>
<script type="text/javascript">

	// $(document).ready(function(){
		
		
	// 	$('table').DataTable({ 
	// 		"ordering": false,
	//     	"bPaginate": false,
	//         "bLengthChange": false,
	// 	    "bFilter": true,
	// 	    "bInfo": false,
	// 	    });
	// 	var $table = $('#tableSortable');
	//     $table.floatThead({
	// 		responsiveContainer: function($table){
	// 			return $table.closest('.table-responsive');
	// 		}
	//     });
	//     $table.floatThead('reflow');
	// });
</script>
@endsection