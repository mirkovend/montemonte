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
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Cash Sales</a></li>
            <li class="breadcrumb-item active">Generate Cash Sales</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Cash Sales</h5>

                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>SR #</label>
                                {!! Form::select('customer_id',collect(['' => ''] + $sr->all()),null,['class'=>'form-control']) !!}
                               
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                               <label>OR #</label>
                               {!! Form::text('or_number',null,['class'=>'form-control','v-model'=>"data.or_number"]) !!}
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <h2>Total Cash Sales: @{{cash_sales}} </h2>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row spacing" v-show="show">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::button('SAVE ENTRY', ['class'=>'btn btn-primary','style'=>'margin-top:0px;','@click'=>'submit']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Row end-->

@stop


@section('script')

<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>

<script type="text/javascript" src='{{ asset("assets/ui/lib/dp/dist/datepicker.min.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/deposit.js") }}'></script>
<script type="text/javascript">
    Vue.config.devtools = true;
    Vue.config.debug = true;
    Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');
    var vm = new Vue({
        el:'body',

        data:{
            show:false,
            cash_sales:0,
            data:{
                or_number:"",
                dailysales_id:"",
            }

        },

        methods:{
            select_sr:function(){
                page_url = '{{action("ReportsFormController@sr_data")}}';

                var resource = this.$resource(page_url);  
                resource.get({sr_id:this.data.dailysales_id}).then(response => {

                    if(response.data.result != null){
                        this.show=true;
                        this.cash_sales = response.data.result.turnover;
                    }else{
                        this.show=false;
                        this.cash_sales = 0;
                    }

                }, response => {

                });

            },
            submit:function(){
                page_url = '{{action("ReportsFormController@cash_sales_store")}}';
                var resource = this.$resource(page_url);  
                resource.save(this.data).then(response => {
                   this.show = false;
                }, response => {
                    // error callback
                });
            }
        },
        attached: function() {

            var args = {
                    placeholder: "Select SR #",
                    allowClear: true,
                    theme:"bootstrap",
            };
            this.$nextTick(function() {
                $('select').select2(args).on('change', function(event) {
                   vm.data.dailysales_id = $(this).val();
                   vm.select_sr();
                });;
            });

        }

    });

</script>
@stop
