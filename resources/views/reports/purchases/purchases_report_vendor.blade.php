@extends('template.theme')
@section('css')
<link href='{{ asset("assets/datatable/jquery.dataTables.min.css") }}' rel="stylesheet">
<style type="text/css">
		.select2-container {
		    width: 100% !important;
		    padding: 0;

		}
		
        table.floatThead-table {
            border-top: none;
            border-bottom: none;
            background-color: #eee;
        }
</style>
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
	<div class="col-lg-12">
		<h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Reports</a></li>
				<li class="breadcrumb-item active">Purchases</li>
			</ol>
		</h2>
	</div>
</div>
<div class="wrapper wrapper-content">
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">

			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Purchases Report</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">

			            <div class="table-responsive">

			            	<div class="row">
						        <div class="col-md-12" style="margin:6px 0;">
							        {!! Form::open(['method'=>'POST','action'=>'ReportController@purchases_type','class'=>'form-inline'])!!}
							            <div class="form-group">
							             {!! Form::select('detail_type',[1=>'Item Details',2=>'Item Summary',3=>'Vendor Details',4=>'Vendor Summary'],null,['class'=>'form-control'])!!}
							            </div>
							           <!--  <div class="form-group">
							              <label>From</label>
							              {!! Form::date('dt',\Carbon\Carbon::now()->toDateString(),['class'=>'form-control dt']) !!}
							            </div>
							            <div class="form-group">
							              <label>To</label>
							              {!! Form::date('dt',\Carbon\Carbon::now()->toDateString(),['class'=>'form-control dt']) !!}
							            </div> -->
							            <button type="submit" class="btn btn-primary">Submit</button>
							        </form>
							    </div>
						    </div>
							<table class="table table-hover" id="tablez">
					            <thead class="text-uppercase">
					                <th >Item</th>
					                <th width=".1"></th>
					                <th>Type</th>
					                <th width=".1"></th>
					                <th>Date</th>
					                <th width=".1"></th>
					                <th>Ref</th>
					                <th width=".1"></th>
					                <th>Memo</th>
					                <th width=".1"></th>
					                <th>ITEM</th>
					                <th width=".1"></th>
					                <th>Qty</th>
					                <th width=".1"></th>
					                <th>U/M</th>
					                <th width=".1"></th>
					                <th>Avg Cost</th>
					                <th width=".1"></th>
					                <th>Amount</th>
					                <th width=".1"></th>
					                <th>Balance</th>
					            </thead>
					            <tbody>
					              @foreach($suppliers as $supplier)
					                <tr>
					                  <td colspan="21"><h4>{{$supplier->supplier_name}}</h4></td>
					                </tr>
					                  @foreach($supplier->voucheritem as $item)
					                    <tr>
					                      <td></td>
					                      <td></td>
					                      <td>BILL</td>
					                      <td></td>
					                      <td>{{$item->dt}}</td>
					                      <td></td>
					                      <td>CV{{$item->voucher_number}}</td>
					                      <td></td>
					                      <td>MEMO</td>
					                      <td></td>
					                      <td>{{$item->item->item_name}}</td>
					                      <td></td>
					                      <td class="text-right">{{$item->item_rcv}}</td>
					                      <td></td>
					                      <td></td>
					                      <td></td>
					                      <td class="text-right">{{$item->item->po_item->item_price}}</td>
					                      <td></td>
					                      <td class="text-right">{{$item->item->po_item->item_total}}</td>
					                      <td></td>
					                      <td class="text-right">{{$item->item->po_item->item_total}}</td>
					                    </tr>
					                  @endforeach
					                  @foreach($supplier->voucherjobitem as $jobitem)
					                    <tr>
					                      <td></td>
					                      <td></td>
					                      <td>BILL</td>
					                      <td></td>
					                      <td>{{$jobitem->dt}}</td>
					                      <td></td>
					                      <td>CV{{$jobitem->voucher_number}}</td>
					                      <td></td>
					                      <td>MEMO</td>
					                      <td></td>
					                      <td>{{$jobitem->jo_item->item->item_name}}</td>
					                      <td></td>
					                      <td class="text-right">{{number_format(1,2)}}</td>
					                      <td></td>
					                      <td></td>
					                      <td></td>
					                      <td class="text-right">{{$jobitem->joborder_payment}}</td>
					                      <td></td>
					                      <td class="text-right">{{$jobitem->joborder_payment}}</td>
					                      <td></td>
					                      <td class="text-right">{{$jobitem->joborder_payment}}</td>
					                    </tr>
					                  @endforeach
					                <tr>
					                  <td><strong>TOTAL {{$supplier->supplier_name}}</strong> </td>
					                  <td></td>
					                   <td colspan="9"></td>
					                   <td></td>
					                          <td style="border-top: 4px solid black; " class="text-right"><b>7</b></td>
					                          <td></td>
					                          <td colspan="3"></td>
					                          <td></td>
					                          <td style="border-top: 4px solid black; " class="text-right"><b>7</b></td>
					                          <td></td>
					                          <td style="border-top: 4px solid black; " class="text-right"><b>7</b></td>
					                </tr>
					              @endforeach
					            </tbody>
					        </table>
						</div>
					</div>


				</div>


			</div>

		</div>

	</div>
</div>
</div><!-- end wrapper -->
@endsection

@section('script')
<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>
<script type="text/javascript" src='{{ asset("assets/ui/lib/numeric.js") }}'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		// $('table').DataTable({ "ordering": false});
		var $table = $('#tablez');
	    $table.floatThead({
			responsiveContainer: function($table){
				return $table.closest('.table-responsive');
			}
	    });
	    $table.floatThead('reflow');
	});
</script>
@endsection