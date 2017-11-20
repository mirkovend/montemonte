<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Charge_invoice;
use App\Charge_invoice_item;
use App\Cash_invoice;

use App\Payment;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
    	$customers = Customer::get();
        $title = "Customer Lists";
    	return view('customers.index',compact('customers','title'));
    }

    public function create(){
        $title = "Create Customer";
    	return view('customers.create',compact('title'));
    }

    public function store(){
    	$customer = Request::all();

        $validator = Validator::make(Request::except('customer_contact'), [
   
            'customer_name'                             => 'required',
            'customer_address'                          => 'required',          
        ],
        [
            'customer_name.required'                    => 'Customer Name Field Required',
            'customer_address.required'                 => 'Customer Address Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('customer/create')
                        ->withErrors($validator)
                        ->withInput();
        }

    	Customer::create($customer);
    	Session::flash('flash_message','Customer Entry Saved.');
    	return redirect('customer');
    }

    public function edit($id){
    	
    }

    public function update($id){
    	
    }

    public function view_jacket($id){
        $customer = Customer::find($id);
    	$ch_invoice = Charge_invoice::where('customer_id',$id)->get();
        $cashinvoice = Cash_invoice::where('cash_invoice_to',$id)->get();
        $payment = Payment::where('customer_id',$id)->get();
        $trans_data = collect();
        $trans_data = $trans_data->merge($ch_invoice);
        $trans_data = $trans_data->merge($cashinvoice);
        $trans_data = $trans_data->merge($payment);
       

        $balance = 0;
        $sub_balance = 0;
        $trans_array = collect();
        $balance += $customer->beginning_bal;
        foreach ($trans_data as $key => $transaction) {

            $trans = new \stdClass;

            if($transaction['table'] == "charge_invoices"){

                $trans->date = Carbon::parse($transaction->dt)->toFormattedDateString();
                $trans->type = "Invoice";
                $trans->ref = $transaction->charge_invoice_number;
                $trans->amount =  $transaction->invoice->sum('amount');
                $trans->charge =  $transaction->invoice->sum('amount');
                $balance += $transaction->invoice->sum('amount');

            }elseif($transaction['table'] == "cash_invoices"){

                $trans->date = Carbon::parse($transaction->dt)->toFormattedDateString();
                $trans->type = "Sales Receipt";
                $trans->ref = $transaction->cash_invoice_number;
                $trans->amount = $transaction->ci_item->sum('amount');
                $trans->charge = 0;

            }elseif($transaction['table'] == "payments"){

                $trans->date = Carbon::parse($transaction->dt)->toFormattedDateString();
                $trans->type = "Payment";
                $trans->ref = $transaction->reference_number;
                $trans->amount = $transaction->payment;
                $trans->charge = -$transaction->payment;
                $balance -=$transaction->payment;

            }

            $trans_array[] = $trans;
        }
        $trans_array = $trans_array->sortByDesc('date');

    	return view('customers.jacket',compact('ch_invoice','payment','customer','trans_array','sub_balance','balance'));
    }
}
