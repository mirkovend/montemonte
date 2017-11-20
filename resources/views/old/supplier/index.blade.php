@extends('template.theme')

@section('content')

<div class="row wrapper white-bg page-heading">
  <div class="col-lg-12">
    <h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
    	<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
			<li class="breadcrumb-item">Supplier</li>
		</ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content">
	<div class="row">
        <div class="col-md-12">

            <div class="panel panel-default">
                <div class="panel-body">

                    <form class="form-horizontal" method="post" action="http://demo.tryib.com/customers/list/">
                        <div class="form-group">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <span class="fa fa-search"></span>
                                    </div>
                                    <input type="text" name="name" class="form-control" placeholder="Search by Name...">
                                    <div class="input-group-btn">
                                        <button class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">

                                <a href="{{action('SupplierController@create')}}" class="btn btn-success btn-block"><i class="fa fa-plus"></i> Add New Supplier</a>

                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
	<div class="row">
		@foreach($suppliers as $supplier)
		<div class="col-md-3 sdiv">
			<div class="panel panel-default">
				<div class="panel-body profile">
					<div class="profile-image">
						<img src="{{ asset("assets/uploads/system/profile-icon.png") }}" class="img-thumbnail img-responsive" alt=" ">
					</div>
					<div class="profile-data">
						<div class="profile-data-name">{{ $supplier->fname }} {{ $supplier->lname }}</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="contact-info">
						<p><small>Company Name</small><br>{{ $supplier->companyName }}<br><small>Email</small><br>{{ $supplier->email }} </p>
						<p>
							<a href="" class="btn btn-primary btn-xs">
								<i class="fa fa-search"></i> View
							</a>
							<a href="#" class="btn btn-danger btn-xs cdelete" id="uid1000">
								<i class="fa fa-trash"></i> Delete
							</a>
						</p>
					</div>
				</div>
			</div>
		</div>
		@endforeach
	</div>
	<div class="row">

		<div class="col-md-12">
				{{ $suppliers->links() }} 
		</div>
	</div>
	<div id="ajax-modal" class="modal container fade" tabindex="-1" style="display: none;"></div>
</div>

@endsection
