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
				<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Supplier</a></li>
				<li class="breadcrumb-item active">Add Supplier</li>
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
						<h5>Supplier Information</h5>
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

					

						{!! Form::open(['url'=>'supplier','files'=>'true'])!!}
							<div class="col-md-6 pull-left">

								<div class="form-group col-md-3  col-xs-12 ">
									{!! Form::text('fname',null,['class'=>'form-control','placeholder'=>'First Name'])!!}

								</div>
								<div class="form-group  col-md-4  col-xs-12 ">
									{!! Form::text('mname',null,['class'=>'form-control','placeholder'=>'Middle Name'])!!}
								</div>
								<div class="form-group col-md-3  col-xs-12 ">
									{!! Form::text('lname',null,['class'=>'form-control','placeholder'=>'Last Name'])!!}
								</div>
								<div class="form-group col-md-2  col-xs-12 ">									
									{!! Form::text('suffix',null,['class'=>'form-control','placeholder'=>'Suffix'])!!}
								</div>
								<div class="form-group col-lg-12  col-xs-12 ">
									{!! Form::text('companyName',null,['class'=>'form-control','placeholder'=>'Company Name'])!!}

								</div>
								<div class="form-group col-lg-12  col-xs-12 ">
									{!! Form::text('displayName',null,['class'=>'form-control','placeholder'=>'Display Name'])!!}									
								</div>
								<div class="form-group col-lg-12  col-xs-12 ">
									{!! Form::textarea('address',null,['class'=>'form-control','placeholder'=>'Address','style'=>'width: 100%;height: 80px;'])!!}

								</div>
								<div class="form-group col-lg-6">
									{!! Form::text('city',null,['class'=>'form-control','placeholder'=>'City'])!!}	
								</div>
								<div class="form-group col-lg-6">
									{!! Form::text('StateProvince',null,['class'=>'form-control','placeholder'=>'State/Province'])!!}
								</div>
								<div class="form-group col-lg-12  col-xs-12 ">
									{!! Form::textarea('notes',null,['class'=>'form-control','placeholder'=>'Notes','style'=>'width: 100%;height: 80px;'])!!}
								</div>
							</div>
							<div class="col-md-6 pull-right">
								<div class="form-group col-md-12 ">
									{!! Form::email('email',null,['class'=>'form-control','placeholder'=>'Email Address'])!!}	
								</div>
								<div class="form-group  col-md-6">
									{!! Form::text('phone',null,['class'=>'form-control','placeholder'=>'Phone Number'])!!}	
								</div>
								<div class="form-group  col-md-6">
									{!! Form::text('mobile',null,['class'=>'form-control','placeholder'=>'Mobile Number'])!!}
								</div>

								<div id="kv-avatar-errors" class="center-block" style="width:800px;display:none"></div>

								<div class="kv-avatar center-block" style="width:200px">

									{!! Form::file('avatar',['class'=>'file-loading','id'=>'avatar']) !!}

								</div>
								<!-- include other inputs if needed and include a form submit (save) button -->

							</div>
							<div class="row">
		                        <div class="col-md-12">
		                            <div class="form-group">
		                                <div class="col-md-offset-2 col-lg-10">
		                                    <button class="md-btn md-btn-primary waves-effect waves-light" type="submit" ><i class="fa fa-check"></i> Save</button> | <a href="{{action('SupplierController@index')}}">Or Cancel</a>
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
		defaultPreviewContent: "<img src={{asset('assets/bootstrap-fileinput-master/uploads/default_avatar_male.jpg')}} alt='Your Avatar' style='width:160px'>",
		layoutTemplates: {main2: '{preview} ' + ' {remove} {browse}'},
		allowedFileExtensions: ["jpg", "png", "gif"]
	});
</script>
@endsection