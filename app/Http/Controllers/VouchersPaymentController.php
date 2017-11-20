<?php

namespace App\Http\Controllers;

use Request;
use App\Voucher_payment;
use App\Voucher;
use App\Chart_of_account;
use App\Http\Requests;
use Session;
use App\coa_transaction;
use Carbon\Carbon;
class VouchersPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $voucher_payments = Voucher_payment::get();
        return view('vouchers.payments.index',compact('voucher_payments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $vouchers = Voucher::where('withCheque',0)->get();
        $account = Chart_of_account::where('is_sub',19)->pluck('coa_title','id');
        return view('vouchers.payments.create',compact('vouchers','account'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
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

                $ap = coa_transaction::insert([
                    'coa_id'=>174,
                    'dt' => Request::get('dt'),
                    'ref'=>Request::get('cheque_number'),
                    'type'=>'Payment',
                    'debit'=>$value,
                    'created_at'=>carbon::now(),
                    'updated_at'=>carbon::now()
                ]);//AP
                $ap->coa_transaction_link()->create(['coa_id'=>Request::get('account')]);

                $link = coa_transaction::insert([
                    'coa_id'=>Request::get('account'),
                    'dt' => Request::get('dt'),
                    'ref'=>Request::get('cheque_number'),
                    'type'=>'Payment',
                    'credit'=>$value,
                    'created_at'=>carbon::now(),
                    'updated_at'=>carbon::now()
                ]);
                $link->coa_transaction_link()->create(['coa_id'=>174]);
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
