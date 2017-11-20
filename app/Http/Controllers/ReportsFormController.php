<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;

use App\Http\Requests;
use Request;
use App\Item;
use App\Cash_invoice_item;
use App\Charge_invoice;
use Carbon\Carbon;
use App\DailySales;
use App\Cash_sale;
use App\Payment;

class ReportsFormController extends Controller
{

    public function index(){

        $sr = DailySales::latest()->get();
        return view('reports_form.index',compact('sr'));
    }
    public function daily_sales(){

    	return view('reports_form.dailysales');
    }

    public function type(){
    	$type = Request::get('query');
    	$cash_sales = collect();
    	$charge_invoice = collect();
        $egg_id = [];
    	if($type == 1){
    		$items = Item::whereHas('cash_invoice_item',function($q){
                $q->whereDate('dt','=',Carbon::now()->toDateString());
            })
            ->orwhereHas('invoice_item',function($q){
                $q->whereDate('dt','=',Carbon::now()->toDateString());
            })
            ->with([

                    'cash_invoice_item'=>function($q){
                        $q->whereDate('dt','=',Carbon::now()->toDateString());
                    },

                    'invoice_item'=>function($q){
                        $q->whereDate('dt','=',Carbon::now()->toDateString());
                    }
                ])
            ->where('category_id',42)
            ->get();

    		$cash = Cash_invoice_item::where('dt',Carbon::now()->toDateString())->get();

            
            $total_cash = 0;
            $total_qty = 0;
            $ave_price = 0;
            $total_cash_turnover =0;
    		foreach ($items as $key => $item) {

                $total_cash += $item->cash_invoice_item->sum('amount') + $item->invoice_item->sum('amount');
                $total_qty += $item->cash_invoice_item->sum('cash_invoice_qty') + $item->invoice_item->sum('charge_invoice_qty');

    			$trans = new \stdClass;
		        $trans->product = ucfirst($item->item_name);
		        $trans->qty = number_format($item->cash_invoice_item->sum('cash_invoice_qty') + $item->invoice_item->sum('charge_invoice_qty'),2);
		        $trans->amount = number_format($item->cash_invoice_item->sum('amount') + $item->invoice_item->sum('amount'),2);
		        $cash_sales[] = $trans;
                array_push($egg_id, $item->id);
    		}
            $trans = new \stdClass;
            $trans->product = "TOTAL";
            $trans->qty = number_format($total_qty,2);
            $trans->amount = number_format($total_cash,2);
            $cash_sales[] = $trans;

            $charge_invoices = Charge_invoice::with([
                    'customer_belong',
                    'invoice'=>function($q){
                       $q->whereDate('dt','=',Carbon::now()->toDateString());
                    }
                ])
                ->whereHas('invoice',function($query) use($egg_id,$type){
                    if($type == 1){
                        $query->whereIn('size_id',$egg_id);
                    }else{
                        $query->whereNotIn('size_id',$egg_id);
                    }
                })
                ->where('dt',Carbon::now()->toDateString())
                ->get();
                  
            $total_collection = 0;
            foreach ($charge_invoices as $key => $invoice) {
                $total_collection += $invoice->invoice->sum('amount');
                $trans = new \stdClass;
                $trans->invoice_number = $invoice->charge_invoice_number;
                $trans->customer = $invoice->customer_belong->customer_name;
                $trans->amount = number_format($invoice->invoice->sum('amount'),2);
                $charge_invoice[] = $trans;
            }
            $trans = new \stdClass;
            $trans->invoice_number = "TOTAL";
            $trans->customer = "";
            $trans->amount = number_format($total_collection,2);
            $charge_invoice[] = $trans;
            
            $ave_price = number_format($total_cash / $total_qty,2);
            $turnover = number_format($total_cash - $total_collection,2);
    	}
        return response()->json(['cash'=>$cash_sales,'collection'=>$charge_invoice,'aveprice'=>$ave_price,'turnover'=>$turnover]);
    }

    public function store(){
        $aveprice = str_replace(',','',Request::get('aveprice'));
        $turnover = str_replace(',','',Request::get('turnover'));
        DailySales::create([
            'sr_no'=>Request::get('sr_no'),
            'dt'=>Carbon::now()->toDateString(),
            'aveprice'=>Request::get('aveprice'),
            'turnover'=>$turnover
            ]);

    }

    public function cash_sales_index(){
        $cash_sales = Cash_sale::latest()->get();
        return view('reports_form.cash_sales_index',compact('cash_sales'));
    }

    public function cash_sales(){
        $sr = DailySales::whereDate('dt','=',Carbon::now()->toDateString())->pluck('sr_no','id');
        return view('reports_form.cash_sales',compact('sr'));
    }
    public function sr_data(){
        $id = Request::get('sr_id');
        $sr = DailySales::find($id);
        
        return response()->json(['result'=>$sr]);
        
    }

    public function cash_sales_store(){
        // debug(Request::all());
        $all = Request::all();
        $all['dt'] = Carbon::now()->toDateString();

        Cash_sale::create($all);
    }


    public function collection_report(){

        return view('reports.cash_collection.cash_collection');

    }

    public function collection_data(){
        $collection = collect();
        $cash_sales = Cash_sale::with('sr')->latest()->get();
        $payments = Payment::latest()->get();

        
        foreach ($cash_sales as $cash_sale) {
            $trans = new \stdClass;
            $trans->dt = $cash_sale->dt;
            $trans->or = $cash_sale->or_number;
            $trans->ref = $cash_sale->sr->sr_no;
            $trans->cash = number_format($cash_sale->sr->turnover,2);
            $trans->collection = "";
            $trans->toll_hatched = "";
            $trans->chiken_dung = "";
            $trans->cull_layer = "";
            $trans->ready_to_lay = "";
            $trans->cartoon = "";
            $trans->others = "";            
            $collection[] = $trans;
        }
        foreach ($payments as $payment) {
            $trans = new \stdClass;
            $trans->dt = $payment->dt;
            $trans->or = $payment->or_number;
            $trans->ref = $payment->reference_number;
            
            if($payment->deposit_to == 467){
                $trans->collection = number_format($payment->payment,2);
            }else{
                $trans->collection = "";
            }

            if($payment->deposit_to == 31){
                $trans->toll_hatched = number_format($payment->payment,2);
            }else{
                $trans->toll_hatched = "";
            }

            if($payment->deposit_to == 29){
                $trans->chiken_dung = number_format($payment->payment,2);
            }else{
                $trans->chiken_dung = "";

            }

            if($payment->deposit_to == 33){
                $trans->cull_layer = number_format($payment->payment,2);
            }else{
                $trans->cull_layer = "";
            }

            if($payment->deposit_to == 40){
                $trans->ready_to_lay = number_format($payment->payment,2);
            }else{
                $trans->ready_to_lay = "";
            }

            if($payment->deposit_to == 28){
                $trans->cartoon = number_format($payment->payment,2);
            }else{
                $trans->cartoon = "";
            }

            if($payment->deposit_to == 37){
                $trans->others = number_format($payment->payment,2);
            }else{
                $trans->others = "";
            }

            $collection[] = $trans;




        }
        return response()->json(['results'=>$collection]);
    }
}
