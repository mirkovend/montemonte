<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Request;
use App\accountType;
use App\chartofaccount;
use App\recurring;
use App\Http\Requests;
use Carbon\Carbon;
use App\recurringItem;
class RecurringController extends Controller
{
    public function create(){


    	$title = 'Create Recurring';
    	$accountType = accountType::where('name','Fixed Asset')->first();
    	$chart_account = chartofaccount::all();
        $coa = [];
        $coa_expense_fixed = [];
        foreach ($chart_account->where('type',"$accountType->id") as $value) {
            $coa[$value->id] = $value->account_title;
            $coa_expense_fixed[$value->id] =  $value->account_title;
            foreach ($value->subaccount as $value1) {
                $coa[$value1->id]= '&rarr;'.$value1->account_title;
                 $coa_expense_fixed[$value1->id] = '&rarr;'.$value1->account_title;;
            }
        }

        foreach ($chart_account->where('category','Expenses') as $value2) {
            $coa_expense_fixed[$value2->id] = $value2->account_title;
            foreach ($value2->subaccount as $value3) {
                $coa_expense_fixed[$value3->id]= '&rarr;'.$value3->account_title;
            }
        }

        $charts = $coa;
    	return view('recurring.create',compact('title','charts','coa_expense_fixed'));
    }

    public function store(){
    	$all = Request::all();
    	$data = [];
       	$amount = str_replace("₱ ","",$all['amount']);
				$all['amount']= str_replace(",","",$amount);
       	$info = [
	       	'chart_of_account_id'=>$all['coa_id'],
	       	'amount'=>$all['amount'],
	       	'date'=>$all['sdate'],
	       	'type'=>$all['type'],
	       	'cycle_number'=>$all['cycle_number'],
       	];
       	$recu = recurring::create($info);
    	if($all['type'] == "monthly"){
    		foreach ($all['chart_of_account_id'] as $key => $value) {
				$credit = str_replace("₱ ","",$all['credit'][$key]);
				$all['credit'][$key] = str_replace(",","",$credit);
				$debit = str_replace("₱ ","",$all['debit'][$key]);
				$all['debit'][$key] = str_replace(",","",$debit);
				

	       		
	       		for ($i=0; $i <= $all['cycle_number']; $i++) {
	       			$date = Carbon::parse($all['sdate'])->addMonths($i);
	       			$data1 = [
	       				'recurring_id' => $recu->id,
						'chart_of_account_id' => $value,
						'debit' => $all['debit'][$key],
						'credit' => $all['credit'][$key],
						'date' => $date,
					];
					recurringItem::create($data1);
	    		}
       		}
    	}else{
    		foreach ($all['chart_of_account_id'] as $key => $value) {
				$credit = str_replace("₱ ","",$all['credit'][$key]);
				$all['credit'][$key] = str_replace(",","",$credit);
				$debit = str_replace("₱ ","",$all['debit'][$key]);
				$all['debit'][$key] = str_replace(",","",$debit);

				for ($i=0; $i <= $all['cycle_number']; $i++) {
					$date = Carbon::parse($all['sdate'])->addMonths($i);
	       			$data1 = [
						'chart_of_account_id' => $value,
						'debit' => $all['debit'][$key],
						'credit' => $all['credit'][$key],
						'date' => $date,
					];
					recurringItem::create($data1);
	    		}

       		}
    	}
    		
    	return redirect()->back();
    }
}
