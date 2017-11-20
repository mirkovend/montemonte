<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Request;

use App\Http\Requests;
use App\Disbursement_items;
use App\chartofaccount;
use App\accountReceivableItem;
use App\accountType;
use Carbon\Carbon;
use App\coaItem;
use App\recurringItem;
class ReportController extends Controller
{
    //

    public function general_ledger(){
    	$title = "General Ledger";
        $ledgers = disbursement_items::latest()->groupBy('chart_of_account_id')->get();
      
    	return view('report.genledger',compact('title','ledgers'));
    }

    public function view_ledger($id){
    	$item = array();
        $item_count = 0;
        $Ritem = array();
        $Ritem_count = 0;
        $ledger = chartofaccount::find($id);
      	$title = $ledger->account_title;

        foreach($ledger->item as $ledge){
           
            $item[$item_count] = $ledge;
            $item[$item_count]->reference = $ledge->disbursement->reference;
            $item_count++;
        }
        foreach($ledger->receivable_item as $receivableItem){
           
            $item[$item_count] = $receivableItem;
            $item[$item_count]->reference = $receivableItem->receive->reference;
            $item_count++;
        }
        
       $ledgers = collect($item)->sortBy('created_at');
    	return view('report.view_ledger',compact('title','ledger','ledgers'));
    }
    public function trial_balance(){
    	$dt = Carbon::now()->toFormattedDateString();

      $date = Carbon::now()->toDateString();
      $array = [];
        $chartofaccount = chartofaccount::all();
        $array[] = $chartofaccount->where('category','Assets');
        $array[] = $chartofaccount->where('category','Liabilities');
        $array[] = $chartofaccount->where('category','Equity');
        $array[] = $chartofaccount->where('category','Revenue');
        $array[] = $chartofaccount->where('category','Expenses');
        $items = array();
        $item_count = 0;

        foreach ($array as $key => $value) {
            foreach ($value as $key => $value1) {

            $items[] = $value1;
              foreach ($value1->subaccount as $sub) {
                 $items[] = $sub;
              }
            }
        }
        $account = [];
        $account_count = 0;
        foreach ($items as  $item) {
          $account[] = $item;


          $coa_debit = coaItem::where('chart_of_account_id',$item->id)->sum('debit');
          $coa_credit = coaItem::where('chart_of_account_id',$item->id)->sum('credit');

          $rec_debit = recurringItem::where('chart_of_account_id',$item->id)->whereBetween('date',['1990-03-23',$date])->sum('debit');

          $rec_credit = recurringItem::where('chart_of_account_id',$item->id)->whereBetween('date',['1990-03-23',$date])->sum('credit');

          $totdebit = $coa_debit+$rec_debit;
          $totcredit = $coa_credit+$rec_credit;
          if($item->category == 'Assets' || $item->category == 'Expenses' ){
            if($item->account_type->name == 'Fixed Asset' && $totcredit != 0 ){
              $account[$account_count]->debit = 0;
              $account[$account_count]->credit =$totcredit - $totdebit;
            }else{
              $account[$account_count]->debit =$totdebit - $totcredit;
              $account[$account_count]->credit =0;
            }
            
          }else{
            $account[$account_count]->debit =0;
            $account[$account_count]->credit =$totcredit- $totdebit;
          }
          $account_count++;
        }
        $coas = $account;

      	$title = 'Trial Balance as of '. $dt;
    	return view('report.trial_balance',compact('title','dt','charts','coas'));
    }


