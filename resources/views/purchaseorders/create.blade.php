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
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Purchase Order</a></li>
            <li class="breadcrumb-item active">Add Purchase Order</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Purchase Order Info</h5>

                </div>
                {!! Form::open(['method'=>'POST','action'=>'PurchaseorderController@store']) !!}
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
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            @if($newsupplier == NULL)
                            <div class="form-group">
                                {!! Form::label('','To:') !!}
                                {!! Form::select('supplier_id',collect(['' => 'PLEASE SELECT'] + $suppliers->all()),null,['class'=>'form-control select']) !!}
                            </div>
                            @else
                            <div class="form-group">
                                {!! Form::label('','To:') !!}
                                {!! Form::select('supplier_id',collect(['' => 'PLEASE SELECT'] + $suppliers->all()),$newsupplier->id,['class'=>'form-control select']) !!}
                            </div>
                            @endif
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <div class="form-group">
                                <button type="button" style="margin-top:25px;" class="btn btn-primary" data-toggle="modal" href="#myModal"><span class="fa fa-plus"></span></button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row spacing">
                        <div class="col-md-6 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Purchase Order Number:') !!}
                                {!! Form::text('purchase_order_number',$start_number,['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Date:') !!}
                                {!! Form::text('dt',\Carbon\Carbon::now()->toDateString(),['class'=>'form-control dt']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row spacing">
                        <div class="col-md-6 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Deliver/Ship to:') !!}
                                {!! Form::select('purchase_order_to',[''=>'PLEASE SELECT','LAYER'=>'LAYER','HATCHERY'=>'HATCHERY','FEEDMILL'=>'FEEDMILL','OFFICE'=>'OFFICE','RESIDENCE'=>'RESIDENCE'],null,['class'=>'form-control select']) !!}
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Terms:') !!}
                                {!! Form::select('term_id',collect(['' => 'PLEASE SELECT'] + $terms->all()),null,['class'=>'form-control select']) !!}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Purchase Order Type:') !!}
                                {!! Form::select('purchase_order_type',[''=>'PLEASE SELECT','INVENTORY'=>'INVENTORY','EXPENSE'=>'EXPENSE'],null,['class'=>'form-control select']) !!}
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Receiving Date:') !!}
                                {!! Form::text('return_dt',null,['class'=>'form-control dt']) !!}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="clearfix"></div>
                    <div id="wrap">
                        <div class="wrapp">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::select('account_item_id[]',collect(['' => 'PLEASE SELECT ITEM'] + $coa_item->all()),null,['class'=>'form-control select']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" name="item_desc[]" class="form-control" placeholder="Description" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::select('item_label[]',[''=>'PLEASE SELECT DEPT','ADMIN'=>'ADMIN','GADMIN'=>'GENERAL ADMIN','HATCHERY'=>'HATCHERY','LAYER'=>'LAYER'],null,['class'=>'form-control select']) !!}
                                </div>
                            </div>
                            <div class="row spacing">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" name="item_qty[]" class="form-control qty" placeholder="Quantity" id="qty"/>
                                    </div>
                                </div>
                           
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" name="item_price[]" class="form-control unit_price" placeholder="Unit Price" id="unit_price"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" name="item_total[]" class="form-control amount" id="amount" placeholder="Amount"/>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary" id="bot" style="margin-top:0px;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
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

         var x = 1;
        $('#bot').on('click',function(){
        x++; 
        var data = '<div class="clearfix"></div><div class="wrapp'+x+'" style="margin-top:20px;"><div class="clearfix"></div><div class="row"><div class="col-md-4"><div class="form-group">';  
            data += '{!! Form::select("account_item_id[]",collect(["" => "PLEASE SELECT ITEM"] + $coa_item->all()),null,["class"=>"form-control select"]) !!}';
            data += '</div></div>';

            data += '<div class="col-md-4"><div class="form-group">';                       
            data += '<input type="text" name="item_desc[]" class="form-control" placeholder="Description" />';
            data += '</div></div>';

            data += '<div class="col-md-3"><div class="form-group">';                       
            data += '{!! Form::select("item_label[]",[""=>"PLEASE SELECT","ADMIN"=>"ADMIN","GADMIN"=>"GENERAL ADMIN","HATCHERY"=>"HATCHERY","LAYER"=>"LAYER"],null,["class"=>"form-control select"]) !!}';
            data += '</div></div></div>';

            data += '<div class="row spacing">';

            data += '<div class="col-md-3"><div class="form-group">';                       
            data += '<input type="text" name="item_qty[]" class="form-control qty" placeholder="Quantity" id="qty"/>';
            data += '</div></div>';

    

            data += '<div class="col-md-3"><div class="form-group">';                       
            data += '<input type="text" name="item_price[]" class="form-control unit_price" placeholder="Unit Price" id="unit_price"/>';
            data += '</div></div>';

            data += '<div class="col-md-2"><div class="form-group">';                       
            data += '<input type="text" name="item_total[]" class="form-control amount" id="amount" placeholder="Amount"/>';
            data += '</div></div>';

            data += '<div class="col-md-1"><div class="form-group">';  
            data += '<button type="button" class="btn btn-primary minus" ><i class="fa fa-minus" aria-hidden="true"></i></button>';
            data += '</div></div></div>';
            

            /*$('.wrapp').on('keyup','#amount',function(){
                var amount;
                var qty;
                amount = parseFloat($('#amount').val());
                qty = parseFloat($('#qty').val());
                var result = amount/qty;
                $('#unit_price').val(result.toFixed(2));        
            });*/


            $('#wrap').append(data);
            $(".wrapp"+x+" .select").select2({
                theme: "bootstrap"
            });

        });

        $('#wrap').on('click','.minus',function(){
            var $a = $(this).parent('div').parent('div').parent('div').parent('div').attr('class');
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
        var wrapper = '#wrap';
        $(wrapper).on('keyup','.amount',function(){
            var a = $(this).parent('div').parent('div').parent('div').parent('div').attr('class');
            var qty =$("#wrap ."+a+" .qty").val();

            console.log(a);
            var total1 = $(this).val() / qty;
            $("#wrap ."+a+' .unit_price').val(total1);
  
        });


    });
</script>
@stop