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

		#balancesheet.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
		    padding: 1px 8px;
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

						<div class="form-group">
							<a class="btn btn-primary" href="{{action('ReportsController@balance_sheet_view')}}">Balance Sheet</a>
							<a class="btn btn-danger" href="{{action('ReportsController@balance_sheet_detail_view')}}">Balance Sheet Details</a>
						</div>

					    
		          		<div class="panel panel-default">
						    <div class="panel-heading">
						        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
						    </div>
						    <div class="panel-body">
						        
						        <form id="search-form" class="form-inline" >
						            <div class="form-group">
						                <input type="text" class="form-control" name="item_name" v-model="search"  placeholder="Search Account" autocomplete="off" @keyup="filter1">
						            </div>
						         

						           	
						       		<div class="form-group date" >
						                <div class="input-group date">
							   				<input type="text" id="firstdate" class="form-control datepick" v-model="date1" placeholder="Select Date"><span class="input-group-addon" ><i class="glyphicon glyphicon-calendar"></i></span>
							   			</div>
						            </div>
						            <div class="form-group" >
						                <div class="input-group date">
							   				<input type="text" id="seconddate" class="dates form-control" v-model="date2" placeholder="Compare Date" ><span class="input-group-addon" ><i class="glyphicon glyphicon-calendar"></i></span>
							   			</div>
						              
						            </div>

						        </form>

						    </div>
						</div>
						<div class="pull-right">
								<div class="material-switch pull-right">
							        <input id="someSwitchOptionPrimary" name="someSwitchOption001" type="checkbox" v-model="checked" @change="filter1"/>
							        <label for="someSwitchOptionPrimary" class="label-primary"></label>
							    </div>
							
						</div>
						<table id="balancesheet" class="table" cellspacing="0" width="100%">
							<thead>
								<th>Name</th>
								<th v-if="date1">
									@{{date1}}
								</th>
								<th  v-else>
									@{{startDatetime}}
								</th>
								<th v-if="date2">
									@{{date2}}
								</th>
							</thead>
							<tbody v-if="loading == true">
								<tr >
									<td colspan="3" ><h4 class="text-center">Loading</h4></td>
								</tr>
							</tbody>

							<tbody v-else>
								<td colspan="3" v-if="list.length == 0">
									<h4 class="text-center">No data Available</h4>
								</td>
								<tr v-for="coa in list">
									<td v-if="coa.keys == 'type'">
										<h3 v-if="coa.total">
											<strong>@{{coa.name}}</strong>
										</h3>
										<h3 v-else>
										@{{coa.name}}
										</h3>
									</td>
									<td v-else>

										<p v-bind:style="{ 'padding-left': coa.space + 'em' }" v-if="coa.total">
											<strong>@{{coa.name}}</strong>
										</p>
										<p v-bind:style="{ 'padding-left': coa.space + 'em' }" v-else>
											@{{coa.name}}
										</p>
									</td>

									<td  v-if="coa.total"><strong>@{{coa.amount}}</strong></td>
									<td  v-else>@{{coa.amount}}</td>
									<td v-if="date2"> @{{coa.amount2}}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>


			</div>

		</div>

	</div>
</div>

@endsection

@section('script')<!-- 
<script type="text/javascript" src='https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js'></script>
<script type="text/javascript" src='https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js'></script>
<script type="text/javascript" src='https://cdn.datatables.net/fixedcolumns/3.2.3/js/dataTables.fixedColumns.min.js'></script> -->


<!-- <script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script> -->

<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
 <!--  -->
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>

<script type="text/javascript">
	Vue.config.devtools = true;
	Vue.config.debug = true;
	Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');
	var vm = new Vue({
		el:'body',
		data: {
			value: moment('2015-01-01').format("MMMM D, YYYY"),
			list:[],
			remove:false,
			date1:null,
			date2:null,
			search:null,
			checked:false,
			loading:false,
			startDatetime: moment().format("MMMM D, YYYY"),
		
	},

	created: function(){
		this.filter1();
	},

	methods:{
		fetchDataList: function(){
			this.loading = true;
			var resource = this.$resource('balance_sheet/balance_sheet_data');	
			// resource.save(function(data){
			// 	this.list = data.results;
			// }.bind(this));

			resource.save().then(response => {
			// console.log(response);
				this.list = response.data.results;
				this.loading = false;
			}, response => {
			// error callback
			});

		},
		collapse_list: function(){
			this.loading = true;
			var resource = this.$resource('balance_sheet/bs_collapse');	
			// resource.save(function(data){
			// 	this.list = data.results;
			// }.bind(this));
			resource.save().then(response => {
			// console.log(response);
				this.list = response.data.results;
				this.loading = false;
			}, response => {
			// error callback
			});


		},
		filter1:function(){
			this.loading = true;
			if(this.checked == true){

				var resource = this.$resource('balance_sheet/bs_collapse');	
				// resource.save({date1:this.date1,date2:this.date2,search:this.search},function(data){
				// 	this.list = data.results;
				// }.bind(this));
				resource.save({date1:this.date1,date2:this.date2,search:this.search}).then(response => {
					// console.log(response);
					this.list = response.data.results;
					this.loading = false;
				});
			}else{

				var resource = this.$resource('balance_sheet/balance_sheet_data');
				resource.save({date1:this.date1,date2:this.date2,search:this.search}).then(response => {
					this.list = response.data.results;
					this.loading = false;
				});
				
			}
		},

	},
    attached: function() {

       	var args = {
    		"autoApply":true,
            autoUpdateInput: true,
            singleDatePicker: true,
            locale: {
		        cancelLabel: 'Clear',
		        format: 'YYYY-MM-DD'
		    }
       	};
        this.$nextTick(function() {
            $('#firstdate').daterangepicker({
			  "autoApply":true,
	            autoUpdateInput: false,
	            singleDatePicker: true,
	            locale: {
			        cancelLabel: 'Clear',
			        format: 'YYYY-MM-DD'
			    }
			}, function(chosen_date) {
				vm.date1 = moment(chosen_date).format('MMMM D, YYYY');
				vm.filter1();
			});

			$('#seconddate').daterangepicker({
			  "autoApply":true,
	            autoUpdateInput: false,
	            singleDatePicker: true,
	            locale: {
			        cancelLabel: 'Clear',
			        format: 'YYYY-MM-DD'
			    }
			}, function(chosen_date) {
			  
			  	vm.date2 = moment(chosen_date).format('MMMM D, YYYY');
				vm.filter1();
			});

        });

    }
		
	});
</script>

@endsection