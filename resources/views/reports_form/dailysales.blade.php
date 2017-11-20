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
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Sales Report</a></li>
            <li class="breadcrumb-item active">Add Sales Report</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><a href="{{action('ReportsFormController@index')}}" class="btn btn-success btn-xs"> <span class="glyphicon glyphicon-arrow-left"></span> Back</a> Generate Sales Report</h5>

                </div>
                <div class="ibox-content">
                    <div class="alert alert-success spacing"  v-show="noti"> 
                        <span class="icon-thumbs-up"></span>    
                        <em> @{{message}}</em>
                    </div>

                    {!! Form::open(['method'=>'POST','action'=>'ChargeinvoiceController@store']) !!}

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('','Report Type')!!}
                                    {!! Form::select('type',[''=>'Select Type',1=>'Egg Table',2=>'Misc'],null,['class'=>'form-control','v-model'=>'type','@change'=>'getData()']) !!}

                                </div>
                            </div>
                            <div class="col-md-4" v-show="show">
                                <div class="form-group">
                                    {!! Form::label('','Sales Report #')!!}
                                    {!! Form::text('sr_no',null,['class'=>'form-control','v-model'=>'sr_no']) !!}
                                   

                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div v-show="show">
                                <div v-if="loading == true">
                                    <div id="divLoading"></div>
                                    
                                </div>
                                <div v-else>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12" >
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Qty</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="cash in cashes">
                                                            <td>@{{cash.product}}</td>
                                                            <td>@{{cash.qty}}</td>
                                                            <td>@{{cash.amount}}</td>
                                                        </tr>
                                                        <tr v-if="cashes.length == 0">
                                                            <td colspan="3" class="text-center">No Data Available</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-md-12 space">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            
                                                            <th colspan="3" class="text-center">Details of Credit Sales</th>
                                                            
                                                        </tr>
                                                        <tr>
                                                            <th>Invoice #</th>
                                                            <th>Customers</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="collection in collections">
                                                            <td>@{{ collection.invoice_number}}</td>
                                                            <td>@{{ collection.customer}}</td>
                                                            <td>@{{ collection.amount}}</td>
                                                        </tr>
                                                        <tr v-if="collections.length == 0">
                                                            <td colspan="3" class="text-center">No Data Available</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h3 class="text-right">{!! Form::label('','Ave. price :')!!} &#x20B1; @{{aveprice}} </h3>
                                                </div>
                                                <div class="col-md-12">
                                                    <h3 class="text-right">{!! Form::label('','Total Cash Turn Over :')!!} &#x20B1; @{{turnover}} </h3>
                                                    
                                                </div>
                                            </div>
                                        
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    
                        <div class="row spacing" v-show="show">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::button('SAVE ENTRY', ['class'=>'btn btn-primary','style'=>'margin-top:0px;','@click'=>'submit']) !!}
                                </div>
                            </div>
                        </div>
                    {!! Form::close()!!}
                </div>
            </div>
            
        </div>

       <!-- Widget-1 end-->

        <!-- Widget-2 end-->
    </div> <!-- Row end-->

@stop


@section('script')

<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript" src='{{ asset("assets/js/moment.min.js") }}'></script>
<script type="text/javascript">

    Vue.config.devtools = true;
    Vue.config.debug = true;
    Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');
    var vm = new Vue({
        el:'body',

        data:{
            list:[],
            cashes:[],
            collections:[],
            type:"",
            sr_no:"",
            noti:false,
            message:"",
            loading:false,
            show:false,
            turnover:0,
            aveprice:0,
        },

        methods:{
            getData:function(){
                this.loading = true;
                this.show = true;
                page_url = '{{action("ReportsFormController@type")}}';
                var resource = this.$resource(page_url);    
                
                resource.get({query:this.type}).then(response => {
                    console.log(response.data.cash);
                    this.cashes = response.data.cash;
                    this.collections = response.data.collection;
                    this.turnover = response.data.turnover;
                    this.aveprice = response.data.aveprice;
                    this.loading = false;
                }, response => {
                    this.cashes = [];
                    this.collections = [];
                    this.turnover = 0;
                    this.aveprice = 0;
                    this.loading = false;
                });

            },
            submit:function(){
                this.loading = true;
                this.noti = true;
                page_url = '{{action("ReportsFormController@store")}}';
                var resource = this.$resource(page_url);  
                resource.save({turnover:this.turnover,aveprice:this.aveprice,sr_no:this.sr_no}).then(response => {
                    this.message = "Sales Report Entry Save.";
                    this.loading = false;
                }, response => {
                    // error callback
                });
            }
        }

    });

</script>
@stop