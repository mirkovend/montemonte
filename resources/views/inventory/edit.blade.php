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
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Inventory</a></li>
            <li class="breadcrumb-item active">Edit Inventory</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Inventory Info</h5>

                </div>
             
                {!! Form::model($inv,['method'=>'PATCH','action'=>['InventoryController@update',$inv->id]]) !!}
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
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Date:') !!}
                                {!! Form::text('dt',null,['class'=>'form-control select dt']) !!}
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('','Reference #:') !!}
                                {!! Form::text('ref_no',null,['class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row spacing">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('','Adjustment Item :') !!}
                                {!! Form::select('adjustment_item',([''=>'Select Item'] + $item->all()),$inv->item_id,['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <!-- <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('','Type :') !!}
                                {!! Form::select('type',([''=>'Select Type','plus'=>'Plus','minus'=>'Minus']),null,['class'=>'form-control']) !!}
                            </div>
                        </div> -->
                    </div>
                    <hr>
                    <div class="row spacing">
                        <div class="col-md-12">
                            <table class="table" id="tableitem">
                                <thead>
                                    <tr>
                                        <th width="40%">Item</th>
                                        <th class="text-center">Quantity On-Hand</th>
                                        <th class="text-center">Quantity Difference</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $x= 1;?>
                                    @foreach($inv->inv_items as $invitem)
                                    <?php 
                                      $total = $invitem->item_account->quantity + ($invitem->item_account->item_flow()->sum('debit') - $invitem->item_account->item_flow()->sum('credit'));

                                        $price = 0;

                                        if( $invitem->item_account->unit_measure == "bags"){
                                            $price =  $invitem->item_account->item_flow_one->ave_cost;
                                        }else{
                                            if($invitem->item_account->po_item){
                                                $price =  $invitem->item_account->po_item->item_price;
                                            }else{
                                                $price = 0;
                                            }
                                            
                                        }
                                    ?>
                                    <tr id="i0{{$invitem->id}}">
                                        <td>
                                        {!! Form::select('items_id[]',([''=>'Select Item'] + $item->all()),$invitem->item_id,['id'=>'items','class'=>'form-control items']) !!}
                                        <input type="hidden" class="itemprice" name="itemprice[]" value="{{$price}}">
                                        </td>
                                        <td class="qtyonhand text-center">{{number_format($total+$invitem->credit_quantity,2)}}</td>
                                        <td><input type="text" name="quantity[]" class="form-control" value="{{$invitem->credit_quantity}}"></td>
                                        <td>
                                        @if($x > 1)
                                        <button class="btn btn-danger delete" id="delete"><span class=" glyphicon glyphicon-minus"></span></button>
                                        @else
                                        <button class="btn btn-primary" id="addmore"><span class=" glyphicon glyphicon-plus"></span></button>
                                        @endif
                                        </td>
                                    </tr>
                                    <?php $x++;?>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('','Layer Quantity :') !!}
                                    {!! Form::text('lm_quantity',null,['class'=>'form-control']) !!}
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("select").select2({
             theme: "bootstrap"
        });

        
        $('.dt').datepicker({
          format: 'yyyy-mm-dd'
        });
        var i = 1;
        $("#tableitem").on('click','#addmore',function(e){
                e.preventDefault();
                var item = '<tr id="i'+i+'">';
                item += '<td>{!! Form::select("items_id[]",collect(["" => "Select Item"] + $item->all()),null,["class"=>"form-control select items"]) !!}</td>'
                  item += '<input type="hidden" class="itemprice" name="itemprice[]" value="0"></td>';
                        item += '<td class="qtyonhand text-center">0.00</td>'
                        item +='<td><input type="text" name="quantity[]" class="form-control" value="0"></td>';
                        item +='<td><button class="btn btn-danger delete" id="delete"><span class=" glyphicon glyphicon-minus"></span></button></td>';
                item +="</tr>";
                $("#tableitem tbody").append(item);
                $("select").select2({
                     theme: "bootstrap"
                });
            i++;
        });


        $("#tableitem").on('change','.items',function(){

            var id = $(this).val();
            var wrap_id = $(this).closest('tr').attr('id');
            
            $.ajax({    
                url: "{{ action('InventoryController@items') }}",
                type:"get",
                dataType:'json',   
                data: { id : id },
                success: function(response){
                    $('#'+wrap_id+' .qtyonhand').text(response.total+' '+response.um);
                    $('#'+wrap_id+' .itemprice').val(response.itemprice);
                },
                error: function(){
                    $('#'+wrap_id+' .qtyonhand').text("0.00");
                }
                
            });
        });

        $("#tableitem").on('click','.delete',function(){
            var id = $(this).val();
            var wrap_id = $(this).closest('tr').remove();
            
        });

    });
</script>
@stop