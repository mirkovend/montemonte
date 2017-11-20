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
				<li class="breadcrumb-item active">Egg Production</li>
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
						<h5>Egg Production Report</h5>
					</div>
					<div class="ibox-content" id="ibox_form">
						<div class="panel panel-default">
						    <div class="panel-heading">
						        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
						    </div>
						    <div class="panel-body">
						        
						        {!! Form::open(['class'=>'form-inline','id'=>'search-form']) !!}

						            <div class="form-group">
						                <label for="name">Search</label>
						                <input type="text" class="form-control" name="search" id="name" placeholder="Search" autocomplete="off" v-model="query.search" @keyup="fetchHeader">
						                <input type="text" v-model="query.date" class="form-control datepicker" >
						            </div>
						         	

						           	<button type="submit" id="sub" class="btn btn-primary">Search</button>
						       		<!-- <input type="checkbox" name="checkbox" id="checkbox" value="1"  data-toggle="toggle" data-on="Collapse" data-off="Expand" data-onstyle="success" data-offstyle="danger"> -->
						       		
						        {!! Form::close() !!}
						    </div>
						</div>
						<div class="responsive">
							
						
					    <table id="trialbalance" class="table table-hover" cellspacing="0" width="100%">
							<thead >
							  <th v-for="header in egg_header">@{{header.item_name}}</th>
							</thead>
							<tbody>
								<tr v-for="items in list">
									<td v-for="row in items.data">@{{row}}</td>
								</tr>
								<tr class="success" >
									<td v-for="total in total_col">@{{total}}</td>
								</tr>
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
				total_col:[],
				egg_header:[],
				remove:false,
				loading:false,
				query:{
					triger:0,
					search:"",
					date:"",
				},
			
		},

	    created: function(){
			
			this.fetchHeader();
		},
		methods:{
			fetchHeader:function(){
				page_url = '{{action("Reports2Controller@egg_production_header")}}';
	  			var resource = this.$resource(page_url);	
				resource.get().then(response => {
					
					this.egg_header = response.data;
					this.fetchDataList(response.data);
				}, response => {
				// error callback
				});
			},
			fetchDataList: function(data){
				this.loading = true;
				page_url = '{{action("Reports2Controller@egg_production_data")}}';
	  			var resource = this.$resource(page_url);	
				resource.get({query:this.query,data:data}).then(response => {
					// console.log(response.data.total);
					this.total_col = response.data.total;
					this.list = response.data.results;
	            	this.loading = false;
				}, response => {
				// error callback
				});
			},
			selectdate:function(){
				this.query.triger = 1;
				this.loading = true;
				this.fetchHeader();
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

@endsection
