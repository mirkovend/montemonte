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
            <li class="breadcrumb-item active">Add Voucher</li>
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
                    {!! Form::open(['method'=>'POST','action'=>'VoucherController@search_po_list']) !!}
                    
                        <div class="row">
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('','Payee') !!}
                                    {!! Form::select('supplier_id',collect(['' => 'PLEASE SELECT'] + $suppliers->all()),$supplier,['class'=>'form-control select','id'=>'supplier_id']) !!}
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('','Voucher Type') !!}
                                    {!! Form::select('voucher_type',['' => 'PLEASE SELECT','WITH PO' => 'WITH PO','WITHOUT PO' => 'WITHOUT PO','WITH JO' => 'WITH JO','WITH PCF' => 'WITH PCF'],$voucher_type,['class'=>'form-control select','id'=>'voucher_type']) !!}
                                </div>
                            </div>
                            <div class="show_button" style="display:none;">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::submit('DISPLAY RECORDS', ['class'=>'btn btn-primary','style'=>'margin-top:25px;']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    {!! Form::close() !!}
                    {!! Form::open(['method'=>'POST','action'=>'VoucherController@store','id'=>'form_voucher']) !!}
                        <div class="row spacing">
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('','Date:') !!}
                                    {!! Form::text('dt',\Carbon\Carbon::now()->toDateString(),['class'=>'form-control dt']) !!}
                                    
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('','Voucher Number: (CV-00000)') !!}
                                    {!! Form::text('voucher_number',$start_number,['class'=>'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="checkbox" style="margin-top:30px;">
                                    <label>
                                        <input value="1" type="checkbox" name="status">
                                        Cancel Voucher
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row spacing">
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('','Explanation:') !!}
                                    {!! Form::text('explanation',null,['class'=>'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('','Amount:') !!}
                                    {!! Form::text('amount',null,['class'=>'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('','Bill Due:') !!}
                                    {!! Form::text('bill_due',\Carbon\Carbon::now()->toDateString(),['class'=>'form-control dt']) !!}
                                </div>
                            </div>
                        </div>
                        <hr>
                        @if(!empty($po_list))
                        <div class="row spacing po_list" style="">
                            <div class="col-md-12">
                                <h3>Journal Entry</h3>
                                <table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkall" class="check_all"></th>
                                            <th>Purchase Order Number</th>
                                            <th>Purchase Order Date</th>
                                            <th>Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($po_list as $row)
                                        <?php
                                            $voucher_payment = $row->invoice->sum('item_total');
                                            $sum_vi = 0;
                                            $sum_poi = 0;
                                            foreach($row->invoice as $poi){
                                                $sum_poi += $poi->item_total;
                                                
                                                foreach($poi->voucher_item as $vi){
                                                   $sum_vi += $vi->po_item->item_price * $vi->item_rcv;
                                                }                             
                                            }
                                            
                                            $balance =  $sum_poi - $sum_vi;
                                            
                                            
                                        ?>
                                        <td><input type="checkbox" name="purchase_order_number[]" value="{{$row->purchase_order_number}}" /></td>
                                        <td><a href="#" data-toggle="modal" data-target="#po_modal{{ $row->id }}">{{ $row->purchase_order_number }}</a></td>
                                        <td>{{ \Carbon\Carbon::parse($row->dt)->toFormattedDateString() }}</td>
                                        <td>
                                            {{ number_format($balance,2) }}
                                            {!! Form::hidden('purchase_order_amount[]',$balance,['class'=>'form-control']) !!}

                                        </td>

                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @elseif(!empty($petty))
                        <div class="row spacing po_list" style="">
                            <div class="col-md-12">
                                <h3>Journal Entry - PETTY CASH</h3>
                                <table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkall" class="check_all"></th>
                                            <th>PCF Number</th>
                                            <th>PCF To</th>
                                            <th>PCF Date</th>
                                            <th>Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($petty as $p_data)
                                        <?php 
                                            $pcf_payment = App\Voucher_pcf_item::where('pcf_number',$p_data->pcf_number)->sum('pcf_payment');
                                        ?>
                                        <tr>
                                            <td><input type="checkbox" name="id[]" value="{{$p_data->id}}" /></td>
                                            <td>
                                                {{ $p_data->pcf_number }}
                                                <input type="hidden" name="pcf_number[<?php echo $p_data->id; ?>][pcf]" value="{{ $p_data->pcf_number }}">
                                            </td>
                                            <td>{{ $p_data->supplier->first()->supplier_name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($p_data->dt)->toFormattedDateString() }}</td>
                                            <td>
                                                P {{ number_format($p_data->invoice->sum('amount') - $pcf_payment,2) }}
                                                <input type="hidden" name="pcf_payment[<?php echo $p_data->id; ?>][py]" value="{{ $p_data->invoice->sum('amount') - $pcf_payment}} " class="form-control payment">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @elseif(!empty($job))
                        <div class="row spacing po_list" style="">
                            <div class="col-md-12">
                                <h3>Journal Entry - JOB ORDER</h3>
                                <table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkall" class="check_all"></th>
                                            <th>Job Order Number</th>
                                            <th>Job Order To</th>
                                            <th>Job Order Date</th>
                                            <th>Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($job as $j_data)
                                        <?php 
                                           $joborder_payment = App\Voucher_job_item::where('joborder_number',$j_data->joborder_number)->sum('joborder_payment');
                                        ?>
                                        <tr>
                                            <td><input type="checkbox" name="id[]" value="{{$j_data->id}}" /></td>
                                            <td>
                                                {{$j_data->joborder_number}}
                                                <input type="hidden" name="joborder_number[<?php echo $j_data->id; ?>][jo]" value="{{ $j_data->joborder_number }}">
                                            </td>
                                            <td>{{$j_data->supplier->first()->supplier_name }}</td>
                                            <td>{{\Carbon\Carbon::parse($j_data->dt)->toFormattedDateString()}}</td>
                                            <td>
                                                P {{ number_format($j_data->invoice->sum('amount') - $joborder_payment,2) }}
                                                
                                                <input type="hidden" name="joborder_payment[<?php echo $j_data->id; ?>][py]" value="{{ $j_data->invoice->sum('amount') - $joborder_payment }}">
                                            </td>

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="journal_entry" style="display:none;margin-top:10px;margin-bottom:10px;">
                            <hr><h3>Journal Entry</h3><hr>
                            <div class="clearfix"></div>
                            
                            <div id="wrap">
                                <div class="wrapp">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('','Account Title') !!}
                                            {!! Form::select('coa_id[]',collect(['' => 'PLEASE SELECT'] + $coa->all()),null,['class'=>'form-control select']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('','Debit') !!}
                                            {!! Form::text('debit[]',null,['class'=>'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('','Credit') !!}
                                            {!! Form::text('credit[]',null,['class'=>'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary" id="bot" style="margin-top:24px;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>   
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::submit('SAVE ENTRY', ['class'=>'btn btn-primary','style'=>'margin-top:25px;']) !!}
                                </div>
                            </div>
                        </div>
                </div> <!-- END IBOXCONTENT-->
        
            </div>
            
        </div>

       <!-- Widget-1 end-->

        <!-- Widget-2 end-->
    </div> <!-- Row end-->
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
         
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">CLOSE</button>
            {!! Form::submit('INSERT ENTRY', ['class'=>'btn btn-lg btn-primary','style'=>'margin-top:0px;']) !!}
        </div>
     
    </div>
  </div>
</div>


@if(!empty($po_list))
@foreach($po_list as $row)
<div id="po_modal{{ $row->id }}" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="icon-plus"></i> Purchase Order Info</h4>
            </div>
            <div class="modal-body">
                <?php 
                    $voucher_payment = $row->invoice->sum('item_total');
                    $sum_vi = 0;
                    $sum_poi = 0;
                    foreach($row->invoice as $poi){
                        $temp = 0;
                        $sum_poi += $poi->item_total;
                        foreach($poi->voucher_item as $vi){
                           $temp += $vi->item_rcv;
                        } 
                ?>
                    @if($poi->item_qty != $temp)
                    <div class="row">
                        <div class="col-md-2">
                            <input type="checkbox" name="id[]" value="{{$poi->id}}" />
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li>Date Posted: {{ $poi->dt }}</li>
                                <li>Item Description: {{ $poi->item_desc }}</li>
                                <li>Item Qty: {{ $poi->item_qty - $temp }}</li>
                                <li>Item Price: {{ $poi->item_price }}</li>
                                <li>Item Total: {{ number_format((($poi->item_qty - $temp) *  $poi->item_price),2) }}</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('item_rcv[]',null,['class'=>'form-control','placeholder'=>'Enter Quantity Received']) !!}
                        </div>
                    </div>
                    @endif
                <?php } ?>                    
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@elseif(!empty($job))
@foreach($job as $j_data)
<div id="pcf_modal{{ $j_data->id }}" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="icon-plus"></i> Job Order Info</h4>
            </div>
            <div class="modal-body">
                <?php 
                    foreach($j_data->invoice as $job){
                        $job_id = App\Voucher_job_item::where('joborder_id',$job->id)->sum('joborder_payment');
                ?>
                @if($job->amount != $job_id)
                <div class="row">
                    <div class="col-md-2">
                        <input type="checkbox" name="id[]" value="{{$job->id}}" />
                    </div>
                    <div class="col-md-6">
                        <ul>
                            <li>Description: {{ $job->description }} {{ $job->id }}</li>
                            <li>Amount: P {{ $job->amount }}</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        {!! Form::text('joborder_payment[]',null,['class'=>'form-control','placeholder'=>'Enter Payment Amount']) !!}
                    </div>
                </div>
                @endif
                <?php } ?>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif

{!! Form::close() !!}
@stop


@section('script')

<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>

<script type="text/javascript" src='{{ asset("assets/ui/lib/dp/dist/datepicker.min.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/deposit.js") }}'></script>
<script type="text/javascript">

       


    $(document).ready(function() {



        $("[name='voucher_type']").change(function(){ 
            if($(this).val() == "WITHOUT PO" ){
                $('.journal_entry').slideDown();
                $('.show_button').slideUp();
                $('.po_list').slideUp();
            }else if($(this).val() == "WITH PO"){
                $('.journal_entry').slideUp();
                $('.show_button').slideDown();
                $('.po_list').slideDown();
            }else if($(this).val() == "WITH PCF"){
                $('.show_button').slideDown();
                $('.journal_entry').slideUp();
                $('.po_list').slideUp();
            }else if($(this).val() == "WITH JO"){
                $('.show_button').slideDown();
                $('.journal_entry').slideUp();
                $('.po_list').slideUp();
            }
        });

        $('#myTable1').DataTable();

        $("#checkall").click(function () {
            $('#myTable1 tbody input[type="checkbox"]').prop('checked', this.checked);
        });

        $("#form_voucher").submit(function(){
            $sp_id = $("#supplier_id").val();
            $vt = $("#voucher_type").val();
            $('<input />').attr('type', 'hidden')
                .attr('name',"supplier_id")
                .attr('value', $sp_id)
                .appendTo('#form_voucher');
            $('<input />').attr('type', 'hidden')
                .attr('name',"voucher_type")
                .attr('value', $vt)
                .appendTo('#form_voucher');
            return true;
        });
        
        var x = 1;
        $('#bot').on('click',function(){
        x++; 
        var data = '<div class="clearfix"></div><div class="wrapp'+x+'" style="margin-top:20px;"><div class="clearfix"></div><div class="col-md-3"><div class="form-group">';  
            data += '{!! Form::select("coa_id[]",collect(["" => "PLEASE SELECT"] + $coa->all()),null,["class"=>"form-control select"]) !!}';
            data += '</div></div>';

            data += '<div class="col-md-3"><div class="form-group">';                       
            data += '{!! Form::text("debit[]",null,["class"=>"form-control"]) !!}';
            data += '</div></div>';

            data += '<div class="col-md-3"><div class="form-group">';                       
            data += '{!! Form::text("credit[]",null,["class"=>"form-control"]) !!}';
            data += '</div></div>';

            data += '<div class="col-md-2"><div class="form-group">';  
            data += '<button type="button" class="btn btn-primary minus" ><i class="fa fa-minus" aria-hidden="true"></i></button>';
            data += '</div></div></div>';
 
            $('#wrap').append(data);
            $(".wrapp"+x+" .select").select2({
                 theme: "bootstrap"
            });
             
        });

        $('#wrap').on('click','.minus',function(){
            var $a = $(this).parent('div').parent('div').parent('div').attr('class');
            $('.'+$a).remove();
            var sum = 0;
            var num = [];
            $('.amount').each(function(){
                var total = $(this).val();
                num.push(total)
            });
            $.each(num,function(){
                sum += parseFloat(this);
            });
            
            $('#total').val(sum);
        });

   
        $(".select").select2({
             theme: "bootstrap"
        });

        $('.dt').datepicker();



    });
</script>
@stop