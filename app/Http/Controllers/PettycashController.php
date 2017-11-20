<?php

namespace App\Http\Controllers;

use App\Pettycash;
use App\Starting_account_number;
use App\Coa_item;
use App\Pcf_item;
use App\Supplier;
use App\Item;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PettycashController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
    	$pcf = Pettycash::get();
    	return view('pettycashes.index',compact('pcf'));
    }

    public function create(){
    	$pcf = Pettycash::orderBy('pcf_number','DESC')->first();
        $supplier_id = Session::get('supplier_id');
        $newsupplier = \App\Supplier::where('id',$supplier_id)->first();

        if($pcf==NULL){
            $start = Starting_account_number::select('account_number')->where('account_name','=','pettycash_number')->first();
            $start_number = $start->account_number;
        }else{
            $start_number = $pcf->pcf_number+1;
        }

        $coa_item = Item::pluck('item_name','id');
        $supplier = Supplier::pluck('supplier_name','id');

    	return view('pettycashes.create',compact('start_number','coa_item','supplier','newsupplier'));
    }

    public function create_supplier(){
        $supplier = Request::get('supplier_name');
        $data = ['supplier_name'    =>      $supplier];
        $supplier_id = \App\Supplier::create($data)->id;
        Session::put('supplier_id',$supplier_id);
        Session::flash('flash_message','Supplier Entry Saved.');
        return redirect()->back();
    }

    public function store(){
    	$pcf = Request::all();

        $validator = Validator::make(Request::except('amount'), [
   
            'pcf_to'                                    => 'required',
            'dept.*'                                    => 'required',
            'account_item_id.*'                         => 'required',
            'description.*'                             => 'required',         
        ],
        [
            'pcf_to.required'                           => 'Supplier Field Required',
            'dept.*.required'                           => 'Department Field Required',
            'account_item_id.*.required'                => 'Item Field Required',
            'description.*.required'                    => 'Description Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('pettycash/create')
                        ->withErrors($validator)
                        ->withInput();
        }

    	Pettycash::create($pcf);
    	
    	foreach(Request::get('account_item_id') as $key => $value){
            $account_id = Item::where('id',$value)->first();
	    	$data = [
	    		'pcf_number'		        =>		Request::get('pcf_number'),
	    		'dt'						=>		Request::get('dt'),
	    		'account_item_id'			=>		$value,
	    		'description'				=>		Request::get('description')[$key],
	    		'amount'				    =>		Request::get('amount')[$key],
                'qty'                       =>      Request::get('qty')[$key],
                'unit_cost'                 =>      Request::get('unit_cost')[$key],
                'dept'                      =>      Request::get('dept')[$key],
	    	];

	    	Pcf_item::create($data);
	    }

    	Session::flash('flash_message','Pettycash Entry Saved.');
    	return redirect('pettycash');
    }

    public function edit($id){
    	$pcf = Pettycash::find($id);
        $supplier_id = Session::get('supplier_id');
        $newsupplier = \App\Supplier::where('id',$supplier_id)->first();

        $pcf_items = Pcf_item::where('pcf_number',$pcf->pcf_number)->get();
        $coa_item = Item::pluck('item_name','id');
        $supplier = Supplier::pluck('supplier_name','id');

    	return view('pettycashes.edit',compact('coa_item','pcf','pcf_items','id','supplier','newsupplier'));
    }

    public function update($id){
        $pcf = Pettycash::find($id);

        $pcf_items = Request::except('_token','_method','account_item_id','description','amount','tableSortable_length','qty','unit_cost');
        $pcf->invoice()->delete();
        Pettycash::where('id',$id)->update($pcf_items);

        if(Request::has('account_item_id')){
        
            foreach(Request::get('account_item_id') as $key => $value){
                if(!empty($value)){
                    $data = [
                        'pcf_number'                =>      Request::get('pcf_number'),
                        'qty'                       =>      Request::get('qty')[$key],
                        'dt'                        =>      Request::get('dt'),
                        'unit_cost'                 =>      Request::get('unit_cost')[$key],
                        'account_item_id'           =>      $value,
                        'description'               =>      Request::get('description')[$key],
                        'amount'                    =>      Request::get('amount')[$key],
                    ];

                    Pcf_item::create($data);
                }
            }
        }

    	Session::flash('flash_message','Pettycash Entry Updated.');
    	return redirect()->back();
    }

    public function delete_item($id){
    	$item = Pcf_item::where('id',$id)->first();
        Pcf_item::where('id',$id)->delete($item);
    	Session::flash('flash_message','Pettycash Entry Deleted.');
    	return redirect()->back();
    }
}
