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
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Chart of Accounts</a></li>
            <li class="breadcrumb-item active">Add Account</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Account Info</h5>

                </div>
             
                {!! Form::open(['method'=>'POST','action'=>'ChartofaccountController@store']) !!}
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
                                {!! Form::select('detail_type_id',collect(['' => 'PLEASE SELECT'] + $detailtype->all()),null,['class'=>'form-control select']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row spacing">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('','Account Title:') !!}
                                {!! Form::text('coa_title',null,['class'=>'form-control']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('','Select Typical Balance:') !!}
                                 {!! Form::select('typical_balance',[''=>'PLEASE SELECT','Debit'=>'DEBIT','Credit'=>'CREDIT'],null,['class'=>'form-control select']) !!}
                            </div>
                            <div class="checkbox" style="margin-top:30px;">
                                <label>
                                    <input value="1" type="checkbox" name="isSubitem1" class="isSubitem1">
                                    Sub Item?
                                </label>

                            </div>
                            <div class="form-group subitem1">
                                  
                                    <select class="form-control select" name="sub_item">
                                     {!! $coa !!}
                                    </select>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <div class="row spacing">
                       
                        <div class="col-md-12">

                            <div class="form-group">
                                {!! Form::label('','Balance:') !!}
                                {!! Form::text('balance','0.00',['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-12">

                            <div class="form-group">
                                {!! Form::label('','As of:') !!}
                                {!! Form::text('dt',null,['class'=>'form-control dt']) !!}
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
<script type="text/javascript">
        

    $(document).ready(function() {

        $(".select").select2({
             theme: "bootstrap"
        });

        $('.dt').datepicker();

        $(".subitem1").hide();
        

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