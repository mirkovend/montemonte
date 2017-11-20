@extends('template.theme')
@section('css')

<link href='https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css' rel="stylesheet">
<link href='https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css' rel="stylesheet">
<link href='https://cdn.datatables.net/fixedcolumns/3.2.3/css/fixedColumns.dataTables.min.css' rel="stylesheet">

<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href='{{ asset("assets/css/bootstrap-datetimepicker.min.css") }}' />

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
						<div class="panel panel-default">
						    <div class="panel-heading">
						        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
						    </div>
						    <div class="panel-body">
						        
						        {!! Form::open(['class'=>'form-inline','id'=>'search-form']) !!}

						            <div class="form-group">
						                <label for="name">Search</label>
						                <input type="text" class="form-control" name="search" id="name" placeholder="Search" autocomplete="off" v-model="query.search" @keyup="fetchDataList">
						                <input type="text" v-model="query.date" class="form-control datepicker" >
						            </div>
						         	

						           	<button type="submit" id="sub" class="btn btn-primary">Search</button>
						       		<!-- <input type="checkbox" name="checkbox" id="checkbox" value="1"  data-toggle="toggle" data-on="Collapse" data-off="Expand" data-onstyle="success" data-offstyle="danger"> -->
						       		
						        {!! Form::close() !!}
						    </div>
						</div>
					    <table id="trialbalance" class="table table-hover" cellspacing="0" width="100%">
							<thead>
							  <th>Account Title</th>
							  <th>Debit</th>
							  <th>Credit</th>
							</thead>
							<tbody>
								<tr v-if="loading == true">
									<td colspan="3" ><h4 class="text-center">Loading</h4></td>
								</tr>
								<tr v-else>
									<td colspan="3" v-if="list.length == 0">
										<h4 class="text-center">No data Available</h4>
									</td>
								</tr>
								<tr v-for="coa in list" v-if="loading == false">
									<td  v-bind:style="{ 'padding-left': coa.space + 'em'}" v-if="coa.key">		<h3>@{{coa.account_title}}</h3>
									</td>
									<td  v-bind:style="{ 'padding-left': coa.space + 'em'}" v-else>
										@{{coa.account_title}}
									</td>
									<td>@{{coa.debit}}</td>
									<td>@{{coa.credit}}</td>
								</tr>
								
							</tbody>
						</table>	
			          
					</div>
				</div>


			</div>

		</div>

	</div>
</div>
</div><!-- end wrapper -->
@endsection

@section('script')
<script type="text/javascript" src='https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js'></script>
<script type="text/javascript" src='https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js'></script>
<script type="text/javascript" src='https://cdn.datatables.net/fixedcolumns/3.2.3/js/dataTables.fixedColumns.min.js'></script>


<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script>

<!-- <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> -->
 
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript" src='{{ asset("assets/js/moment.min.js") }}'></script>
<script>
	Vue.config.devtools = true;
	Vue.config.debug = true;
	Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');
	var vm = new Vue({
		el:'body',

		data:{
			
				list:[],
				remove:false,
				loading:false,
				pagination:{},

				query:{
					triger:false,
					search:"",
					date:"",
				},
			
		},

	    created: function(){
			this.fetchDataList();
		},
		methods:{
			fetchDataList: function(page_url){
				this.loading = true;
				page_url = '{{action("ReportsController@trial_balance")}}';
	  			var resource = this.$resource(page_url);	
		
				resource.get({query:this.query}).then(response => {
					console.log(response.data);
				   	this.list = response.data.data;
	            	this.loading = false;
				}, response => {
				// error callback
				});
			},
			selectdate:function(){
				this.query.triger = true;
				this.loading = true;
				this.fetchDataList();
			}
		},
		attached: function() {

	       	var args = {
	    		"autoApply":true,
	            autoUpdateInput: true,
	            locale: {
			        cancelLabel: 'Clear',
			        format: 'YYYY-MM-DD'
			    }
	       	};
	        this.$nextTick(function() {
	            $('.datepicker').daterangepicker(args)
		        $('.datepicker').on('change', function(event) {
				   vm.selectdate();
				});
	        });

	    }



	});
	
</script>

<!-- <script type="text/javascript">
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

		var tables = $('#trialbalance');
	    oTable = tables.DataTable({
	    	dom: "<'row'<'col-xs-12'<'col-xs-6'l><'col-xs-6'p>>r>"+
			"<'row'<'col-xs-12't>>"+
			"<'row'<'col-xs-12'<'col-xs-6'i><'col-xs-6'p>>>",
        	"serverSide": true,
	        "ordering": false,
	    	"bPaginate": false,
	        "bLengthChange": false,
		    "bFilter": true,
		    "bInfo": false,
		    // scrollY:        "500px",
	     //    scrollX:        true,
	     //    scrollCollapse: true,
	     //    paging:         false,
		    responsive: true,
		    // fixedHeader: true,
		    ajax: {
	            url: '{{action("ReportsController@trial_balance")}}',
	            data: function (d) {
	                d.search = $('input[name=search]').val();
	            }
	        },
	        "columns": [
	            { "data": "account_title" },
	            { "data": "debit" },
	            { "data": "credit" },
        	]
	    });
	    $('#search-form').on('submit', function(e) {
	        oTable.draw();
	        e.preventDefault();
	    });
	   	$('#checkbox').change(function() {
	      $('#search-form').submit();
	    })

	});
</script> -->
@endsection
