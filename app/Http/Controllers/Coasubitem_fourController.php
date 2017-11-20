<?php

namespace App\Http\Controllers;

use App\Coa_item;
use App\Coa_subitem;
use App\Coa_subitem_three;
use App\Coa_subitem_four;
use App\Chart_of_account;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class Coasubitem_fourController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
    	$coasubitem = Coa_subitem_four::get();
    	return view('coasubitemfours.index',compact('coasubitem'));
    }

    public function create(){
    	$coa = Coa_subitem_three::pluck('coa_item_name','id');
        return view('coasubitemfours.create',compact('coa'));
    }

    public function store(){
    	$coaitem = Request::all();
        $validator = Validator::make(Request::all(), [
   
            'coa_sub3_id'                             => 'required',
            'coa_item_name'                          => 'required',          
        ],
        [
            'coa_sub3_id.required'                    => 'Subaccount Field Required',
            'coa_item_name.required'                 => 'Subaccount Name Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('coasubitemfour/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        Coa_subitem_four::create($coaitem);
        Session::flash('flash_message','Chart of Account Subaccount Level 4 Entry Saved.');
        return redirect()->back();
    }

    public function edit($id){
    	$coaitem = Coa_subitem_four::find($id);
        $coa = Coa_subitem_three::pluck('coa_item_name','id');
        return view('coasubitemfours.edit',compact('coaitem','id','coa'));
    }

    public function update($id){
    	$coaitem = Request::except('_token','_method');
        Coa_subitem_four::where('id',$id)->update($coaitem);
        Session::flash('flash_message','Chart of Account Subaccount Level 4 Entry Updated.');
        return redirect()->back();
    }

    public function delete_item($id){
    	$coaitem = Coa_subitem_four::find($id);
        Coa_subitem_four::where('id',$id)->delete($coaitem);
        Session::flash('flash_message','Chart of Account Subaccount Level 4 Entry Deleted.');
        return redirect()->back();
    }
}
