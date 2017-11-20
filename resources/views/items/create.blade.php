@extends('template.theme')

@section('css')
<link href='{{ asset("assets/datatable/jquery.dataTables.min.css") }}' rel="stylesheet">
<link href='{{ asset("assets/ui/lib/dp/dist/datepicker.min.css") }}' rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
  <div class="col-lg-12">
    <h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Items</a></li>
            <li class="breadcrumb-item active">Add Item</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Item Info</h5>

                </div>
             
                {!! Form::open(['method'=>'POST','action'=>'ItemController@store']) !!}
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
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('','Select Type:') !!}
                                {!! Form::select('item_type_id',collect(['' => 'PLEASE SELECT'] + $itemtypes->all()),null,['class'=>'form-control select','id'=>'item']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row spacing">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('','Item Name:') !!}
                                {!! Form::text('item_name',null,['class'=>'form-control']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('','Category:') !!}
                                {!! Form::select('category_id',collect(['' => 'PLEASE SELECT'] + $cats->all()),null,['class'=>'form-control select','id'=>'cat']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('','Unit of Measure:') !!}
                                {!! Form::text('unit_measure',null,['class'=>'form-control']) !!}
                            </div>
                          <!--   <div class="checkbox" style="margin-top:30px;">
                                <label>
                                    <input value="1" type="checkbox" name="isSubitem1" class="isSubitem1">
                                    Sub Item?
                                </label>

                            </div>
                            <div class="form-group subitem1">
                                  
                                    <select class="form-control select" name="sub_item">
                                     <?php echo $itemlist?>
                                    </select>
                            </div> -->
                            <div id="contain" style="display:none">
                                <div class="form-group">
                                    {!! Form::label('','Initial Quantity:') !!}
                                    {!! Form::text('quantity','0',['class'=>'form-control']) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('','As Of:') !!}
                                    {!! Form::text('dt',null,['class'=>'form-control dt']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <div class="row spacing">
                       
                        <div class="col-md-12">
                            <div class="form-group">
                            {!! Form::label('','Asset:') !!}
                                <select class="form-control js-data-example-ajax" name="coa_id" id="coa"></select>
                            </div>
                            <div class="form-group" id="coa_incomes">
                            {!! Form::label('','Income:') !!}
                                <select class="form-control select" name="income_coa" id="coa_income"></select>
                            </div>
                            <div class="form-group" id="coa_costofsales">
                            {!! Form::label('','Cost of sales:') !!}
                                <select class="form-control select" name="costofsale_coa" id="coa_costofsale"></select>
                            </div>
                           
                        </div>
                    </div>
                    <div class="row spacing">
                        <div class="col-md-12">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.full.js"></script>
<script type="text/javascript">
        

    $(document).ready(function() {
// 

        $('#item').select2({
            placeholder: "Select a Account Type",
        }).change(function(e){
            
            if($(this).val() == 1){
                $("#contain").show();
            }else{
                $("#contain").hide();
            }

        });

        

        $('#cat').select2({
            placeholder: "Select a Category",
        })
        $('.dt').datepicker();

        $(".subitem1").hide();
        $('#item').on('change',function($q){
            if($(this).val() == 1){
                $("#coa_incomes").show();
                $("#coa_costofsales").show();
            }else{

                $("#coa_incomes").hide();
                $("#coa_costofsales").hide();
            }
            
        });

       $('#coa').select2({
            placeholder: "Select a Asset Account",
            allowClear: true,
            ajax: {
                url: '{{action("ItemController@selectItem")}}',
                data: function (params) {
                  var query = {
                    search: params.term,
                    type: 'public'
                  }

                  // Query parameters will be ?search=[term]&type=public
                  return query;
                },
              },
              // matcher: oldMatcher(matchStart)

        });
       $('#coa_income').select2({
            width: 'resolve', // need to override the changed default
            placeholder: "Select a Income Account",
            allowClear: true,
            ajax: {
                url: '{{action("ItemController@selectItem")}}',
                data: function (params) {
                  var query = {
                    search: params.term,
                    type: 'public'
                  }

                  // Query parameters will be ?search=[term]&type=public
                  return query;
                },
              },
              // matcher: oldMatcher(matchStart)

        });
       $('#coa_costofsale').select2({
        width: 'resolve',
            placeholder: "Select a Cost of Sales Account",
            allowClear: true,
            ajax: {
                url: '{{action("ItemController@selectItem")}}',
                data: function (params) {
                  var query = {
                    search: params.term,
                    type: 'public'
                  }

                  // Query parameters will be ?search=[term]&type=public
                  return query;
                },
              },
              // matcher: oldMatcher(matchStart)

        });
     

       
        $(".isSubitem1").click(function() {
            if($(this).is(":checked")) {
                $(".isSubitem2").attr('checked', false);
                $(".subitem1").show();
                $(".subitem2").hide();
            } else {
                $(".subitem1").hide();
            }
        }); 


    });
</script>
@stop