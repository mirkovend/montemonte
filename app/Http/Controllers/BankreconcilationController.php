<?php

namespace App\Http\Controllers;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Coa_subitem;
use App\Voucher;
use App\Voucher_payment;
use App\Deposit;
use App\Chart_of_account;
use App\Bankrecon;
use App\BankreconItem;
class BankreconcilationController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
    	$bankrecon = Bankrecon::with('coa')->latest()->get();

        return view('reconcilations.index',compact('bankrecon'));
    }

    public function create(){
        $coa = Chart_of_account::where('is_sub',2)->pluck('coa_title','id');
        $vouchers = Voucher::where('withCheque',1)->where('isReconciled',0)->get();
        $deposits = Deposit::where('isReconciled',0)->get();
        $account = Chart_of_account::whereIN('id',[451,456])->pluck('coa_title','id');
    	return view('reconcilations.create',compact('coa','vouchers','deposits','account'));
    }

    public function store(){
        $bank = Request::except('deposit_other_id','cheque_payments_id','cheque_length','depositpayment_length');
       
        $deposit_other_id = Request::get('deposit_other_id');
        

        $cheque_payments_id = Request::get('cheque_payments_id');
        
        $bank['status'] = "Pending";
        
        $bankrecon = Bankrecon::create($bank);
        if (Request::has('deposit_other_id')){
            foreach ($deposit_other_id as $key => $value) {
                $bankrecon->bankReconItem()->create(['payment_id'=>$value,'type'=>'deposit']);
            }
        }
        if (Request::has('cheque_payments_id')){
            foreach ($cheque_payments_id as $key => $value) {
                $bankrecon->bankReconItem()->create(['payment_id'=>$value,'type'=>'cheque_payment']);
            }
        }

        return redirect('bankreconcilation');
       
    	
    }

    public function verify_bank_recon(){
        $id = Request::get('id');
        $bankdata = Bankrecon::with('coa','bankReconItem')->find(15);
        $bankdata->update(['status'=>"Verified"]);
        $bankdata->coa->update(['balance'=>$bankdata->ending_balance]);
        dd();
        $vouchers = Voucher_payment::with(['voucher.supplier_one','bankrecon_item'=>function($q) use ($id) {
            $q->where('bankrecon_id',$id);
        }])->where('isReconciled',0)->where('account',$bankdata->coa_id)->get();

        $deposits = Deposit::with(['bankrecon_item'=>function($q) use ($id) {$q->where('bankrecon_id',$id);}])->where('isReconciled',0)->where('deposit_to_id',$bankdata->coa_id)->get();

        $deposits->each(function ($item, $key) {
            Deposit::find($item->id)->update(['isReconciled'=>1]);
        });

        $vouchers->each(function ($item, $key) {
            Voucher_payment::find($item->id)->update(['isReconciled'=>1]);
        });
        
    }

    public function edit($id){

        $coa = Chart_of_account::where('is_sub',2)->pluck('coa_title','id');
        
       
        $account = Chart_of_account::whereIN('id',[451,456])->pluck('coa_title','id');
        $bankdata = Bankrecon::with('coa')->find($id);
        $vouchers = Voucher_payment::with(['voucher.supplier_one','bankrecon_item'=>function($q) use ($id) {
            $q->where('bankrecon_id',$id);
        }])->where('account',$bankdata->coa_id)->get();

        $deposits = Deposit::with(['bankrecon_item'=>function($q) use ($id) {$q->where('bankrecon_id',$id);}])->where('deposit_to_id',$bankdata->coa_id)->get();
       
        return view('reconcilations.edit',compact('coa','vouchers','deposits','account','bankdata'));
    	
    }

    public function update($id){
    	
    }

    public function destroy($id){
    	
    }

    public function bank_deposit(){
        $all = Request::all();
        $deposit = Deposit::where('isReconciled',0)->where('deposit_to_id',$all['id'])->get();
        return $deposit;
    }
    public function bank_payment(){
        $all = Request::all();
        $vouchers = Voucher_payment::with('voucher.supplier_one')->where('isReconciled',0)->where('account',$all['id'])->get();
        return $vouchers;
    }
    public function bank_account(){
        $all = Request::all();
        $bank = Chart_of_account::find($all['id']);
        return $bank;
    }
}
