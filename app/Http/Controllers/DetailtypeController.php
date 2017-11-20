<?php

namespace App\Http\Controllers;

use App\Detail_type;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class DetailtypeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
    	$detailtype = Detail_type::get();
    	return view('detailtypes.index',compact('detailtype'));
    }

    public function create(){
    	return view('detailtypes.create');
    }

    public function store(){
    	$detailtype = Request::all();
        $validator = Validator::make(Request::all(), [
   
            'detail_type_name'                             => 'required',          
        ],
        [
            'detail_type_name.required'                    => 'Detail Type Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('detailtype/create')
                        ->withErrors($validator)
                        ->withInput();
        }

    	Detail_type::create($detailtype);
    	Session::flash('flash_message','Detail Type Entry Saved.');
    	return redirect()->back();
    }

    public function edit($id){
    	$detailtype = Detail_type::find($id);
    	return view('detailtypes.edit',compact('detailtype','id'));
    }

    public function update($id){
    	$detailtype = Request::except('_token','_method');
    	Detail_type::where('id',$id)->update($detailtype);
    	Session::flash('flash_message','Detail Type Entry Updated.');
    	return redirect()->back();
    }

    public function delete_item($id){
    	$detailtype = Detail_type::find($id);
    	Detail_type::where('id',$id)->delete($detailtype);
    	Session::flash('flash_message','Detail Type Entry Deleted.');
    	return redirect()->back();
    }
}
