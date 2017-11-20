<?php

namespace App\Http\Controllers;

use App\Item_type;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ItemtypeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
    	$itemtypes = Item_type::get();
    	return view('itemtypes.index',compact('itemtypes'));
    }

    public function create(){
    	return view('itemtypes.create');
    }

    public function store(){
    	$itemtype = Request::all();
    	Item_type::create($itemtype);
    	Session::flash('flash_message','Item Type Entry Saved.');
    	return redirect()->back();
    }

    public function edit($id){
    	$itemtype = Item_type::find($id);
    	return view('itemtypes.edit',compact('itemtype','id'));
    }

    public function update($id){
    	$itemtype = Request::except('_token','_method');
    	Item_type::where('id',$id)->update($itemtype);
    	Session::flash('flash_message','Item Type Entry Updated.');
    	return redirect()->back();
    }

    public function delete_item($id){
    	$itemtype = Item_type::find($id);
    	Item_type::where('id',$id)->delete($itemtype);
    	Session::flash('flash_message','Item Type Entry Deleted.');
    	return redirect()->back();
    }
}
