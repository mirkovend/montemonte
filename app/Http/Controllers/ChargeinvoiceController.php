<?php

namespace App\Http\Controllers;

use App\Egg_unit;
use App\Egg_size;
use App\Starting_account_number;
use App\Customer;
use App\Charge_invoice;
use App\Charge_invoice_item;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Item;
use App\itemFlow;
use App\coa_transaction;
class ChargeinvoiceController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
    	$chargeinvoice = Charge_invoice::orderBy('dt','DESC')->get();
    	return view('chargeinvoices.index',compact('chargeinvoice'));
    }

    public function create(){
        $chargeinvoice = Charge_invoice::orderBy('charge_invoice_number','DESC')->first();
        
        $customer_id = Session::get('customer_id');
        $newcustomer = \App\Customer::where('id',$customer_id)->first();
        
        if($chargeinvoice==NULL){
            $start = Starting_account_number::select('account_number')->where('account_name','=','charge_invoice')->first();
            $start_number = $start->account_number;
        }else{
            $start_number = $chargeinvoice->charge_invoice_number+1;
        }

        $customer = Customer::pluck('customer_name','id');
    	$unit = Egg_unit::pluck('unit_code','id');
    	$size = Item::where('item_type_id',1)->pluck('item_name','id');
    	return view('chargeinvoices.create',compact('unit','size','start_number','customer','newcustomer'));
    }

    public function create_customer(){
        $customer = Request::get('customer_name');
        $data = ['customer_name'    =>      $customer];
        $customer_id = \App\Customer::create($data)->id;
        Session::put('customer_id',$customer_id);
        Session::flash('flash_message','Customer Entry Saved.');
        return redirect()->back();
    }

    public function store(){
    	$items = Request::all();

        $validator = Validator::make(Request::except('size_id'), [
   
            'customer_id'               => 'required',
            'dept.*'                    => 'required',
            'charge_invoice_qty.*'      => 'required',
            'unit_id.*'                 => 'required',
            'unit_price.*'              => 'required',
        ],
        [
            'customer_id.required'          => 'Customer Field Required',
            'dept.*.required'               => 'Department Field Required',
            'charge_invoice_qty.*.required' => 'Quantity Field Required',
            'unit_id.*.required'            => 'Unit Field Required',
            'unit_price.*.required'         => 'Unit Price Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('chargeinvoice/create')
                        ->withErrors($validator)
                        ->withInput();
        }
    
        if(Request::get('status')== 1){
            $items = Request::except('customer_id');
            Charge_invoice::create($items);
        }else{
            Charge_invoice::create($items);
        }

        if(Request::get('status')== 0){
            foreach(Request::get('charge_invoice_qty') as $key => $value){
                $data = [
                    'charge_invoice_number'   	=>  Request::get('charge_invoice_number'),
                    'charge_invoice_qty'      	=>  $value,
                    'unit_price'            	=>  Request::get('unit_price')[$key],
                    'amount'                	=>  Request::get('amount')[$key],
                    'dept'               	    =>  Request::get('dept')[$key],
                    'size_id'               	=>  Request::get('size_id')[$key],
                    'dt'                        =>  Request::get('dt'),
                ];
                $item = Item::where('id',Request::get('size_id')[$key])->first();
                Charge_invoice_item::create($data);
                itemFlow::insert(['type'=>'invoice','item_id'=>Request::get('size_id')[$key],'ref_no'=>Request::get('charge_invoice_number'),'credit'=>Request::get('amount')[$key],'created_at'=>carbon::now(),'updated_at'=>carbon::now()]);

               $coa1 = coa_transaction::create([
                    'coa_id'=>41,
                    'dt' => Request::get('dt'),
                    'ref'=>Request::get('charge_invoice_number'),
                    'type'=>'Invoice',
                    'debit'=>Request::get('amount')[$key],
                ]);//AP
                $coa1->coa_transaction_link()->create(['coa_id'=>$item->income_coa]);
                $coa2 = coa_transaction::create([
                    'coa_id'=>$item->income_coa,
                    'dt' => Request::get('dt'),
                    'ref'=>Request::get('charge_invoice_number'),
                    'type'=>'Invoice',
                    'credit'=>Request::get('amount')[$key],
                ]);
                $coa2->coa_transaction_link()->create(['coa_id'=>41]);
            }
        }

        Session::flash('flash_message','Charge Invoice Entry Saved.');
    	return redirect()->back();
        
    }

    public function edit($id){
        $customer_id = Session::get('customer_id');
        $newcustomer = \App\Customer::where('id',$customer_id)->first();
        
        $invoice = Charge_invoice::where('id',$id)->first();
        $items = Charge_invoice_item::where('charge_invoice_number',$invoice->charge_invoice_number)->get();
        $customer = Customer::pluck('customer_name','id');
        $unit = Egg_unit::pluck('unit_code','id');
        $size = Item::where('is_sub',69)->pluck('item_name','id');
        return view('chargeinvoices.edit',compact('invoice','id','unit','size','items','customer','newcustomer'));
    }

    public function update($id){
        $items = Request::except('_token','_method','charge_invoice_qty','unit_id','size_id','unit_price','amount','tableSortable_length');
        Charge_invoice::where('id',$id)->update($items);

            if(empty(Request::get('status'))){
                $items['status'] = 0;
                Charge_invoice::where('id',$id)->update($items);
            }
       
            Charge_invoice::where('id',$id)->update($items);
            Charge_invoice_item::where('charge_invoice_number',$items['charge_invoice_number'])->delete();
            foreach(Request::get('charge_invoice_qty') as $key => $value){
                if(!empty($value)){
                    $data = [
                        'charge_invoice_number'     =>  Request::get('charge_invoice_number'),
                        'charge_invoice_qty'        =>  $value,
                        'unit_price'                =>  Request::get('unit_price')[$key],
                        'amount'                    =>  Request::get('amount')[$key],
                        'size_id'                   =>  Request::get('size_id')[$key],
                        'dt'                        =>  Request::get('dt'),
                    ];

                    Charge_invoice_item::create($data);
                }
            }
       
        Session::flash('flash_message','Charge Invoice Entry Updated.');
        return redirect()->back();
    } 

    public function delete_item($id){
        $item = Charge_invoice_item::where('id',$id)->first();
        Charge_invoice_item::where('id',$id)->delete($item);
        Session::flash('flash_message','Charge Invoice Item Deleted.');
        return redirect()->back();
    }

    public function edit_item($id){
        $item = Charge_invoice_item::find($id);
        $unit = Egg_unit::pluck('unit_code','id');
        $size = Egg_size::pluck('size_code','id');
        $back = Charge_invoice::where('charge_invoice_number',$item->charge_invoice_number)->first();
        return view('chargeinvoices.edit_item',compact('item','id','unit','size','back'));
    }

    public function update_item($id){
        $item = Request::except('_method','_token');
        Charge_invoice_item::where('id',$id)->update($item);
        Session::flash('flash_message','Charge Invoice Item Updated.');
        return redirect()->back();
    }

    public function cancel_invoice($id){
        $items = Charge_invoice_item::where('charge_invoice_number',$id)->get();
        
        $data = [
            'status'    =>  1,
        ];

        Charge_invoice::where('charge_invoice_number',$id)->update($data);
        Charge_invoice_item::where('charge_invoice_number',$id)->delete($items);
        Session::flash('flash_message','Charge Invoice Cancelled.');
        return redirect()->back();

    }
}