    public function trial_balance_post(){

      $all = Request::all();
      $dt = Carbon::parse($all['to'])->toFormattedDateString();
      $array = [];
        $chartofaccount = chartofaccount::all();
        $array[] = $chartofaccount->where('category','Assets');
        $array[] = $chartofaccount->where('category','Liabilities');
        $array[] = $chartofaccount->where('category','Equity');
        $array[] = $chartofaccount->where('category','Revenue');
        $array[] = $chartofaccount->where('category','Expenses');
        $items = array();
        $item_count = 0;
        foreach ($array as $key => $value) {
            foreach ($value as $key => $value1) {

            $items[] = $value1;
              foreach ($value1->subaccount as $sub) {
                 $items[] = $sub;
              }
            }
        }
        $account = [];
        $account_count = 0;
        foreach ($items as  $item) {
          $account[] = $item;

          $item_coa = coaItem::where('chart_of_account_id',$item->id)->whereBetween('created_at',['1990-03-23',$all['to']])->get();
          $coa_debit = $item_coa->sum('debit');
          $coa_credit = $item_coa->sum('credit');

           $rec_coa = recurringItem::where('chart_of_account_id',$item->id)->whereBetween('date',['1990-03-23',$all['to']])->get();

          $totdebit = $coa_debit + $rec_coa->sum('debit');
          $totcredit = $coa_credit+ $rec_coa->sum('credit');
          if($item->category == 'Assets' || $item->category == 'Expenses' ){
            if($item->account_type->name == 'Fixed Asset' && $totcredit != 0 ){
              $account[$account_count]->debit = 0;
              $account[$account_count]->credit =$totcredit - $totdebit;
            }else{
              $account[$account_count]->debit =$totdebit - $totcredit;
              $account[$account_count]->credit =0;
            }
            
          }else{
            $account[$account_count]->debit =0;
            $account[$account_count]->credit =$totcredit- $totdebit;
          }
          $account_count++;
        }
        $coas = $account;
        $title = 'Trial Balance As of '. Carbon::parse($all['to'])->toFormattedDateString();
      return view('report.trial_balance_post',compact('all','title','dt','charts','coas'));
    }

    public function balance_sheet(){
    	$dt = Carbon::now()->toFormattedDateString();
    	$charts = disbursement_items::latest()->groupBy('chart_of_account_id')->get();
      $coas = chartofaccount::all();
      $type = accountType::all();
      $liab_equit = [];
      $liab_equit[] = $type->where('category','Liabilities');
      $liab_equit[] = $type->where('category','Equity');

      $type_liab_equity = [];

      foreach ($liab_equit as $key => $value) {
      
        foreach ($value as  $value1) {
          $type_liab_equity[] = $value1;
        }
      }
      $expense_total = 0;
      $expense_total1 = 0;
      $expenses = $type->where('category','Expenses');
      foreach ($expenses as  $expense) {
        foreach($expense->coa as $coa){
          $expense_total += $coa->coa_items()->sum('debit') - $coa->coa_items()->sum('credit');

          $rec = recurringItem::where('chart_of_account_id',$coa->id)->whereBetween('date',['1990-02-02',Carbon::now()])->get();

          $expense_total += $rec->sum('debit')-$rec->sum('credit');
        }
      }
      $revenues = $type->where('category','Revenue');
      $revenue_total = 0;
      foreach ($revenues as  $revenue) {
        foreach($revenue->coa as $coa){
          $revenue_total += $coa->coa_items()->sum('credit')-$coa->coa_items()->sum('debit');
          $rec1 = recurringItem::where('chart_of_account_id',$coa->id)->whereBetween('date',['1990-02-02',Carbon::now()])->get();

          $revenue_total += $rec1->sum('credit')-$rec->sum('debit');
        }
      }
      $retain_earnings = $revenue_total-$expense_total;
      $title = 'Balance Sheet as of '. $dt;
    	return view('report.balance_sheet',compact('title','dt','charts','coas','type','type_liab_equity','retain_earnings'));
    }


