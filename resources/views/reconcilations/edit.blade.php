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
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Bank Reconcilation</a></li>
            <li class="breadcrumb-item active">Add Bank Reconcilation</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Bank Reconcilation Info</h5>

                </div>
             
               {!! Form::model($bankdata,['method'=>'POST','action'=>['BankreconcilationController@update',$bankdata->id],'id'=>'bankform']) !!}
                <div class="ibox-content">
                   @if(Session::has('flash_message'))
                    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
                    @endif
                    @if (count($errors) > 0)
                        <div class="alert alert-danger" style="background:#c0392b;">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li style="color:#FFF;font-size:16px;">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Select Account:') !!}
                                {!! Form::select('coa_id',collect(['' => 'PLEASE SELECT'] + $coa->all()),null,['class'=>'form-control','id'=>'bank_acc']) !!}
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Select Date:') !!}
                                {!! Form::text('dt',\Carbon\Carbon::now()->toDateString(),['class'=>'form-control dt']) !!}
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Beginning Balance:') !!}
                                {!! Form::text('beg_bal',null,['class'=>'form-control','id'=>'beg_bal']) !!}
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Ending Balance:') !!}
                                {!! Form::text('ending_balance',null,['class'=>'form-control','id'=>'ending_bal']) !!}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row spacing">
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Service Charge:') !!}
                                {!! Form::text('service_charge',null,['class'=>'form-control','id'=>'scharge']) !!}
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Select Date:') !!}
                                {!! Form::text('service_date',\Carbon\Carbon::now()->toDateString(),['class'=>'form-control dt']) !!}
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Select Account:') !!}
                                {!! Form::select('service_account_id',collect(['' => 'PLEASE SELECT'] + $account->all()),null,['class'=>'form-control select']) !!}
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Select Department:') !!}
                                {!! Form::select('service_department',[''=>'PLEASE SELECT','ADMIN'=>'ADMIN','GADMIN'=>'GENERAL ADMIN','HATCHERY'=>'HATCHERY','LAYER'=>'LAYER'],null,['class'=>'form-control select']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row spacing">
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Interest Earned:') !!}
                                {!! Form::text('interest_earned',null,['class'=>'form-control','id'=>'iearned']) !!}
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Select Date:') !!}
                                {!! Form::text('interest_date',\Carbon\Carbon::now()->toDateString(),['class'=>'form-control dt']) !!}
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Select Account:') !!}
                                {!! Form::select('interest_account_id',collect(['' => 'PLEASE SELECT'] + $account->all()),null,['class'=>'form-control select']) !!}
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Select Department:') !!}
                                {!! Form::select('interest_department',[''=>'PLEASE SELECT','ADMIN'=>'ADMIN','GADMIN'=>'GENERAL ADMIN','HATCHERY'=>'HATCHERY','LAYER'=>'LAYER'],null,['class'=>'form-control select']) !!}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row spacing">
                        <div class="col-md-6">
                            <p>Cheques and Payments</p>
                            <table id="cheque" class="table table-bordered table-striped" style="text-transform:uppercase;font-size:10px;">
                                <thead>
                                    <tr> 
                                        <th><input type="checkbox" id="checkall" class="check_all"></th>
                                        <th>Date</th>
                                        <th>Cheque #</th>
                                        <th>Payee</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($vouchers as $payment)
                                    <tr>
                                        <td><input type="checkbox" name="cheque_payments_id[]" id="{{$payment->id}}" value="{{$payment->id}}"  data-amount="{{$payment->payment}}" class="flat single check" "></td>
                                        <td>{{$payment->dt}}</td>
                                        <td>{{$payment->cheque_number}}</td>
                                        <td>{{$payment->voucher->supplier_one->supplier_name}}</td>
                                        <td>{{$payment->payment}}</td>
                                    </tr>

                                @endforeach
                                </tbody>
                            </table>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p>Beginning Balance:</p>
                                    <p>Items you have marked cleared</p>
                                    <p>
                                        <span><label id="depocount">0</label></span>
                                        <span style="margin-left:50px;"><label>Deposits</label></span>
                                    </p>
                                    <p>
                                        <span><label id="paycount">0</label></span>
                                        <span style="margin-left:50px;"><label>Checks and Payments</label></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><span><label id="beggining">0.00</label></span></p>
                                    <div style="margin-top:35px;"></div>
                                    <p><span><label id="deposit">0.00</label></span></p>
                                    <p><span><label id="payment">0.00</label></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p>Deposits and Other Credits</p>
                            <table id="depositpayment" class="table table-bordered table-striped" style="text-transform:uppercase;font-size:10px;">
                                <thead>
                                    <tr> 
                                        <th><input type="checkbox" id="checkall" class="check_all"></th>
                                        <th>Date</th>
                                        <th>Cheque #</th>
                                        <th>Memo</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($deposits as $payment)
                                    <tr>
                                        <td><input type="checkbox" name="deposit_other_id[]" id="{{$payment->id}}" value="{{$payment->id}}"  data-amount="{{$payment->amount}}" class="flat single check" <?php echo ($payment->bankrecon_item)? "checked" : ""; ?>></td>
                                        <td>{{$payment->dt}}</td>
                                        <td>{{$payment->cheque_number}}</td>
                                        <td>{{$payment->deposit_memo}}</td>
                                        <td>{{$payment->amount}}</td>
                                    </tr>

                                @endforeach
                                </tbody>
                            </table>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><label for="">Service Charge:</label></p>
                                    <p><label for="">Interest Earned:</label></p>
                                    <p><label for="">Ending Balance:</label></p>
                                    <p><label for="">Cleared Balance:</label></p>
                                    <p><label for="">Difference:</label></p>
                                </div>
                                <div class="col-md-6">
                                    <p><label id="service">0.00</label></p>
                                    <p><label id="interest">0.00</label></p>
                                    <p><label id="ending">0.00</label></p>
                                    <p><label id="cleared">0.00</label></p>
                                    <p><label id="diff">0.00</label></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
        <div class="row spacing">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::submit('SAVE ENTRY', ['class'=>'btn btn-primary','style'=>'margin-top:0px;']) !!}
                </div>
            </div>    
        </div>
                </div> <!-- END IBOXCONTENT-->
                {!! Form::close() !!}
            </div>
            
        </div>

       <!-- Widget-1 end-->

        <!-- Widget-2 end-->
    </div> <!-- Row end-->
    <div id="ajax-modal" class="modal container fade-scale" tabindex="-1" style="display: none;"></div>

@stop


@section('script')

<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>

<script type="text/javascript" src='{{ asset("assets/ui/lib/dp/dist/datepicker.min.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/deposit.js") }}'></script>
<script type="text/javascript">
        

    $(document).ready(function() {

        $(".select").select2({
             theme: "bootstrap"
        });

        $('.dt').datepicker();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#cheque').DataTable();
        $('#depositpayment').DataTable();

        $("#bank_acc").select2({ theme: "bootstrap"})
        .on('change',function(){

            var id = $(this).val();

            $.ajax({
                url: "{{ action('BankreconcilationController@bank_deposit') }}",
                type:"get",
                dataType:'json',   
                data: { id : id },
                success: function(response){
                    var table1 = $('#depositpayment').DataTable().clear();
                    console.log(response);
                    if(response.length != 0){
                        $.each(response,function(index,value){
                              table1.row.add([
                                  '<input type="checkbox" name="deposit_other_id[]" id="'+[value.id]+'" value="'+[value.id]+'"  data-amount="'+[value.amount]+'" class="flat single check" ">',
                                  [value.dt],
                                  [value.cheque_number],
                                  [value.deposit_memo],
                                  [value.amount],

                              ]).draw();


                           });  
                    }else{
                       $('#depositpayment').DataTable().draw();
                    }   
                }
                
            });
            $.ajax({
                url: "{{ action('BankreconcilationController@bank_account') }}",
                type:"get",
                dataType:'json',   
                data: { id : id },

                success: function(response){
                    $("#beg_bal").val(response.balance)
                    $("#beggining").text(response.balance)
                    console.log(response);
                }
                
            });
            $.ajax({
                url: "{{ action('BankreconcilationController@bank_payment') }}",
                type:"get",
                dataType:'json',   
                data: { id : id },

                success: function(response){
                    var table = $('#cheque').DataTable().clear();
                    console.log(response);
                    if(response.length != 0){
                        $.each(response,function(index,value){
                          table.row.add([
                              '<input type="checkbox" name="cheque_payments_id[]" id="'+[value.id]+'" data-amount="'+[value.payment]+'" value="'+[value.id]+'" class="flat single check" ">',
                              [value.dt],
                              [value.cheque_number],
                              [value.voucher.supplier_one.supplier_name],
                              [value.payment],

                          ]).draw();


                       });
                    }else{
                       $('#cheque').DataTable().draw();
                    }   
                }
                
            });
        });

        $( ".dt" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:'yy-mm-dd'
        });

        $("#beg_bal").keyup(function(){
             var total = parseFloat($(this).val()).toFixed(2);
            $("#beggining").text(total);
            clearedData();
            DifferenceData();
        });

        // $("#iearned").keyup(function(){
        //      var total = parseFloat($(this).val()).toFixed(2);
        //     $("#deposit").text(total);
        // });
        // $("#iearned").keyup(function(){
        //      var total = parseFloat($(this).val()).toFixed(2);
        //     $("#payment").text(total);
        // });

        $("#scharge").keyup(function(){
            var total = parseFloat($(this).val()).toFixed(2);
            $("#service").text(total);
            clearedData();
            DifferenceData();
        });

        $("#iearned").keyup(function(){
             var total = parseFloat($(this).val()).toFixed(2);
            $("#interest").text(total);
            clearedData();
            DifferenceData();
        });

        $("#ending_bal").keyup(function(){
             var total = parseFloat($(this).val()).toFixed(2);
            $("#ending").text(total);
            clearedData();
            DifferenceData();
        });
        var deposittotal = 0;
        $("#depositpayment").on('click','.check',function(){
           var total = 0;
           var count = 0;
            $.each($('#depositpayment .check:checked'),function(index,value){
                // var input = $("<input>")
                //    .attr("type", "hidden")
                //    .attr("name", "emp_id[]").val($(this).attr('id'));
                // $('#bankform').append($(input));

                // $('#plantil').submit();
                total +=parseFloat($(value).data('amount'));
                
                
            });
            deposittotal = total;
            count = $('#depositpayment .check:checked').length;
            $("#depocount").text(count);
            $("#deposit").text(total.toFixed(2));
            clearedData();
        });
        var chequetotal = 0;
        $("#cheque").on('click','.check',function(){
           var total = 0;
           var count = 0;
            $.each($('#cheque .check:checked'),function(index,value){
                // var input = $("<input>")
                //    .attr("type", "hidden")
                //    .attr("name", "emp_id[]").val($(this).attr('id'));
                // $('#bankform').append($(input));

                // $('#plantil').submit();
                total +=parseFloat($(value).data('amount'));
                
                
            });
            chequetotal = total;
            count = $('#cheque .check:checked').length;
            $("#paycount").text(count);
            $("#payment").text(total.toFixed(2));
            clearedData();

        });
        var cleared = 0;
        function clearedData(){
            var begs = $("#beggining").text(parseFloat($("#beg_bal").val()).toFixed(2));
            $("#service").text(parseFloat($("#scharge").val()).toFixed(2));
            $("#interest").text(parseFloat($("#iearned").val()).toFixed(2));
            $("#ending").text(parseFloat($("#ending_bal").val()).toFixed(2));
            var total1 = 0;
            var beg =0;
            var interest=0;
            var end =0;
            var charge=0;
            

            beg = parseFloat($("#beggining").text());
            interest =parseFloat($("#interest").text());
            end = parseFloat($("#ending").text());
            charge =parseFloat($("#service").text());
            total1 = ((deposittotal + interest + beg) - (chequetotal+charge)).toFixed(2);
            cleared = total1;
            $("#cleared").text(total1);
            console.log(parseFloat($("#beg_bal").val()).toFixed(2));
            DifferenceData();
        }

        function DifferenceData(){
            var total = 0;
           
            total = cleared - parseFloat($("#ending").text());
            $("#diff").text(total.toFixed(2));
        }

        function data(){
            var total3 = 0;
            var count3 = 0;
            $.each($('#cheque .check:checked'),function(index,value){
           
                total3 +=parseFloat($(value).data('amount'));
                
                
            });
            chequetotal = total3;
            count = $('#cheque .check:checked').length;
            $("#paycount").text(count3);
            $("#payment").text(total3.toFixed(2));

            var total4 = 0;
            var count4 = 0;
            $.each($('#depositpayment .check:checked'),function(index,value){
           
                total4 +=parseFloat($(value).data('amount'));
                
                
            });
            deposittotal = total4;
            count4 = $('#depositpayment .check:checked').length;
            $("#depocount").text(count4);
            $("#deposit").text(total4.toFixed(2));

            clearedData()
        }
        data();

        $("#verify").click(function(){
            var id = "{{$bankdata->id}}";
            $.ajax({
                url: "{{ action('BankreconcilationController@verify_bank_recon') }}",
                type:"get",   
                data: { id : id },

                success: function(response){
                    $("#noti").append('<div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em>This Entry Has Been Verify!</em></div>');
                }
                
            });
        });

    });
</script>
@stop