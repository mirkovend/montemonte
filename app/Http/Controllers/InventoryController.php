<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;

use App\Http\Requests;
use App\Item;
use App\Inventory;
use App\Inventory_item;
use App\itemFlow;
use Carbon\Carbon;
use Request;
class InventoryController extends Controller
{
    public function index(){
        $inventories = Inventory::with('inv_items','item_account','inv_items.item_account')->latest()->get();
    
    	return view('inventory.index',compact('inventories'));
    }
    public function create(){
    	$item = Item::lists('item_name','id');
    	return view('inventory.create',compact('item'));
    }
    public function store(){
        $amount_cost = 0;
    	$itemid = Request::get('items_id');
        $quantity = Request::get('quantity');
        $itemprice = Request::get('itemprice');
        $all = Request::except('quantity','items_id','itemprice');
        $all['item_id'] = Request::get('adjustment_item');
      
        $inv = Inventory::create($all);
        
        foreach ($itemid as $key => $value) {
          
           

            if($all['lm_quantity'] == 0){
                itemFlow::insert(['type'=>'bill_adjus','item_id'=>$all['item_id'],'ref_no'=>$all['ref_no'],'debit'=>$quantity[$key],'ave_cost'=>$itemprice[$key],'created_at'=>carbon::now(),'updated_at'=>carbon::now()]);
                $inv->inv_items()->create(['item_id'=>$value,'credit_quantity'=>$quantity[$key],'ave_cost'=>$itemprice[$key]]);
                itemFlow::insert(['type'=>'adjustment','item_id'=>$value,'ref_no'=>$all['ref_no'],'credit'=>$quantity[$key],'ave_cost'=>$itemprice[$key],'created_at'=>carbon::now(),'updated_at'=>carbon::now()]);
                $amount_cost += $itemprice[$key] * $quantity[$key];

            }else{
                $amount_cost += $itemprice[$key] * $quantity[$key];
                 $inv->inv_items()->create(['item_id'=>$value,'credit_quantity'=>$quantity[$key],'ave_cost'=>$itemprice[$key]]);
                itemFlow::insert(['type'=>'adjustment','item_id'=>$value,'ref_no'=>$all['ref_no'],'credit'=>$quantity[$key],'ave_cost'=>$itemprice[$key],'created_at'=>carbon::now(),'updated_at'=>carbon::now()]);
            }


        }

        if($all['lm_quantity'] != 0){
            // $inv->inv_items()->create(['item_id'=>Request::get('adjustment_item'),'debit_quantity'=>$all['lm_quantity'],'ave_cost'=>($amount_cost/$all['lm_quantity'])]);

            itemFlow::insert(['type'=>'bill_adjus','item_id'=>Request::get('adjustment_item'),'ref_no'=>$all['ref_no'],'debit'=>$all['lm_quantity'],'ave_cost'=>($amount_cost/$all['lm_quantity']),'created_at'=>carbon::now(),'updated_at'=>carbon::now()]);
        }
    	$inv->update(['amount_cost'=>$amount_cost]);
        return redirect('inventory');
    }

    public function edit($id){
        $inv = Inventory::find($id);
        $item = Item::lists('item_name','id');
        return view('inventory.edit',compact('item','inv'));
    }

    public function update($id){
        $amount_cost = 0;
        $itemid = Request::get('items_id');
        $quantity = Request::get('quantity');
        $itemprice = Request::get('itemprice');
        $all = Request::except('quantity','items_id','itemprice');
        $all['item_id'] = Request::get('adjustment_item');

        $all['dt'] = Carbon::parse($all['dt'])->toDateString(); 
       
        $inv = Inventory::find($id);

        $inv->update($all);
        
        $inv->inv_items()->delete();
        itemFlow::where('ref_no',$inv->ref_no)->delete();
        foreach ($itemid as $key => $value) {
          
           

            if($all['lm_quantity'] == 0){
                itemFlow::insert(['type'=>'bill_adjus','item_id'=>$all['item_id'],'ref_no'=>$all['ref_no'],'debit'=>$quantity[$key],'ave_cost'=>$itemprice[$key],'created_at'=>carbon::now(),'updated_at'=>carbon::now()]);
                $inv->inv_items()->create(['item_id'=>$value,'credit_quantity'=>$quantity[$key],'ave_cost'=>$itemprice[$key]]);
                itemFlow::insert(['type'=>'adjustment','item_id'=>$value,'ref_no'=>$all['ref_no'],'credit'=>$quantity[$key],'ave_cost'=>$itemprice[$key],'created_at'=>carbon::now(),'updated_at'=>carbon::now()]);
                $amount_cost += $itemprice[$key] * $quantity[$key];

            }else{
                $amount_cost += $itemprice[$key] * $quantity[$key];
                 $inv->inv_items()->create(['item_id'=>$value,'credit_quantity'=>$quantity[$key],'ave_cost'=>$itemprice[$key]]);
                itemFlow::insert(['type'=>'adjustment','item_id'=>$value,'ref_no'=>$all['ref_no'],'credit'=>$quantity[$key],'ave_cost'=>$itemprice[$key],'created_at'=>carbon::now(),'updated_at'=>carbon::now()]);
            }


        }

        if($all['lm_quantity'] != 0){
            // $inv->inv_items()->create(['item_id'=>Request::get('adjustment_item'),'debit_quantity'=>$all['lm_quantity'],'ave_cost'=>($amount_cost/$all['lm_quantity'])]);

            itemFlow::insert(['type'=>'bill_adjus','item_id'=>Request::get('adjustment_item'),'ref_no'=>$all['ref_no'],'debit'=>$all['lm_quantity'],'ave_cost'=>($amount_cost/$all['lm_quantity']),'created_at'=>carbon::now(),'updated_at'=>carbon::now()]);
        }
        $inv->update(['amount_cost'=>$amount_cost]);
        return redirect('inventory');

    }

    public function items(){
		$item = Item::with('item_flow','po_item','item_flow_one')->find(request::get('id'));
        $price = 0;
        
        if($item->unit_measure == "bags"){
           $price = $item->item_flow_one->ave_cost;
        }else{
           $price = $item->po_item->item_price;
        }
        
		$total = $item->quantity + ($item->item_flow()->sum('debit') - $item->item_flow()->sum('credit'));
		$data = [
        'total'=>number_format($total,2),
        'um'=>$item->unit_measure,
        'itemprice'=>$price,
        ];
		return $data;
    }
}
