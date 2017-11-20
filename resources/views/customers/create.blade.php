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
				<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Customer</a></li>
				<li class="breadcrumb-item active">Add Customer</li>
			</ol>
		</h2>
	</div>
</div>
<div class="wrapper wrapper-content">
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">

			<div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Customer Information</h5>
					</div>
					<div class="ibox-content" id="ibox_form">
						@if (count($errors) > 0)
						    <div class="alert alert-danger" id="">
						        <ul>
						            @foreach ($errors->all() as $error)
						                <li>{{ $error }}</li>
						            @endforeach
						        </ul>
						    </div>
						@endif

					

						{!! Form::open(['method'=>'POST','action'=>'CustomerController@store'])!!}
							<div class="col-md-12 pull-left">

								<div class="form-group col-md-12  col-xs-12 ">
									{!! Form::text('customer_name',null,['class'=>'form-control','placeholder'=>'Name']) !!}

								</div>
								<div class="form-group  col-md-12  col-xs-12 ">
									{!! Form::text('customer_address',null,['class'=>'form-control','placeholder'=>'Address']) !!}
								</div>
								<div class="form-group col-md-12  col-xs-12 ">
									{!! Form::text('customer_contact',null,['class'=>'form-control','placeholder'=>'Contact Number']) !!}
								</div>
								<div class="form-group col-md-12  col-xs-12 ">									
									{!! Form::text('beginning_bal',null,['class'=>'form-control','placeholder'=>'Beginning Balance']) !!}
								</div>
							</div>
							
							<div class="row">
		                        <div class="col-md-12">
		                            <div class="form-group">
		                                <div class="col-md-offset-2 col-lg-10">
		                                    <button class="md-btn md-btn-primary waves-effect waves-light" type="submit" ><i class="fa fa-check"></i> Save</button> | <a href="{{action('CustomerController@index')}}">Or Cancel</a>
		                                </div>
		                            </div>
		                        </div>
                    		</div>
						{!! Form::close()!!}

					</div>


				</div>


			</div>

		</div>

	</div>
</div>
</div><!-- end wrapper -->
@endsection

@section('script')
<script src="{{asset('assets/bootstrap-fileinput-master/js/fileinput.min.js')}}"></script>
<script>

	$("#avatar").fileinput({
		overwriteInitial: true,
		maxFileSize: 1500,
		showClose: false,
		showCaption: false,
		browseLabel: '',
		removeLabel: '',
		browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
		removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
		removeTitle: 'Cancel or reset changes',
		elErrorContainer: '#kv-avatar-errors',
		msgErrorClass: 'alert alert-block alert-danger',
		defaultPreviewContent: "<img src={{asset('assets/bootstrap-fileinput-master/uploads/default_avatar_male.jpg')}} alt='Your Avatar' style='width:1120px'>",
		layoutTemplates: {main2: '{preview} ' + ' {remove} {browse}'},
		allowedFileExtensions: ["jpg", "png", "gif"]
	});
</script>
@endsection