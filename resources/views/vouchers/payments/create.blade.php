@extends('template.theme')

@section('css')
<link href='{{ asset("assets/datatable/jquery.dataTables.min.css") }}' rel="stylesheet">
<link href='{{ asset("assets/ui/lib/dp/dist/datepicker.min.css") }}' rel="stylesheet">
<style type="text/css">
.select2-container {
    width: 100% !important;
}
</style>
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
  <div class="col-lg-12">
    <h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Voucher</a></li>
            <li class="breadcrumb-item active">Add Voucher Payment</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Voucher Info</h5>

                </div>
                
                <div class="ibox-content">
                @if(Session::has('flash_message'))
                    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
                @endif
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {!! Form::open(['method'=>'POST','action'=>'VouchersPaymentController@store']) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('','Date') !!}
                                {!! Form::text('dt',\Carbon\Carbon::now()->toDateString(),['class'=>'form-control dt']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('','Cheque Number') !!}
                                {!! Form::text('cheque_number',null,['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('','Account') !!}
                                {!! Form::select('account',collect(['' => 'PLEASE SELECT'] + $account->all()),null,['class'=>'form-control select']) !!}
                            </div>
                        </div>
                    </div>
                    <hr>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkall" class="check_all"></th>
                                        <th>Date Due</th>
                                        <th>Payee</th>
                                        <th>Voucher Number</th>
                                        <th>Amount Due</th>
                                        <th>Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vouchers as $data)
                                    <tr>
                                        <td><input type="checkbox" name="id[]" value="{{$data->id}}" /></td>
                                        <td>{{ $data->bill_due}}</td>
                                        <td>{{  $data->supplier->first()->supplier_name  }}</td>
                                        <td>CV - {{ $data->voucher_number }}</td>
                                        <?php 
                                            $v_payments = App\Voucher_payment::where('voucher_number',$data->voucher_number)->sum('payment');
                                        ?>
                                        <td>{{ number_format($data->amount - $v_payments,2) }}</td>

                                        <td>
                                            {!! Form::text('payment[]','0.00',['class'=>'form-control']) !!}
                                            {!! Form::hidden('voucher_number[]',$data->voucher_number,['class'=>'form-control']) !!}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
            dateFormat:'yy-mm-dd'
        });
        $("#tableSortable").DataTable();


    });
</script>
@stop