<?php

namespace App\Http\Controllers;

use App\Coa_item;
use App\Chart_of_account;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
class CoaitemController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
    	$coaitem = Coa_item::get();
    	return view('coaitems.index',compact('coaitem'));
    }

    public function create(){
    	$coa = Chart_of_account::pluck('coa_title','id');
    	return view('coaitems.create',compact('coa'));
    }

    public function store(){
    	$coaitem = Request::all();
        $validator = Validator::make(Request::all(), [
   
            'coa_id'                                    => 'required',
            'coa_item_name'                             => 'required',          
        ],
        [
            'coa_id.required'                           => 'Chart of Account Title Field Required',
            'coa_item_name.required'                    => 'Subaccount Name Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('coaitem/create')
                        ->withErrors($validator)
                        ->withInput();
        }

    	Coa_item::create($coaitem);
    	Session::flash('flash_message','Chart of Account Item Entry Saved.');
    	return redirect()->back();
    }

    public function edit($id){
    	$coaitem = Coa_item::find($id);
    	$coa = Chart_of_account::pluck('coa_title','id');
    	return view('coaitems.edit',compact('coaitem','id','coa'));
    }

    public function update($id){
    	$coaitem = Request::except('_token','_method');
    	Coa_item::where('id',$id)->update($coaitem);
    	Session::flash('flash_message','Chart of Account Item Entry Updated.');
    	return redirect()->back();
    }

    public function delete_item($id){
    	$coaitem = Coa_item::find($id);
    	Coa_item::where('id',$id)->delete($coaitem);
    	Session::flash('flash_message','Chart of Account Item Entry Deleted.');
    	return redirect()->back();
    }
}
