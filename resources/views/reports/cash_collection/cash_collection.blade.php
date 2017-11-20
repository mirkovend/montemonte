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
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Reports</a></li>
            <li class="breadcrumb-item active">Collection Report</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Collection Report</h5>

                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="3"></th>
                                        <th colspan="2" class="text-center">TABLE EGGS</th>
                                        <th colspan="6"></th>
                                    </tr>
                                    <tr>
                                        <th>Date</th>
                                        <th>OR #</th>
                                        <th>SR/PR #</th>
                                        <th>Cash</br>Sales</th>
                                        <th>A/R</th>
                                        <th>Toll</br>Hatched</th>
                                        <th>Chickend</br>Dung</th>
                                        <th>Cull</br>Layer</th>
                                        <th>Ready</br>to Lay</th>
                                        <th>Cartoon</br>Egg Tray</th>
                                        <th>Other</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="data in list">
                                        <td>@{{data.dt}}</td>
                                        <td>@{{data.or}}</td>
                                        <td>@{{data.ref}}</td>
                                        <td>@{{data.cash}}</td>
                                        <td>@{{data.collection}}</td>
                                        <td>@{{data.toll_hatched}}</td>
                                        <td>@{{data.chiken_dung}}</td>
                                        <td>@{{data.cull_layer}}</td>
                                        <td>@{{data.ready_to_lay}}</td>
                                        <td>@{{data.cartoon}}</td>
                                        <td>@{{data.others}}</td>
                                    </tr>
                                    
                                </tbody>
                            </table>
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
            list:[],
            cash_sales:0,
            data:{
                or_number:"",
                dailysales_id:"",
            }

        },
        created: function(){
            this.getData();
        },
        methods:{
            getData:function(){
                page_url = '{{action("ReportsFormController@collection_data")}}';

                var resource = this.$resource(page_url);  
                resource.save(this.data).then(response => {
                    this.list = response.data.results;
                    
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
