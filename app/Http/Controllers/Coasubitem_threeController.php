<?php

namespace App\Http\Controllers;

use App\Coa_item;
use App\Coa_subitem;
use App\Coa_subitem_three;
use App\Chart_of_account;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class Coasubitem_threeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
    	$coasubitem = Coa_subitem_three::get();
    	return view('coasubitemthrees.index',compact('coasubitem'));
    }

    public function create(){
    	$coa = Coa_subitem::pluck('coa_item_name','id');
        return view('coasubitemthrees.create',compact('coa'));
    }

    public function store(){
    	$coaitem = Request::all();
        $validator = Validator::make(Request::all(), [
   
            'coa_sub2_id'                             => 'required',
            'coa_item_name'                          => 'required',          
        ],
        [
            'coa_sub2_id.required'                    => 'Subaccount Field Required',
            'coa_item_name.required'                 => 'Subaccount Name Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('coasubitemthree/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        Coa_subitem_three::create($coaitem);
        Session::flash('flash_message','Chart of Account Subaccount Level 3 Entry Saved.');
        return redirect()->back();
    }

    public function edit($id){
    	$coaitem = Coa_subitem_three::find($id);
        $coa = Coa_subitem::pluck('coa_item_name','id');
        return view('coasubitemthrees.edit',compact('coaitem','id','coa'));
    }

    public function update($id){
    	$coaitem = Request::except('_token','_method');
        Coa_subitem_three::where('id',$id)->update($coaitem);
        Session::flash('flash_message','Chart of Account Subaccount Level 3 Entry Updated.');
        return redirect()->back();
    }

    public function delete_item($id){
    	$coaitem = Coa_subitem_three::find($id);
        Coa_subitem_three::where('id',$id)->delete($coaitem);
        Session::flash('flash_message','Chart of Account Subaccount Level 3 Entry Deleted.');
        return redirect()->back();
    }
}
