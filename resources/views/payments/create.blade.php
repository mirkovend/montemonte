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
                                    {!! Form::select('customer_id',collect(['' => 'PLEASE SELECT'] + $customers->all()),$customer_id,['class'=>'form-control select','id'=>'themes']) !!}
                                @else
                                    {!! Form::select('customer_id',collect(['' => 'PLEASE SELECT'] + $customers->all()),null,['class'=>'form-control select','id'=>'themes']) !!}
                                @endif
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    <hr>
                    {!! Form::open(['method'=>'POST','action'=>'PaymentController@store']) !!}
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
                                {!! Form::hidden('customer_id',$customer_id,['class'=>'form-control']) !!}
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
                                <?php $x=0; ?>
                                @foreach($charge_invoice as $data)
                                    <tr id="total{{$x}}">
                                        <td><input type="checkbox" name="id[]" value="{{ $data->id }}" class="checks" /></td>
                                        <td>{{ $data->dt }}</td>
                                        <td>
                                            {{ $data->charge_invoice_number }}
                                            
                                            <input type="hidden" name="charge_invoice_number[<?php echo $data->id; ?>][ci]" value="{{ $data->charge_invoice_number }}">
                                        </td>
                                        <td>
                                            @if($data->payment != NULL)
                                                P {{ number_format($data->invoice->where('charge_invoice_number',$data->charge_invoice_number)->sum('amount') - $data->payment->sum('payment'),2) }}

                                                {!! Form::hidden('payments[]',$data->invoice->where('charge_invoice_number',$data->charge_invoice_number)->sum('amount') - $data->payment->sum('payment'),['class'=>'form-control','class'=>'collectible']) !!}
                                            @else
                                                P {{ number_format($data->invoice->where('charge_invoice_number',$data->charge_invoice_number)->sum('amount'),2) }}

                                                {!! Form::hidden('payments[]',$data->invoice->where('charge_invoice_number',$data->charge_invoice_number)->sum('amount'),['class'=>'form-control','class'=>'collectible']) !!}
                                            @endif
                                        </td>
                                        <td>
                                            
                                        <input type="text" name="payment_made[<?php echo $data->id; ?>][py]" value="0.00" class="form-control payment">

                                        </td>
                                    </tr>
                                <?php $x++; ?>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
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
<div id="ajax-modal" class="modal container fade-scale" tabindex="-1" style="display: none;"></div>
</div>
<div class="modal fade" id="myModal"  role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          {!! Form::open(['method'=>'post','action'=>'PurchaseorderController@create_supplier']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="icon-plus"></i> Add Supplier</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('','Supplier Name:') !!}
                            {!! Form::text('supplier_name',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('','Beginning Balance:') !!}
                            {!! Form::text('beginning_bal',null,['class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">CLOSE</button>
                {!! Form::submit('INSERT ENTRY', ['class'=>'btn btn-lg btn-primary','style'=>'margin-top:0px;']) !!}
            </div>
            {!! Form::close() !!}
        </div>
      </div>
    </div>
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