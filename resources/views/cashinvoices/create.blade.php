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
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Cash Invoice</a></li>
            <li class="breadcrumb-item active">Add Cash Invoice</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Cash Invoice Info</h5>

                </div>
                <div class="ibox-content">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger" id="">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    {!! Form::open(['method'=>'POST','action'=>'CashinvoiceController@store']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payee</label>
                                   @if($newcustomer == NULL)
                                            {!! Form::select('cash_invoice_to',collect(['' => 'PLEASE SELECT'] + $customer->all()),null,['class'=>'form-control js-example-basic-single']) !!}
                                    @else
                                        {!! Form::select('cash_invoice_to',collect(['' => 'PLEASE SELECT'] + $customer->all()),$newcustomer->id,['class'=>'form-control js-example-basic-single']) !!}
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Account</label>
                                 
                                    {!! Form::select('account',collect(['' => 'PLEASE SELECT'] + $coa->all()),null,['class'=>'form-control js-example-basic-single']) !!}
                            
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-12">
                                <div class="form-group">
                                    <button type="button" style="margin-top:25px;" class="btn btn-primary" data-toggle="modal" href="#myModal"><span class="fa fa-plus"></span></button>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                             <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date</label>
                                   
                                    {!! Form::text('dt',\Carbon\Carbon::now()->toDateString(),['class'=>'form-control dt','datepicker'=>'','data-date-format'=>'yyyy-mm-dd','data-auto-close'=>'true']) !!}
                                   
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('','Cash Invoice Number:') !!}
                                    {!! Form::text('cash_invoice_number',$start_number,['class'=>'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="checkbox" style="margin-top:30px;">
                                    <label>
                                        <input value="1" type="checkbox" name="status">
                                        Cancel Invoice
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('','Department:') !!}
                                    {!! Form::select('dept',[''=>'PLEASE SELECT','ADMIN'=>'ADMIN','GADMIN'=>'GENERAL ADMIN','HATCHERY'=>'HATCHERY','LAYER'=>'LAYER'],null,['class'=>'form-control select']) !!}
                                </div>
                            </div>
                            
                            <div class="clearfix"></div>
                            <hr>
                            <div id="wrap">
                                <div class="wrapp">
                                    <div class="col-md-2 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            {!! Form::label('','Department:') !!}
                                            {!! Form::select('dept[]',[''=>'PLEASE SELECT','ADMIN'=>'ADMIN','GADMIN'=>'GENERAL ADMIN','HATCHERY'=>'HATCHERY','LAYER'=>'LAYER'],null,['class'=>'form-control select']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            {!! Form::label('','Quantity:') !!}
                                            <input type="text" name="cash_invoice_qty[]" class="form-control qty" id="qty" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('','Item:') !!}
                                            {!! Form::select('size_id[]',collect(['' => 'PLEASE SELECT'] + $size->all()),null,['class'=>'form-control js-example-basic-single']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            {!! Form::label('','Unit Price:') !!}
                                            <input type="text" name="unit_price[]" class="form-control unit_price" id="unit_price" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            {!! Form::label('','Amount:') !!}
                                            <input type="text" name="amount[]" class="form-control amount" id="amount" />
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary" id="bot" style="margin-top:24px;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                        </div>
                                    </div>

                                 </div> <!-- end wrap -->

                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr>
                        <div class="row spacing">
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('','Total Amount Due:') !!}
                                    {!! Form::text('',null,['class'=>'form-control','readonly'=>'readonly','id'=>'total']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row spacing">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::submit('SAVE ENTRY', ['class'=>'btn btn-primary','style'=>'margin-top:0px;']) !!}
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
    <div id="ajax-modal" class="modal container fade-scale" tabindex="-1" style="display: none;"></div>
<div id="ajax-modal" class="modal container fade-scale" tabindex="-1" style="display: none;"></div>
</div>
<div class="modal fade" id="myModal"  role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
         {!! Form::open(['method'=>'post','action'=>'CashinvoiceController@create_customer']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="icon-plus"></i> Add Customer</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('','Customer Name:') !!}
                            {!! Form::text('customer_name',null,['class'=>'form-control']) !!}
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
        var x = 1;
        $('#bot').on('click',function(){
        x++;
        var data = '<div class="clearfix"></div><div class="wrapp'+x+'" style="margin-top:20px;"><div class="clearfix"></div>';

            data += '<div class="col-md-2"><div class="form-group">';
            data += '{!! Form::select("dept[]",[""=>"PLEASE SELECT","ADMIN"=>"ADMIN","GADMIN"=>"GENERAL ADMIN","HATCHERY"=>"HATCHERY","LAYER"=>"LAYER"],null,["class"=>"form-control js-example-basic-single"]) !!}';
            data += '</div></div>';
        

            data += '<div class="col-md-2"><div class="form-group">';
            data += '<input type="text" name="cash_invoice_qty[]" class="form-control qty" id="qty" />';
            data += '</div></div>';

          

            data += '<div class="col-md-3"><div class="form-group">';
            data += '{!! Form::select("size_id[]",collect(["" => "PLEASE SELECT"] + $size->all()),null,["class"=>"form-control js-example-basic-single"]) !!}';
            data += '</div></div>';

            data += '<div class="col-md-2"><div class="form-group">';
            data += '<input type="text" name="unit_price[]" class="form-control unit_price" id="unit_price" />';
            data += '</div></div>';

            data += '<div class="col-md-2"><div class="form-group">';
            data += '<input type="text" name="amount[]" class="form-control amount" id="amount" />';
            data += '</div></div>';

            data += '<div class="col-md-1"><div class="form-group">';
            data += '<button type="button" class="btn btn-danger minus" ><i class="fa fa-minus" aria-hidden="true"></i></button>';
            data += '</div></div></div>';

            $('#wrap').append(data);
            $(".wrapp"+x+" .js-example-basic-single").select2({
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

        $(".js-example-basic-single").select2({
             theme: "bootstrap"
        });

        $('[data-toggle="datepicker"]').datepicker();




        var wrapper = '#wrap';
        $(wrapper).on('keyup','.unit_price',function(){

            var a = $(this).parent('div').parent('div').parent('div').attr('class');
            var qty =$("."+a+" .qty").val();

            var total1 = qty * $(this).val();
            $("."+a+' .amount').val(total1);

            var sum = 0;

            $('.amount').each(function(){
                sum += parseFloat(this.value);
            });
            $('#total').val(sum);
        });

        $(wrapper).on('keyup','.amount',function(){

            var a = $(this).parent('div').parent('div').parent('div').attr('class');
            var qty =$("."+a+" .qty").val();

            var total1 = $(this).val() / qty;
            $("."+a+' .unit_price').val(total1);
            
            var sum = 0;

            $('.amount').each(function(){
                sum += parseFloat(this.value);
            });
            $('#total').val(sum);
        });
    });
</script>
@stop