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
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Delivery Receipt</a></li>
            <li class="breadcrumb-item active">Edit Delivery Receipt</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Delivery Receipt Info</h5>

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

                    {!! Form::open(['method'=>'PATCH','action'=>['DeliveryreceiptController@update',$id]]) !!}

                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Date:') !!}
                                {!! Form::text('dt',$dr->dt,['class'=>'form-control dt']) !!}
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Delivery Receipt Number:') !!}
                                {!! Form::text('delivery_receipt_number',$dr->delivery_receipt_number,['class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="clearfix"></div>
                    <div id="wrap">
                        <?php $b = 1;?>
                        @foreach($dr->invoice as $item)
                        <div class="wrappr{{$b}}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('','Quantity:') !!}
                                    <input type="text" name="delivery_receipt_qty[]" class="form-control qty" id="qty" value="{{$item->delivery_receipt_qty}}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('','Size:') !!}
                                    {!! Form::select('size_id[]',collect(['' => 'PLEASE SELECT'] + $size->all()),$item->size_id,['class'=>'form-control size js-example-basic-single']) !!}
                                </div>
                            </div>
                          
                            @if($b==1)
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary" id="bot" style="margin-top:24px;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            @else
                            <div class="col-md-2">
                                <div class="form-group"> 
                                    <button type="button" class="btn btn-danger minus" style="margin-top:24px;"><i class="fa fa-minus" aria-hidden="true"></i></button>
                               </div>
                            </div>
                            @endif
                        </div>
                        <?php $b++;?>
                         <div class="clearfix"></div>
                        @endforeach
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="row spacing">
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Total Quantity:') !!}
                                {!! Form::text('',null,['class'=>'form-control','readonly'=>'readonly','id'=>'total']) !!}
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="clearfix"></div>
                    <div id="wrappe">
                        <?php $a = 1;?>
                        @foreach($dr->egghouseitem as $egg)
                        
                        <div class="wrapperse{{$a}}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('','Quantity:') !!}
                                    <input type="text" name="house_qty[]" class="form-control qty2" id="qty2" value="{{$egg->house_qty}}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('','House:') !!}
                                    {!! Form::select('house_id[]',collect(['' => 'PLEASE SELECT'] + $house->all()),$egg->house_id,['class'=>'form-control unit js-example-basic-single']) !!}
                                </div>
                            </div>
                            
                            @if($a==1)
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary" id="bot2" style="margin-top:24px;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            @else
                            <div class="col-md-2">
                                <div class="form-group"> 
                                    <button type="button" class="btn btn-danger minus2" style="margin-top:24px;"><i class="fa fa-minus" aria-hidden="true"></i></button>
                               </div>
                            </div>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                        <?php $a++;?>
                        @endforeach
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="row spacing">
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Total Quantity:') !!}
                                {!! Form::text('',null,['class'=>'form-control','readonly'=>'readonly','id'=>'total2']) !!}
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
               
                {!! Form::close() !!}
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
        $('#bot2').on('click',function(){
        x++; 
        var data = '<div class="clearfix"></div><div class="wrappers'+x+'" style="margin-top:20px;"><div class="clearfix"></div><div class="col-md-3"><div class="form-group">';  
            data += '<input type="text" name="house_qty[]" class="form-control qty2" id="qty2" />';
            data += '</div></div>';

            data += '<div class="col-md-3"><div class="form-group">';  
            data += '{!! Form::select("house_id[]",collect(["" => "PLEASE SELECT"] + $house->all()),null,["class"=>"form-control js-example-basic-single"]) !!}';
            data += '</div></div>';

            data += '<div class="col-md-2"><div class="form-group">';  
            data += '<button type="button" class="btn btn-danger minus2" ><i class="fa fa-minus" aria-hidden="true"></i></button>';
            data += '</div></div></div>';
 
            $('#wrappe').append(data);
            $(".wrappers"+x+" .js-example-basic-single").select2({
                 theme: "bootstrap"
            });
             
        });

        $('#wrapper').on('click','.minus2',function(){
            var $a2 = $(this).parent('div').parent('div').parent('div').attr('class');
            $('.'+$a2).remove();
            var sum = 0;
            var num = [];
            $('.qty2').each(function(){
                var total = $(this).val();
                num.push(total)
            });
            $.each(num,function(){
                sum += parseFloat(this);
            });
            
            $('#total2').val(sum);
        });

        var y = 1;
        $('#bot').on('click',function(){
        y++; 
        var data = '<div class="clearfix"></div><div class="wrapp'+y+'" style="margin-top:20px;"><div class="clearfix"></div><div class="col-md-3"><div class="form-group">';  
            data += '<input type="text" name="delivery_receipt_qty[]" class="form-control qty" id="qty" />';
            data += '</div></div>';

          

            data += '<div class="col-md-3"><div class="form-group">';                       
            data += '{!! Form::select("size_id[]",collect(["" => "PLEASE SELECT"] + $size->all()),null,["class"=>"form-control js-example-basic-single"]) !!}';
            data += '</div></div>';

            data += '<div class="col-md-2"><div class="form-group">';  
            data += '<button type="button" class="btn btn-danger minus" ><i class="fa fa-minus" aria-hidden="true"></i></button>';
            data += '</div></div></div>';
 
            $('#wrap').append(data);
            $(".wrapp"+y+" .js-example-basic-single").select2({
                 theme: "bootstrap"
            });
             
        });

        $('#wrap').on('click','.minus',function(){
            var $a = $(this).parent('div').parent('div').parent('div').attr('class');
            $('.'+$a).remove();
            var sum = 0;
            var num = [];
            $('.qty').each(function(){
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
            var sum2 = 0;
            $('.qty').each(function(){
                var total = $(this).val();
                sum2 += parseFloat(total);
            });
            
            $('#total').val(sum2);
        $(wrapper).on('keyup','.qty',function(){

            var a = $(this).parent('div').parent('div').parent('div').attr('class');
            var qty =$("."+a+" .qty").val();

            console.log($(this).parent('div').parent('div').parent('div').attr('class'));
            var total1 = qty;
            $("."+a+' .amount').val(total1);

            var sum = 0;
            var num = [];
            $('.qty').each(function(){
                var total = $(this).val();
                num.push(total)
            });
            $.each(num,function(){
                sum += parseFloat(this);
            });
            
            $('#total').val(sum);
        });
        var wrapper = '#wrappe';
        var sum1 = 0;
        $('.qty2').each(function(){
            var total = $(this).val();
            sum1 += parseFloat(total);
        });
        $('#total2').val(sum1);
        $(wrapper).on('keyup','.qty2',function(){

            var a2 = $(this).parent('div').parent('div').parent('div').attr('class');
            var qty =$("."+a2+" .qty2").val();

            console.log($(this).parent('div').parent('div').parent('div').attr('class'));
            var total1 = qty;
            $("."+a2+' .amount').val(total1);

            var sum = 0;
            var num = [];
            $('.qty2').each(function(){
                var total = $(this).val();
                num.push(total)
            });
            $.each(num,function(){
                sum += parseFloat(this);
            });
            
            $('#total2').val(sum);
        });
    });
</script>
@stop