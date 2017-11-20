@extends('template.theme')
<?php use Carbon\Carbon;?>
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
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Chart Of Account</a></li>
            <li class="breadcrumb-item active">Add General Journal Entries</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>General Journal Entries</h5>

                </div>
                 {!! Form::open(["action"=>"ChartofaccountController@journal_store"])!!}
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" name="dt" value="{{ Carbon::now()->toDateString()}}" class="form-control dt">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input placeholder="Reference Number" type="text" name="ref" value="" class="form-control dt">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                   
                            <table class="table" id="journal">
                                <thead>
                                    <th width="30%">Account</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Memo</th>
                                    <th width="25%">Payee</th>
                                    <th></th>
                                </thead>
                                
                                <tbody>
                                    <tr>
                                        <td>{!! Form::select('account[]',$coas,null,['class'=>'form-control select account','id'=>'account','width'=>'100%',"placeholder" => "Select Account"]) !!}</td>
                                        <td>{!! Form::text('debit[]',null,['class'=>'form-control amount'])!!}</td>
                                        <td>{!! Form::text('credit[]',null,['class'=>'form-control amount'])!!}</td>
                                        <td>{!! Form::text('memo[]',null,['class'=>'form-control'])!!}</td>
                                        <td>{!! Form::select('payee[]',$payees,null,['class'=>'form-control select payee','id'=>'accounttype','width'=>'100%',"placeholder" => "Select Payee"]) !!}</td>
                                        <td><button class="btn btn-primary" id="addmore"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>
                                    </tr>
                                </tbody>
                                
                            </table>
                             <button type="submit" class="btn btn-primary">Save Entry</button>
                    {!! Form::close() !!}
                    </div>
                   
                </div>
            </div>
            
        </div>
    </div>
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
        $(".select").select2({
             theme: "bootstrap"
        });

        $('.dt').datepicker();

        $(".subitem1").hide();
       

        function formatState (state) {
        // console.log(state);
          if (!state.id) {
            return state.text;
          }
          var baseUrl = "/user/pages/images/flags";
          var $state = $(
            '<span>' + state.text + '</span><cite><small class="text-right"><i style="color:gray;" class="pull-right">'+ state.type +'</i></small></cite>'
          );
          return $state;
        };
        $("#accounttype").select2({
            theme: "bootstrap",
            ajax: {
                url: '{{action('ChartofaccountController@customer_supplier')}}',
                dataType: 'json',
                processResults: function (data) {
                
                  return {
                    results: data.results
                  };
                }
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
              },
            templateResult: formatState
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
         $('#journal').on('click','#addmore',function(e){
            e.preventDefault();
            var data = '<tr>';
            data +='<td>{!! Form::select("account[]", $coas, null, ["placeholder" => "Select Account","class"=>"form-control account1"])!!}</td>';
            data += '<td><input type="text" class="form-control amount" id="amount" name="debit[]"></td>';
            data += '<td><input type="text" class="form-control amount" id="amount" name="credit[]"></td>';

            data += '<td><input type="text" class="form-control amount" id="amount" name="memo[]"></td>';
            data +='<td>{!! Form::select("payee[]", $payees, null, ["placeholder" => "Select Payee","class"=>"form-control account payee1"])!!}</td>';
            data += '<td><button class="btn btn-danger" id="remove"><i class="fa fa-minus-circle" aria-hidden="true"></i></button></td>'
            $('#journal tbody').append(data);
            $(".account1").select2({
              theme: "bootstrap",
              
            placeholder: "Select a Account",
            });
            $(".payee1").select2({
              theme: "bootstrap",


                placeholder: "Select Payee",
            });
            // $('.amount').autoNumeric('init', {

            //     dGroup: 3,
            //     aPad: true,
            //     pSign: 'p',
            //     aDec: '.',
            //     aSep: ','

            // });
        });
        $('#journal').on('click','#remove',function(e){
            e.preventDefault();
            $(this).parents('tr').remove();
        });

    });
</script>
@stop