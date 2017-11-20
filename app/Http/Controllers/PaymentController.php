<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Charge_invoice;
use App\Charge_invoice_item;
use App\Chart_of_account;
use App\Payment;
use App\Voucher;
use App\Voucher_payment;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Coa_transaction;
class PaymentController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
    	$payments = Payment::get();
        return view('payments.index',compact('payments'));
    }

    public function create(){
    	$customers = Customer::pluck('customer_name','id');
        $coa = \App\Chart_of_account::whereIN('is_sub',[19,25])->pluck('coa_title','id');

        $customer_id = Request::get('customer_id');
       
        $charge_invoice = Charge_invoice::where('customer_id',$customer_id)->where('status',0)->where('isPaid',0)->get();

        return view('payments.create',compact('customers','charge_invoice','customer_id','coa'));
    }

    public function search_invoice(){
        $customers = Customer::pluck('customer_name','id');
        $coa = \App\Chart_of_account::whereIN('is_sub',[19,25])->pluck('coa_title','id');

        $customer_id = Request::get('customer_id');
       
        $charge_invoice = Charge_invoice::where('customer_id',$customer_id)->where('status',0)->where('isPaid',0)->get();

        return view('payments.create',compact('customers','charge_invoice','customer_id','coa'));
    }
    public function receive_pay($invoice){
        $customers = Customer::pluck('customer_name','id');
        $coa = \App\Chart_of_account::whereIN('is_sub',[19,25])->pluck('coa_title','id');

       
        $charge_invoice = Charge_invoice::where('charge_invoice_number',$invoice)->where('status',0)->where('isPaid',0)->first();

        $cust = Customer::find($charge_invoice->customer_id);

        return view('payments.receive_payment',compact('customers','charge_invoice','customer_id','coa','cust'));
    }

    public function store(){
    	$payment = [
            'customer_id'                   =>      Request::get('customer_id'),
            'dt'                            =>      Request::get('dt'),
            'reference_number'              =>      Request::get('reference_number'),
            'or_number'                     =>      Request::get('or_number'),
            'deposit_to'                    =>      Request::get('deposit_to'),
        ];
        $validator = Validator::make(Request::all(), [
   
            'customer_id'               => 'required',
            'dt'                      => 'required',
            'reference_number'      => 'required',
            'or_number'                 => 'required',
            'deposit_to'                 => 'required',
            'id.*'              => 'required',
        ],
        [
            'customer_id.required'          => 'Customer Field Required',
            'dt.required'                 => 'Date Field Required',
            'reference_number.required' => 'Reference Number Field Required',
            'or_number.required'            => 'Or Number Field Required',
            'deposit_to.required'         => 'Deposit To Field Required',
            'id.*.required'         => 'Transaction Required',
        ]);

        if ($validator->fails()) {
            return redirect('payment/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        foreach(Request::get('id') as $key => $value){
            $total_amount = Charge_invoice::where('charge_invoice_number',Request::get('charge_invoice_number')[$value]["ci"])->first();
            
            $payment['charge_invoice_number'] = Request::get('charge_invoice_number')[$value]["ci"];
            $payment['payment'] = Request::get('payment_made')[$value]["py"];

            Payment::create($payment);

            $coa1 = Coa_transaction::create([
                    'coa_id'=>Request::get('deposit_to'),
                    // 'link_coa'=>23,
                    'dt' => Request::get('dt'),
                    'ref'=>Request::get('or_number'),
                    'type'=>'Payment',
                    'debit'=>Request::get('payment_made')[$value]["py"],
                ]);
            $coa1->coa_transaction_link()->create(['coa_id'=>41]);
            
            $coa2 = Coa_transaction::create([
                'coa_id'=>41,
                // 'link_coa'=>Request::get('deposit_to'),
                'dt' => Request::get('dt'),
                'ref'=>Request::get('or_number'),
                'type'=>'Payment',
                'credit'=>Request::get('payment_made')[$value]["py"],
                ]);
            $coa2->coa_transaction_link()->create(['coa_id'=>Request::get('deposit_to')]);


            $payment_made = Payment::where('charge_invoice_number',Request::get('charge_invoice_number')[$value]["ci"])->sum('payment');
            
            if($total_amount->invoice->sum('amount') == $payment_made){
                $data2 = [
                    'isPaid'        =>      1,
                ];
                Charge_invoice::where('charge_invoice_number',Request::get('charge_invoice_number')[$value]["ci"])->update($data2);
            }       
        }

        Session::flash('flash_message','Payment Entry Saved.');
        return redirect('payment');
    }
    public function receive_payment($invoice){
      
        $payment = [
            'customer_id'                   =>      Request::get('customer_id'),
            'dt'                            =>      Request::get('dt'),
            'reference_number'              =>      Request::get('reference_number'),
            'or_number'                     =>      Request::get('or_number'),
            'deposit_to'                    =>      Request::get('deposit_to'),
        ];
        $validator = Validator::make(Request::except('id','charge_invoice_number','payments','payment_made'), [
   
            'customer_id'               => 'required',
            'dt'                      => 'required',
            'reference_number'      => 'required',
            'or_number'                 => 'required',
            'deposit_to'                 => 'required',
            // 'id.*'              => 'required',
        ],
        [
            'customer_id.required'          => 'Customer Field Required',
            'dt.required'                 => 'Date Field Required',
            'reference_number.required' => 'Reference Number Field Required',
            'or_number.required'            => 'Or Number Field Required',
            'deposit_to.required'         => 'Deposit To Field Required',
            // 'id.*.required'         => 'Transaction Required',
        ]);

        if ($validator->fails()) {
            return redirect('payment/receive/'.$invoice)
                        ->withErrors($validator)
                        ->withInput();
        }

        foreach(Request::get('id') as $key => $value){
            $total_amount = Charge_invoice::where('charge_invoice_number',Request::get('charge_invoice_number')[$value]["ci"])->first();
            
            $payment['charge_invoice_number'] = Request::get('charge_invoice_number')[$value]["ci"];
            $payment['payment'] = Request::get('payment_made')[$value]["py"];

            Payment::create($payment);

            $coa1 = Coa_transaction::create([
                    'coa_id'=>Request::get('deposit_to'),
                    // 'link_coa'=>23,
                    'dt' => Request::get('dt'),
                    'ref'=>Request::get('or_number'),
                    'type'=>'Payment',
                    'debit'=>Request::get('payment_made')[$value]["py"],
                ]);
            $coa1->coa_transaction_link()->create(['coa_id'=>41]);
            
            $coa2 = Coa_transaction::create([
                'coa_id'=>41,
                // 'link_coa'=>Request::get('deposit_to'),
                'dt' => Request::get('dt'),
                'ref'=>Request::get('or_number'),
                'type'=>'Payment',
                'credit'=>Request::get('payment_made')[$value]["py"],
                ]);
            $coa2->coa_transaction_link()->create(['coa_id'=>Request::get('deposit_to')]);


            $payment_made = Payment::where('charge_invoice_number',Request::get('charge_invoice_number')[$value]["ci"])->sum('payment');
            
            if($total_amount->invoice->sum('amount') == $payment_made){
                $data2 = [
                    'isPaid'        =>      1,
                ];
                Charge_invoice::where('charge_invoice_number',Request::get('charge_invoice_number')[$value]["ci"])->update($data2);
            }       
        }

        Session::flash('flash_message','Payment Entry Saved.');
        return redirect('payment');
    }

    public function edit($id){
        $payment = Payment::find($id);
        return view('payments.edit',compact('payment','id'));
    }

    public function update($id){
        $payment = Payment::find($id);
        $payment_made = Request::except('_token','_method');
        Payment::where('id',$id)->update($payment_made);

        $total_amount = Charge_invoice::where('charge_invoice_number',$payment->charge_invoice_number)->first();
        $sum_amount = Payment::where('charge_invoice_number',$payment->charge_invoice_number)->sum('payment');
        if($total_amount != $sum_amount){
            $data2 = [
                'isPaid'        =>      0,
            ];
            Charge_invoice::where('charge_invoice_number',$payment->charge_invoice_number)->update($data2);
        }
        Session::flash('flash_message','Payment Entry Updated.');
        return redirect()->back();
    }

    public function delete_item($id){
        
    }

    public function index_voucher_payment(){
        $voucher_payments = Voucher_payment::get();
        return view('payments.index_voucher_payment',compact('voucher_payments'));
    }

    public function create_voucher_payment(){
        $vouchers = Voucher::where('withCheque',0)->get();
        $account = \App\Coa_subitem::where('coa_sub_id',1)->pluck('coa_item_name','id');
        return view('payments.create_voucher_payment',compact('vouchers','account'));
    }

    public function store_voucher_payment(){
        foreach (Request::get('payment') as $key => $value) {
            if($value != '0.00'){
                $data = [
                    'dt'                    =>          Request::get('dt'),
                    'cheque_number'         =>          Request::get('cheque_number'),
                    'account'               =>          Request::get('account'),
                    'voucher_number'        =>          Request::get('voucher_number')[$key],
                    'payment'               =>          $value,
                ];

                Voucher_payment::create($data);
            }

            $voucher_amount = Voucher::where('voucher_number',Request::get('voucher_number')[$key])->first();
            $voucher_payment = Voucher_payment::where('voucher_number',Request::get('voucher_number')[$key])->sum('payment');
            if($voucher_amount->amount == $voucher_payment){
                $data2 = [
                    'withCheque'      =>      1,
                ];
                Voucher::where('voucher_number',Request::get('voucher_number')[$key])->update($data2);
            }
        }

        Session::flash('flash_message','Payment Entry Saved.');
        return redirect()->back();
    }
    public function test(){

        return Request::all();
    }
}
