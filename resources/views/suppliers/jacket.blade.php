@extends('template.theme')
@section('css')

<link href="{{asset('assets/bootstrap-fileinput-master/css/fileinput.min.css')}}" media="all" rel="stylesheet">
<style type="text/css">.kv-avatar .file-input {
    display: table-cell;
    max-width: 220px;
}</style>
@endsection


@section('content')
<div class="row wrapper white-bg page-heading">
	<div class="col-lg-12">
		<h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
				<li class="breadcrumb-item ">Supplier</li>
				<li class="breadcrumb-item active">{{ $supplier->supplier_name }}</li>
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
						<h5><strong>{{ $supplier->supplier_name }}</strong> Transaction's</h5>
					</div>
					<div class="ibox-content" id="ibox_form">
				
		            <div class="table-responsive">
						<table class="table table-bordered sys_table responsive" id="tableSortable" style="text-transform:uppercase;">
							<thead>	
								<tr>
									<th>Date</th>
									<th>Type</th>
									<th>Ref</th>
									<th>Balance</th>
								</tr>
							</thead>
							<tbody>
							@foreach($supplier->voucher as $voucher)
								<tr>
									<th>Date</th>
									<th>Type</th>
									<th>Ref</th>
									<th>Total</th>
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

