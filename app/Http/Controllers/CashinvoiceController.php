<?php

namespace App\Http\Controllers;

use App\Egg_unit;
use App\Egg_size;
use App\Cash_invoice;
use App\Cash_invoice_item;
use App\Starting_account_number;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Item;
use App\itemFlow;
use App\Coa_transaction;
use App\Coa_transaction_link;
use App\Chart_of_account;
class CashinvoiceController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
    	$cashinvoice = Cash_invoice::orderBy('dt','DESC')->get();
    	return view('cashinvoices.index',compact('cashinvoice'));
    }

    public function create(){
        $cashinvoice = Cash_invoice::orderBy('cash_invoice_number','DESC')->first();
        $customer_id = Session::get('customer_id');
        $newcustomer = \App\Customer::where('id',$customer_id)->first();
        $coa = Chart_of_account::where('is_sub',25)->lists('coa_title','id');

        if($cashinvoice==NULL){
            $start = Starting_account_number::select('account_number')->where('account_name','=','cash_invoice')->first();
            $start_number = $start->account_number;
        }else{
            $start_number = $cashinvoice->cash_invoice_number+1;
        }

    	$unit = Egg_unit::pluck('unit_code','id');
    	$size = Item::where('item_type_id',1)->pluck('item_name','id');
        $customer = \App\Customer::pluck('customer_name','id');
    	return view('cashinvoices.create',compact('unit','size','start_number','customer','newcustomer','coa'));
    }

    public function create_customer(){
        $customer = Request::get('customer_name');
        $data = ['customer_name' => $customer,'beginning_bal'=>Request::get('beginning_bal')];
        $customer_id = \App\Customer::create($data)->id;
        Session::put('customer_id',$customer_id);
        Session::flash('flash_message','Customer Entry Saved.');
        return redirect()->back();
    }

    public function store(){
    	$items = Request::all();
      
        $validator = Validator::make(Request::all(), [

            'cash_invoice_to'           => 'required',
            'dept.*'                    => 'required',
            'cash_invoice_qty.*'        => 'required',
            'unit_id.*'                 => 'required',
            'size_id.*'                 => 'required',
            'unit_price.*'              => 'required',
        ],
        [
            'cash_invoice_to.required'      => 'Sold To Field Required',
            'dept.*.required'               => 'Department Field Required',
            'cash_invoice_qty.*.required'   => 'Quantity Field Required',
            'unit_id.*.required'            => 'Unit Field Required',
            'size_id.*.required'            => 'Size Field Required',
            'unit_price.*.required'         => 'Unit Price Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('cashinvoice/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        if(Request::get('status')== 1){
            $items = Request::except('cash_invoice_to');
            Cash_invoice::create($items);
        }else{
            Cash_invoice::create($items);
        }

        if(Request::get('status')== 0){
            foreach(Request::get('cash_invoice_qty') as $key => $value){
                $data = [
                    'cash_invoice_number'   =>  Request::get('cash_invoice_number'),
                    'cash_invoice_qty'      =>  $value,
                    'unit_price'            =>  Request::get('unit_price')[$key],
                    'amount'                =>  Request::get('amount')[$key],
                    'dept'                  =>  Request::get('dept')[$key],
                    'size_id'               =>  Request::get('size_id')[$key],
                    'dt'                    =>  Request::get('dt'),
                ];

                Cash_invoice_item::create($data);

                $item = Item::find(Request::get('size_id')[$key]);
                $coa1 = Coa_transaction::create([
                    'coa_id'=>Request::get('account'),
                    'dt' => Request::get('dt'),
                    'ref'=>Request::get('cash_invoice_number'),
                    'type'=>'Sales Receipt',
                    'debit'=>Request::get('amount')[$key],
                ]);
                $coa1->coa_transaction_link()->create(['coa_id'=>$item->income_coa]);

                $coa2 = Coa_transaction::create([
                    'coa_id'=>$item->income_coa,
                    'dt' => Request::get('dt'),
                    'ref'=>Request::get('cash_invoice_number'),
                    'type'=>'Sales Receipt',
                    'credit'=>Request::get('amount')[$key],
                ]);
                $coa2->coa_transaction_link()->create(['coa_id'=>Request::get('account')]);
                
                itemFlow::insert(['type'=>'ci','item_id'=>Request::get('size_id')[$key],'ref_no'=>Request::get('cash_invoice_number'),'credit'=>$value,'created_at'=>carbon::now(),'updated_at'=>carbon::now()]);
            }
        }

        Session::flash('flash_message','Cash Invoice Entry Saved.');
    	return redirect()->back();

    }

    public function edit($id){
        $invoice = Cash_invoice::where('id',$id)->first();
        $customer = \App\Customer::pluck('customer_name','id');
        $items = Cash_invoice_item::where('cash_invoice_number',$invoice->cash_invoice_number)->get();
        $unit = Egg_unit::pluck('unit_code','id');
        
        $size = Item::where('is_sub',69)->pluck('item_name','id');
        return view('cashinvoices.edit',compact('invoice','id','unit','size','items','customer'));
    }

    public function update($id){
        $items = Request::except('_token','_method','cash_invoice_qty','unit_id','size_id','unit_price','amount','tableSortable_length');
        
        if(empty(Request::get('status'))){
            $items['status'] = 0;
            Cash_invoice::where('id',$id)->update($items);
        }
            Cash_invoice::where('id',$id)->update($items);
      

            Cash_invoice_item::where('cash_invoice_number',$items['cash_invoice_number'])->delete();

            foreach(Request::get('cash_invoice_qty') as $key => $value){
                
                    $data = [
                        'cash_invoice_number'   =>  Request::get('cash_invoice_number'),
                        'cash_invoice_qty'      =>  $value,
                        'unit_price'            =>  Request::get('unit_price')[$key],
                        'amount'                =>  Request::get('amount')[$key],
                        'size_id'               =>  Request::get('size_id')[$key],
                        'dt'                    =>  Request::get('dt'),
                    ];

                    Cash_invoice_item::create($data);
                
            }
        Session::flash('flash_message','Cash Invoice Entry Updated.');
        return redirect()->back();
    }

    public function delete_item($id){
        $item = Cash_invoice_item::where('id',$id)->first();
        Cash_invoice_item::where('id',$id)->delete($item);
        Session::flash('flash_message','Cash Invoice Item Deleted.');
        return redirect()->back();
    }

    public function cancel_invoice($id){
        $items = Cash_invoice_item::where('cash_invoice_number',$id)->get();

        $data = [
            'status'    =>  1,
        ];

        Cash_invoice::where('cash_invoice_number',$id)->update($data);
        Cash_invoice_item::where('cash_invoice_number',$id)->delete($items);
        Session::flash('flash_message','Cash Invoice Cancelled.');
        return redirect()->back();

    }
}
