<?php

namespace App\Http\Controllers;

use App\Joborder;
use App\Joborder_item;
use App\Starting_account_number;
use App\Coa_item;
use App\Supplier;
use App\Item;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class JoborderController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
    	$job = Joborder::get();
    	return view('joborders.index',compact('job'));
    }

    public function create(){
        $job = Joborder::orderBy('joborder_number','DESC')->first();
        $supplier_id = Session::get('supplier_id');
        $newsupplier = \App\Supplier::where('id',$supplier_id)->first();
        
        if($job==NULL){
            $start = Starting_account_number::select('account_number')->where('account_name','=','joborder_number')->first();
            $start_number = $start->account_number;
        }else{
            $start_number = $job->joborder_number+1;
        }

        $coa_item = Item::where('item_type_id',2)->pluck('item_name','id');
        $supplier = Supplier::pluck('supplier_name','id');

    	return view('joborders.create',compact('start_number','coa_item','supplier','newsupplier'));
    }

    public function create_supplier(){
        $supplier = Request::get('supplier_name');
        $data = ['supplier_name' => $supplier];
        $supplier_id = \App\Supplier::create($data)->id;
        Session::put('supplier_id',$supplier_id);
        Session::flash('flash_message','Supplier Entry Saved.');
        return redirect()->back();
    }

    public function store(){
    	$job = Request::all();

        $validator = Validator::make(Request::all(), [
   
            'joborder_to'                               => 'required',
            'dept'                                      => 'required',
            'account_item_id.*'                         => 'required',
            'description.*'                             => 'required',
            'amount.*'                                  => 'required',            
        ],
        [
            'joborder_to.required'                     => 'Supplier Field Required',
            'dept.required'                             => 'Department Field Required',
            'account_item_id.*.required'                => 'Item Field Required',
            'description.*.required'                    => 'Description Field Required',
            'amount.*.required'                         => 'Amount Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('joborder/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        Joborder::create($job);
        
        foreach(Request::get('account_item_id') as $key => $value){
            /*$account_id = Item::where('id',$value)->first();
            dd($account_id);

            if($account_id->coa_sub1 != 0){
                $acc_id = $account_id->coa_sub1;
            }elseif($account_id->coa_sub2 != 0){
                $acc_id = $account_id->coa_sub2;
            }elseif($account_id->coa_sub3 != 0){
                $acc_id = $account_id->coa_sub3;
            }elseif($account_id->coa_sub4 != 0){
                $acc_id = $account_id->coa_sub4;
            }*/
            
            $data = [
                'joborder_number'           =>      Request::get('joborder_number'),
                'dt'                        =>      Request::get('dt'),
                'account_item_id'           =>      $value,
                'description'               =>      Request::get('description')[$key],
                'amount'                    =>      Request::get('amount')[$key],
            ];
           
            Joborder_item::create($data);
        }

        Session::flash('flash_message','Job Order Entry Saved.');
        return redirect()->back();
    }

    public function edit($id){
    	$job = Joborder::find($id);
        $supplier_id = Session::get('supplier_id');
        $newsupplier = \App\Supplier::where('id',$supplier_id)->first();
        
        $coa_item = Item::pluck('item_name','id');
        $supplier = Supplier::pluck('supplier_name','id');
        $jo_items = Joborder_item::where('joborder_number',$job->joborder_number)->get();
        return view('joborders.edit',compact('job','id','coa_item','supplier','jo_items','newsupplier'));
    }

    public function update($id){
    	$job = Joborder::find($id);
        $job->invoice()->delete();
        $jo_items = Request::except('_token','_method','account_item_id','description','amount','tableSortable_length');
        Joborder::where('id',$id)->update($jo_items);

        foreach(Request::get('account_item_id') as $key => $value){
            if(!empty($value)){
                $data = [
                    'joborder_number'           =>      Request::get('joborder_number'),
                    'dt'                        =>      Request::get('dt'),
                    'account_item_id'           =>      $value,
                    'description'               =>      Request::get('description')[$key],
                    'amount'                    =>      Request::get('amount')[$key],
                ];

                Joborder_item::create($data);
            }
        }
        Session::flash('flash_message','Job Order Entry Updated.');
        return redirect()->back();
    }

    public function delete_item($id){
    	
    }
}
