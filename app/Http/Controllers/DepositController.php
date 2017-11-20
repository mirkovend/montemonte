<?php

namespace App\Http\Controllers;

use App\Deposit;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Coa_transaction;
use App\Chart_of_account;

class DepositController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
    	$deposits = Deposit::get();
        return view('deposits.index',compact('deposits'));
    }

    public function create(){
        $subaccount = \App\Chart_of_account::where('is_sub',19)->pluck('coa_title','id');
        $subaccount_to = \App\Chart_of_account::where('is_sub',25)->pluck('coa_title','id');
    	return view('deposits.create',compact('subaccount','subaccount_to'));
    }

    public function store(){
    	$deposit = Request::all();

        $validator = Validator::make(Request::all(), [
   
            'deposit_to_id'                             => 'required',
            'reference_number'                          => 'required',
            'deposit_from_id'                           => 'required',
            'amount'                                    => 'required',
            'cheque_number'                             => 'required',
            'deposit_memo'                              => 'required',           
        ],
        [
            'deposit_to_id.required'                    => 'Deposit To Field Required',
            'reference_number.required'                 => 'Reference Number Field Required',
            'deposit_from_id.required'                  => 'Deposit From Field Required',
            'amount.required'                           => 'Amount Field Required',
            'deposit_memo.required'                     => 'Memo Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('deposit/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        Deposit::create($deposit);
        //bank
        $bank = Coa_transaction::create([
            'coa_id'=>$deposit['deposit_to_id'],
            'type' =>"Deposit",
            'link_coa'=>$deposit['deposit_from_id'],
            'dt'    =>  $deposit['dt'],
            'ref'   => $deposit['reference_number'],
            'debit' => $deposit['amount'],
        ]);
        $bank->coa_transaction_link()->create(['coa_id'=>$deposit['deposit_from_id']]);
        //cash on hand
        $coh = Coa_transaction::create([
            'coa_id'=>$deposit['deposit_from_id'],
            'type' =>"Deposit",
            'link_coa'=>$deposit['deposit_to_id'],
            'dt'    =>  $deposit['dt'],
            'ref'   => $deposit['reference_number'],
            'credit' => $deposit['amount'],
        ]);
        $coh->coa_transaction_link()->create(['coa_id'=>$deposit['deposit_to_id']]);
        
    	Session::flash('flash_message','Deposit Entry Saved.');
    	return redirect()->back();
    }

    public function edit($id){
    	$deposit = Deposit::find($id);
        $subaccount = \App\Chart_of_account::where('is_sub',2)->pluck('coa_title','id');
        $subaccount_to = \App\Chart_of_account::where('is_sub',9)->pluck('coa_title','id');
        return view('deposits.edit',compact('subaccount','subaccount_to','deposit','id'));
    }

    public function update($id){
    	$deposit = Deposit::find($id);
        $query = Request::except('_token','_method');
        Deposit::where('id',$id)->update($query);
    	Session::flash('flash_message','Deposit Entry Updated.');
    	return redirect()->back();
    }

    public function delete_item($id){
    	$deposit = Deposit::find($id);
        Deposit::where('id',$id)->delete();
    	Session::flash('flash_message','Deposit Entry Deleted.');
    	return redirect()->back();
    }
}