    public function balance_sheet_post(){
      $dt = Carbon::now()->toFormattedDateString();
      $all = Request::all();
      $charts = disbursement_items::latest()->groupBy('chart_of_account_id')->get();
      $coas = chartofaccount::all();
      $type = accountType::all();
      $liab_equit = [];
      $liab_equit[] = $type->where('category','Liabilities');
      $liab_equit[] = $type->where('category','Equity');

      $type_liab_equity = [];

      foreach ($liab_equit as $key => $value) {
      
        foreach ($value as  $value1) {
          $type_liab_equity[] = $value1;
        }
      }
      $expense_total = 0;
     
      $expenses = $type->where('category','Expenses');
      foreach ($expenses as  $expense) {
        foreach($expense->coa as $coa){
            $item_coa = coaItem::where('chart_of_account_id',$coa->id)
              ->whereBetween('created_at',['1990-02-02',$all['to']])
              ->get();
            $rec = recurringItem::where('chart_of_account_id',$coa->id)
              ->whereBetween('date',['1990-02-02',$all['to']])
              ->get();
          
          $expense_total += $item_coa->sum('debit')-$item_coa->sum('credit');              
          $expense_total += $rec->sum('debit')-$rec->sum('credit');
        }
      }
      $revenues = $type->where('category','Revenue');
      $revenue_total = 0;
      foreach ($revenues as  $revenue) {
        foreach($revenue->coa as $coa){
          $item_coa = coaItem::where('chart_of_account_id',$coa->id)
              ->whereBetween('created_at',['1990-02-02',$all['to']])
              ->get();
          $revenue_total += $item_coa->sum('credit')-$item_coa->sum('debit');
          $rec1 = recurringItem::where('chart_of_account_id',$coa->id)
            ->whereBetween('date',['1990-02-02',Carbon::now()])
            ->get();
          $revenue_total += $rec1->sum('credit')-$rec->sum('debit');
        }
      }
      $retain_earnings = $revenue_total-$expense_total;
      $title = 'Balance Sheet as of '. $dt;
      return view('report.balance_sheet_post',compact('all','title','dt','charts','coas','type','type_liab_equity','retain_earnings'));
    }


    public function income_statement(){
        $Ecredit_total = 0;
        $Edebit_total = 0;
        $Rcredit_total = 0;
        $Rdebit_total = 0;

        $expense_array = [];
        $expense_count = 0;

        $dt = Carbon::now()->toFormattedDateString();
        $title = 'Income Statement as of '. $dt;

        $type = accountType::all();
        $types = [];
        $types[] = $type->where('category','Revenue');
        $types[] = $type->where('category','Expenses');

        $income_expense = [];

        foreach ($types as $key => $value) {
          foreach ($value as  $value1) {
            $income_expense[] = $value1;
          }
        }


        // $charts = disbursement_items::latest()->groupBy('chart_of_account_id')->get();
        // $coas = chartofaccount::latest()->orderBy('category','Asc')->get();
        // $expenses = chartofaccount::latest()->groupBy('type')->get();
        // foreach (chartofaccount::where('category','Revenue')->get() as $rev) {
        //      $Rcredit_total += $rev->item->sum('credit')+$rev->receivable_item->sum('credit');
        //      $Rdebit_total += $rev->item->sum('debit')+$rev->receivable_item->sum('debit');
        // }
        // foreach (chartofaccount::where('category','Expenses')->get() as $exp) {
        //      $expense_array[$expense_count] = $exp;
        //      $Ecredit_total += $exp->item->sum('credit');
        //      $Edebit_total += $exp->item->sum('debit');
        //      $expense_count++;
        // }

        // $revenue = $Rcredit_total - $Rdebit_total;
        // $expense = $Edebit_total - $Ecredit_total;
        // $expense_array = collect($expense_array)->sortBy('category')->values()->all();
        
        return view('report.income_statement',compact('title','dt','charts','type','coas','revenue','expense','expenses','expense_array'));
    }
    public function income_statement_post(){
       $all = Request::all();
        $dt = Carbon::now()->toFormattedDateString();
        $from = $all['from'];
        $to = $all['to'];
        $title = 'Income Statement From '. Carbon::parse($from)->toFormattedDateString().' - '.Carbon::parse($to)->toFormattedDateString();

        $type = accountType::all();
        $types = [];
        $types[] = $type->where('category','Revenue');
        $types[] = $type->where('category','Expenses');
     
        $income_expense = [];
       
        foreach ($types as $key => $value) {
          foreach ($value as  $value1) {
            $income_expense[] = $value1;
          }
        }
        return view('report.income_statement_post',compact('title','dt','from','to','type','revenue','income_expense'))->withInput($all);
    }
}
