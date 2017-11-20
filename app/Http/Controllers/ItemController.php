<?php

namespace App\Http\Controllers;

use App\Item;
use App\Item_type;
use App\Chart_of_account;
use App\Coa_subitem;
use Datatables;
use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ItemCategory;
use App\ItemFlow;
class ItemController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }
    private function sub($data){

       return $data->pluck('item_name','id');
    }
   

    private function category_list($data = [],$parent = 0) 
    { 
        // build our category list only once 
        static $cats; 
        static $i = 0;
        static $a = 0;
        $tab = str_repeat("&#160;&#160;&#160;",$i);

      
        
        $pusher = "--";
        $showPusher = str_repeat($pusher,$a);
        $cats = array(); 
        $boldopen = "";

        $boldclose = "";
     
        
        $html = "";
        $list_items = collect();
        if($data[$parent])
        {
            $i++;
            foreach ($data[$parent] as $key => $value) {
                # code...
              
                $html .= "<option value='$value->id' >".$tab.$value->item_name.$boldclose."</option>";
                if($value->sub){ 
                    if(count($value->sub)>0){
                        
                         $child = $this->category_list($data, $value->id);


                        if($child)
                        {
                          $i--;

                          $html .= $child;
                        }
                    }
                }
               
            }
        return $html;
        }else
        {
        return false;
        }


 

       return $list_items; 
    
    }
    public function index(){

        function lists($datas,$parent = 0){
            static $i = 0;
            static $a = 0;
            $tab = str_repeat("---",$i);

             $html = "";
            if($datas[$parent])
            {
                $i++;
                foreach ($datas[$parent] as $key => $data) {
                # code...
                if(count($data->category)>0){
                   $catname = $data->category->name; 
                }else{
                    $catname = "";
                }
                if(count($data->coa_title)>0){
                   $coa_title =  $data->coa_title->coa_title; 
                }else{
                    $coa_title = "";
                }
                $html .="<tr>
                        <td style='padding-left: ".($i*2)."em;'>".$data->item_name."</td>
                        <td >".$catname."</td>
                        <td>".$data->item_type->item_type_name."</td>
                        <td>".$coa_title."</td>";
                        $balance = $data->balance;
                        foreach ($data->item_flow as $key => $item_flow) {
                            $trans = new \stdClass; 
                            if($item_flow->type == "po"){
                                $balance+= ($item_flow->vc_item->item_rcv * $item_flow->vc_item->po_item->item_price);
                        
                            }elseif($item_flow->type == "ci"){
                                    $balance += $item_flow->ci_item->unit_price * $item_flow->credit;      
                            }elseif($item_flow->type == "invoice"){
                                    $balance += $item_flow->invoice_item->unit_price * $item_flow->credit;
                                

                            }elseif($item_flow->type == "dr"){
                                    $balance +=  $item_flow->dr_item->unit_price * $item_flow->debit;

                            }elseif($item_flow->type == "adjustment"){
                                    $balance += ($item_flow->inventory_item->ave_cost * $item_flow->credit) - ($item_flow->inventory_item->ave_cost * $item_flow->credit)*2;
                               
                            }elseif($item_flow->type == "bill_adjus"){
                                    $balance +=  $item_flow->inventory->amount_cost;
                                
                            }
                        }
                        $html .="<td>P ".number_format($balance,2)."</td>
                        <td width = 80 class='text-center'>
                            <div class='btn-group'>
                                <button type='button' class='btn btn-lg btn-primary dropdown-toggle' data-toggle='dropdown'>
                                    <span class='icon-gear'></span> <span class='caret'></span>
                                </button>
                                <ul class='dropdown-menu dropdown-menu-arrow' role='menu' style='text-align:left;text-transform:uppercase;'>
                                    <li><a href=".action('ItemController@show',$data->id)."><span class='icon-edit'></span> View History</a></li>
                                    <li><a href=".action('ItemController@edit',$data->id)."><span class='icon-edit'></span> Edit Entry</a></li>
                                    <li><a href=".action('ItemController@delete_item',$data->id)."><span class='icon-remove'></span> Delete Entry</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>";
                    if($data->sub){ 
                        if(count($data->sub)>0){
                            
                             $child = lists($datas, $data->id);


                            if($child)
                            {
                              $i--;

                              $html .= $child;
                            }
                        }
                    }
                   
                }
                return $html;
            }
        }
        $itemlist = Item::with('category','item_type','coa_title','sub','item_flow','item_flow.ci_item','item_flow.invoice_item','item_flow.vc_item','item_flow.vc_item.po_item','item_flow.invoice_item','item_flow.dr_item','item_flow.inventory_item','item_flow.inventory')->orderBy('item_name','ASC')->get();

        foreach($itemlist as $item){
          
            $data[$item->is_sub][] = $item;

        }
        $items = lists($data);
        $cat = ItemCategory::lists('name','id');
        $catlist = ItemCategory::with('category')->get();
        return view('items.index',compact('items','cat','catlist'));
    }

    public function create(){
        $item_arr = [];

        $itemtypes = Item_type::pluck('item_type_name','id');
        $cats = ItemCategory::pluck('name','id');
        $mother_item = Item::with('sub')->orderBy('item_name','ASC')->get();
        $coas = Chart_of_account::with('sub')->get();
 
        function coa_list($data = [],$parent = 0){
                    // build our category list only once 
            static $cats; 
            static $i = 0;
            static $a = 0;
            $tab = str_repeat("&#160;&#160;&#160;",$i);



            $pusher = "--";
            $showPusher = str_repeat($pusher,$a);
            $cats = array(); 
            $boldopen = "";

            $boldclose = "";


            $html = "";
            $list_items = collect();
            if($data[$parent])
            {
                $i++;
                foreach ($data[$parent] as $key => $value) {
                # code...
                     if(count($value->sub)>0){
                        $html .='<optgroup label="'.$tab.$value->coa_title.'">';
                     }else{
                        $html .= "<option value='$value->id' >".$tab.$value->coa_title."</option>";
                     }
                   
                    if($value->sub){ 
                        if(count($value->sub)>0){

                            $child = coa_list($data, $value->id);


                            if($child)
                            {
                                $html .= $child;
                            }
                            $html .='</optgroup>';
                        }
                    }

                }
                 $i--;
                return $html;
            }else{
                return false;
            }
            return $list_items; 
        }

        foreach($mother_item as $item){
            $data[$item->is_sub][] = $item;
        }
        foreach($coas as $coa){
            $data2[$coa->is_sub][] = $coa;
        }
       
        $coa_lists = coa_list($data2);
        $itemlist = $this->category_list($data);
        // dump($itemlist);
        return view('items.create',compact('itemtypes','coa_lists','item_arr','itemlist','cats'));
    }
    public function selectItem(){


        function coa_list($data = [],$parent = 0){
                    // build our category list only once 
            static $cats; 
            static $i = 0;
            static $a = 0;
            $tab = str_repeat('->',$i);



            $pusher = "--";
            $showPusher = str_repeat($pusher,$a);
            $cats = array(); 
            $boldopen = "";

            $boldclose = "";


            $html = "";
            $list_items = collect();
            if(count($data->where('is_sub',0)) > 0){
                $coas = $data->where('is_sub',0);
            }else{
                $coas =$data;
            }
            $coas->load('sub');
            $i++;

            foreach ( $coas as $key => $value) {
                $childs = [];
               
                // $html .= "<option value='$value->id' >".$tab.$value->coa_title."</option>";
                if(count($value->sub)>0){
                    $item = new \stdClass;
                    $item->text = $tab.$value->coa_title;
                    $item->children = ['id'=> $value->id,'text'=>$tab.$value->coa_title];
                    $list_items[] =$item; 
                }else{
                    $item = new \stdClass;
                    $item->id = $value->id;
                    $item->text = $tab.$value->coa_title;
                    $list_items[] =$item; 
                }
               
                if($value->sub){ 
                    if(count($value->sub)>0){

                        $child = coa_list($value->sub, $value->id);


                        if($child)
                        {
                            $i--;
                            $list_items = collect($list_items)->merge($child);
                          
                        }
                    }
                }

            }
            
            return $list_items;
          
        }
        
         $coas = Chart_of_account::with('sub')->where(function($q){
            if(Request::has('search')){
                $q->where('coa_title','like','%'.Request::get('search').'%');
            }
         })->get();
        // foreach($coas as $coa){
        //     $data2[$coa->is_sub][] = $coa;
        // }
        $coa_lists = coa_list($coas);

        return Response()->json([
            'placeholder'=>['id'=>'-1'],
            'results'=>$coa_lists]);
    }
    public function selectedItem($id){


        function coa_list($data = [],$parent = 0){
                    // build our category list only once 
            static $cats; 
            static $i = 0;
            static $a = 0;
            $tab = str_repeat('->',$i);



            $pusher = "--";
            $showPusher = str_repeat($pusher,$a);
            $cats = array(); 
            $boldopen = "";

            $boldclose = "";


            $html = "";
            $list_items = collect();
            if(count($data->where('is_sub',0)) > 0){
                $coas = $data->where('is_sub',0);
            }else{
                $coas =$data;
            }
            $coas->load('sub');
            $i++;

            foreach ( $coas as $key => $value) {
                $childs = [];
               
                // $html .= "<option value='$value->id' >".$tab.$value->coa_title."</option>";
                if(count($value->sub)>0){
                    $item = new \stdClass;
                    $item->text = $tab.$value->coa_title;
                    $item->children = ['id'=> $value->id,'text'=>$tab.$value->coa_title];
                    $list_items[] =$item; 
                }else{
                    $item = new \stdClass;
                    $item->id = $value->id;
                    $item->text = $tab.$value->coa_title;
                    $list_items[] =$item; 
                }
               
                if($value->sub){ 
                    if(count($value->sub)>0){

                        $child = coa_list($value->sub, $value->id);


                        if($child)
                        {
                            $i--;
                            $list_items = collect($list_items)->merge($child);
                          
                        }
                    }
                }

            }
            
            return $list_items;
          
        }
        
         $coas = Chart_of_account::with('sub')->where(function($q){
            if(Request::has('search')){
                $q->where('coa_title','like','%'.Request::get('search').'%');
            }
         })->where('id',$id)->get();
         // debug($coas);
        // foreach($coas as $coa){
        //     $data2[$coa->is_sub][] = $coa;
        // }
        $coa_lists = coa_list($coas);

        return Response()->json([
            'placeholder'=>['id'=>'-1'],
            'results'=>$coa_lists]);
    }
    public function store(){
        $items = Request::except('dt','quantity');
       
        $item = Item::create($items);
        ItemFlow::create([
                'item_id'=>$item->id,
                'type' =>"Inventory Starting Value",
                'ref_no'   => "Start",
                "debit" => Request::get('quantity'),
                "dt"    => Carbon::parse(Request::get('dt'))->toDateString(),
            ]);
        Session::flash('flash_message','Entry Saved');
        return redirect()->back();
    }



    public function loadSubaccount(){
        $id = Request::get('combobox1');
        $subitem = Coa_subitem::where('coa_sub_id',$id)->get();  
        return $subitem;      
    }
    public function show($id){
        $items = Item::with('item_flow','item_flow.ci_item','item_flow.invoice_item','item_flow.vc_item','item_flow.vc_item.po_item','item_flow.invoice_item','item_flow.dr_item','item_flow.inventory_item','item_flow.inventory')->where('id',$id)
            ->orderBy('item_name','ASC')
            ->first();
        $asset_val_total = 0; $onhand = 0;
        
        $transaction_data = [];
        if($items->balance != 0){
            $trans = new \stdClass;
            $trans->type = "OPENING BALANCE";
            $trans->name = "";
            $trans->date = "";
            $trans->ref = "";
            $trans->qty = "";
            $trans->cost = "";
            $trans->onhand = "";
            $trans->um ="";
            $trans->avgcost = "";
            $trans->assetval = number_format($asset_val_total += $items->balance,2);
            $transaction_data[] = $trans;
        }
        foreach ($items->item_flow as $key => $item_flow) {
            $trans = new \stdClass; 
            if($item_flow->type == "po"){
                $trans->type = "BILL";
                $trans->name = $item_flow->vc_item->po->supplier_one->supplier_name;
                $trans->date = \Carbon\Carbon::parse($item_flow->vc_item->dt)->toFormattedDateString();
                $trans->ref = $item_flow->ref_no;
                $trans->qty = number_format($item_flow->vc_item->item_rcv,2);
                $trans->cost = number_format($item_flow->vc_item->item_rcv * $item_flow->vc_item->po_item->item_price,2);
                $trans->onhand = number_format($onhand += $item_flow->vc_item->item_rcv,2);
                $trans->um = $items->unit_measure;
                $trans->avgcost = number_format($item_flow->vc_item->po_item->item_price,2);
                $trans->assetval = number_format($asset_val_total+= ($item_flow->vc_item->item_rcv * $item_flow->vc_item->po_item->item_price),2);
        
            }elseif($item_flow->type == "ci"){
                
                    $trans->type = "SALES RECEIPT";
                    $trans->name = $item_flow->ci->customer_belong->customer_name;
                    
                    $trans->date = \Carbon\Carbon::parse($item_flow->ci->dt)->toFormattedDateString();
                    $trans->ref = $item_flow->ref_no;
                    $trans->qty = number_format($item_flow->credit - ($item_flow->credit *2),2);
                    $trans->cost = number_format($item_flow->ci_item->unit_price * $item_flow->credit,2);
                    $trans->onhand = number_format($onhand += ($item_flow->credit - ($item_flow->credit *2)),2);
                    $trans->um = $items->unit_measure;
                    $trans->avgcost = number_format($item_flow->ci_item->unit_price,2);
                    $trans->assetval = number_format($asset_val_total += $item_flow->ci_item->unit_price * $item_flow->credit,2);      
            }elseif($item_flow->type == "invoice"){
                 
                    $trans->type = "INVOICE";
                    $trans->name = $item_flow->invoice->customer_belong->customer_name;
                    $trans->date = \Carbon\Carbon::parse($item_flow->invoice->dt)->toFormattedDateString();
                    $trans->ref = $item_flow->ref_no;
                    $trans->qty = number_format($item_flow->credit- ($item_flow->credit *2),2);
                    $trans->cost = number_format($item_flow->invoice_item->unit_price * $item_flow->credit,2);
                    $trans->onhand = number_format($onhand += ($item_flow->credit - ($item_flow->credit *2)),2);
                    $trans->um = "PCS";
                    $trans->avgcost = number_format($item_flow->invoice_item->unit_price,2);
                    $trans->assetval = number_format($asset_val_total += $item_flow->invoice_item->unit_price * $item_flow->credit,2);
                

            }elseif($item_flow->type == "dr"){
                 
                    $trans->type = "ITEM RECEIPT";
                    $trans->name = "";
                    $trans->date = \Carbon\Carbon::parse($item_flow->dr->dt)->toFormattedDateString();
                    $trans->ref = "DR#".$item_flow->ref_no;
                    $trans->qty = number_format($item_flow->debit,2);
                    $trans->cost = number_format($item_flow->dr_item->unit_price * $item_flow->debit,2);
                    $trans->onhand = number_format($onhand += $item_flow->debit,2);
                    $trans->um = "PCS";
                    $trans->avgcost = number_format($item_flow->dr_item->unit_price,2);
                    $trans->assetval = number_format($asset_val_total +=  $item_flow->dr_item->unit_price * $item_flow->debit,2);
                

            }elseif($item_flow->type == "adjustment"){
                 
                    $trans->type = "ADJUSTMENT";
                    $trans->name = "";
                    $trans->date = \Carbon\Carbon::parse($item_flow->inventory->dt)->toFormattedDateString();
                    $trans->ref = $item_flow->ref_no;
                    $trans->qty = number_format($item_flow->credit - ($item_flow->credit *2),2);
                    $trans->cost = number_format($item_flow->inventory_item->ave_cost * $item_flow->credit,2);
                    $trans->onhand = number_format($onhand += ($item_flow->credit - ($item_flow->credit *2)),2);
                    $trans->um = "PCS";
                    $trans->avgcost = number_format($item_flow->inventory_item->ave_cost,2);
                    $trans->assetval = number_format($asset_val_total += ($item_flow->inventory_item->ave_cost * $item_flow->credit) - ($item_flow->inventory_item->ave_cost * $item_flow->credit)*2,2);

                
            }elseif($item_flow->type == "bill_adjus"){
                 
                    $trans->type = "MIXED ADJUSTMENT";
                    $trans->name = "";
                    $trans->date = \Carbon\Carbon::parse($item_flow->inventory->dt)->toFormattedDateString();
                    $trans->ref = $item_flow->ref_no;
                    $trans->qty = number_format($item_flow->debit,2);
                    $trans->cost = number_format($item_flow->inventory->amount_cost,2);
                    $trans->onhand = number_format($onhand += $item_flow->debit,2);
                    $trans->um = "PCS";
                    $trans->avgcost = number_format($item_flow->inventory->amount_cost/$item_flow->debit,2);
                    $trans->assetval = number_format($asset_val_total +=  $item_flow->inventory->amount_cost ,2);
                
            }
            $transaction_data[] = $trans;
        }
        
        return view('items.show',compact('items','transaction_data','asset_val_total'));
    }
    public function search($id){
        $term = Request::get('date');

        $date = explode(" - ", $term);

        $items = Item::with([
                'item_flow',
                'item_flow.ci_item'=>function($q)use($date){
                    if(Request::has('date')){
                        $q->whereBetween('dt',[$date[0],$date[1]]);
                    }
                },
                'item_flow.invoice_item'=>function($q)use($date){
                    if(Request::has('date')){
                        $q->whereBetween('dt',[$date[0],$date[1]]);
                    }
                },
                'item_flow.vc_item'=>function($q)use($date){
                    if(Request::has('date')){
                        $q->whereBetween('dt',[$date[0],$date[1]]);
                    }
                },
                'item_flow.vc_item.po_item',
                'item_flow.dr_item'=>function($q)use($date){
                    if(Request::has('date')){
                        $q->whereBetween('dt',[$date[0],$date[1]]);
                    }
                },
                'item_flow.inventory_item',
                'item_flow.inventory'=>function($q)use($date){
                    if(Request::has('date')){
                        $q->whereBetween('dt',[$date[0],$date[1]]);
                    }
                }
            ])->where('id',$id)
            ->orderBy('item_name','ASC')
            ->first();
        $asset_val_total = 0; 
        $onhand = 0;

        $transaction_data = [];
        if($items->balance != 0){
            $trans = new \stdClass;
            $trans->type = "OPENING BALANCE";
            $trans->name = "";
            $trans->date = "";
            $trans->ref = "";
            $trans->qty = "";
            $trans->cost = "";
            $trans->onhand = "";
            $trans->um ="";
            $trans->avgcost = "";
            $trans->assetval = number_format($asset_val_total += $items->balance,2);
            $transaction_data[] = $trans;
        }
        foreach ($items->item_flow as $key => $item_flow) {
           
            if($item_flow->type == "po"){
                if($item_flow->vc_item){
                    $trans = new \stdClass; 
                    $trans->type = "BILL";
                    $trans->name = $item_flow->vc_item->po->supplier_one->supplier_name;
                    $trans->date = \Carbon\Carbon::parse($item_flow->vc_item->dt)->toFormattedDateString();
                    $trans->ref = $item_flow->ref_no;
                    $trans->qty = number_format($item_flow->vc_item->item_rcv,2);
                    $trans->cost = number_format($item_flow->vc_item->item_rcv * $item_flow->vc_item->po_item->item_price,2);
                    $trans->onhand = number_format($onhand += $item_flow->vc_item->item_rcv,2);
                    $trans->um = $items->unit_measure;
                    $trans->avgcost = number_format($item_flow->vc_item->po_item->item_price,2);
                    $trans->assetval = number_format($asset_val_total+= ($item_flow->vc_item->item_rcv * $item_flow->vc_item->po_item->item_price),2);
                    $transaction_data[] = $trans;
                }
            }elseif($item_flow->type == "ci"){
                if($item_flow->ci){
                    $trans = new \stdClass; 
                    $trans->type = "SALES RECEIPT";
                    $trans->name = $item_flow->ci->customer_belong->customer_name;
                    
                    $trans->date = \Carbon\Carbon::parse($item_flow->ci->dt)->toFormattedDateString();
                    $trans->ref = $item_flow->ref_no;
                    $trans->qty = number_format($item_flow->credit - ($item_flow->credit *2),2);
                    $trans->cost = number_format($item_flow->ci_item->unit_price * $item_flow->credit,2);
                    $trans->onhand = number_format($onhand += ($item_flow->credit - ($item_flow->credit *2)),2);
                    $trans->um = $items->unit_measure;
                    $trans->avgcost = number_format($item_flow->ci_item->unit_price,2);
                    $trans->assetval = number_format($asset_val_total += $item_flow->ci_item->unit_price * $item_flow->credit,2);      
                    $transaction_data[] = $trans;
                }
            }elseif($item_flow->type == "invoice"){
                if($item_flow->invoice){
                    $trans = new \stdClass; 
                    $trans->type = "INVOICE";
                    $trans->name = $item_flow->invoice->customer_belong->customer_name;
                    $trans->date = \Carbon\Carbon::parse($item_flow->invoice->dt)->toFormattedDateString();
                    $trans->ref = $item_flow->ref_no;
                    $trans->qty = number_format($item_flow->credit- ($item_flow->credit *2),2);
                    $trans->cost = number_format($item_flow->invoice_item->unit_price * $item_flow->credit,2);
                    $trans->onhand = number_format($onhand += ($item_flow->credit - ($item_flow->credit *2)),2);
                    $trans->um = "PCS";
                    $trans->avgcost = number_format($item_flow->invoice_item->unit_price,2);
                    $trans->assetval = number_format($asset_val_total += $item_flow->invoice_item->unit_price * $item_flow->credit,2);
                
                    $transaction_data[] = $trans;
                }
            }elseif($item_flow->type == "dr"){
                if($item_flow->dr){
                    $trans = new \stdClass; 
                    $trans->type = "ITEM RECEIPT";
                    $trans->name = "";
                    $trans->date = \Carbon\Carbon::parse($item_flow->dr->dt)->toFormattedDateString();
                    $trans->ref = "DR#".$item_flow->ref_no;
                    $trans->qty = number_format($item_flow->debit,2);
                    $trans->cost = number_format($item_flow->dr_item->unit_price * $item_flow->debit,2);
                    $trans->onhand = number_format($onhand += $item_flow->debit,2);
                    $trans->um = "PCS";
                    $trans->avgcost = number_format($item_flow->dr_item->unit_price,2);
                    $trans->assetval = number_format($asset_val_total +=  $item_flow->dr_item->unit_price * $item_flow->debit,2);

                }

            }elseif($item_flow->type == "adjustment"){
                if($item_flow->inventory){
                    $trans = new \stdClass; 
                    $trans->type = "ADJUSTMENT";
                    $trans->name = "";
                    $trans->date = \Carbon\Carbon::parse($item_flow->inventory->dt)->toFormattedDateString();
                    $trans->ref = $item_flow->ref_no;
                    $trans->qty = number_format($item_flow->credit - ($item_flow->credit *2),2);
                    $trans->cost = number_format($item_flow->inventory_item->ave_cost * $item_flow->credit,2);
                    $trans->onhand = number_format($onhand += ($item_flow->credit - ($item_flow->credit *2)),2);
                    $trans->um = "PCS";
                    $trans->avgcost = number_format($item_flow->inventory_item->ave_cost,2);
                    $trans->assetval = number_format($asset_val_total += ($item_flow->inventory_item->ave_cost * $item_flow->credit) - ($item_flow->inventory_item->ave_cost * $item_flow->credit)*2,2);
                    $transaction_data[] = $trans;
                }
            }elseif($item_flow->type == "bill_adjus"){
                if($item_flow->inventory){
                    $trans = new \stdClass; 
                    $trans->type = "MIXED ADJUSTMENT";
                    $trans->name = "";
                    $trans->date = \Carbon\Carbon::parse($item_flow->inventory->dt)->toFormattedDateString();
                    $trans->ref = $item_flow->ref_no;
                    $trans->qty = number_format($item_flow->debit,2);
                    $trans->cost = number_format($item_flow->inventory->amount_cost,2);
                    $trans->onhand = number_format($onhand += $item_flow->debit,2);
                    $trans->um = "PCS";
                    $trans->avgcost = number_format($item_flow->inventory->amount_cost/$item_flow->debit,2);
                    $trans->assetval = number_format($asset_val_total +=  $item_flow->inventory->amount_cost ,2);
                    $transaction_data[] = $trans;
                }
                    
            }
       

        }
        return view('items.show',compact('items','transaction_data','asset_val_total'));
    }
    public function edit($id){
        $itemtypes = Item_type::pluck('item_type_name','id');
        $item_data = Item::find($id);
        $mother_item = Item::with('sub')->orderBy('item_name','ASC')->get();
        $coas = Chart_of_account::with('sub')->get();
        $itemtype = Item_type::find($id);
        $cats = ItemCategory::pluck('name','id');
        foreach($mother_item as $item){
            $data[$item->is_sub][] = $item;
        }
        $itemlist = $this->category_list($data);

        return view('items.edit',compact('cats','itemtypes','coa_lists','item_arr','itemlist','item_data'));
    }

    public function update($id){
        $itemtype = Request::except('_token','_method');

        Item::where('id',$id)->update($itemtype);
        Session::flash('flash_message','Item Type Entry Updated.');
        return redirect()->back();
    }

    public function delete_item($id){
        $itemtype = Item::find($id);
        Item::where('id',$id)->delete($itemtype);
        Session::flash('flash_message','Item Type Entry Deleted.');
        return redirect()->back();
    }
}
