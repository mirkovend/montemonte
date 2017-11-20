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
				<li class="breadcrumb-item active">Sales</li>
			</ol>
		</h2>
	</div>
</div>
<div class="wrapper wrapper-content">
	<div class="wrapper wrapper-content">
		<div class="row">

			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Sales Report</h5>
					</div>
					@if(Session::has('flash_message'))
					    <div class="alert alert-success spacing"><span class="icon-thumbs-up"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
					<div class="ibox-content" id="ibox_form">
					

			            <div class="table-responsive">
			            	<div class="row">
						        <div class="col-md-12" style="margin:6px 0;">
									{!! Form::open(['method'=>'POST','action'=>'ReportController@sales_report_type','class'=>'form-inline'])!!}
										<div class="form-group">
											{!! Form::select('detail_type',
											[1=>'Item Details',2=>'Customers Detail',3=>'Customer Summary'],null,['class'=>'form-control'])!!}
										</div>

										<button type="submit" class="btn btn-primary">Submit</button>
									</form>
							    </div>
						    </div>

							<table class="table table-hover" id="tablez">
								<thead>
								  <th>Item</th>
								  <th width=".1"></th>
								  <th>Type</th>
								  <th width=".1"></th>
								  <th>Date</th>
								  <th width=".1"></th>
								  <th>Ref</th>
								  <th width=".1"></th>
								  <th>Memo</th>
								  <th width=".1"></th>
								  <th>Item</th>
								  <th width=".1"></th>
								  <th>Qty</th>
								  <th width=".1"></th>
								  <th>U/M</th>
								  <th width=".1"></th>
								  <th>Sales Price</th>
								  <th width=".1"></th>
								  <th>Amount</th>
								  <th width=".1"></th>
								  <th>Balance</th>
								</thead>
								<tbody>
								<?php 
								$total_onhand = 0;
								$total_asset = 0;
								$total_qty = 0;
								$total_amount = 0;
								$total_balance = 0;
								?>  
								  @foreach($customers as $customer)
								    <?php 
								      $invoiceArr = [];
								      $y = 0;
								      $x = 0;
								      $ciArr = [];
								      $qty = 0;
								      $amount = 0;
								      $balance = 0;
								    ?>
								    <tr>
								      <td colspan="21">{{$customer->customer_name}}</td>
								    </tr>
								    @foreach($customer->invoice as $invoice)
								      
								      @foreach($invoice->invoice as $item)
								        <?php
								          $invoiceArr[] = $item;
								          $invoiceArr[$x]->types = "INVOICE";
								          $invoiceArr[$x]->memo = "Memo";
								          $invoiceArr[$x]->ref = $item->charge_invoice_number;
								          $invoiceArr[$x]->item = $item->item->item_name;
								          $invoiceArr[$x]->qty = $item->charge_invoice_qty;
								          $invoiceArr[$x]->um = "pc";
								          $invoiceArr[$x]->sp = $item->unit_price;
								          $invoiceArr[$x]->amount =  $item->amount;
								          $invoiceArr[$x]->balance = $item->amount;
								          $x++; 
								        ?>
								      @endforeach
								    @endforeach

								    @foreach($customer->ci as $ci)
								     
								      @foreach($ci->ci_item as $item)
								        <?php
								          $ciArr[] = $item;
								          $ciArr[$y]->types = "SALES RECEIPT";
								          $ciArr[$y]->memo = "Memo";
								          $ciArr[$y]->ref = $item->cash_invoice_number;
								          $ciArr[$y]->item = $item->item->item_name;
								          $ciArr[$y]->qty = $item->cash_invoice_qty;
								          $ciArr[$y]->um = "pc";
								          $ciArr[$y]->sp = $item->unit_price;
								          $ciArr[$y]->amount =  $item->amount;
								          $ciArr[$y]->balance = $item->amount;
								          $y++; 
								        ?>
								      @endforeach
								    @endforeach
								    <?php 
								      $row1 = array_merge($invoiceArr,$ciArr);
								      $row = collect($row1)->sortBy('dt');
								    ?>

								    @foreach($row as $transaction)
								      <tr>
								        <td></td>
								        <td></td>
								        <td>{{$transaction->types}}</td>
								        <td></td>
								        <td>{{$transaction->dt}}</td>
								        <td></td>
								        <td>{{$transaction->memo}}</td>
								        <td></td>
								        <td>{{$transaction->ref}}</td>
								        <td></td>
								        <td>{{$transaction->item}}</td>
								        <td></td>
								        <td class="text-right">{{$transaction->qty}}</td>
								        <td></td>
								        <td>{{$transaction->um}}</td>
								        <td></td>
								        <td class="text-right">{{$transaction->sp}}</td>
								        <td></td>
								        <td class="text-right">{{$transaction->amount}}</td>
								        <td></td>
								        <td class="text-right">{{$balance +=$transaction->balance}}</td>
								      </tr>
								      <?php 
								            $qty +=$transaction->qty;
								            $amount +=$transaction->amount;
								      ?>
								    @endforeach
								    <?php 

								      $total_qty += $qty;
								      $total_amount += $amount;
								      $total_balance += $balance;
								    ?>
								    <tr>
								      <td >Total {{$customer->customer_name}}</td>
								      <td></td>
								      <td colspan="10"></td>
								      <td class="text-right" style="border-top:4px solid black;">{{number_format($qty,2)}}</td>
								      
								      <td colspan="5"></td>
								      <td style="border-top: 4px solid black; " class="text-right">{{number_format($amount,2)}}</td>
								      <td></td>
								      <td style="border-top: 4px solid black; " class="text-right">{{number_format($balance,2)}}</td>
								    </tr>
								  @endforeach
								  <tr>
								    <td class="text-uppercase"><h6>Total:</h6></td>
								    <td></td>
								    <td colspan="10"></td>
								    <td class="text-right" style="border-top:4px solid black;">{{number_format($total_qty,2)}}</td>
								    
								    <td colspan="5"></td>
								    <td style="border-top: 4px solid black; " class="text-right">{{number_format($total_amount,2)}}</td>
								    <td></td>
								    <td style="border-top: 4px solid black; " class="text-right">{{number_format($total_amount,2)}}</td>
								  </tr>

								  
								  <!-- end inventory part -->            
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