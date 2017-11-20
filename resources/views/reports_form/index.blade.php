@extends('template.theme')

@section('css')
<link href='{{ asset("assets/datatable/jquery.dataTables.min.css") }}' rel="stylesheet">
<link href='{{ asset("assets/ui/lib/dp/dist/datepicker.min.css") }}' rel="stylesheet">
<style type="text/css">

#divLoading
{
    display : block;
    position : fixed;
    z-index: 100;
    background-image : url('http://loadinggif.com/images/image-selection/3.gif');
    background-color:#666;
    opacity : 0.4;
    background-repeat : no-repeat;
    background-position : center;
    left : 0;
    bottom : 0;
    right : 0;
    top : 0;
}
#loadinggif.show
{
    left : 50%;
    top : 50%;
    position : absolute;
    z-index : 101;
    width : 32px;
    height : 32px;
    margin-left : -16px;
    margin-top : -16px;
}
div.content {
   width : 1000px;
   height : 1000px;
}
</style>
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
  <div class="col-lg-12">
    <h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
            <li class="breadcrumb-item active">Sales Report</li>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Daily Sales Report</h5>
                </div>
                <div class="ibox-content">
                        <div class="row">
                            <div class="pull-right" style="margin-bottom: 10px;">
                                <a href="{{action('ReportsFormController@daily_sales')}}" class="btn btn-primary btn-sm">Create Entry</a>
                            </div>
                            

                            <table class="table" id="sr">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>SR #</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sr as $salesreport)
                                        <tr>
                                            <td>{{$salesreport->dt}}</td>
                                            <td>{{$salesreport->sr_no}}</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
            
        </div>

       <!-- Widget-1 end-->

        <!-- Widget-2 end-->
    </div> <!-- Row end-->

@stop


@section('script')

<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#sr").DataTable();
    });
</script>
@stop