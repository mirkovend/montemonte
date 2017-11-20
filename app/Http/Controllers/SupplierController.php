<?php

namespace App\Http\Controllers;

use App\Supplier;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
        $title = "Supplier Lists";
    	$supplier = Supplier::orderBy('supplier_name')->get();
    	return view('suppliers.index',compact('supplier','title'));
    }

    public function create(){
        $title = "Create Supplier";
    	return view('suppliers.create',compact('title'));
    }

    public function store(){
    	$supplier = Request::all();

        $validator = Validator::make(Request::except('supplier_contact'), [
   
            'supplier_name'                             => 'required',
            'supplier_address'                          => 'required',          
        ],
        [
            'supplier_name.required'                    => 'Supplier Name Field Required',
            'supplier_address.required'                 => 'Supplier Address Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('supplier/create')
                        ->withErrors($validator)
                        ->withInput();
        }
    	Supplier::create($supplier);
    	Session::flash('flash_message','Supplier Entry Saved.');
    	return redirect()->back();
    }

    public function edit($id){

    	$supplier = Supplier::find($id);
        $title = 'Edit | '.$supplier->supplier_name.' ';
    	return view('suppliers.edit',compact('supplier','id','title'));
    }

    public function update($id){
    	$supplier = Request::except('_token','_method');
    	Supplier::where('id',$id)->update($supplier);
    	Session::flash('flash_message','Supplier Entry Updated.');
    	return redirect()->back();
    }

    public function delete_item($id){
    	$supplier = Supplier::find($id);
    	Supplier::where('id',$id)->delete($supplier);
    	Session::flash('flash_message','Supplier Entry Deleted.');
    	return redirect()->back();
    }

    public function show($id){
        $supplier = Supplier::with('voucher')->find($id);

        return view('suppliers.jacket',compact("supplier"));
    }
}
