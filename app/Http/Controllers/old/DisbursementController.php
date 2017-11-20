<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use App\Supplier;
use App\ChartOfAccount;
use App\Disbursement;
use Request;
use Validator;
class DisbursementController extends Controller
{
    //


    public function index(){
        $title = "Disbursement Lists";
        $disbursements = Disbursement::latest()->get();
    	return view('disbursement.index',compact('disbursements','title'));
    }
    public function create(){
        $title = "Create Check Voucher";
    	$account = Supplier::lists('fname','id');
        $chart = ChartOfAccount::lists('account_title','id');
    	return view('disbursement.create',compact('title','account','chart'));
    }
    public function store(){

    
        $validator = Validator::make(Request::all(), [
   
            'supplier_id'         => 'required',
            'date'         => 'required|date',
            'due_date'         => 'required|date',
            'explanation'   => 'required',
            'amount'   => 'required',
            'voucher_number'       => 'required|unique:disbursements',
            'debit.*'   => 'required',
            'credit.*'   => 'required',
            'chart_of_account_id.*'   => 'required',
        ],
        [
            'debit.*.required' => 'Debit Field Required',
            'credit.*.required' => 'credit Field Required',
            'chart_of_account_id.*.required' => 'Chart of Account Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('disbursement/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $info = Request::except('chart_of_account_id','debit','credit');
        $item = Request::except('payee','date','voucher_number','due_date','explanation','amount');


        $info['status'] = 'Open';
        $amount = str_replace("₱ ","",$info['amount']);
        $info['amount'] = str_replace(",","",$amount);
        $disbursement = Disbursement::create($info);
        foreach ($item['chart_of_account_id'] as $key => $value) {

            $credit = str_replace("₱ ","",$item['credit'][$key]);
            $item['credit'][$key] = str_replace(",","",$credit);
            $debit = str_replace("₱ ","",$item['debit'][$key]);
            $item['debit'][$key] = str_replace(",","",$debit);
            
            $data = ['chart_of_account_id'=>$value,'debit'=>$item['debit'][$key],'credit'=>$item['credit'][$key]];

            $disbursement->disbursement_items()->create($data);
        }
   
        return redirect('disbursement');
    }
}
