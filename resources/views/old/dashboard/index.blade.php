@extends('template.theme')

@section('css')
<link href='{{ asset("assets/datatable/jquery.dataTables.min.css") }}' rel="stylesheet">
<link href='{{ asset("assets/ui/lib/dp/dist/datepicker.min.css") }}' rel="stylesheet">

@stop
@section('content')
<div class="row wrapper white-bg page-heading">
  <div class="col-lg-12">
    <h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
    	<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
			<li class="breadcrumb-item">Disbursement</li>
		</ol>
    </h2>
  </div>
</div>



<div class="wrapper wrapper-content animated fadeIn">
	<div class="row">
	    <div class="col-lg-12 col-md-12 col-sm-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title">
	                <h5>Records 0. Page 1 of 0. </h5>

	            </div>
	            <div class="ibox-content">
	              <canvas id="projects-graph" width="1000" height="400"></canvas>
	            </div>
	        </div>
	        
	    </div>

	   <!-- Widget-1 end-->

	    <!-- Widget-2 end-->
	</div> <!-- Row end-->


<!-- Row end-->


<!-- Row end-->

	<div id="ajax-modal" class="modal container fade-scale" tabindex="-1" style="display: none;"></div>
</div>
@stop


@section('script')

<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js'></script>
<script type="text/javascript">
	$(function(){
  $.getJSON("dashboard/data", function (result) {

    var labels = [],data=[];
    for (var i = 0; i < result.length; i++) {
        labels.push(result[i].month);
        data.push(result[i].projects);
    }

    var buyerData = {
      labels : labels,
      datasets : [
        {
          type:'line',
          label: 'income',
          borderColor:'#ebf442',
          'backgroundColor': 'transparent',
          data : data
        },
        {
          type:'line',
          label: 'expense',
          borderColor:'#35e0c3',
          'backgroundColor': 'transparent',
          data : data
        },
      ]
    };
    console.log(result);
    var buyers = $("#projects-graph");
    
    var chartInstance = new Chart(buyers, {
		type:'line',
	    data: buyerData,
	    options: {
        	responsive: true,
        	title: {
            display: true,
            text: 'Custom Chart Title'
        }
    	}
	});
  });

});
</script>
@stop 