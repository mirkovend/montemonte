@extends('template.theme')

@section('css')
<link href='{{ asset("assets/datatable/jquery.dataTables.min.css") }}' rel="stylesheet">
@stop
@section('content')
<div class="row wrapper white-bg page-heading">
  <div class="col-lg-12">
    <h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Items</a></li>
            <li class="breadcrumb-item"><a href="#" style="color:#2196f3">Categories</a></li>
            <li class="breadcrumb-item active">Add Category</li>
        </ol>
    </h2>
  </div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Category Info</h5>

                </div>
             
                
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
                    {!! Form::model($category,['method'=>'PATCH','action'=>['CategoryController@update',$category->id]]) !!}
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('','Category Name:') !!}
                            {!! Form::text('name',null,['class'=>'form-control ','id'=>"cats"]) !!}
                        </div>
                        <hr>
                        <div class="checkbox" style="margin-top:30px;">
                            <label>
                                @if($category->is_sub == 0)
                                    <input value="1" type="checkbox" name="isSubitem" class="isSubitem" >
                                @else
                                    <input value="1" type="checkbox" name="isSubitem" class="isSubitem" checked="checked">
                                @endif
                                
                                Sub Category
                            </label>

                        </div>
                        <div class="form-group subitem">
                           {!! Form::label('','Sub Category?:') !!}
                           {!! Form::select('is_sub',$cat->pluck('name','id'),null,['class'=>'form-control select','id'=>'item']) !!}
                        </div>
                        <hr>
                        {!! Form::submit('Submit',['class'=>'btn btn-sm btn-primary'])!!}
                    </div>
                    {!! Form::close() !!}
                    
                </div>
                </div> <!-- END IBOXCONTENT-->
             
            </div>
            
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Category List</h5>

                </div>
             
                
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
                        <div class="table-responsive">

                            <table class="table" id="cat-table" width="100%">
                                <thead>
                                    <tr>
                                        <td>Name</td>
                                        <td>Sub Category</td>
                                        <td>Action</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cat as $category)
                                        <tr>
                                            <td>{{$category->name}}</td>
                                            @if($category->category)
                                            <td>{{$category->category->name}}</td>
                                            @else
                                            <td></td>
                                            @endif
                                            <td>
                                                
                                                <div class='btn-group'>
                                                    <button type='button' class='btn btn-sm btn-primary dropdown-toggle' data-toggle='dropdown'>
                                                        <span class='icon-gear'></span> Action<span class='caret'></span>
                                                    </button>
                                                    <ul class='dropdown-menu dropdown-menu-arrow' role='menu' style='text-align:left;text-transform:uppercase;'>
                                                        <li><a href="{{action('CategoryController@edit',$category->id)}}" id='edited' ><span class='icon-edit'></span>Edit Entry</a></li>
                                                        <li><a href="{{action('CategoryController@delete',$category->id)}}"><span class='icon-edit'></span> Delete Entry</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        

                    </div>
                </div>
                </div> <!-- END IBOXCONTENT-->
             
            </div>
            
        </div>
    </div> <!-- Row end-->
   
@stop


@section('script')

<script type="text/javascript" src='{{ asset("assets/datatable/jquery.dataTables.min.js") }}'></script>
<script type="text/javascript">
        

    $(document).ready(function() {
        $("#cat-table").DataTable();
        if($('.isSubitem').is(":checked")) {
            $(".subitem").show();
        } else {
            $(".subitem").hide();
        }
        $(".isSubitem").click(function() {
            
            if($(this).is(":checked")) {
                $(".subitem").show();
            } else {
                $(".subitem").hide();
            }
        }); 
    });
</script>
@stop