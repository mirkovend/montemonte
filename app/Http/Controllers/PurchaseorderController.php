<?php

namespace App\Http\Controllers;

use App\Purchase_order;
use App\Purchase_order_item;
use App\Starting_account_number;
use App\Supplier;
use App\Term;
use App\Coa_item;
use App\Item;
use App\itemFlow;
use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PurchaseorderController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
    	$purchaseorder = Purchase_order::get();
    	return view('purchaseorders.index',compact('purchaseorder'));
    }

    public function create(){
    	$purchaseorder = Purchase_order::orderBy('purchase_order_number','DESC')->first();
        $supplier_id = Session::get('supplier_id');
        $newsupplier = \App\Supplier::where('id',$supplier_id)->first();

        if($purchaseorder==NULL){
            $start = Starting_account_number::select('account_number')->where('account_name','=','purchase_order')->first();
            $start_number = $start->account_number;
        }else{
            $start_number = $purchaseorder->purchase_order_number+1;
        }

        $suppliers = Supplier::pluck('supplier_name','id');
        $terms = Term::pluck('term_days','id');
        $coa_item = Item::pluck('item_name','id');
    	return view('purchaseorders.create',compact('start_number','suppliers','terms','coa_item','newsupplier'));
    }

    public function create_supplier(){
        $supplier = Request::all();
        $supplier_id = \App\Supplier::create($supplier)->id;
        Session::put('supplier_id',$supplier_id);
        Session::flash('flash_message','Supplier Entry Saved.');
        return redirect()->back();
    }

    public function store(){
    	$purchaseorder = Request::all();

        $validator = Validator::make(Request::except('return_dt'), [
   
            'supplier_id'                               => 'required',
            'purchase_order_to'                         => 'required',
            'term_id'                                   => 'required',
            'purchase_order_type'                       => 'required',
            'item_desc.*'                               => 'required',
            'item_label.*'                              => 'required',
            'item_qty.*'                                => 'required',
            // 'item_unit.*'                               => 'required',
            'item_price.*'                              => 'required',
            
        ],
        [
            'supplier_id.required'                      => 'Supplier Field Required',
            'purchase_order_to.required'                => 'Deliver To Field Required',
            'term_id.required'                          => 'Terms Field Required',
            'purchase_order_type.required'              => 'Purchase Order Field Required',
            'item_desc.*.required'                      => 'Description Field Required',
            'item_label.*.required'                     => 'Department Field Required',
            'item_qty.*.required'                       => 'Quantity Field Required',
            // 'item_unit.*.required'                      => 'Unit of Measure Field Required',
            'item_price.*.required'                     => 'Unit Price Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('purchaseorder/create')
                        ->withErrors($validator)
                        ->withInput();
        }

    	Purchase_order::create($purchaseorder);
    	
    	foreach(Request::get('account_item_id') as $key => $value){
	    	$data = [
	    		'purchase_order_number'		=>		Request::get('purchase_order_number'),
	    		'dt'						=>		Request::get('dt'),
	    		'account_item_id'			=>		$value,
	    		'item_desc'					=>		Request::get('item_desc')[$key],
	    		'item_label'				=>		Request::get('item_label')[$key],
	    		'item_qty'					=>		Request::get('item_qty')[$key],
	    		// 'item_unit'					=>		Request::get('item_unit')[$key],
	    		'item_price'				=>		Request::get('item_price')[$key],
	    		'item_total'				=>		Request::get('item_total')[$key],
	    	];



	    	Purchase_order_item::create($data);
	    }

    	Session::flash('flash_message','Purchase Order Entry Saved.');
    	return redirect()->back();
    }

    public function edit($id){
    	$purchaseorder = Purchase_order::with('invoice')->find($id);
    	$po_items = Purchase_order_item::where('purchase_order_number',$purchaseorder->purchase_order_number)->get();
    	$suppliers = Supplier::pluck('supplier_name','id');
        $terms = Term::pluck('term_days','id');
        $coa_item = Item::pluck('item_name','id');
        $supplier_id = Session::get('supplier_id');
        $newsupplier = \App\Supplier::where('id',$supplier_id)->first();
    	return view('purchaseorders.edit',compact('purchaseorder','id','suppliers','terms','po_items','coa_item','newsupplier'));
    }

    public function update($id){
        
            $purchaseorder = [
                'purchase_order_number'     =>      Request::get('purchase_order_number'),
                'dt'                        =>      Request::get('dt'),
                'supplier_id'               =>      Request::get('supplier_id'),
                'term_id'                   =>      Request::get('term_id'),
                'purchase_order_to'         =>      Request::get('purchase_order_to'),
                'purchase_order_type'       =>      Request::get('purchase_order_type'),
                'return_dt'                 =>      Request::get('return_dt'),
                'status'                    =>      Request::get('status'),
            ];
    	
    	$po = Purchase_order::where('id',$id)->update($purchaseorder);

    	$del = Purchase_order_item::where('purchase_order_number',Request::get('purchase_order_number'))->delete();
        Purchase_order_item::where('purchase_order_number',Request::get('purchase_order_number'))->delete();
    	foreach(Request::get('account_item_id') as $key => $value){
	    	$data = [
	    		'purchase_order_number'		=>		Request::get('purchase_order_number'),
	    		'dt'						=>		Request::get('dt'),
	    		'account_item_id'			=>		$value,
	    		'item_desc'					=>		Request::get('item_desc')[$key],
	    		'item_label'				=>		Request::get('item_label')[$key],
	    		'item_qty'					=>		Request::get('item_qty')[$key],
	    		'item_unit'					=>		Request::get('item_unit')[$key],
	    		'item_price'				=>		Request::get('item_price')[$key],
	    		'item_total'				=>		Request::get('item_total')[$key],
	    	];
	    	Purchase_order_item::create($data);
	    }

    	Session::flash('flash_message','Purchase Order Entry Updated.');
    	return redirect()->back();
    }

    public function delete_item($id){
    	
    	Session::flash('flash_message','Purchase Order Entry Deleted.');
    	return redirect()->back();
    }
}
