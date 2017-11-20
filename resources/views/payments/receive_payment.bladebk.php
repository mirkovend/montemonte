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
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Customer Payment</a></li>
            <li class="breadcrumb-item active">Add Payment</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Payment Info</h5>

                </div>
             

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
                    {!! Form::open(['method'=>'POST','action'=>'PaymentController@search_invoice']) !!}
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Select Customer :') !!}
                                @if($charge_invoice != NULL)
                                    {!! Form::select('customer_id',collect(['' => 'PLEASE SELECT'] + $customers->all()),$cust->id,['class'=>'form-control select','id'=>'themes']) !!}
                                @else
                                    {!! Form::select('customer_id',collect(['' => 'PLEASE SELECT'] + $customers->all()),null,['class'=>'form-control select','id'=>'themes']) !!}
                                @endif
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}
                    <hr>

                   
                    <div class="row spacing">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('','Date') !!}
                                {!! Form::text('dt',null,['class'=>'form-control dt']) !!}
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('','Reference Number') !!}
                                {!! Form::text('reference_number',null,['class'=>'form-control']) !!}
                                {!! Form::hidden('customer_id',$charge_invoice->customer_id,['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('','OR Number') !!}
                                {!! Form::text('or_number',null,['class'=>'form-control']) !!}
                            </div>
                        </div>   
                    </div>
                    <div class="row spacing">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('','Amount') !!}
                                {!! Form::text('payment',null,['class'=>'form-control','id'=>'pay']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('','Deposit to') !!}
                                {!! Form::select('deposit_to',collect(['' => 'PLEASE SELECT'] + $coa->all()),null,['class'=>'form-control select']) !!}
                            </div>
                        </div>    
                    </div>
                    <hr>
                    <div class="row spacing">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('','',['id'=>'amt','class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row spacing">
                       
                        <payment></payment>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::submit('SAVE ENTRY', ['class'=>'btn btn-primary','style'=>'margin-top:0px;']) !!}
                            </div>
                        </div>
                    </div>
                </div> <!-- END IBOXCONTENT-->
               
            </div>
            
        </div>

       <!-- Widget-1 end-->

        <!-- Widget-2 end-->
    </div> <!-- Row end-->
<template id="payment">
        <button  @click="addRow">Add row</button>
        <table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkall" class="check_all"></th>
                    <th>Date</th>
                    <th>Invoice Number</th>
                    <th>Amount Due</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
               <tr v-for="addTd in addRows">
                   <td>
                        <select class="form-control select" v-model="addTd.status">
                            <option value="1">Working</option>
                            <option value="0">Defective</option>
                        </select>
                   </td>
               </tr>
            </tbody>
        </table>
        <button  @click="saveRow">Save row</button>
</template>
@stop


@section('script')

<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>

<script type="text/javascript" src='{{ asset("assets/ui/lib/dp/dist/datepicker.min.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/deposit.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/js/components/payment.js") }}'></script>

<script type="text/javascript">
        

    $(document).ready(function() {

        $(".select").select2({
             theme: "bootstrap"
        });

        $( ".dt" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:'yyyy-mm-dd'
        });

        $(".checks").click(function() {
            $total = $(this).closest('tr').attr('id');
            console.log($(this).closest('tr').attr('id'));
            $collect = parseFloat($('#'+$total+" .collectible").val());
            $pay = parseFloat($('#amt').text());
            if($(this).is(":checked")) {
                $amt = $('#'+$total+" .payment").val($collect);
                console.log($pay);
                $('#amt').text($pay - $collect);
            } else {
                $('#amt').text($pay + $collect);
                $('#'+$total+" .payment").val("0.00");
            }
        });

        
        $('#themes').change(function() {
            this.form.submit();
        });
    

    
        $('#pay').keyup(function() {
            //alert('Wewe');
            $('#amt').text($(this).val());
        });

    });
</script>
@stop