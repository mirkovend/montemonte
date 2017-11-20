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

							<table class="table table-bordered" id="tablez">
					          <thead>
					              <th>Item</th>
					              <th>Total</th>
					          </thead>
					          <tbody>
					            <?php 
					              $total_amount = 0;
					            ?>  
					            @foreach($customers as $customer)
					              <?php 
					                $amount = 0;
					                $balance = 0;
					              ?>
					              
					              @foreach($customer->invoice as $invoice)
					                @foreach($invoice->invoice as $item)
					                  <?php
					                    $amount += $item->amount;
					                  ?>
					                @endforeach
					              @endforeach

					              @foreach($customer->ci as $ci)
					                @foreach($ci->ci_item as $item)
					                  <?php
					                    $amount += $item->amount;
					                  ?>
					                @endforeach
					              @endforeach

					              <tr>
					                <td>{{$customer->customer_name}}</td>
					                <td class="text-center">{{$amount}}</td>
					              </tr>
					           
					              <?php 
					                $total_amount += $amount;
					              ?>
					            @endforeach
					            <tr>
					              <td class="text-uppercase"><h6>Total:</h6></td>
					              <td class="text-center" style="border-top: 2px solid black">{{$total_amount}}</td>
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