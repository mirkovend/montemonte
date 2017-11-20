<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;

use App\Http\Requests;
use App\Delivery_receipt;
use App\Item;
use App\Dr_item;
use Request;
class Reports2Controller extends Controller
{
    public function egg_production(){
    	return view('reports.egg_production.egg_production');
    }

    public function egg_production_header(){
    	$item_array = collect();
    	$items = Item::select('item_name','id')->where('category_id',42)->get();
    	$deliveries = Delivery_receipt::with('invoice')->orderBy('dt','ASC')->get();
		
		$head = new \stdClass;
        $head->item_name = "Date";
        $head->id = "";
        $itemArr[] = $head;

        $head = new \stdClass;
        $head->item_name = "Reference";
        $head->id = "";
        $itemArr[] = $head;
    	foreach ($items as  $item) {
    		$head = new \stdClass;
            $head->item_name = $item->item_name;
            $head->id = $item->id;
            $itemArr[] = $head;
    	}
    	$head = new \stdClass;
        $head->item_name = "Total";
        $head->id = "";
        $itemArr[] = $head;

    	return response()->json($itemArr);
    }
    public function egg_production_data(){
    	$item_array = collect();
    	$headers = Request::get('data');
    	$query = Request::get('query');
    
    	$items = Item::select('item_name','id')->where('category_id',42)->get();
    	$deliveries = Delivery_receipt::where(function($q){
    		if(Request::get('query')['triger'] == 1){
    			
    			$date = explode(' - ', Request::get('query')['date']);
	            $from_date =$date[0];
	            $to_date = $date[1];
    			$q->whereBetween('dt',[$from_date,$to_date]);
    		}
    		if(Request::has('query.search')){
    			$q->orWhere('delivery_receipt_number','like','%'.Request::get('query')['search'].'%');
    		}
    		
    		
    	})->orderBy('dt','ASC')->get();


		$dr_items =Dr_item::where(function($q){
			if(Request::get('query')['triger'] == 1){
    			
    			$date = explode(' - ', Request::get('query')['date']);
	            $from_date =$date[0];
	            $to_date = $date[1];
    			$q->whereBetween('dt',[$from_date,$to_date]);
    		}
			if(Request::has('query.search')){
				$q->orWhere('delivery_receipt_number','like','%'.Request::get('query')['search'].'%');
			}
		})->latest()->get();
		$total_all = 0;

        foreach ($deliveries as $dr) {
        	$total = 0;
        	$col_array = [];
        	$head = new \stdClass;
	        $head = $dr;

        	foreach ($headers as $key => $item_headers) {

        		$dr->load(['invoice'=>function($q) use($item_headers){
        			$q->where('size_id',$item_headers['id']);
        		}]);

        		if(!$dr->invoice->isEmpty()){
        			$total += $dr->invoice->first()->delivery_receipt_qty;
        		}

        		if($item_headers['item_name'] == "Date"){
			       	array_push($col_array,$dr->dt);
        		}elseif($item_headers['item_name'] == "Reference"){
			       	array_push($col_array,"DR# ".$dr->delivery_receipt_number);
        		}elseif($item_headers['item_name'] == "Total"){
        			array_push($col_array,number_format($total));
        		}else{
        			
        			if(!$dr->invoice->isEmpty()){
        				
        				array_push($col_array,number_format($dr->invoice->first()->delivery_receipt_qty));
        			}else{
        				array_push($col_array,0);
        			}
        		}
        		
        	}
        	$total_all += $total;
        	$head->data = $col_array;
        	$item_array[] = $head;
        
        }

        $total_arr = [];
        $count = 0;
        if(count($deliveries) > 0){
	        foreach ($headers as $key => $item_headers1) {

	    		if($item_headers1['item_name'] == "Date"){

	    			$total_arr[$count] = "TOTAL";

	    		}elseif($item_headers1['item_name'] == "Reference"){

	    			$total_arr[$count] = "";

	    		}elseif($item_headers1['item_name'] == "Total"){

	    			$total_arr[$count] = number_format($total_all);

	    		}else{
	    				$sum = $dr_items->where('size_id',(int) $item_headers1['id'])->sum('delivery_receipt_qty');
	    			
	    				if(count($sum) > 0){
	    					$total_arr[$count] = number_format($sum);
	    				
	    				}else{
	    					$total_arr[$count] = 0;
	    				}
	    		}
	    		$count++;
	    	}
    	}

      	
    	return response()->json(['results'=>$item_array,'total'=>$total_arr]);
    }
}
