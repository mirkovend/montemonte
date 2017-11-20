<?php

namespace App\Http\Controllers;
// use Illuminate\Http\Request;
use App\Egg_unit;
use App\Egg_size;
use App\Cash_invoice;
use App\Cash_invoice_item;
use App\Starting_account_number;
use App\Customer;
use App\Charge_invoice;
use App\Charge_invoice_item;
use App\supplier_one;
use App\supplier;
use App\Item_type;
use PDF;
use Validator;
use DB;
use Session;
use Request;
use App\Item;
use App\Inventory;
use App\Inventory_item;
use App\itemFlow;
use App\Payment;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Chart_of_account;
use Datatables;
use Illuminate\Support\Collection;
use App\DataTables\PurchasesDataTable;
use App\Detail_type;

class ReportsController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function view_sales_report(){
        return view('reports.sales_report');
    }

    public function print_sales_report(){
        $dateFrom = Request::get('dateFrom');
        $dateTo = Request::get('dateTo');

        $sizes = Egg_size::orderBy('size_label')->get();

        $ci_invoice = Charge_invoice::where('dt','>=',$dateFrom)
            ->where('dt','<=',$dateTo)
            ->where('status',0)
            ->get();

        $cancelled_cs = Cash_invoice::where('dt','>=',$dateFrom)
            ->where('dt','<=',$dateTo)
            ->where('status',1)
            ->get();

        $cancelled_ci = Charge_invoice::where('dt','>=',$dateFrom)
            ->where('dt','<=',$dateTo)
            ->where('status',1)
            ->get();

        $total_cs = Cash_invoice_item::where('dt','>=',$dateFrom)
            ->where('dt','<=',$dateTo)
            ->sum('amount');

        $pdf = PDF::loadView('pdf.sales_report', compact('dateFrom','dateTo','sizes','ci_invoice','cancelled_ci','cancelled_cs','total_cs'))->setPaper('letter');
        return $pdf->stream('SALES_REPORT-'.$dateFrom.'-'.$dateTo.'.pdf');
    }
           

    private function item($datas,$parent = 0,$subs = 0){
        static $i = 0;
        static $a = 0;
       
      
        static $total_asset = 0;
        static $total_asset1 = 0;
        static $total_onhand = 0;
        static $total_onhand1 = 0;
        $tab = str_repeat("---",$i);
       static $itemArr = array();
        $item_count = 0;
        $html = "";
        $totalasset= session('global_total_asset');
        $totalonhand = session('global_total_onhand');
            $i++;
            $datas->load(['sub'=>function($q){
                $q->has("item_flow")->orHas("sub.item_flow");
            },
            'item_flow',
            'item_flow.vc_item',
            
            
            'item_sub',

            'item_flow.ci_item',
            'item_flow.ci',
            'item_flow.ci.customer_belong',

            'item_flow.dr_item',
            'item_flow.dr',
            'item_flow.inventory_item',
            'item_flow.inventory',

            'item_flow.invoice',
            'item_flow.invoice_item',
            'item_flow.invoice.customer_belong']);

            $items = $datas->where('is_sub',$parent);
        if($subs == 0){
            $head = new \stdClass;
            $head->item_name = "Inventory";
            $head->type = "";
            $head->name = "";
            $head->space ="";
            $head->date = "";
            $head->ref = "";
            $head->qty = "";
            $head->cost = "";
            $head->onHand = "";
            $head->um = "";
            $head->avgcost = "";
            $head->assetval = "";
            $itemArr[] = $head;
        }
        if(count($items) > 0){
            
            foreach ($items as  $data) {

                $obj = new \stdClass;
                $obj->item_name = $data->item_name;
                $obj->type = "";
                $obj->name = "";
                $obj->space = ($i*2);
                $obj->date = "";
                $obj->ref = "";
                $obj->qty = "";
                $obj->cost = "";
                $obj->onHand = "";
                $obj->um = "";
                $obj->avgcost = "";
                $obj->assetval = "";
                $itemArr[] = $obj;

                $onhandparent = 0;
                $asset_val_total = 0;
                $sub_onhandparent = 0;
                $sub_asset_val_total = 0;
                foreach ($data->item_flow as $po) {
                    $po->load(['vc_item' => function ($query) use($data) {
                        $query->where('account_item_id', $data->id);
                    },'vc_item.po.supplier_one','vc_item.po_item']);
                    $trans = new \stdClass;
                    

                    if($po->type == "po"){
                        $trans->item_name = "";
                        $trans->type = "BILL";
                        $trans->name = $po->vc_item->po->supplier_one->supplier_name;
                        $trans->space = ($i*2);
                        $trans->date = Carbon::parse($po->vc_item->dt)->toFormattedDateString();
                        $trans->ref = $po->ref_no;
                        $trans->qty = number_format($po->vc_item->item_rcv,2);
                        $trans->cost = number_format($po->vc_item->item_rcv * $po->vc_item->po_item->item_price,2);
                        $trans->onHand = number_format($onhandparent += $po->vc_item->item_rcv,2);
                        $trans->um = "PCS";
                        $trans->avgcost = number_format($po->vc_item->po_item->item_price,2);
                        $trans->assetval = number_format($asset_val_total+= ($po->vc_item->item_rcv * $po->vc_item->po_item->item_price),2);
                    }elseif($po->type == "ci"){
                        $trans->item_name = "";
                        $trans->type = "SALES RECEIPT";
                        $trans->name = $po->ci->customer_belong->customer_name;
                        $trans->space = ($i*2);
                        $trans->date = Carbon::parse($po->ci->dt)->toFormattedDateString();
                        $trans->ref = $po->ref_no;
                        $trans->qty = number_format($po->credit - ($po->credit *2),2);
                        $trans->cost = number_format($po->ci_item->unit_price * $po->credit,2);
                        $trans->onHand = number_format($onhandparent += ($po->credit - ($po->credit *2)),2);
                        $trans->um = "PCS";
                        $trans->avgcost = number_format($po->ci_item->unit_price,2);
                        $trans->assetval = number_format($asset_val_total += $po->ci_item->unit_price * $po->credit,2); 
                    }elseif($po->type == "invoice"){
                        $trans->item_name = "";
                        $trans->type = "INVOICE";
                        $trans->name = $po->invoice->customer_belong->customer_name;
                        $trans->space = ($i*2);
                        $trans->date = Carbon::parse($po->invoice->dt)->toFormattedDateString();
                        $trans->ref = $po->ref_no;
                        $trans->qty = number_format($po->credit- ($po->credit *2),2);
                        $trans->cost = number_format($po->invoice_item->unit_price * $po->credit,2);
                        $trans->onHand = number_format($onhandparent += ($po->credit - ($po->credit *2)),2);
                        $trans->um = "PCS";
                        $trans->avgcost = number_format($po->invoice_item->unit_price,2);
                        $trans->assetval = number_format($asset_val_total += $po->invoice_item->unit_price * $po->credit,2);
                    }elseif($po->type == "dr"){
                        $trans->item_name = "";
                        $trans->type = "ITEM RECEIPT";
                        $trans->name = "";
                        $trans->space = ($i*2);
                        $trans->date = Carbon::parse($po->dr->dt)->toFormattedDateString();
                        $trans->ref = "DR#".$po->ref_no;
                        $trans->qty = number_format($po->debit,2);
                        $trans->cost = number_format($po->dr_item->unit_price * $po->debit,2);
                        $trans->onHand = number_format($onhandparent += $po->debit,2);
                        $trans->um = "PCS";
                        $trans->avgcost = number_format($po->dr_item->unit_price,2);
                        $trans->assetval = number_format($asset_val_total +=  $po->dr_item->unit_price * $po->debit,2);
                    }elseif($po->type == "adjustment"){
                        $trans->item_name = "";
                        $trans->type = "ADJUSTMENT";
                        $trans->name = "";
                        $trans->space = ($i*2);
                        $trans->date = Carbon::parse($po->inventory->dt)->toFormattedDateString();
                        $trans->ref = $po->ref_no;
                        $trans->qty = number_format($po->credit - ($po->credit *2),2);
                        $trans->cost = number_format($po->inventory_item->ave_cost * $po->credit,2);
                        $trans->onHand = number_format($onhandparent += ($po->credit - ($po->credit *2)),2);
                        $trans->um = "PCS";
                        $trans->avgcost = number_format($po->inventory_item->ave_cost,2);
                        $trans->assetval = number_format($asset_val_total += ($po->inventory_item->ave_cost * $po->credit) - ($po->inventory_item->ave_cost * $po->credit)*2,2);
                    }elseif($po->type == "bill_adjus"){
                        $trans->item_name = "";
                        $trans->type = "MIXED ADJUSTMENT";
                        $trans->name = "";
                        $trans->space = ($i*2);
                        $trans->date = Carbon::parse($po->inventory->dt)->toFormattedDateString();
                        $trans->ref = $po->ref_no;
                        $trans->qty = number_format($po->debit,2);
                        $trans->cost = number_format($po->inventory->amount_cost,2);
                        $trans->onHand = number_format($onhandparent += $po->debit,2);
                        $trans->um = "PCS";
                        $trans->avgcost = number_format($po->inventory->amount_cost/$po->debit,2);
                        $trans->assetval = number_format($asset_val_total +=  $po->inventory->amount_cost ,2);
                    }
                    if($data->is_sub = $parent){
                       
                    
                        $sub_onhandparent += $onhandparent;
                        $sub_asset_val_total += $asset_val_total;
                    }else{
                      
                        $sub_asset_val_total += $asset_val_total;
                        $sub_onhandparent += $onhandparent;
                    }
                    $itemArr[] = $trans;
                }
                $total_asset += $sub_asset_val_total;
                $total_asset1 = $sub_asset_val_total;
                $total_onhand += $sub_onhandparent;
                $total_onhand1 = $sub_onhandparent;
                $totalasset = $total_asset;
                $totalonhand =$total_onhand;
                session::put('global_total_asset', $totalasset);
                session::put('global_total_onhand', $totalonhand);
                if(count($data->sub)>0){
                  
                     $child = $this->item($data->sub, $data->id,2);


                    if($child)
                    {
                      $i--;
                    
                    }
                }  
                $obj1 = new \stdClass;
                if($data->is_sub == $parent){
                    
                    $obj1->item_name = $data->item_name." Total:";
                    $obj1->type = "";
                    $obj1->name = "";
                    $obj1->space = ($i*2);
                    $obj1->date = "";
                    $obj1->ref = "";
                    $obj1->qty = "";
                    $obj1->cost = "";
                    $obj1->onHand = number_format($total_onhand1,2);
                    $obj1->um = "";
                    $obj1->avgcost = "";
                    $obj1->assetval = "";
                }else{
                    $obj1->item_name = $data->item_name." Total:";
                    $obj1->type = "";
                    $obj1->name = "";
                    $obj1->space = ($i*2);
                    $obj1->date = "";
                    $obj1->ref = "";
                    $obj1->qty = "";
                    $obj1->cost = "";
                    $obj1->onHand = number_format($sub_onhandparent,2);
                    $obj1->um = "";
                    $obj1->avgcost = "";
                    $obj1->assetval = "";
                }
                
                $itemArr[] = $obj1;
                $item_count++;
            } //end foreach parent
            
            
        }else{
            foreach ($datas as  $data) {
            
                $onhandparent = 0;
                $asset_val_total = 0;
                $sub_onhandparent = 0;
                $sub_asset_val_total = 0;

                $obj = new \stdClass;
                $obj->item_name = $data->item_name;
                $obj->type = "";
                $obj->name = "";
                $obj->space = ($i*2);
                $obj->date = "";
                $obj->ref = "";
                $obj->qty = "";
                $obj->cost = "";
                $obj->onHand = "";
                $obj->um = "";
                $obj->avgcost = "";
                $obj->assetval = "";
                $itemArr[] = $obj;
                foreach ($data->item_flow as $po) {
                     $po->load(['vc_item' => function ($query) use($data) {
                        $query->where('account_item_id', $data->id);
                    },'vc_item.po.supplier_one','vc_item.po_item']);

                    $trans = new \stdClass;
                    if($po->type == "po"){
                        $trans->item_name = "";
                        $trans->type = "BILL";
                        $trans->name = $po->vc_item->po->supplier_one->supplier_name;
                        $trans->space = ($i*2);
                        $trans->date = Carbon::parse($po->vc_item->dt)->toFormattedDateString();
                        $trans->ref = $po->ref_no;
                        $trans->qty = number_format($po->vc_item->item_rcv,2);
                        $trans->cost = number_format($po->vc_item->item_rcv * $po->vc_item->po_item->item_price,2);
                        $trans->onHand = number_format($onhandparent += $po->vc_item->item_rcv,2);
                        $trans->um = "PCS";
                        $trans->avgcost = number_format($po->vc_item->po_item->item_price,2);
                        $trans->assetval = number_format($asset_val_total+= ($po->vc_item->item_rcv * $po->vc_item->po_item->item_price),2);
                    }elseif($po->type == "ci"){
                        $trans->item_name = "";
                        $trans->type = "SALES RECEIPT";
                        $trans->name = $po->ci->customer_belong->customer_name;
                        $trans->space = ($i*2);
                        $trans->date = Carbon::parse($po->ci->dt)->toFormattedDateString();
                        $trans->ref = $po->ref_no;
                        $trans->qty = number_format($po->credit - ($po->credit *2),2);
                        $trans->cost = number_format($po->ci_item->unit_price * $po->credit,2);
                        $trans->onHand = number_format($onhandparent += ($po->credit - ($po->credit *2)),2);
                        $trans->um = "PCS";
                        $trans->avgcost = number_format($po->ci_item->unit_price,2);
                        $trans->assetval = number_format($asset_val_total += $po->ci_item->unit_price * $po->credit,2); 
                    }elseif($po->type == "invoice"){
                        $trans->item_name = "";
                        $trans->type = "INVOICE";
                        $trans->name = $po->invoice->customer_belong->customer_name;
                        $trans->space = ($i*2);
                        $trans->date = Carbon::parse($po->invoice->dt)->toFormattedDateString();
                        $trans->ref = $po->ref_no;
                        $trans->qty = number_format($po->credit- ($po->credit *2),2);
                        $trans->cost = number_format($po->invoice_item->unit_price * $po->credit,2);
                        $trans->onHand = number_format($onhandparent += ($po->credit - ($po->credit *2)),2);
                        $trans->um = "PCS";
                        $trans->avgcost = number_format($po->invoice_item->unit_price,2);
                        $trans->assetval = number_format($asset_val_total += $po->invoice_item->unit_price * $po->credit,2);
                    }elseif($po->type == "dr"){
                        $trans->item_name = "";
                        $trans->type = "ITEM RECEIPT";
                        $trans->name = "";
                        $trans->space = ($i*2);
                        $trans->date = Carbon::parse($po->dr->dt)->toFormattedDateString();
                        $trans->ref = "DR#".$po->ref_no;
                        $trans->qty = number_format($po->debit,2);
                        $trans->cost = number_format($po->dr_item->unit_price * $po->debit,2);
                        $trans->onHand = number_format($onhandparent += $po->debit,2);
                        $trans->um = "PCS";
                        $trans->avgcost = number_format($po->dr_item->unit_price,2);
                        $trans->assetval = number_format($asset_val_total +=  $po->dr_item->unit_price * $po->debit,2);
                    }elseif($po->type == "adjustment"){
                        $trans->item_name = "";
                        $trans->type = "ADJUSTMENT";
                        $trans->name = "";
                        $trans->space = ($i*2);
                        $trans->date = Carbon::parse($po->inventory->dt)->toFormattedDateString();
                        $trans->ref = $po->ref_no;
                        $trans->qty = number_format($po->credit - ($po->credit *2),2);
                        $trans->cost = number_format($po->inventory_item->ave_cost * $po->credit,2);
                        $trans->onHand = number_format($onhandparent += ($po->credit - ($po->credit *2)),2);
                        $trans->um = "PCS";
                        $trans->avgcost = number_format($po->inventory_item->ave_cost,2);
                        $trans->assetval = number_format($asset_val_total += ($po->inventory_item->ave_cost * $po->credit) - ($po->inventory_item->ave_cost * $po->credit)*2,2);
                    }elseif($po->type == "bill_adjus"){
                        $trans->item_name = "";
                        $trans->type = "MIXED ADJUSTMENT";
                        $trans->name = "";
                        $trans->space = ($i*2);
                        $trans->date = Carbon::parse($po->inventory->dt)->toFormattedDateString();
                        $trans->ref = $po->ref_no;
                        $trans->qty = number_format($po->debit,2);
                        $trans->cost = number_format($po->inventory->amount_cost,2);
                        $trans->onHand = number_format($onhandparent += $po->debit,2);
                        $trans->um = "PCS";
                        $trans->avgcost = number_format($po->inventory->amount_cost/$po->debit,2);
                        $trans->assetval = number_format($asset_val_total +=  $po->inventory->amount_cost ,2);
                    }
                    if($data->is_sub = $parent){
                       
                    
                        $sub_onhandparent += $onhandparent;
                        $sub_asset_val_total += $asset_val_total;
                    }else{
                      
                        $sub_asset_val_total += $asset_val_total;
                        $sub_onhandparent += $onhandparent;
                    }
                    $itemArr[] = $trans;
                }
                $total_asset += $sub_asset_val_total;
                $total_asset1 = $sub_asset_val_total;
                $total_onhand += $sub_onhandparent;
                $total_onhand1 = $sub_onhandparent;
                $totalasset = $total_asset;
                $totalonhand =$total_onhand;
                session::put('global_total_asset', $totalasset);
                session::put('global_total_onhand', $totalonhand);
                if(count($data->sub)>0){
                  
                     $child = $this->item($data->sub, $data->id);


                    if($child)
                    {
                      $i--;

                      $child;
                    }
                }
                $obj1 = new \stdClass;
                if($data->is_sub == $parent){
                    
                    $obj1->item_name = $data->item_name." Total:";
                    $obj1->type = "";
                    $obj1->name = "";
                    $obj1->space = ($i*2);
                    $obj1->date = "";
                    $obj1->ref = "";
                    $obj1->qty = "";
                    $obj1->cost = "";
                    $obj1->onHand = number_format($total_onhand1,2);
                    $obj1->um = "";
                    $obj1->avgcost = "";
                    $obj1->assetval = "";
                }else{
                    $obj1->item_name = $data->item_name." Total:";
                    $obj1->type = "";
                    $obj1->name = "";
                    $obj1->space = ($i*2);
                    $obj1->date = "";
                    $obj1->ref = "";
                    $obj1->qty = "";
                    $obj1->cost = "";
                    $obj1->onHand = number_format($sub_onhandparent,2);
                    $obj1->um = "";
                    $obj1->avgcost = "";
                    $obj1->assetval = "";
                }

                
                $itemArr[] = $obj1;
                $item_count++;
            } 
        }
        if($subs == 0){
            $head = new \stdClass;
            $head->item_name = "Inventory Total";
            $head->type = "";
            $head->name = "";
            $head->space ="";
            $head->date = "";
            $head->ref = "";
            $head->qty = "";
            $head->cost = "";
            $head->onHand = number_format(session('global_total_onhand'),2);
            $head->um = "";
            $head->avgcost = "";
            $head->assetval = number_format(session('global_total_asset'),2);
            $itemArr[] = $head;
        }
        return $itemArr;
    }

    public function anydata(){
        
        $inventories = Item::where('item_name','like','%'.Request::get('item_name').'%')
            ->where('item_type_id',1)
            ->where(function($q) {
              $q->has("item_flow")->orHas("sub.item_flow");
            })
            ->orderBy('item_name','ASC')
            ->get();
        $inventory= $this->item($inventories);
        $users = collection::make($inventory);
        session::forget('global_total_asset');
        session::forget('global_total_onhand');
        return Datatables::of($users)
        ->editColumn('item_name', function ($user) {
                return '<h4 style="margin-left:'.$user->space.'em">'.$user->item_name.'</h4>';
            })
        ->make(true);

    }

    public function search(){

    }
    public function inventory_report(){
        
       
        $inventories = Item::where('item_type_id',1)
            ->where(function($q) {
              $q->has("item_flow")->orHas("sub.item_flow");
            })
            ->orderBy('item_name','ASC')
            ->get();
        $inventory = $this->item($inventories);
       
       
        
        return view('reports.inventory_report',compact('inventory'));
    }
    public function inventory_report_store(){

      
        $inventories = Item::where('item_name','like','%'.Request::get('item_name').'%')

            ->where('item_type_id',1)
            ->where(function($q) {
              $q->has("item_flow")->orHas("sub.item_flow");
            })
            ->orderBy('item_name','ASC')
            ->get();
            
        $inventory = $this->item($inventories);
        
          
        
        session::forget('global_total_asset');
        session::forget('global_total_onhand');
        return view('reports.inventory_report',compact('inventory','total_asset','total_onhand'));
    }




    private function purchase($item_type){
           
            $itemArr = [];

            foreach ($item_type as  $type) {
                $qty = 0;
                $amount = 0;
                $total_balance = 0;
                $itemtype = new \stdClass;
                $itemtype->item_name = $type->item_type_name;
                $itemtype->type = "";
                $itemtype->date = "";
                $itemtype->ref = "";
                $itemtype->memo = "";
                $itemtype->source = "";
                $itemtype->qty = "";
                $itemtype->um = "";
                $itemtype->avgcost = "";
                $itemtype->amount = "";
                $itemtype->balance = "";
                $itemtype->space = 0;
                $itemArr[] = $itemtype;
                $collect = $type->item_type;

                $data = $collect->where('is_sub', 0);
                if(count($data) > 0){
                    foreach ($data as $key => $item) {
                        $subqty = 0;
                        $subamount = 0;
                        $balance_total =0;
                        $i=1;

                        $head = new \stdClass;
                        $head->item_name = $item->item_name;
                        $head->type = "";
                        $head->date = "";
                        $head->ref = "";
                        $head->memo = "";
                        $head->source = "";
                        $head->qty = "";
                        $head->um = "";
                        $head->avgcost = "";
                        $head->amount = "";
                        $head->balance = "";
                        $head->space = ($i*2);
                        $itemArr[] = $head;
                        $i++;
                        foreach($item->item_flow as $po){
                            $trans = new \stdClass;
                            if($po->type == "po"){
                                $subqty +=$po->vc_item->item_rcv;
                                $subamount +=$po->vc_item->item_rcv * $po->vc_item->po_item->item_price;

                                $trans->item_name = "";
                                $trans->type = "BILL";
                                $trans->date = Carbon::parse($po->vc_item->dt)->toFormattedDateString();
                                $trans->ref = "CV".$po->ref_no;
                                $trans->memo = "MEMO";
                                $trans->source = $po->vc_item->po->supplier_one->supplier_name;
                                $trans->qty = number_format($po->vc_item->item_rcv,2);
                                $trans->um = "PCS";
                                $trans->avgcost = number_format($po->vc_item->po_item->item_price,2);
                                $trans->amount = number_format(($po->vc_item->item_rcv * $po->vc_item->po_item->item_price),2);
                                $trans->balance = number_format($balance_total += ($po->vc_item->item_rcv * $po->vc_item->po_item->item_price),2);
                                
                                $trans->space = ($i*2);
                            }
                            $itemArr[] = $trans;
                        }

                        foreach ($item->children as  $childrenitem) {
                            $subqty1 = 0;
                            $subamount1 = 0;


                            $head = new \stdClass;
                            $head->item_name = $childrenitem->item_name;
                            $head->type = "";
                            $head->date = "";
                            $head->ref = "";
                            $head->memo = "";
                            $head->source = "";
                            $head->qty = "";
                            $head->um = "";
                            $head->avgcost = "";
                            $head->amount = "";
                            $head->balance = "";
                            $head->space = ($i*2);
                            $itemArr[] = $head;

                            foreach($childrenitem->item_flow as $pochild){
                                $trans = new \stdClass;

                                if($pochild->type == "po"){
                                    $i++;
                                    $subqty1 += $pochild->vc_item->item_rcv;
                                    $subamount1 += $pochild->vc_item->item_rcv * $pochild->vc_item->po_item->item_price;

                                    $trans->item_name = "";
                                    $trans->type = "BILL";
                                    $trans->date = Carbon::parse($pochild->vc_item->dt)->toFormattedDateString();
                                    $trans->ref = "CV".$pochild->ref_no;
                                    $trans->memo = "MEMO";
                                    $trans->source = $pochild->vc_item->po->supplier_one->supplier_name;
                                    $trans->qty = number_format($pochild->vc_item->item_rcv,2);
                                    $trans->um = "PCS";
                                    $trans->avgcost = number_format($pochild->vc_item->po_item->item_price,2);
                                    $trans->amount = number_format(($pochild->vc_item->item_rcv * $pochild->vc_item->po_item->item_price),2);
                                    $trans->balance = number_format($balance_total += ($pochild->vc_item->item_rcv * $pochild->vc_item->po_item->item_price),2);
                                    $trans->space = ($i*2);
                                    $i--;

                                }
                                $itemArr[] = $trans;
                            }//endpochild
                            $total_balance +=$balance_total;
                            $head = new \stdClass;
                            $head->item_name = "Total ".$childrenitem->item_name;
                            $head->type = "";
                            $head->date = "";
                            $head->ref = "";
                            $head->memo = "";
                            $head->source = "";
                            $head->qty = number_format($subqty1,2);
                            $head->um = "";
                            $head->avgcost = "";
                            $head->amount = number_format($subamount1,2);
                            $head->balance = number_format($balance_total,2);
                            $head->space = ($i*2);
                            $itemArr[] = $head;
                            $subqty+=$subqty1;
                            $subamount+=$subamount1;

                            $amount +=$subamount;
                            $qty +=$subqty;
                            // dump($childrenitem->item_name."=".$subqty1);
                        }//endchildren

                        
                        $i--;
                        $head = new \stdClass;
                        $head->item_name = "Total ".$item->item_name;
                        $head->type = "";
                        $head->date = "";
                        $head->ref = "";
                        $head->memo = "";
                        $head->source = "";
                        $head->qty = number_format($subqty,2);
                        $head->um = "";
                        $head->avgcost = "";
                        $head->amount = number_format($subamount,2);
                        $head->balance = number_format($balance_total,2);
                        $head->space = ($i*2);
                        $itemArr[] = $head;
                        // dump($item->item_name."=".$subqty);
                    }
                }else{
                    foreach ($type->item_type as $key => $item) {
                        $subqty = 0;
                        $subamount = 0;
                        $balance_total =0;
                        $i=1;

                        $head = new \stdClass;
                        $head->item_name = $item->item_name;
                        $head->type = "";
                        $head->date = "";
                        $head->ref = "";
                        $head->memo = "";
                        $head->source = "";
                        $head->qty = "";
                        $head->um = "";
                        $head->avgcost = "";
                        $head->amount = "";
                        $head->balance = "";
                        $head->space = ($i*2);
                        $itemArr[] = $head;
                        $i++;
                        foreach($item->item_flow as $po){
                            $trans = new \stdClass;
                            if($po->type == "po"){
                                $subqty +=$po->vc_item->item_rcv;
                                $subamount +=$po->vc_item->item_rcv * $po->vc_item->po_item->item_price;
                                $qty +=$po->vc_item->item_rcv;
                                $amount +=$po->vc_item->item_rcv * $po->vc_item->po_item->item_price;

                                $trans->item_name = "";
                                $trans->type = "BILL";
                                $trans->date = Carbon::parse($po->vc_item->dt)->toFormattedDateString();
                                $trans->ref = "CV".$po->ref_no;
                                $trans->memo = "MEMO";
                                $trans->source = $po->vc_item->po->supplier_one->supplier_name;
                                $trans->qty = number_format($po->vc_item->item_rcv,2);
                                $trans->um = "PCS";
                                $trans->avgcost = number_format($po->vc_item->po_item->item_price,2);
                                $trans->amount = number_format(($po->vc_item->item_rcv * $po->vc_item->po_item->item_price),2);
                                $trans->balance = number_format($balance_total += ($po->vc_item->item_rcv * $po->vc_item->po_item->item_price),2);
                                
                                $trans->space = ($i*2);
                                $total_balance +=$po->vc_item->item_rcv * $po->vc_item->po_item->item_price;
                            }
                            $itemArr[] = $trans;
                        }

                        foreach ($item->children as  $childrenitem) {
                            $subqty1 = 0;
                            $subamount1 = 0;


                            $head = new \stdClass;
                            $head->item_name = $childrenitem->item_name;
                            $head->type = "";
                            $head->date = "";
                            $head->ref = "";
                            $head->memo = "";
                            $head->source = "";
                            $head->qty = "";
                            $head->um = "";
                            $head->avgcost = "";
                            $head->amount = "";
                            $head->balance = "";
                            $head->space = ($i*2);
                            $itemArr[] = $head;
                            foreach($childrenitem->item_flow as $pochild){
                                $trans = new \stdClass;

                                if($pochild->type == "po"){
                                    $i++;
                                    $subqty1 += $pochild->vc_item->item_rcv;
                                    $subamount1 += $pochild->vc_item->item_rcv * $pochild->vc_item->po_item->item_price;
                                    $qty +=$pochild->vc_item->item_rcv;
                                    $amount +=$pochild->vc_item->item_rcv * $pochild->vc_item->po_item->item_price;
                                    $trans->item_name = "";
                                    $trans->type = "BILL";
                                    $trans->date = Carbon::parse($pochild->vc_item->dt)->toFormattedDateString();
                                    $trans->ref = "CV".$pochild->ref_no;
                                    $trans->memo = "MEMO";
                                    $trans->source = $pochild->vc_item->po->supplier_one->supplier_name;
                                    $trans->qty = number_format($pochild->vc_item->item_rcv,2);
                                    $trans->um = "PCS";
                                    $trans->avgcost = number_format($pochild->vc_item->po_item->item_price,2);
                                    $trans->amount = number_format(($pochild->vc_item->item_rcv * $pochild->vc_item->po_item->item_price),2);
                                    $trans->balance = number_format($balance_total += ($pochild->vc_item->item_rcv * $pochild->vc_item->po_item->item_price),2);
                                    $trans->space = ($i*2);
                                    $i--;
                                    $total_balance +=$pochild->vc_item->item_rcv * $pochild->vc_item->po_item->item_price;
                                }
                                $itemArr[] = $trans;

                            }//endpochild
                           
                            $head = new \stdClass;
                            $head->item_name = "Total ".$childrenitem->item_name;
                            $head->type = "";
                            $head->date = "";
                            $head->ref = "";
                            $head->memo = "";
                            $head->source = "";
                            $head->qty = number_format($subqty1,2);
                            $head->um = "";
                            $head->avgcost = "";
                            $head->amount = number_format($subamount1,2);
                            $head->balance = number_format($balance_total,2);
                            $head->space = ($i*2);
                            $itemArr[] = $head;

                            $subqty+=$subqty1;
                            $subamount+=$subamount1;

                            $amount +=$subamount;
                            $qty +=$subqty;
                            // dump($childrenitem->item_name."=".$subqty1);
                        }//endchildren

                        
                        $i--;
                        $head = new \stdClass;
                        $head->item_name = "Total ".$item->item_name;
                        $head->type = "";
                        $head->date = "";
                        $head->ref = "";
                        $head->memo = "";
                        $head->source = "";
                        $head->qty = number_format($subqty,2);
                        $head->um = "";
                        $head->avgcost = "";
                        $head->amount = number_format($subamount,2);
                        $head->balance = number_format($balance_total,2);
                        $head->space = ($i*2);
                        $itemArr[] = $head;
                        // dump($item->item_name."=".$subqty);
                    }
                }
                $itemtype = new \stdClass;
                $itemtype->item_name = "Total ".$type->item_type_name;
                $itemtype->type = "";
                $itemtype->date = "";
                $itemtype->ref = "";
                $itemtype->memo = "";
                $itemtype->source = "";
                $itemtype->qty = number_format($qty,2);
                $itemtype->um = "";
                $itemtype->avgcost = "";
                $itemtype->amount = number_format($amount,2);
                $itemtype->balance = number_format($total_balance,2);
                $itemtype->space = 0;
                $itemArr[] = $itemtype;
            }
            return collection::make($itemArr);

    }

    private function purchase_summary($item_type){
          
            $itemArr = [];

            foreach ($item_type as  $type) {
                $qty = 0;
                $amount = 0;
                $itemtype = new \stdClass;
                $itemtype->item_name = $type->item_type_name;
                $itemtype->qty = "";
                $itemtype->amount = "";
                $itemtype->space = 0;
                $itemArr[] = $itemtype;
                $collect = $type->item_type;
                $data = $collect->where('is_sub', 0);
                if(count($data) > 0){
                    foreach ($data as $key => $item) {
                        $subqty = 0;
                        $subamount = 0;

                        $balance_total =0;
                        $i=1;
                        if(count($item->children) > 0){
                            $head = new \stdClass;
                            $head->item_name = $item->item_name;
                            $head->qty = "";
                            $head->amount = "";
                            $head->space = ($i*2);
                            $itemArr[] = $head;
                        }
                        $i++;
                        foreach($item->item_flow as $po){
                            if($po->type == "po"){
                                $subqty +=$po->vc_item->item_rcv;
                                $subamount += $po->vc_item->item_rcv * $po->vc_item->po_item->item_price;
                            }
                          
                        }

                        foreach ($item->children as  $childrenitem) {
                            $subqty1 = 0;
                            $subamount1 = 0;
                          
                            foreach($childrenitem->item_flow as $pochild){
                             

                                if($pochild->type == "po"){
                                    $i++;
                                    $subqty1 += $pochild->vc_item->item_rcv;
                                    $subamount1 += $pochild->vc_item->item_rcv * $pochild->vc_item->po_item->item_price;

                                    $i--;
                                }
                                                           }//endpochild

                           
                            $subqty+=$subqty1;
                            $subamount+=$subamount1;
                            $head = new \stdClass;
                            $head->item_name = $childrenitem->item_name;
                            $head->type = "";
                            $head->qty = number_format($subqty1,2);
                            $head->amount = number_format($subamount1,2);;
                            $head->space = ($i*2);
                            $itemArr[] = $head;
                            // dump($childrenitem->item_name."=".$subqty1);
                        }//endchildren

                        
                        $i--;
                        if(count($item->children) > 0){
                            $head = new \stdClass;
                            $head->item_name = "Total ".$item->item_name;
                            $head->type = "";
                            $head->qty = number_format($subqty,2);
                            $head->amount = number_format($subamount,2);
                            $head->space = ($i*2);
                            $itemArr[] = $head;
                        }else{
                            $head = new \stdClass;
                            $head->item_name = $item->item_name;
                            $head->qty = number_format($subqty,2);
                            $head->amount = number_format($subamount,2);
                            $head->space = ($i*2);
                            $itemArr[] = $head;
                           
                        }
                        $qty += $subqty; 
                        $amount += $subamount;
                        // dump($item->item_name."=".$subqty);
                    }
                }else{
                    foreach ($type->item_type as $key => $item) {
                        $subqty = 0;
                        $subamount = 0;

                        $balance_total =0;
                        $i=1;
                        if(count($item->children) > 0){
                            $head = new \stdClass;
                            $head->item_name = $item->item_name;
                            $head->qty = "";
                            $head->amount = "";
                            $head->space = ($i*2);
                            $itemArr[] = $head;
                        }
                        $i++;
                        foreach($item->item_flow as $po){
                            if($po->type == "po"){
                                $subqty +=$po->vc_item->item_rcv;
                                $subamount += $po->vc_item->item_rcv * $po->vc_item->po_item->item_price;
                            }
                          
                        }

                        foreach ($item->children as  $childrenitem) {
                            $subqty1 = 0;
                            $subamount1 = 0;
                          
                            foreach($childrenitem->item_flow as $pochild){
                             

                                if($pochild->type == "po"){
                                    $i++;
                                    $subqty1 += $pochild->vc_item->item_rcv;
                                    $subamount1 += $pochild->vc_item->item_rcv * $pochild->vc_item->po_item->item_price;

                                    $i--;
                                }
                                                           }//endpochild

                           
                            $subqty+=$subqty1;
                            $subamount+=$subamount1;
                            $head = new \stdClass;
                            $head->item_name = $childrenitem->item_name;
                            $head->type = "";
                            $head->qty = number_format($subqty1,2);
                            $head->amount = number_format($subamount1,2);;
                            $head->space = ($i*2);
                            $itemArr[] = $head;
                            // dump($childrenitem->item_name."=".$subqty1);
                        }//endchildren

                        
                        $i--;
                        if(count($item->children) > 0){
                            $head = new \stdClass;
                            $head->item_name = "Total ".$item->item_name;
                            $head->type = "";
                            $head->qty = number_format($subqty,2);
                            $head->amount = number_format($subamount,2);
                            $head->space = ($i*2);
                            $itemArr[] = $head;
                        }else{
                            $head = new \stdClass;
                            $head->item_name = $item->item_name;
                            $head->qty = number_format($subqty,2);
                            $head->amount = number_format($subamount,2);
                            $head->space = ($i*2);
                            $itemArr[] = $head;
                           
                        }
                        $qty += $subqty; 
                        $amount += $subamount;
                        // dump($item->item_name."=".$subqty);
                    }
                }
                $itemtype = new \stdClass;
                $itemtype->item_name = "Total ".$type->item_type_name;
                $itemtype->qty = number_format($qty,2);
                $itemtype->amount = number_format($amount,2);
                $itemtype->space = 0;
                $itemArr[] = $itemtype;
            }


            return collection::make($itemArr);
    }

    private function purchase_vendor($data){
        $itemArr = [];
        
        $total_balance = 0;
        foreach ($data as $supplier) { //supplier
            $balance = 0;
            $qty = 0;
            $amount = 0;
            $suppliers = new \stdClass;
            $suppliers->supplier = $supplier->supplier_name;
            $suppliers->type = "";
            $suppliers->date = "";
            $suppliers->ref = "";
            $suppliers->memo = "";
            $suppliers->item = "";
            $suppliers->qty = "";
            $suppliers->um = "";
            $suppliers->cost = "";
            $suppliers->amount = "";
            $suppliers->balance = "";
            $itemArr[] = $suppliers;


             $vc_data = collect($supplier->voucheritem);
             $vouchers = $vc_data->merge($supplier->voucherjobitem);


            foreach ($vouchers as $key => $voucher) {
               
                $vc = new \stdClass;
                $vc->supplier = "";
                $vc->type = "BILL";
                $vc->date = Carbon::parse($voucher->dt)->toFormattedDateString();
                $vc->ref = "CV".$voucher->voucher_number;
                
                $vc->memo = $voucher->voucher->explanation;
                $vc->item = $voucher->item->item_name;
                $vc->qty = number_format((!empty($voucher->item_rcv))? $voucher->item_rcv: 0,2) ;
                $vc->um = $voucher->item->unit_measure;
                $vc->cost = number_format((!empty($voucher->item_rcv))? $voucher->voucher->amount / $voucher->item_rcv : $voucher->voucher->amount,2);
                $vc->amount = number_format((int)$voucher->voucher->amount,2);
                $vc->balance = number_format($balance += $voucher->voucher->amount,2);
                $amount += $voucher->voucher->amount;
                $qty += (!empty($voucher->item_rcv))? $voucher->item_rcv: 0 ;
                $itemArr[] = $vc;
            }


            $suppliers = new \stdClass;
            $suppliers->supplier = $supplier->supplier_name." Total:";
            $suppliers->type = "";
            $suppliers->date = "";
            $suppliers->ref = "";
            $suppliers->memo = "";
            $suppliers->item = "";
            $suppliers->qty = number_format($qty,2);
            $suppliers->um = "";
            $suppliers->cost = "";
            $suppliers->amount = number_format($amount,2);
            $suppliers->balance = number_format($balance,2);
            $suppliers->key = "total";
            $itemArr[] = $suppliers;


        } //supplier
        return collection::make($itemArr);
    }
    
    private function puchase_vendor_summary($data){
        $itemArr = [];
        
        $total_balance = 0;

        foreach ($data as $supplier) { //supplier
            $balance = 0;
            $qty = 0;
            $amount = 0;
          


             $vc_data = collect($supplier->voucheritem);
             $vouchers = $vc_data->merge($supplier->voucherjobitem);


            foreach ($vouchers as $key => $voucher) {
                $balance += $voucher->voucher->amount;
            }
            $suppliers = new \stdClass;
            $suppliers->supplier = $supplier->supplier_name;
            $suppliers->balance =number_format($balance,2);
            $itemArr[] = $suppliers;
            $total_balance += $balance;
        } //supplier

        $suppliers = new \stdClass;
        $suppliers->supplier = "TOTAL:";
        $suppliers->balance =number_format($total_balance,2);
        $suppliers->key ="total";
        $itemArr[] = $suppliers;

        return collection::make($itemArr);
    }

    public function purchases_report_view(){
        return view('reports.purchases.purchases_report');
    }

    public function purchases_report_data(){

        $name = Request::get('item_name');
        $item_type = Item_type::with(['item_type'=>function($q) use($name){
            $q->where(function($s) {
              $s->has("item_flow.vc_item.po_item");
            })
            ->orderBy('item_type_id','ASC')
            ->orderBy('item_name', 'ASC')
            // ->where('is_sub',0)
            ->where('item_name','like','%'.$name.'%');
        },

        'item_type.item_flow'=>function($q){ 
                    $q->where('type','po');
                    $q->orWhere('type','dr');
                    $q->orWhere('type','jo');
                },
                'item_type.children'=>function($q){
                    $q->has("item_flow.vc_item.po_item");
                },
                'item_type.children.item_flow'=>function($q){ 
                    $q->where('type','po');
                    $q->orWhere('type','dr');
                    $q->orWhere('type','jo');
                },
                'item_type.item_flow.vc_item',
                'item_type.item_flow.vc_item.po.supplier_one',
                'item_type.item_flow.vc_item.po_item',

                'item_type.children.item_flow.vc_item',
                'item_type.children.item_flow.vc_item.po.supplier_one',
                'item_type.children.item_flow.vc_item.po_item'

        ])->where(function($q) {
              $q->has("item_type.item_flow.vc_item.po_item");
            })
            ->get();    
        $inv = $this->purchase($item_type);
       
      

        return Datatables::of($inv)
        ->editColumn('item_name', function ($user) {
                return '<h4 style="margin-left:'.$user->space.'em">'.$user->item_name.'</h4>';
            })
        ->make(true);
    }


    public function item_summary(){


        $name = Request::get('item_name');
        $item_type = Item_type::with(['item_type'=>function($q) use($name){
            $q->where(function($s) {
              $s->has("item_flow.vc_item.po_item");
            })
            ->orderBy('item_type_id','ASC')
            ->orderBy('item_name', 'ASC')
            // ->where('is_sub',0)
            ->where('item_name','like','%'.$name.'%');
        },

        'item_type.item_flow'=>function($q){ 
                    $q->where('type','po');
                    $q->orWhere('type','dr');
                    $q->orWhere('type','jo');
                },
                'item_type.children'=>function($q){
                    $q->has("item_flow.vc_item.po_item");
                },
                'item_type.children.item_flow'=>function($q){ 
                    $q->where('type','po');
                    $q->orWhere('type','dr');
                    $q->orWhere('type','jo');
                },
                'item_type.item_flow.vc_item',
                'item_type.item_flow.vc_item.po.supplier_one',
                'item_type.item_flow.vc_item.po_item',

                'item_type.children.item_flow.vc_item',
                'item_type.children.item_flow.vc_item.po.supplier_one',
                'item_type.children.item_flow.vc_item.po_item'

        ])->where(function($q) {
              $q->has("item_type.item_flow.vc_item.po_item");
            })
            ->get();    

        $inv = $this->purchase_summary($item_type);
       


        return Datatables::of($inv)
        ->editColumn('item_name', function ($user) {
                return '<h4 style="margin-left:'.$user->space.'em">'.$user->item_name.'</h4>';
            })
        ->make(true);
    }

    public function purchase_vendor_detail(){
         $suppliers = Supplier::with(['voucheritem','voucheritem.voucher','voucherjobitem.voucher','voucheritem.item','voucherjobitem.item'])
            ->where(function($q){
                $q->has('voucheritem')->orHas('voucherjobitem');
            })
            ->orderBy('supplier_name','ASC')->get();

            $vDetail = $this->purchase_vendor($suppliers);
            return Datatables::of($vDetail)
            ->editColumn('supplier', function ($vendor) {
                if(!isset($vendor->key)){
                    return '<h3>'.$vendor->supplier.'</h3>';
                }else{
                    return $vendor->supplier;
                }
                
            })
            ->setRowClass(function ($vendor) {

                return (isset($vendor->key) ? 'total-border' : '');
            })
            ->make(true);
            
    }

    public function vendor_summary(){
        $suppliers = Supplier::with(['voucheritem','voucherjobitem','voucherjobitem.voucher','voucheritem.voucher'])
            ->where(function($q){
                $q->has('voucheritem')->orHas('voucherjobitem');
            })
            ->orderBy('supplier_name','ASC')->get();

            $vDetail = $this->puchase_vendor_summary($suppliers);

            return Datatables::of($vDetail)
            ->setRowClass(function ($vendor) {
                    return (isset($vendor->key) ? 'text-bold' : ' ');
            })
            ->make(true);
    }



    private function sales_report_itemDetails($item_type){

        $itemArr = [];
        foreach ($item_type as  $type) {

                $qty = 0;
                $amount = 0;
                $amount_old =0;
                $qty_old = 0;
                $total_balance = 0;
                $total_balance_old = 0;
                $itemtype = new \stdClass;
                $itemtype->item_name = $type->item_type_name;
                $itemtype->type = "";
                $itemtype->date = "";
                $itemtype->source = "";
                $itemtype->ref = "";
                $itemtype->memo = "";
                $itemtype->qty = "";
                $itemtype->um = "";
                $itemtype->price = "";
                $itemtype->amount = "";
                $itemtype->balance = "";
                $itemtype->space = 0;
                $itemArr[] = $itemtype;
                $collect = $type->item_type;
                $data = $collect->where('is_sub', 0);
                if(count($data) > 0){
                    foreach ($data as $key => $item) {
                        $subqty = 0;
                        $subamount = 0;
                        $balance_total =0;
                        $balance_total_old = 0;
                        $i=1;

                        $head = new \stdClass;
                        $head->item_name = $item->item_name;
                        $head->type = "";
                        $head->date = "";
                        $head->ref = "";
                        $head->memo = "";
                        $head->source = "";
                        $head->qty = "";
                        $head->um = "";
                        $head->price = "";
                        $head->amount = "";
                        $head->balance = "";
                        $head->space = ($i*2);
                        $itemArr[] = $head;
                        $i++;


                        $mergeI = collect($item->cash_invoice_item)->merge($item->invoice_item)->sortBy('dt');
                     
                        if(Request::has('daterange')){
                            $mergeOld = collect($item->cash_invoice_item_old)->merge($item->invoice_item_old)->sortBy('dt');
                            foreach ($mergeOld as  $data_old) {
                                $amount_old +=$data_old->amount;
                                $total_balance_old +=$data_old->amount;
                                $qty_old += ($data['table'] == "cash_invoice_items")? $data_old->cash_invoice_qty : $data_old->charge_invoice_qty;
                            }
                            $qty += $qty_old;
                            $amount += $amount_old;
                            $total_balance += $total_balance_old;
                        }
                        foreach ($mergeI as $key => $data) {
                            $invoices = new \stdClass;
                            if($data['table'] == "cash_invoice_items"){
                                $invoices->item_name = "";
                                $invoices->type = "SALES RECEIPT";
                                $invoices->date = Carbon::parse($data->dt)->toFormattedDateString();
                                $invoices->ref = $data->cash_invoice_number;
                                $invoices->memo = "";
                                $invoices->source = $data->cash_inv->customer_belong->customer_name;
                                $invoices->qty = number_format($data->cash_invoice_qty,2);
                                $invoices->um = $item->unit_measure;
                                $invoices->price = number_format($data->unit_price,2);
                                $invoices->amount = number_format($data->amount,2);
                                $invoices->balance = number_format($balance_total += $data->amount,2);
                                $invoices->space = ($i*2);
                                 $qty +=$data->cash_invoice_qty;
                                 $subqty += $data->cash_invoice_qty;
                            }else{
                                $invoices->item_name = "";
                                $invoices->type = "INVOICE";
                                $invoices->date = Carbon::parse($data->dt)->toFormattedDateString();
                                $invoices->ref = $data->charge_invoice_number;
                                $invoices->memo = "";
                                $invoices->source = $data->ci->customer_belong->customer_name;
                                $invoices->qty = number_format($data->charge_invoice_qty,2);
                                $invoices->um = $item->unit_measure;
                                $invoices->price = number_format($data->unit_price,2);
                                $invoices->amount = number_format($data->amount,2);
                                $invoices->balance = number_format($balance_total += $data->amount,2);
                                $invoices->space = ($i*2);
                                $qty +=$data->charge_invoice_qty;
                                $subqty += $data->charge_invoice_qty;
                            }

                            $itemArr[] = $invoices;

                            $subamount += $data->amount;
                            
                        }

                        foreach ($item->children as  $childrenitem) {

                            $subqty1 = 0;
                            $subqty1_old = 0;
                            $subamount1 = 0;
                            $subamount1_old = 0;
                            $balance_total1 =0;
                            $balance_total_old1 =0;
                            $head = new \stdClass;
                            $head->item_name = $childrenitem->item_name;
                            $head->type = "";
                            $head->date = "";
                            $head->source = "";
                            $head->ref = "";
                            $head->memo = "";
                            $head->qty = "";
                            $head->um = "";
                            $head->price = "";
                            $head->amount = "";
                            $head->balance = "";
                            $head->space = ($i*2);
                            $itemArr[] = $head;

                            $invoicess = collect($childrenitem->cash_invoice_item);
                            $mergeInvoice = $invoicess->merge($childrenitem->invoice_item)->sortBy('dt');
                            if(Request::has('daterange')){
                                $mergeInvoice_old = collect($childrenitem->cash_invoice_item_old)->merge($childrenitem->invoice_item_old)->sortBy('dt');

                                foreach ($mergeInvoice_old as $key => $sub_data_old) {
                                    $subqty1_old += ($sub_data_old['table'] == "cash_invoice_items")? $sub_data_old->cash_invoice_qty : $sub_data_old->charge_invoice_qty;

                                    $balance_total_old1 += $sub_data_old->amount;
                                  
                                    $subamount1_old +=$sub_data_old->amount;
                                }

                                $qty += $subqty1_old;
                                $amount += $subamount1_old;
                                $total_balance += $balance_total_old1;
                            }
                            
                            foreach ($mergeInvoice as $key => $items) {
                                $invoices = new \stdClass;
                                if($items['table'] == "cash_invoice_items"){
                                    $invoices->item_name = "";
                                    $invoices->type = "SALES RECEIPT";
                                    $invoices->date = Carbon::parse($items->dt)->toFormattedDateString();
                                    $invoices->ref = $items->cash_invoice_number;
                                    $invoices->memo = "";
                                    $invoices->source = $items->cash_inv->customer_belong->customer_name;
                                    $invoices->qty = number_format($items->cash_invoice_qty,2);
                                    $invoices->um = $childrenitem->unit_measure;
                                    $invoices->price = number_format($items->unit_price,2);
                                    $invoices->amount = number_format($items->amount,2);
                                    $invoices->balance = number_format($balance_total1 += $items->amount,2);
                                    $invoices->space = ($i*2);
                                    $subqty1 +=$items->cash_invoice_qty;
                                }else{
                                    $invoices->item_name = "";
                                    $invoices->type = "INVOICE";
                                    $invoices->date = Carbon::parse($items->dt)->toFormattedDateString();
                                    $invoices->ref = $items->charge_invoice_number;
                                    $invoices->memo = "";
                                    $invoices->source = $items->ci->customer_belong->customer_name;
                                    $invoices->qty = number_format($items->charge_invoice_qty,2);
                                    $invoices->um = $childrenitem->unit_measure;
                                    $invoices->price = number_format($items->unit_price,2);
                                    $invoices->amount = number_format($items->amount,2);
                                    $invoices->balance = number_format($balance_total1 += $items->amount,2);
                                    $invoices->space = ($i*2);
                                    $subqty1 +=$items->charge_invoice_qty;
                                }

                                $itemArr[] = $invoices;

                                $subamount1 +=$items->amount;
                            }

                            $subqty +=$subqty1 + $subqty1_old;
                            $qty += $subqty1;
                            $amount +=$subamount1;
                            $subamount +=$subamount1 + $subamount1_old;
                            $total_balance +=$balance_total1;
                            $balance_total += $balance_total1+$balance_total_old1;
                            $head = new \stdClass;
                            $head->item_name = $childrenitem->item_name.' Total';
                            $head->type = "";
                            $head->date = "";
                            $head->source = "";
                            $head->ref = "";
                            $head->memo = "";
                            $head->qty = number_format( $subqty1+$subqty1_old,2);
                            $head->um = "";
                            $head->price = "";
                            $head->amount = number_format($subamount1+$subamount1_old,2);
                            $head->balance = number_format($balance_total1+$balance_total_old1,2);
                            $head->space = ($i*2);
                            $head->key ="total";
                            $itemArr[] = $head;
                        }//endchildren

                        
                        $i--;
                        $head = new \stdClass;
                        $head->item_name = "Total ".$item->item_name;
                        $head->type = "";
                        $head->date = "";
                        $head->source = "";
                        $head->ref = "";
                        $head->memo = "";
                        $head->qty = number_format($subqty,2);
                        $head->um = "";
                        $head->price = "";
                        $head->amount = number_format($subamount,2);
                        $head->balance = number_format($balance_total,2);
                        $head->key ="total";
                        $head->space = ($i*2);
                        $itemArr[] = $head;
                        // dump($item->item_name."=".$subqty);
                    }
                }else{
                    foreach ($collect as $key => $item) {
                        $subqty = 0;
                        $subamount = 0;
                        $balance_total =0;
                        $total_balance_old =0;
                        $i=1;

                        $head = new \stdClass;
                        $head->item_name = $item->item_name;
                        $head->type = "";
                        $head->date = "";
                        $head->ref = "";
                        $head->memo = "";
                        $head->source = "";
                        $head->qty = "";
                        $head->um = "";
                        $head->price = "";
                        $head->amount = "";
                        $head->balance = "";
                        $head->space = ($i*2);
                        $head->key ="total";
                        $itemArr[] = $head;
                        $i++;
                        $mergeI = collect($item->cash_invoice_item)->merge($item->invoice_item)->sortBy('dt');
                        if(Request::has('daterange')){
                            $mergeOld = collect($item->cash_invoice_item_old)->merge($item->invoice_item_old)->sortBy('dt');
                            
                            foreach ($mergeOld as  $data_old) {
                                $amount_old +=$data_old->amount;
                                $total_balance_old +=$data_old->amount;
                                $qty_old += ($data_old['table'] == "cash_invoice_items")? $data_old->cash_invoice_qty : $data_old->charge_invoice_qty;
                            }

                            $qty += $qty_old;
                            $amount += $amount_old;
                            $total_balance += $total_balance_old;
                        }


                        foreach ($mergeI as $key => $data) {
                            $invoices = new \stdClass;
                            if($data['table'] == "cash_invoice_items"){
                                $invoices->item_name = "";
                                $invoices->type = "SALES RECEIPT";
                                $invoices->date = Carbon::parse($data->dt)->toFormattedDateString();
                                $invoices->ref = $data->cash_invoice_number;
                                $invoices->memo = "";
                                $invoices->source = $data->cash_inv->customer_belong->customer_name;
                                $invoices->qty = number_format($data->cash_invoice_qty,2);
                                $invoices->um = $item->unit_measure;
                                $invoices->price = number_format($data->unit_price,2);
                                $invoices->amount = number_format($data->amount,2);
                                $invoices->balance = number_format($balance_total += $data->amount,2);
                                $invoices->space = ($i*2);
                                $qty +=$data->cash_invoice_qty;
                            }else{
                                $invoices->item_name = "";
                                $invoices->type = "INVOICE";
                                $invoices->date = Carbon::parse($data->dt)->toFormattedDateString();
                                $invoices->ref = $data->charge_invoice_number;
                                $invoices->memo = "";
                                $invoices->source = $data->ci->customer_belong->customer_name;
                                $invoices->qty = number_format($data->charge_invoice_qty,2);
                                $invoices->um = $item->unit_measure;
                                $invoices->price = number_format($data->unit_price,2);
                                $invoices->amount = number_format($data->amount,2);
                                $invoices->balance = number_format($balance_total += $data->amount,2);
                                $invoices->space = ($i*2);
                                $qty +=$data->charge_invoice_qty;
                            }

                            $itemArr[] = $invoices;

                            
                            $amount +=$data->amount;
                            $total_balance +=$data->amount;
                        }
                        foreach ($item->children as  $childrenitem) {

                           
                            $subqty1 = 0;
                            $subqty1_old = 0;
                            $subamount1 = 0;
                            $subamount1_old = 0;
                            $balance_total1 =0;
                            $balance_total_old1 =0;

                            $head = new \stdClass;
                            $head->item_name = $childrenitem->item_name;
                            $head->type = "";
                            $head->date = "";
                            $head->source = "";
                            $head->ref = "";
                            $head->memo = "";
                            $head->qty = "";
                            $head->um = "";
                            $head->price = "";
                            $head->amount = "";
                            $head->balance = "";
                            $head->space = ($i*2);
                            $itemArr[] = $head;

                            $invoicess = collect($childrenitem->cash_invoice_item);

                            $mergeInvoice = $invoicess->merge($childrenitem->invoice_item)->sortBy('dt');
                            if(Request::has('daterange')){
                                $mergeInvoice_old = collect($childrenitem->cash_invoice_item_old)->merge($childrenitem->invoice_item_old)->sortBy('dt');

                                foreach ($mergeInvoice_old as $key => $sub_data_old) {
                                    $subqty1_old += ($sub_data_old['table'] == "cash_invoice_items")? $sub_data_old->cash_invoice_qty : $sub_data_old->charge_invoice_qty;

                                    $balance_total_old1 += $sub_data_old->amount;
                                  
                                    $subamount1_old +=$sub_data_old->amount;
                                }

                                $qty += $subqty1_old;
                                $amount += $subamount1_old;
                                $total_balance += $balance_total_old1;
                            }
                            foreach ($mergeInvoice as $key => $item) {
                                $invoices = new \stdClass;
                                if($item['table'] == "cash_invoice_items"){
                                    $invoices->item_name = "";
                                    $invoices->type = "SALES RECEIPT";
                                    $invoices->date = Carbon::parse($item->dt)->toFormattedDateString();
                                    $invoices->ref = $item->cash_invoice_number;
                                    $invoices->memo = "";
                                    $invoices->source = $item->cash_inv->customer_belong->customer_name;
                                    $invoices->qty = number_format($item->cash_invoice_qty,2);
                                    $invoices->um = $childrenitem->unit_measure;
                                    $invoices->price = number_format($item->unit_price,2);
                                    $invoices->amount = number_format($item->amount,2);
                                    $invoices->balance = number_format($balance_total1 += $item->amount,2);
                                    $invoices->space = ($i*2);
                                     $subqty1 +=$item->cash_invoice_qty;
                                }else{
                                    $invoices->item_name = "";
                                    $invoices->type = "INVOICE";
                                    $invoices->date = Carbon::parse($item->dt)->toFormattedDateString();
                                    $invoices->ref = $item->charge_invoice_number;
                                    $invoices->memo = "";
                                    $invoices->source = $item->ci->customer_belong->customer_name;
                                    $invoices->qty = number_format($item->charge_invoice_qty,2);
                                    $invoices->um = $childrenitem->unit_measure;
                                    $invoices->price = number_format($item->unit_price,2);
                                    $invoices->amount = number_format($item->amount,2);
                                    $invoices->balance = number_format($balance_total1 += $item->amount,2);
                                    $invoices->space = ($i*2);
                                     $subqty1 +=$item->charge_invoice_qty;
                                }

                                $itemArr[] = $invoices;

                                $subamount1 +=$item->amount;
                            }
                            
                            $subqty+=$subqty1;
                            $subamount+=$subamount1;

                            $amount +=$subamount;
                            $qty +=$subqty;
                        }//endchildren
                        
                        // $qty+=$subqty1;
                        // $amount+=$subamount1;
                        // $total_balance = 0;   
                        $i--;
                        $head = new \stdClass;
                        $head->item_name = "Total ".$item->item_name;
                        $head->type = "";
                        $head->date = "";
                        $head->source = "";
                        $head->ref = "";
                        $head->memo = "";
                        $head->qty = number_format($qty,2);
                        $head->um = "";
                        $head->price = "";
                        $head->amount = number_format($amount,2);
                        $head->balance = number_format($balance_total+$total_balance_old,2);
                        $head->key ="total";
                        $head->space = ($i*2);
                        $itemArr[] = $head;
                        // dump($item->item_name."=".$subqty);
                    }
                }
                $itemtype = new \stdClass;
                $itemtype->item_name = "Total ".$type->item_type_name;
                $itemtype->type = "";
                $itemtype->date = "";
                $itemtype->ref = "";
                $itemtype->memo = "";
                $itemtype->source = "";
                $itemtype->qty = number_format($qty,2);
                $itemtype->um = "";
                $itemtype->price = "";
                $itemtype->amount = number_format($amount,2);
                $itemtype->balance = number_format($total_balance,2);
                $itemtype->space = 0;
                $itemtype->key ="total";
                $itemArr[] = $itemtype;
            }
            return collection::make($itemArr);
    }

    public function sales_report_detail(){

        $name = Request::get('item_name');

        $dates = explode(" - ", Request::get('daterange'));
        
        $item_type = Item_type::with(['item_type'=>function($q) use($name){
            $q->where(function($s) {
                $s->has("invoice_item")->orHas("children.invoice_item");
                $s->orHas('cash_invoice_item');
                $s->orHas('children.cash_invoice_item');
                if(Request::has('daterange')){
                    $s->orHas("invoice_item_old")->orHas("children.invoice_item_old");
                    $s->orHas('cash_invoice_item_old');
                    $s->orHas('children.cash_invoice_item_old');
                }
            })
            ->orderBy('item_type_id','ASC')
            ->orderBy('item_name', 'ASC')
            // ->where('is_sub',0)
            ->where(function($q){
                if(Request::has('item_name')){
                    $q->where('item_name','like','%'.Request::get('item_name').'%');
                }
            });
        },
        'item_type.children'=>function($q) use($name){
            $q->where(function($s) {
                $s->has("invoice_item")->orHas("cash_invoice_item");
            })
            ->orderBy('item_type_id','ASC')
            ->orderBy('item_name', 'ASC');
        },

        'item_type.invoice_item'=>function($q) use($dates){
            if(Request::has('daterange')){
                $q->whereBetween('dt',[$dates[0],$dates[1]]);
            }
        },

        "item_type.invoice_item.ci",
        "item_type.invoice_item.ci.customer_belong",

        'item_type.cash_invoice_item'=>function($q) use($dates){
            if(Request::has('daterange')){
                $q->whereBetween('dt',[$dates[0],$dates[1]]);
            }
        },
        "item_type.cash_invoice_item.cash_inv",
        "item_type.cash_invoice_item.cash_inv.customer_belong",

        "item_type.children.cash_invoice_item"=>function($q) use($dates){
            if(Request::has('daterange')){
                $q->whereBetween('dt',[$dates[0],$dates[1]]);
            }
        },
        "item_type.children.cash_invoice_item.cash_inv",
        "item_type.children.cash_invoice_item.cash_inv.customer_belong",

        "item_type.children.invoice_item"=>function($q) use($dates){
            if(Request::has('daterange')){
                $q->whereBetween('dt',[$dates[0],$dates[1]]);
            }
        },
        "item_type.children.invoice_item_old"=>function($q) use($dates){
               
            if(Request::has('daterange')){
                $q->whereDate('dt','<',$dates[0]);
            }
        },
        'item_type.invoice_item_old'=>function($q) use($dates){
            if(Request::has('daterange')){
                $q->whereDate('dt','<',$dates[0]);
            }
        },
        'item_type.cash_invoice_item_old'=>function($q) use($dates){
            if(Request::has('daterange')){
                $q->whereDate('dt','<',$dates[0]);
            }
        },
        "item_type.children.cash_invoice_item_old"=>function($q) use($dates){
            if(Request::has('daterange')){
                $q->whereDate('dt','<',$dates[0]);
            }
        },

        "item_type.children.invoice_item.ci",
        "item_type.children.invoice_item.ci.customer_belong",

        ])->where(function($q) {

            $q->has("item_type.invoice_item")
                ->orHas("item_type.children.invoice_item")
                ->orHas("item_type.cash_invoice_item")
                ->orHas("item_type.children.cash_invoice_item");
            if(Request::has('daterange')){
                $q->has("item_type.children.invoice_item_old")
                    ->orHas("item_type.invoice_item_old")
                    ->orHas("item_type.cash_invoice_item_old")
                    ->orHas("item_type.children.cash_invoice_item_old");
            }
            })

            ->get();  

        // if(Request::has('daterange')){
        //     $item_type->load([
                   

        //         ]);
        // }
        $inv = $this->sales_report_itemDetails($item_type);
       
    

        return Datatables::of($inv)
        ->editColumn('item_name', function ($item) {
                if(!isset($item->key)){
                    return '<h4 style="margin-left:'.$item->space.'em">'.$item->item_name.'</h4>';
                }else{
                    return '<p style="margin-left:'.$item->space.'em">'.$item->item_name.'</p>';
                }
                
            })

        ->setRowClass(function ($item) {

            return (isset($item->key) ? 'total-border' : '');
        })
        ->make(true);
    }

    private function sales_report_custDetails($customers){

        $itemArr = [];
        $qty_total = 0;
        $amount_total = 0;
        $balance_overall = 0;
        foreach ($customers as  $customer) {
            
                $qty = 0;
                $amount = 0;
                $total_balance = 0;

                $qty_old = 0;
                $amount_old = 0;
                $total_balance_old = 0;

                $itemtype = new \stdClass;
                $itemtype->cust_name = $customer->customer_name;
                $itemtype->type = "";
                $itemtype->date = "";
                $itemtype->source = "";
                $itemtype->ref = "";
                $itemtype->memo = "";
                $itemtype->qty = "";
                $itemtype->um = "";
                $itemtype->price = "";
                $itemtype->amount = "";
                $itemtype->balance = "";
                $itemtype->space = 0;
                $itemArr[] = $itemtype;

                $ci = collect($customer->ci);
                $invoices = $ci->merge($customer->invoice);
                $rows = $invoices->sortBy('dt');
                if(Request::has('daterange')){
                    $ci = collect($customer->ci);
                    $rows_old = collect($customer->ci_old)->merge($customer->old)->sortBy('dt');
                  
                    foreach ($rows_old as $data_old) {
                        $amount_old += $data_old->invoice->sum('amount');
                        foreach ($data_old->invoice as $item_old) {

                            $total_balance_old += ($data_old['table']=="cash_invoices")? $item_old->amount : $item_old->amount;
                            $qty_old += ($data_old['table']=="cash_invoices")? $item_old->cash_invoice_qty : $item_old->charge_invoice_qty;
                            
                            
                        }

                    }
                    $qty_total +=$qty_old;
                    $balance_overall+=$total_balance_old;
                    $amount_total += $amount_old;

                }
                foreach ($rows as $key => $data) {
                    
                    
                    if($data['table']=="cash_invoices"){
                        foreach ($data->invoice as $ci_item) {
                            $trans = new \stdClass;
                            $trans->cust_name = "";
                           
                            $trans->type = "SALES RECEIPT";
                            $trans->date = Carbon::parse($data->dt)->toFormattedDateString();
                            $trans->source = $ci_item->item->item_name;
                            $trans->ref = $data->cash_invoice_number;
                            $trans->memo = "";
                            $trans->qty = number_format($ci_item->cash_invoice_qty,2);
                            $trans->um = "";
                            $trans->price = number_format($ci_item->unit_price,2);
                            $trans->amount = number_format($ci_item->amount,2);
                            $trans->balance = number_format($total_balance += $ci_item->amount,2);
                            $trans->space = 0;
                            $itemArr[] = $trans;

                            $qty += $ci_item->cash_invoice_qty;
                        }
                    }else{
                        foreach ($data->invoice as $invoice_item) {
                            $trans = new \stdClass;
                            $trans->cust_name = "";
                           
                            $trans->type ="INVOICE";
                            $trans->date = Carbon::parse($data->dt)->toFormattedDateString();
                            $trans->source = $invoice_item->item->item_name;
                            $trans->ref = $data->charge_invoice_number;
                            $trans->memo = "";
                            $trans->qty = number_format($invoice_item->charge_invoice_qty,2);
                            $trans->um = "";
                            $trans->price = number_format($invoice_item->unit_price,2);
                            $trans->amount = number_format($invoice_item->amount,2);
                            $trans->balance = number_format($total_balance += $invoice_item->amount,2);
                            $trans->space = 0;
                            $itemArr[] = $trans;

                            $qty += $invoice_item->charge_invoice_qty;
                        }
                    }

                    
                    $amount += $data->invoice->sum('amount');
                   
                    
                }
                $qty_total += $qty;
                $amount_total +=$amount;
                $balance_overall +=$total_balance;
                $itemtype = new \stdClass;
                $itemtype->cust_name = "Total ".$customer->customer_name;
                $itemtype->type = "";
                $itemtype->date = "";
                $itemtype->ref = "";
                $itemtype->memo = "";
                $itemtype->source = "";
                $itemtype->qty = number_format($qty+$qty_old,2);
                $itemtype->um = "";
                $itemtype->price = "";
                $itemtype->amount = number_format($amount+$amount_old,2);
                $itemtype->balance = number_format($total_balance+$total_balance_old,2);
                $itemtype->space = 0;
                $itemtype->key ="total";
                $itemArr[] = $itemtype;


        }
            $itemtype = new \stdClass;
            $itemtype->cust_name = "<h2><b>Total</b></h2>";
            $itemtype->type = "";
            $itemtype->date = "";
            $itemtype->ref = "";
            $itemtype->memo = "";
            $itemtype->source = "";
            $itemtype->qty = number_format($qty_total,2);
            $itemtype->um = "";
            $itemtype->price = "";
            $itemtype->amount = number_format($amount_total,2);
            $itemtype->balance = number_format($balance_overall,2);
            $itemtype->space = 0;
            $itemtype->key ="total";
            $itemArr[] = $itemtype;
        return collection::make($itemArr);
    }

    public function sales_report_cust(){
        $dates = explode(' - ',Request::get('daterange'));

        $customers = Customer::with([

            'ci'=>function($q) use($dates){
                if(Request::has('daterange')){
                    $q->whereBetween('dt',[$dates[0],$dates[1]]);
                }
            },
            'ci.ci_item',
            'ci.ci_item.item',
            'invoice'=>function($q) use($dates){
                if(Request::has('daterange')){
                    $q->whereBetween('dt',[$dates[0],$dates[1]]);
                }
            },
            'invoice.invoice',
            'invoice.invoice.item'

        ])
        ->where(function($q){
            $q->has('invoice')->orHas('ci');
        })
        ->orderBy('customer_name','ASC')
        ->where(function($q){
            if(Request::has('item_name')){
                $q->where('customer_name','like','%'.Request::get('item_name').'%');
            }
        })
        ->get();
        if(Request::has('daterange')){
            $customers->load([
                'ci_old'=>function($q) use($dates){
                    $q->whereDate('dt','<',$dates[0]);
                },
                'old'=>function($q) use($dates){
                    $q->whereDate('dt','<',$dates[0]);
                },

                ]);
        }
        $custDetail = $this->sales_report_custDetails($customers);
        return Datatables::of($custDetail)

        ->editColumn('cust_name', function ($vendor) {
            if(!isset($vendor->key)){
                return '<h3>'.$vendor->cust_name.'</h3>';
            }else{
                return $vendor->cust_name;
            }
            
        })
   
        ->setRowClass(function ($vendor) {

            return (isset($vendor->key) ? 'total-border' : '');
        })
        ->make(true);
    }

    private function sales_report_custSummary($customers){
        $itemArr = [];
        
        $total_balance = 0;

        foreach ($customers as $customer) { //supplier
            
            $balance = 0;

            $balance_old = 0;
            if(Request::has('daterange')){
                foreach ($customer->ci_old as $ci_old) {
                    $balance_old += $ci_old->invoice->sum('amount');
                }
                foreach ($customer->old as $invoice_old) {
                    $balance_old += $invoice_old->invoice->sum('amount');
                }

                $total_balance +=$balance_old;
            }

            foreach ($customer->ci as $ci) {
                $balance += $ci->invoice->sum('amount');
            }
            foreach ($customer->invoice as $invoice) {
                $balance += $invoice->invoice->sum('amount');
            }
            $total_balance +=$balance;
            $cust = new \stdClass;
            $cust->cust_name = $customer->customer_name;
            $cust->balance =number_format($balance,2);
           
            $itemArr[] = $cust;
        } //supplier

        $cust = new \stdClass;
        $cust->cust_name = "TOTAL:";
        $cust->balance =number_format($total_balance,2);
        $cust->key ="total";
        $itemArr[] = $cust;

        return collection::make($itemArr);
    }

    public function sales_report_cust_summary(){
        $dates = explode(' - ',Request::get('daterange'));

        $customers = Customer::with([

            'ci'=>function($q) use($dates){
                if(Request::has('daterange')){
                    $q->whereBetween('dt',[$dates[0],$dates[1]]);
                }
            },
            'ci.ci_item',
            'ci.ci_item.item',
            'invoice'=>function($q) use($dates){
                if(Request::has('daterange')){
                    $q->whereBetween('dt',[$dates[0],$dates[1]]);
                }
            },
            'invoice.invoice',
            'invoice.invoice.item'

        ])
        ->where(function($q){
            $q->has('invoice')->orHas('ci');
        })
        ->orderBy('customer_name','ASC')
        ->where(function($q){
            if(Request::has('item_name')){
                $q->where('customer_name','like','%'.Request::get('item_name').'%');
            }
        })
        ->get();
        if(Request::has('daterange')){
            $customers->load([
                'ci_old'=>function($q) use($dates){
                    $q->whereDate('dt','<',$dates[0]);
                },
                'old'=>function($q) use($dates){
                    $q->whereDate('dt','<',$dates[0]);
                },

                ]);
        }
        $summary = $this->sales_report_custSummary($customers);
        return Datatables::of($summary)

        ->editColumn('cust_name', function ($customer) {
            if(isset($customer->key)){
                return '<h3>'.$customer->cust_name.'</h3>';
            }else{
                return $customer->cust_name;
            }
            
        })
   
        ->setRowClass(function ($customer) {

            return (isset($customer->key) ? 'total-border1' : '');
        })
        ->make(true);

    }
    public function sales_report(){
        return view('reports.sales_report.sales_report');

    }


    public function recievable_report(){
        
       
        return view('reports.receivable.recievable');
    }

    public function customer_balance_detail(){
        $name = Request::get('item_name');
        $date = explode(' - ', Request::get('daterange'));
        $customers = Customer::where(function($q) {
          $q->has("invoice");
        })
        ->where(function($q){
            if(Request::has('item_name')){
                $q->where('customer_name','like','%'.Request::get('item_name').'%');
            }
        })
        ->with(['invoice' => function($q) use($date){
                if(Request::has('daterange')){
                    $q->whereBetween('dt',[$date[0],$date[1]]);
                }
            },'invoice.invoice'])
        ->orderBy('customer_name','ASC')
        ->get();

        if(Request::has('daterange')){
            $customers->load(['old'=>function($q) use($date){
                $q->whereDate('dt','<',$date[0]);
            }]);
        }

        $data = $this->customer_balance_detail_data($customers);

        return Datatables::of($data)
            ->editColumn('cust_name', function ($customer) {
                if(isset($customer->key)){
                    return '<h3>'.$customer->cust_name.'</h3>';
                }else{
                    return $customer->cust_name;
                }

            })
            ->setRowClass(function ($customer) {
                if(isset($customer->key)){
                    return "total-border1";
                }elseif(isset($customer->subkey)){
                    return "total-border1";
                }
               
            })
            ->make(true);
        

    }
    private function customer_balance_detail_data($customers){
        $itemArr = [];
        
        $total_balance = 0;
        $total_amount = 0;
        $old_bal = 0;
        $old_amount = 0;

        foreach ($customers as $customer) { //cust
                $balance = 0;
                $amount = 0;
                $bal_old = 0;
                $amount_old = 0;
           
                $cust = new \stdClass;
                $cust->cust_name = $customer->customer_name;
                $cust->type = "";
                $cust->date = "";
                $cust->ref = "";
                $cust->class = "";
                $cust->account = "";
                $cust->amount = "";
                $cust->balance ="";
                $cust->key ="total";
                $itemArr[] = $cust;
                // $invs = $customer->load(['invoice'=>function($q) use($from,$to){
                //     $q->whereBetween('dt',[$from,$to]);
                // }]);

                // debug($customer->invoice()->where('dt',"<",$from)->get());
                if(Request::has('daterange')){
                    foreach ($customer->old as $invoice_data_old) {
                        $bal_old += $invoice_data_old->invoice->sum('amount');
                        $amount_old += $invoice_data_old->invoice->sum('amount');
                        $bal_old += -$invoice_data_old->payments->sum('payment');
                        $amount_old += -$invoice_data_old->payments->sum('payment');
                    }
                }
                foreach ($customer->invoice as $key => $invoice_data) {
                    $balance += $invoice_data->invoice->sum('amount');
                    $amount += $invoice_data->invoice->sum('amount');
                    $trans = new \stdClass;
                    $trans->cust_name = "";
                    // if($invoice_data['table'] == "charge_invoices"){
                    //     $type = "INVOICE";
                    // }elseif($invoice_data['table'] == "general_journal"){
                    //     $type = "general journal";
                    // }else{
                    //     $type = "payment";
                    // }
                    $trans->type = "Invoice";
                    $trans->date = $invoice_data->dt;
                    $trans->ref = $invoice_data->charge_invoice_number;
                    $trans->class = $invoice_data->dept;
                    $trans->account = "Accounts Receivable";
                    $trans->amount = number_format($invoice_data->invoice->sum('amount'),2);
                    $trans->balance =number_format($balance,2);
                    $itemArr[] = $trans;

                    foreach ($invoice_data->payments as $payment) {
                        $balance += -$payment->payment;
                        $amount += -$payment->payment;
                        $trans = new \stdClass;
                        $trans->cust_name = "";
                        $trans->type = "Payment";
                        $trans->date = $payment->dt;
                        $trans->ref = $payment->or_number;
                        $trans->class = "";
                        $trans->account = "Accounts Receivable";
                        $trans->amount = "(".number_format($payment->payment,2).")";
                        $trans->balance =number_format($balance,2);
                        $itemArr[] = $trans;
                    }
                }


                $old_bal +=$bal_old;
                $old_amount +=$amount_old;
                $cust = new \stdClass;
                $cust->cust_name = "Total ".$customer->customer_name;
                $cust->type = "";
                $cust->date = "";
                $cust->ref = "";
                $cust->class = "";
                $cust->account = "";
                $cust->amount = number_format($amount+$amount_old,2);
                $cust->balance =number_format($balance+$bal_old,2);
                $cust->subkey ="totalsub";
                $itemArr[] = $cust;
          
            $total_amount += $amount;
            $total_balance += $balance;
            
        } //supplier

        if(count($customers)> 0){
            $cust = new \stdClass;
            $cust->cust_name = "TOTAL";
            $cust->type = "";
            $cust->date = "";
            $cust->ref = "";
            $cust->class = "";
            $cust->account = "";
            $cust->amount = number_format($total_amount+$old_amount,2);
            $cust->balance =number_format($total_balance+$old_bal,2);
            $cust->key ="overall";
            $itemArr[] = $cust;
        }

        return collection::make($itemArr);
    }

    public function customer_balance_summary(){
        $date = explode(' - ', Request::get('daterange'));
        $customers = Customer::where(function($q) {
          $q->has("invoice");
        })
        ->where(function($q){
            if(Request::has('item_name')){
                $q->where('customer_name','like','%'.Request::get('item_name').'%');
            }
        })
        ->with(['invoice'=>function($q) use($date){
            if(Request::has('daterange')){
                $q->whereBetween('dt',[$date[0],$date[1]]);
            }
        },'invoice.invoice'])
        ->orderBy('customer_name','ASC')
        ->get();

        if(Request::has('daterange')){
            $old = Customer::where(function($q) {
              $q->has("invoice");
            })
            ->where(function($q){
                if(Request::has('item_name')){
                    $q->where('customer_name','like','%'.Request::get('item_name').'%');
                }
            })
            ->with(['invoice'=>function($q) use($date){
                if(Request::has('daterange')){
                    $q->where('dt','<',$date[0]);
                }
            },'invoice.invoice'])
            ->orderBy('customer_name','ASC')
            ->get();

        }else{
            $old = [];
        }
        $data = $this->customer_balance_summary_data($customers,$old);

        return Datatables::of($data)
            ->editColumn('cust_name', function ($customer) {
                if(isset($customer->key)){
                    return '<h3>'.$customer->cust_name.'</h3>';
                }else{
                    return $customer->cust_name;
                }

            })
            ->setRowClass(function ($customer) {
                if(isset($customer->key)){
                    return "total-border1";
                }elseif(isset($customer->subkey)){
                    return "total-border1";
                }
               
            })
            ->make(true);
        

    }

    private function customer_balance_summary_data($customers,$old){
        $itemArr = [];
        
        $total_balance = 0;
        $balance = 0;
        $old_bal = 0;
        if(count($old) > 0){
            foreach ($old as $old_cust) {
                foreach ($old_cust->invoice as $invoice_data1) {
                    $old_bal += $invoice_data1->invoice->sum('amount');
                    $old_bal += -$invoice_data1->payments->sum('payment');
                }
            }
        }


        foreach ($customers as $customer) { //supplier

            foreach ($customer->invoice as $key => $invoice_data) {
                $balance += $invoice_data->invoice->sum('amount');
                $balance += -$invoice_data->payments->sum('payment');

            }
            $cust = new \stdClass;
            $cust->cust_name = $customer->customer_name;
            $cust->balance =number_format($balance,2);
           
            $itemArr[] = $cust;
        } //supplier

        if(count($customers)>0){
            $cust = new \stdClass;
            $cust->cust_name = "TOTAL:";
            $cust->balance =number_format($balance+$old_bal,2);
            $cust->key ="total";
            $itemArr[] = $cust;
        }
        return collection::make($itemArr);
    }

    public function customer_aging_detail(){
   
        $date = explode(' - ',Request::get('daterange'));

        $ci = Charge_invoice::whereHas('customer_belong', function ($query){
                if(Request::has('item_name')){
                    $query->where('customer_name', 'like', '%'.Request::get('item_name').'%');
                }
        })
        ->where(function($q) use($date){
            if(Request::has('daterange')){
                $q->whereBetween('dt',[$date[0],$date[1]]);
            }
        })->get();

        $ci_old = Charge_invoice::whereHas('customer_belong', function ($query){
                if(Request::has('item_name')){
                    $query->where('customer_name', 'like', '%'.Request::get('item_name').'%');
                }
        })
        ->where(function($q) use($date){
            if(Request::has('daterange')){
                $q->whereDate('dt',"<",$date[0]);
            }
        })->get();

        $ci_payments = Payment::whereHas('customer_belong', function ($query){
            if(Request::has('item_name')){
                $query->where('customer_name', 'like', '%'.Request::get('item_name').'%');
            }
           
        })
        ->where(function($q) use($date){
            if(Request::has('daterange')){
                $q->whereDate('dt',"<",$date[0]);
            }
        })->get();

        $ci_payments_old = Payment::whereHas('customer_belong', function ($query){
            if(Request::has('item_name')){
                $query->where('customer_name', 'like', '%'.Request::get('item_name').'%');
            }
           
        })
        ->where(function($q) use($date){
            if(Request::has('daterange')){
                $q->whereBetween('dt',[$date[0],$date[1]]);
            }
        })->get();
       
        $agingData = collect($ci)->merge($ci_payments)->sortBy('dt');

        $agingDataOld = collect($ci_old)->merge($ci_payments_old)->sortBy('dt');

        
        $data = $this->customer_aging_detail_data($agingData,$agingDataOld);
        
        return Datatables::of($data)
            // ->filter(function ($query) {

            //        $query->collection->where('type', "Invoice");

               
            // })
            // ->editColumn('cust_name', function ($customer) {
            //     if(isset($customer->key)){
            //         return '<h3>'.$customer->cust_name.'</h3>';
            //     }else{
            //         return $customer->cust_name;
            //     }

            // })
            // ->setRowClass(function ($customer) {
            //     if(isset($customer->key)){
            //         return "total-border1";
            //     }elseif(isset($customer->subkey)){
            //         return "total-border1";
            //     }
               
            // })
            ->make(true);
    }

    private function customer_aging_detail_data($agingData,$agingDataOld){

            $itemArr = [];
                
            // current
                $currentbalance = 0;
                $old_current_bal = 0;

                $aging = new \stdClass;
                $aging->age = "Current";
                $aging->type = "";
                $aging->date = "";
                $aging->ref = "";
                $aging->po = "";
                $aging->name = "";
                $aging->terms = "";
                $aging->duedate = "";
                $aging->class ="";
                $aging->aging ="";
                $aging->balance ="";
                $aging->key ="head";
                $itemArr[] = $aging;


                if(Request::has('daterange')){
                    foreach ($agingDataOld as $current) {
                        $dt = Carbon::parse($current->dt);
                        if($dt->diffInDays(Carbon::now()) >= 61 && $dt->diffInDays(Carbon::now()) <= 90){
                            ($$current['table']=="charge_invoices")? 
                                $old_current_bal += $$current->invoice->sum('amount')
                            : 
                                $old_current_bal += -$$current->payment;
                        }

                    }
                }
                foreach ($agingData as $key => $current_ci_data) {

                    $dt = Carbon::parse($current_ci_data->dt);
                    if($dt->diffInDays(Carbon::now()) == 0){

                        ($current_ci_data['table']=="charge_invoices")? 
                            $currentbalance += $current_ci_data->invoice->sum('amount')
                        : 
                            $currentbalance += -$current_ci_data->payment;
                        $dt = Carbon::parse($current_ci_data->dt);
                        $aging = new \stdClass;
                        $aging->age = "";
                        $aging->type = ($current_ci_data['table']=="charge_invoices")? "Invoice": "Payment";
                        $aging->date = $current_ci_data->dt;
                        $aging->ref = ($current_ci_data['table']=="charge_invoices")? $current_ci_data->charge_invoice_number: $current_ci_data->or_number;
                        $aging->po = "";
                        $aging->name = $current_ci_data->customer_belong->customer_name;
                        $aging->terms = "";
                        $aging->duedate = $current_ci_data->dt;
                        $aging->class =($current_ci_data['table']=="charge_invoices")? $current_ci_data->dept : "";
                        $aging->aging = $dt->diffInDays(Carbon::now());
                        $aging->balance = ($current_ci_data['table']=="charge_invoices")? number_format($current_ci_data->invoice->sum('amount')): "(".number_format($current_ci_data->payment,2).")";
                       
                        $itemArr[] = $aging;

                    }
                }
                $aging = new \stdClass;
                $aging->age = "Total Current";
                $aging->type = "";
                $aging->date = "";
                $aging->ref = "";
                $aging->po = "";
                $aging->name = "";
                $aging->terms = "";
                $aging->duedate = "";
                $aging->class ="";
                $aging->aging ="";
                $aging->balance =number_format($currentbalance+$old_current_bal,2);
                $aging->key ="foot";
                $itemArr[] = $aging;

            // Current End
            
            // 1-30 start
                $balance = 0;
                $amount = 0;
                $old_bal =0;
             

                $aging = new \stdClass;
                $aging->age = "1-30";
                $aging->type = "";
                $aging->date = "";
                $aging->ref = "";
                $aging->po = "";
                $aging->name = "";
                $aging->terms = "";
                $aging->duedate = "";
                $aging->class ="";
                $aging->aging ="";
                $aging->balance ="";
                $aging->key ="head";
                $itemArr[] = $aging;
                if(Request::has('from')){
                    foreach ($agingDataOld as $old_data) {
                        $dt = Carbon::parse($old_data->dt);
                        if($dt->diffInDays(Carbon::now()) >= 1 && $dt->diffInDays(Carbon::now()) <= 30){
                            ($old_data['table']=="charge_invoices")? 
                                $old_bal += $old_data->invoice->sum('amount')
                            : 
                                $old_bal += -$old_data->payment;
                        }

                    }
                }
                foreach ($agingData as $key => $ci_data) {
                    $dt = Carbon::parse($ci_data->dt);
                    if($dt->diffInDays(Carbon::now()) >= 1 && $dt->diffInDays(Carbon::now()) <= 30){

                        ($ci_data['table']=="charge_invoices")? 
                            $balance += $ci_data->invoice->sum('amount')
                        : 
                            $balance += -$ci_data->payment;
                        
                        $aging = new \stdClass;
                        $aging->age = "";
                        $aging->type = ($ci_data['table']=="charge_invoices")? "Invoice": "Payment";
                        $aging->date = $ci_data->dt;
                        $aging->ref = ($ci_data['table']=="charge_invoices")? $ci_data->charge_invoice_number: $ci_data->or_number;
                        $aging->po = "";
                        $aging->name = $ci_data->customer_belong->customer_name;
                        $aging->terms = "";
                        $aging->duedate = $ci_data->dt;
                        $aging->class =($ci_data['table']=="charge_invoices")? $ci_data->dept : "";
                        $aging->aging = $dt->diffInDays(Carbon::now());
                        $aging->balance = ($ci_data['table']=="charge_invoices")? number_format($ci_data->invoice->sum('amount')): "(".number_format($ci_data->payment).")";
                        
                        $itemArr[] = $aging;

                    }
                }
                $aging = new \stdClass;
                $aging->age = "Total 1-30";
                $aging->type = "";
                $aging->date = "";
                $aging->ref = "";
                $aging->po = "";
                $aging->name = "";
                $aging->terms = "";
                $aging->duedate = "";
                $aging->class ="";
                $aging->aging ="";
                $aging->balance =number_format($balance+$old_bal,2);
                $aging->key ="foot";
                $itemArr[] = $aging;
            // 1-30 end

            // 31-60 start
                $balance31_60 = 0;
                $old_bal60 = 0;

                $aging = new \stdClass;
                $aging->age = "31-60";
                $aging->type = "";
                $aging->date = "";
                $aging->ref = "";
                $aging->po = "";
                $aging->name = "";
                $aging->terms = "";
                $aging->duedate = "";
                $aging->class ="";
                $aging->aging ="";
                $aging->balance ="";
                $aging->key ="head";
                $itemArr[] = $aging;
                if(Request::has('from')){
                    foreach ($agingDataOld as $old_data60) {
                        $dt = Carbon::parse($old_data60->dt);
                        if($dt->diffInDays(Carbon::now()) >= 31 && $dt->diffInDays(Carbon::now()) <= 60){
                            ($old_data60['table']=="charge_invoices")? 
                                $old_bal60 += $old_data60->invoice->sum('amount')
                            : 
                                $old_bal60 += -$old_data60->payment;
                        }


                    }
                }
                foreach ($agingData as $key => $ci_data31_60) {
                    $dt = Carbon::parse($ci_data31_60->dt);
                    if($dt->diffInDays(Carbon::now()) >= 31 && $dt->diffInDays(Carbon::now()) <= 60){

                        ($ci_data31_60['table']=="charge_invoices")? 
                            $balance31_60 += $ci_data31_60->invoice->sum('amount')
                        : 
                            $balance31_60 += -$ci_data31_60->payment;
                        
                        $aging = new \stdClass;
                        $aging->age = "";
                        $aging->type = ($ci_data31_60['table']=="charge_invoices")? "Invoice": "Payment";
                        $aging->date = $ci_data31_60->dt;
                        $aging->ref = ($ci_data31_60['table']=="charge_invoices")? $ci_data31_60->charge_invoice_number: $ci_data31_60->or_number;
                        $aging->po = "";
                        $aging->name = $ci_data31_60->customer_belong->customer_name;
                        $aging->terms = "";
                        $aging->duedate = $ci_data31_60->dt;
                        $aging->class =($ci_data31_60['table']=="charge_invoices")? $ci_data31_60->dept : "";
                        $aging->aging = $dt->diffInDays(Carbon::now());
                        $aging->balance = ($ci_data31_60['table']=="charge_invoices")? number_format($ci_data31_60->invoice->sum('amount')): "(".number_format($ci_data31_60->payment).")";
                        
                        $itemArr[] = $aging;

                    }
                }
                $aging = new \stdClass;
                $aging->age = "Total 31-60";
                $aging->type = "";
                $aging->date = "";
                $aging->ref = "";
                $aging->po = "";
                $aging->name = "";
                $aging->terms = "";
                $aging->duedate = "";
                $aging->class ="";
                $aging->aging ="";
                $aging->balance =number_format($balance31_60+$old_bal60,2);
                $aging->key ="foot";
                $itemArr[] = $aging;
            // 1-30 end

            // 61-90 start
                $balance61_90 = 0;
                $amount = 0;
                $old_bal90 = 0;

                $aging = new \stdClass;
                $aging->age = "61-90";
                $aging->type = "";
                $aging->date = "";
                $aging->ref = "";
                $aging->po = "";
                $aging->name = "";
                $aging->terms = "";
                $aging->duedate = "";
                $aging->class ="";
                $aging->aging ="";
                $aging->balance ="";
                $aging->key ="head";
                $itemArr[] = $aging;
                if(Request::has('from')){
                    foreach ($agingDataOld as $old_data90) {
                        $dt = Carbon::parse($old_data90->dt);
                        if($dt->diffInDays(Carbon::now()) >= 61 && $dt->diffInDays(Carbon::now()) <= 90){
                            ($old_data90['table']=="charge_invoices")? 
                                $old_bal90 += $old_data90->invoice->sum('amount')
                            : 
                                $old_bal90 += -$old_data90->payment;
                        }

                    }
                }
                foreach ($agingData as $key => $ci_data61_90) {
                    $dt = Carbon::parse($ci_data61_90->dt);
                    if($dt->diffInDays(Carbon::now()) >= 61 && $dt->diffInDays(Carbon::now()) <= 90){

                        ($ci_data61_90['table']=="charge_invoices")? 
                            $balance61_90 += $ci_data61_90->invoice->sum('amount')
                        : 
                            $balance61_90 += -$ci_data61_90->payment;
                        
                        $aging = new \stdClass;
                        $aging->age = "";
                        $aging->type = ($ci_data61_90['table']=="charge_invoices")? "Invoice": "Payment";
                        $aging->date = $ci_data61_90->dt;
                        $aging->ref = ($ci_data61_90['table']=="charge_invoices")? $ci_data61_90->charge_invoice_number: $ci_data61_90->or_number;
                        $aging->po = "";
                        $aging->name = $ci_data61_90->customer_belong->customer_name;
                        $aging->terms = "";
                        $aging->duedate = $ci_data61_90->dt;
                        $aging->class =($ci_data61_90['table']=="charge_invoices")? $ci_data61_90->dept : "";
                        $aging->aging = $dt->diffInDays(Carbon::now());
                        $aging->balance = ($ci_data61_90['table']=="charge_invoices")? number_format($ci_data61_90->invoice->sum('amount')): "(".number_format($ci_data61_90->payment).")";
                        
                        $itemArr[] = $aging;

                    }
                }
                $aging = new \stdClass;
                $aging->age = "Total 61-90";
                $aging->type = "";
                $aging->date = "";
                $aging->ref = "";
                $aging->po = "";
                $aging->name = "";
                $aging->terms = "";
                $aging->duedate = "";
                $aging->class ="";
                $aging->aging ="";
                $aging->balance =number_format($balance61_90+$old_bal90,2);
                $aging->key ="foot";
                $itemArr[] = $aging;
            // 61-90 end

            // >90 start
                $balance90 = 0;
                $amount = 0;
                $old_bal90over =0;

                $aging = new \stdClass;
                $aging->age = "> 90";
                $aging->type = "";
                $aging->date = "";
                $aging->ref = "";
                $aging->po = "";
                $aging->name = "";
                $aging->terms = "";
                $aging->duedate = "";
                $aging->class ="";
                $aging->aging ="";
                $aging->balance ="";
                $aging->key ="head";
                $itemArr[] = $aging;

                if(Request::has('from')){
                    foreach ($agingDataOld as $old_data90over) {
                        $dt = Carbon::parse($old_data90->dt);
                        if($dt->diffInDays(Carbon::now()) > 90 ){
                            ($old_data90over['table']=="charge_invoices")? 
                                $old_bal90over += $old_data90over->invoice->sum('amount')
                            : 
                                $old_bal90over += -$old_data90over->payment;
                        }

                    }
                }
                foreach ($agingData as $key => $ci_data90) {
                    $dt = Carbon::parse($ci_data90->dt);
                    if($dt->diffInDays(Carbon::now()) > 90 ){

                        ($ci_data90['table']=="charge_invoices")? 
                            $balance90 += $ci_data90->invoice->sum('amount')
                        : 
                            $balance90 += -$ci_data90->payment;
                        
                        $aging = new \stdClass;
                        $aging->age = "";
                        $aging->type = ($ci_data90['table']=="charge_invoices")? "Invoice": "Payment";
                        $aging->date = $ci_data90->dt;
                        $aging->ref = ($ci_data90['table']=="charge_invoices")? $ci_data90->charge_invoice_number: $ci_data90->or_number;
                        $aging->po = "";
                        $aging->name = $ci_data90->customer_belong->customer_name;
                        $aging->terms = "";
                        $aging->duedate = $ci_data90->dt;
                        $aging->class =($ci_data90['table']=="charge_invoices")? $ci_data90->dept : "";
                        $aging->aging = $dt->diffInDays(Carbon::now());
                        $aging->balance = ($ci_data90['table']=="charge_invoices")? number_format($ci_data90->invoice->sum('amount')): "(".number_format($ci_data90->payment).")";
                        
                        $itemArr[] = $aging;

                    }
                }
                $aging = new \stdClass;
                $aging->age = "Total > 90";
                $aging->type = "";
                $aging->date = "";
                $aging->ref = "";
                $aging->po = "";
                $aging->name = "";
                $aging->terms = "";
                $aging->duedate = "";
                $aging->class ="";
                $aging->aging ="";
                $aging->balance =number_format($balance90+$old_bal90over,2);
                $aging->key ="foot";
                $itemArr[] = $aging;
            // >90 end


            return collect($itemArr);
    }


    public function customer_aging_summary(){
        $date = explode(' - ',Request::get('daterange'));

        $customers = Customer::has('invoice')
            ->with(['invoice'=>function($q) use($date){
                if(Request::has('daterange')){
                    $q->where('dt','>=',$date[0]);
                    $q->where('dt','<=',$date[1]);
                }
            },'invoice.invoice','invoice.payments'])
            ->where(function($q){
                if(Request::has('item_name')){
                    $q->where('customer_name','like','%'.Request::get('item_name').'%');
                }
            })
            ->get();
       
      
        if(Request::has('daterange')){
            $customers->load(['old'=>function($q) use($date){
                $q->where('dt','<',$date[0]);
            }]);
        }
        
        $data = $this->customer_aging_summary_data($customers);
        
        return Datatables::of($data)
            ->editColumn('name', function ($customer) {
                return '<h3>'.$customer->name.'</h3>';
               
            })
            ->setRowClass(function ($customer) {
                if(isset($customer->key)){
                    return "total-border1";
                }
               
            })
            ->make(true);
    }

    private function customer_aging_summary_data($data){
        $itemArr = [];
        
        $overall = 0;
        foreach ($data as $customer) {
            $total = 0;
            $old_total = 0;
            $current = 0;
            $amount30 = 0;
            $amount60 = 0;
            $amount90 = 0;
            $amountover90 = 0;
            $aging = new \stdClass;
            $aging->name = $customer->customer_name;
            
            if(Request::has('daterange')){
                foreach ($customer->old as $old_invoice) {
                    $old_total += $old_invoice->invoice->sum('amount') - $old_invoice->payments->sum('payment');
                }
            }

            foreach ($customer->invoice as $invoice) {
                $dt = Carbon::parse($invoice->dt);
                
                $total += $invoice->invoice->sum('amount') - $invoice->payments->sum('payment');

                if($dt->diffInDays(Carbon::now()) == 0){
                    $current = $invoice->invoice->sum('amount') - $invoice->payments->sum('payment');
                  
                }
                if($dt->diffInDays(Carbon::now()) >= 1 && $dt->diffInDays(Carbon::now()) <= 30){
                    $amount30 = $invoice->invoice->sum('amount') - $invoice->payments->sum('payment');
                  
                }

                if($dt->diffInDays(Carbon::now()) >= 31 && $dt->diffInDays(Carbon::now()) <= 60){
                    $amount60 = $invoice->invoice->sum('amount') - $invoice->payments->sum('payment');
                    
                }

                if($dt->diffInDays(Carbon::now()) >= 61 && $dt->diffInDays(Carbon::now()) <= 90){
                    $amount90 = $invoice->invoice->sum('amount') - $invoice->payments->sum('payment');
                    
                }
                if($dt->diffInDays(Carbon::now()) >= 91 ){
                    $amountover90 = $invoice->invoice->sum('amount') - $invoice->payments->sum('payment');
                  
                }

            }
            $overall += $total+$old_total;
            $aging->current = number_format($current,2);
            $aging->aging1_30 = number_format($amount30,2);
            $aging->aging31_60 = number_format($amount60,2);
            $aging->aging61_90 = number_format($amount90,2);
            $aging->aging90 = number_format($amountover90,2);
            $aging->total = number_format($total+$old_total,2);
            $itemArr[] = $aging;
        }
        if(count($data) > 0){
            $aging = new \stdClass;
            $aging->name = "Total";
            $aging->current = "";
            $aging->aging1_30 = "";
            $aging->aging31_60 = "";
            $aging->aging61_90 = "";
            $aging->aging90 = "";
            $aging->total = number_format($overall,2);
            $aging->key ="foot";
            $itemArr[] = $aging;
        }
        return collect($itemArr);
    }

    public function ap_vendor_view(){
        return view('reports.ap');
    }

    public function ap_aging_summary(){
        $date = explode(' - ', Request::get('daterange'));

        function data($data){
            $itemArr = [];
        
            $overall = 0;
            foreach ($data as $vendor) {
                $total = 0;
                $old_total = 0;
                $current = 0;
                $amount30 = 0;
                $amount60 = 0;
                $amount90 = 0;
                $amountover90 = 0;
                $aging = new \stdClass;
                $aging->name = $vendor->supplier_name;
                
                if(Request::has('daterange')){
                    foreach ($vendor->voucher_old as $old_voucher) {
                        $old_total += $old_voucher->amount - $old_voucher->voucher_payments->sum('payment');
                    }
                }

                foreach ($vendor->voucher as $voucher) {
                    $dt = Carbon::parse($voucher->dt);
                    
                    $total += $voucher->amount - $voucher->voucher_payments->sum('payment');;

                    if($dt->diffInDays(Carbon::now()) == 0){
                        $current += $voucher->amount - $voucher->voucher_payments->sum('payment');
                      
                    }
     
                    if($dt->diffInDays(Carbon::now()) >= 1 && $dt->diffInDays(Carbon::now()) <= 30){
                        $amount30 += $voucher->amount - $voucher->voucher_payments->sum('payment');
                      
                    }

                    if($dt->diffInDays(Carbon::now()) >= 31 && $dt->diffInDays(Carbon::now()) <= 60){
                        $amount60 += $voucher->amount - $voucher->voucher_payments->sum('payment');
                        
                    }

                    if($dt->diffInDays(Carbon::now()) >= 61 && $dt->diffInDays(Carbon::now()) <= 90){
                        $amount90 += $voucher->amount - $voucher->voucher_payments->sum('payment');
                        
                    }
                    if($dt->diffInDays(Carbon::now()) >= 91 ){
                        $amountover90 += $voucher->amount - $voucher->voucher_payments->sum('payment');
                      
                    }

                }
                $overall += $total+$old_total;
                $aging->current = number_format($current,2);
                $aging->aging1_30 = number_format($amount30,2);
                $aging->aging31_60 = number_format($amount60,2);
                $aging->aging61_90 = number_format($amount90,2);
                $aging->aging90 = number_format($amountover90,2);
                $aging->total = number_format($total+$old_total,2);
                $itemArr[] = $aging;
            }
            if(count($data) > 0){
                $aging = new \stdClass;
                $aging->name = "<h3>Total</h3>";
                $aging->current = "";
                $aging->aging1_30 = "";
                $aging->aging31_60 = "";
                $aging->aging61_90 = "";
                $aging->aging90 = "";
                $aging->total = "<h3>".number_format($overall,2)."</h3>";
                $aging->key ="foot";
                $itemArr[] = $aging;
            }
            return collect($itemArr);
        }



        $vendor = Supplier::has('voucher')
            ->with([
                'voucher'=>function($q) use($date)
                {

                    if(Request::has('daterange')){
                        $q->where('dt','>=',$date[0]);
                        $q->where('dt','<=',$date[1]);
                    }

                },
                'voucher.voucher_payments'=>function($q) use($date)
                {

                    if(Request::has('daterange')){
                        $q->where('dt','>=',$date[0]);
                        $q->where('dt','<=',$date[1]);
                    }

                },
                'voucher.voucher_item'
            ])
            ->where(function($q){

                if(Request::has('item_name')){
                    $q->where('supplier_name','like','%'.Request::get('item_name').'%');
                }

            })
            ->get();
      
     
        if(Request::has('daterange'))
        {
            $vendor->load([
                'voucher_old'=>function($q) use($date)
                {
                    $q->where('dt','<',$date[0]);
                },
                
                'voucher_old.voucher_payments'=>function($q) use($date)
                {

                    if(Request::has('daterange')){
                       $q->where('dt','<',$date[0]);
                    }

                }
            ]);
        }
        
        $data = data($vendor);
        
        return Datatables::of($data)
            ->editColumn('name', function ($vendor) 
            {
                return '<h4>'.$vendor->name.'</h4>';
               
            })
            ->setRowClass(function ($vendor) 
            {

                if(isset($vendor->key)){
                    return "total-border1";
                }
               
            })
            ->make(true);
    }

    public function vendor_balance_detail(){
        $date = explode(' - ', Request::get('daterange'));

        function data($data)
        {
            $itemArr = [];
            $balance = 0;
            foreach ($data as $vendor) 
            {
                $sub_balance = 0;
                $old_current_bal = 0;
                $old_bal= 0;
                $detail = new \stdClass;
                $detail->name = "<h3>".$vendor->supplier_name."</h3>";
                $detail->type = "";
                $detail->date = "";
                $detail->ref = "";
                $detail->duedate = "";
                $detail->aging ="";
                $detail->balance ="";
                $detail->key ="head";
                $itemArr[] = $detail;
                $payments = [];
                
                if(Request::has('daterange')){
                    foreach ($vendor->voucher_old as $old_voucher) {
                        $old_bal += $old_voucher->amount - $old_voucher->voucher_payments->sum('payment');
                    }
                }



                foreach ($vendor->voucher as $voucher) 
                {
                    $x = 0;
                    foreach ($voucher->voucher_payments as $payment) 
                    {
                        $payments[] = $payment;
                        $payments[$x]->keys = "payments";
                        $x++;
                    }
                }

                $transactions = collect($vendor->voucher)->merge($payments)->sortBy('dt');

                foreach ($transactions as $transaction) 
                {

                    
                    (isset($transaction->keys))? $sub_balance -= $transaction->payment : $sub_balance +=$transaction->amount;
                    $detail = new \stdClass;
                    $detail->name = "";
                    $detail->type = (isset($transaction->keys))? "Payment" :"Bill";
                    $detail->date = Carbon::parse($transaction->dt)->toDateString();
                    $detail->ref = "CV".$transaction->voucher_number;
                    $detail->duedate = (!isset($transaction->keys))? Carbon::parse($transaction->bill_due)->toDateString():"";
                    $detail->aging = Carbon::parse($transaction->dt)->diffInDays(Carbon::now());;
                    $detail->balance = (!isset($transaction->keys))? number_format($transaction->amount):"(".number_format($transaction->payment).")";
                    $itemArr[] = $detail;
                }

                $balance +=$sub_balance + $old_bal;
                $detail = new \stdClass;
                $detail->name = "Total ".$vendor->supplier_name;
                $detail->type = "";
                $detail->date = "";
                $detail->ref = "";
                $detail->duedate = "";
                $detail->aging ="";
                $detail->balance = number_format($sub_balance + $old_bal,2);
                $detail->key ="foot";
                $itemArr[] = $detail;


            }
            if(count($data) > 1)
            {
                $detail = new \stdClass;
                $detail->name = "<h2>TOTAL</h2>";
                $detail->type = "";
                $detail->date = "";
                $detail->ref = "";
                $detail->duedate = "";
                $detail->aging ="";
                $detail->balance = "<h2>".number_format($balance,2)."</h2>";
                $detail->key ="foot";
                $itemArr[] = $detail;
            }
            return collect($itemArr);
        }

        $vendor = Supplier::has('voucher')
            ->with([
                'voucher'=>function($q) use($date)
                {

                    if(Request::has('daterange')){
                        $q->where('dt','>=',$date[0]);
                        $q->where('dt','<=',$date[1]);
                    }

                },
                'voucher.voucher_payments'=>function($q) use($date)
                {

                    if(Request::has('daterange')){
                        $q->where('dt','>=',$date[0]);
                        $q->where('dt','<=',$date[1]);
                    }

                },
                'voucher.voucher_item'
            ])
            ->where(function($q){

                if(Request::has('item_name')){
                    $q->where('supplier_name','like','%'.Request::get('item_name').'%');
                }

            })
            ->get();
      
     
        if(Request::has('daterange'))
        {
            $vendor->load([
                'voucher_old'=>function($q) use($date)
                {
                    $q->where('dt','<',$date[0]);
                },

                'voucher_old.voucher_payments'=>function($q) use($date)
                {

                    if(Request::has('daterange')){
                       $q->where('dt','<',$date[0]);
                    }

                }
            ]);
        }

        $data = data($vendor);

        return Datatables::of($data)
 
            ->setRowClass(function ($vendor) 
            {

                if(isset($vendor->key)){
                    return "total-border1";
                }
               
            })
            ->make(true);


    }

    public function vendor_balance_summary(){
        $date = explode(' - ',Request::get('daterange'));


        function data($data)
        {
            $itemArr = [];
        
            $total_balance = 0;
            $balance = 0;
            $old_bal = 0;
         


            foreach ($data as $vendor) 
            { //supplier
                $sub_balance = 0;

                if(Request::has('daterange')){

                    foreach ($vendor->voucher_old as $old_voucher) {
                        // debug($old_voucher->voucher_payments);
                        $old_bal += $old_voucher->amount - $old_voucher->voucher_payments->sum('payment');
                    }
                }
                foreach ($vendor->voucher as $voucher) 
                {   
                    debug($voucher->voucher_payments);
                    $sub_balance += $voucher->amount - $voucher->voucher_payments->sum('payment');
                }
                $total_balance +=$sub_balance;
                $vend = new \stdClass;
                $vend->name = $vendor->supplier_name;
                $vend->balance =number_format($sub_balance,2);
               
                $itemArr[] = $vend;
            } //supplier

            if(count($data)>0)
            {
                $vend = new \stdClass;
                $vend->name = "<h3><b>TOTAL:</b></h3>";
                $vend->balance ="<h3><b>".number_format($total_balance+$old_bal,2)."</b></h3>";
                $vend->key ="total";
                $itemArr[] = $vend;
            }
            return collection::make($itemArr);
        }

        $vendor = Supplier::has('voucher')
            ->with([

            'voucher'=>function($q) use($date)
            {

                if(Request::has('daterange')){
                    $q->where('dt','>=',$date[0]);
                    $q->where('dt','<=',$date[1]);
                }

            },
            'voucher.voucher_payments'=>function($q) use($date)
            {

                if(Request::has('daterange')){
                    $q->where('dt','>=',$date[0]);
                    $q->where('dt','<=',$date[1]);
                }

            },
            'voucher.voucher_item'])
            ->where(function($q){

                if(Request::has('item_name')){
                    $q->where('supplier_name','like','%'.Request::get('item_name').'%');
                }

            })
            ->get();
      
     
        if(Request::has('daterange'))
        {
            $vendor->load([
                'voucher_old'=>function($q) use($date)
                {
                    $q->where('dt','<',$date[0]);
                },

                'voucher_old.voucher_payments'=>function($q) use($date)
                {

                    if(Request::has('daterange')){
                       $q->where('dt','<',$date[0]);
                    }

                }
            ]);
        }
        $data = data($vendor);

        return Datatables::of($data)
            ->editColumn('name', function ($vendor) {
                if(isset($customer->key)){
                    return '<h3>'.$vendor->name.'</h3>';
                }else{
                    return $vendor->name;
                }

            })
            ->setRowClass(function ($vendor) {
                if(isset($vendor->key)){
                    return "total-border1";
                }elseif(isset($vendor->subkey)){
                    return "total-border1";
                }
               
            })
            ->make(true);
      
    }
    public function balance_sheet_view(){
        return view('reports.balancesheets.balance_sheet');
    }

    public function balance_sheet_data()

    {   

        $i=0;
        // debug(Request::get('daterange'));
        function balancesheet($datas,$parent = 0){
            $coa_arr = collect();
            static $i = 1;

            if(count($datas->where('is_sub',$parent))>0)
            {
                $coas =$datas->where('is_sub',$parent); 
            }else{
                $coas = $datas;
            }
            $coas->load('children','transactions','transactions2');
            if(Request::has('date1'))
            {   
                $coas->load(['transactions'=>function($q){
                    
                        $date = carbon::parse(Request::get('date1'));
                        $date_year2 = Carbon::parse('last day of January '.  $date->year)->toDateString();
                        $q->whereBetween('dt',[$date_year2,$date->toDateString()]);
                    }
                ]);
            }
            if(Request::has('date2'))
            {   
                $coas->load(['transactions2'=>function($q){
                        $date = carbon::parse(Request::get('date2'));
                        $date_year2 = Carbon::parse('last day of January '. $date->year)->toDateString();
                        $q->whereBetween('dt',[$date_year2,$date->toDateString()]);
                    }
                ]);
            }
            $i++;
            foreach ($coas as $key => $coa) {
                $account = new \stdClass;
                $account->data = $coa;
                $account->space = $i*2;
                // $account->balance = $coa->transactions->sum('debit') - $coa->transactions->sum('credit');
                // $account->body = "head";
              
                $coa_arr[] = $account;

                if(count($coa->children)>0){
                   $child =  balancesheet($coa->children, $coa->id);

                    if($child) {
                        $i++;
                        
                        
                        $coa_arr = collect($coa_arr)->merge($child);
                        $i--;
                       
                    }
                    $account = new \stdClass;
                    $account->data = $coa;
                    $account->body ="foot";
                    $account->space = $i*2;
                    $coa_arr[] = $account;   
                }
            }
            $i--;

            return $coa_arr;
        }
        //query
        $detailtype = Detail_type::with([
            'chartofaccount.transactions'=>function($q) {
                if(Request::has('date1')) {   
                    $date = carbon::parse(Request::get('date1'));
                    $date_year = Carbon::parse('last day of January '. $date->year)->toDateString();
                    $q->whereBetween('dt',[$date_year,$date->toDateString()]);
                }
            },'chartofaccount.transactions2'=>function($q){
                if(Request::has('date2')){   
                    $date2 = carbon::parse(Request::get('date2'));
                    $date_year2 = Carbon::parse('last day of January '. $date2->year)->toDateString();
                    $q->whereBetween('dt',[$date_year2,$date2->toDateString()]);
                }

            },'chartofaccount'=>function($q){
               

                if(Request::has('search')){
                    $q->where('coa_title','like','%'.Request::get('search').'%');
                }
            },'chartofaccount.sub.transactions',
        ])
        ->get();
        $retain_query = $detailtype->where('type','expenses')->merge($detailtype->where('type','revenue'));
        $coa_array = [];
        $space = 0;
        $detail = $detailtype->whereIn('type',['asset','liability','equity'])->groupBy('type');

        $total_liabity_equity = 0;
        $total_liabity_equity2 = 0;
       

        foreach ($detail as $key => $type) {
            $space ++;
            $trans = new \stdClass;
            $trans->name = ucfirst(str_plural($key));
            $trans->keys = "type";
            $trans->space = 0;
            $trans->amount ="";
            $trans->amount2 ="";
            $coa_array[] = $trans;
            $sum_detail_type = 0;
            $sum_detail_type2 = 0;
            foreach ($type as $key1 => $detailtype){

                $space ++;
                $trans = new \stdClass;
                $trans->name = $detailtype->detail_type_name;
                $trans->keys = "detail_type";
                $trans->space = $space*1;
                $trans->amount ="";
                $trans->amount2 ="";
                $coa_array[] = $trans;

                $sum_coa = 0;
                $sum_coa2 = 0;
                $earnings = 0;
                $earnings2 = 0;

                foreach (balancesheet($detailtype->chartofaccount) as $coa) {
                 
                    if(!isset($coa->body)){
                        if($key == "asset"){
                            $trans = new \stdClass;
                            if(count($coa->data->children) == 0){
                                $trans->amount = number_format($coa->data->transactions->sum('debit')-$coa->data->transactions->sum('credit'),2);
                                if(Request::has('date2')){

                                    $trans->amount2 = number_format($coa->data->transactions2->sum('debit')-$coa->data->transactions2->sum('credit'),2);
                                }else{
                                    $trans->amount2 ="";
                                }
                               
                            }
                            $trans->name = $coa->data->coa_title;
                            $trans->space = $coa->space;
                            $coa_array[] = $trans;
                        }else{
                            if($coa->data->coa_title != "Retained Earnings"){
                                $trans = new \stdClass;
                                if(count($coa->data->children) == 0){
                                    $trans->amount = number_format($coa->data->transactions->sum('credit')-$coa->data->transactions->sum('debit'),2);
                                    if(Request::has('date2')){

                                        $trans->amount2 = number_format($coa->data->transactions2->sum('credit')-$coa->data->transactions2->sum('debit'),2);
                                    }else{
                                        $trans->amount2 ="";
                                    }
                                   
                                }
                                $trans->name = $coa->data->coa_title;
                                $trans->space = $coa->space;
                                $coa_array[] = $trans;
                            }
                        }
                    }else{

                        if($key == "asset"){

                            foreach ($coa->data->children as $key123 => $coa_trans) {
                                $sum_coa += $coa_trans->transactions->sum('debit')-$coa_trans->transactions->sum('credit');
                            }

                            $trans = new \stdClass;
                            $trans->name = "Total ".$coa->data->coa_title;
                            $trans->total ="total";
                            $trans->amount =number_format($sum_coa,2);
                            if(Request::has('date2')){

                                foreach ($coa->data->children as $coa_trans1) {
                                    $sum_coa2 += $coa_trans1->transactions2->sum('debit')-$coa_trans1->transactions2->sum('credit');
                                }
                                $trans->amount2 =number_format($sum_coa2,2);
                            }else{
                                $trans->amount2 ="";
                            }
                            
                            $trans->space = $coa->space;
                            $coa_array[] = $trans;
                        }else{
                            foreach ($coa->data->children as $key123 => $coa_trans) {
                                $sum_coa += $coa_trans->transactions->sum('credit')-$coa_trans->transactions->sum('debit');
                            }

                            $trans = new \stdClass;
                            $trans->name = "Total ".$coa->data->coa_title;
                            $trans->total ="total";
                            $trans->amount =number_format($sum_coa,2);
                            if(Request::has('date2')){

                                foreach ($coa->data->children as $coa_trans1) {
                                    $sum_coa2 += $coa_trans1->transactions2->sum('credit')-$coa_trans1->transactions2->sum('debit');
                                }
                                $trans->amount2 =number_format($sum_coa2,2);
                            }else{
                                $trans->amount2 ="";
                            }
                            
                            $trans->space = $coa->space;
                            $coa_array[] = $trans;
                        }
                    }
                }
                if($key == "equity"){
                                                            
                    foreach ($retain_query as $detail) {
           
                        foreach ($detail->chartofaccount as $coa_acc) {
                           
                            $earnings += $coa_acc->transactions->sum('credit')-$coa_acc->transactions->sum('debit');
                            if(Request::has('date2')){
                                $earnings2 += $coa_acc->transactions2->sum('credit')-$coa_acc->transactions2->sum('debit');
                            }
                        }
                    }
                    $trans = new \stdClass;
                    // if(count($coa->data->children) == 0){
                        $trans->amount = number_format($earnings,2);
                        if(Request::has('date2')){

                            $trans->amount2 = number_format($earnings2,2);
                        }else{
                            $trans->amount2 ="";
                        }
                       
                    // }
                    $trans->name = "Retained Earnings";
                    $trans->space = $space+2;
                    $coa_array[] = $trans; 
                }
                $sumdetail = 0;
                $sumdetail2 = 0;

                if($key == "asset"){
                    foreach ($detailtype->chartofaccount as $chartofaccount) {
                        $sumdetail += $chartofaccount->transactions->sum('debit') - $chartofaccount->transactions->sum('credit');
                    }

                    if(Request::has('date2')){
                        foreach ($detailtype->chartofaccount as $chartofaccount) {
                            $sumdetail2 += $chartofaccount->transactions2->sum('debit') - $chartofaccount->transactions2->sum('credit');
                        }
                        $sum_detail_type2 += $sumdetail2 + $earnings2;
                    }
                }else{
                    foreach ($detailtype->chartofaccount as $chartofaccount) {
                        $sumdetail += $chartofaccount->transactions->sum('credit') - $chartofaccount->transactions->sum('debit');
                    }

                    if(Request::has('date2')){
                        
                        foreach ($detailtype->chartofaccount as $chartofaccount) {
                            $sumdetail2 += $chartofaccount->transactions2->sum('credit') - $chartofaccount->transactions2->sum('debit');
                        }
                        $sum_detail_type2 += $sumdetail2 + $earnings2;
                    }
                }
                


                $sum_detail_type += $sumdetail+$earnings;

                if($key != "asset"){
                    $total_liabity_equity +=$sumdetail+$earnings;
                    $total_liabity_equity2 += $sumdetail2+$earnings2;
                }
                //Total detail type
                $trans = new \stdClass;
                $trans->name = "Total ".$detailtype->detail_type_name;
                $trans->keys = "detail_type";
                $trans->total ="total";
                $trans->space = $space*1;
                $trans->amount =number_format($sumdetail+$earnings,2);
              
                if(Request::has('date2')){  
                    $trans->amount2 = number_format($sumdetail2+$earnings2,2);
                }
                $coa_array[] = $trans;                
                $space --;
            }


          
            //Total type
            $trans = new \stdClass;
            $trans->name = "Total ".ucfirst(str_plural($key));
            $trans->keys = "type";
            $trans->space = 0;
            $trans->total ="total";
            $trans->amount = number_format($sum_detail_type,2);
            if(Request::has('date2')){  
                $trans->amount2 = number_format($sum_detail_type2,2);
            }
            $coa_array[] = $trans;
            $space --;
        }

        $trans = new \stdClass;
        $trans->name =  "Total Liabilities And Equities";
        $trans->amount = number_format($total_liabity_equity,2);
        $trans->keys = "type";
        $trans->total ="total";
        if(Request::has('date2')){

            $trans->amount2 =number_format($total_liabity_equity2,2);
        }else{
            $trans->amount2 ="";
        }
        $coa_array[] = $trans;

        return response()->json(['results'=>$coa_array]);
        
    }

    public function bs_collapse(){

        function collapse($datas,$parent = 0){
            static $i = 1;
            $collapse_arr = collect();
            if(count($datas->where('is_sub',$parent))>0){
                $coas = $datas->where('is_sub',$parent); 
            }else{
                $coas = $datas;
            }
            $coas->load('children','transactions','transactions2');
            foreach ($coas as $key => $data) {

                $trans = new \stdClass;
                $trans->data = $data;
                $collapse_arr[] = $trans;
                if($data->children){ 
                    if(count($data->children)>0){
                       $child =  collapse($data->children, $data->id);

                        if($child){
                            $i++;
                            $collapse_arr = collect($collapse_arr)->merge($child);
                            $i--;
                          
                        }
                          
                    }
                }
            }


            return $collapse_arr;
        }
        $detailtype = Detail_type::with(['chartofaccount.transactions'=>function($q) {
                if(Request::has('date1')) {   
                    $date = carbon::parse(Request::get('date1'));
                    $date_year = Carbon::parse('last day of January '. $date->year)->toDateString();
                    $q->whereBetween('dt',[$date_year,$date->toDateString()]);
                }
            },'chartofaccount.transactions2'=>function($q){
                if(Request::has('date2')){   
                    $date2 = carbon::parse(Request::get('date2'));
                    $date_year2 = Carbon::parse('last day of January '. $date2->year)->toDateString();
                    $q->whereBetween('dt',[$date_year2,$date2->toDateString()]);
                }

            },'chartofaccount'=>function($q){

                if(Request::has('search')){
                    $q->where('coa_title','like','%'.Request::get('search').'%');
                }
            },'chartofaccount.children',
        ])
        ->get();

        $retain_query = $detailtype->where('type','expenses')->merge($detailtype->where('type','revenue'));

        $coa_array = [];
        $space = 0;
        $details = $detailtype->whereIn('type',['asset','liability','equity'])->groupBy('type');
        $total_equity_liability1 = 0;
        $total_equity_liability2 = 0;
        $retain_earning1 = 0;
        $retain_earning2 = 0;
        foreach ($details as $key => $detail) {
            $space++;
            $trans = new \stdClass;
            $trans->name = ucfirst(str_plural($key));
            $trans->keys = "type";
            $trans->space = 0;
            $trans->amount ="";
            $trans->amount2 = "";
            $coa_array[] = $trans;
            $total_type1 = 0;
            $total_type2 = 0;
            foreach ($detail as $detail_data) {
                $trans = new \stdClass;
                $trans->name = ucfirst($detail_data->detail_type_name);
                $trans->keys = "detail_type";
                $trans->space = $space*2;
                $trans->amount = "";
                $trans->amount2 = "";
                $coa_array[] = $trans;
                $space++;
                $sum_detail1 = 0;
                $sum_detail2 = 0;
                foreach($detail_data->chartofaccount->where('is_sub',0) as $coa_data){

                    $sum = 0;
                    $sum1 = 0;
                    if($key != "asset"){
                        $sum += $coa_data->transactions->sum('credit') -$coa_data->transactions->sum('debit');
                        $sum1 += $coa_data->transactions2->sum('credit')-$coa_data->transactions2->sum('debit');
                       
                        foreach (collapse($coa_data->children,$coa_data->id) as $coa_child ) {
                               
                            $sum += $coa_child->data->transactions->sum('credit') - $coa_child->data->transactions->sum('debit');
                            
                            $sum1 += $coa_child->data->transactions2->sum('credit') - $coa_child->data->transactions2->sum('debit');
 
                         
                           
                        }
                        
                        $total_equity_liability1 += $sum;
                        $total_equity_liability2 += $sum1;
                    }else{
                        $sum += $coa_data->transactions->sum('debit') -$coa_data->transactions->sum('credit');
                  
                        $sum1 += $coa_data->transactions2->sum('debit')-$coa_data->transactions2->sum('credit');
                    

                        
                        foreach (collapse($coa_data->children,$coa_data->id) as $coa_child ) {
                               
                            $sum += $coa_child->data->transactions->sum('debit') - $coa_child->data->transactions->sum('credit');
                            $sum1 += $coa_child->data->transactions2->sum('debit') - $coa_child->data->transactions2->sum('credit');

                        }
                    }


                    $sum_detail1+=$sum;
                    $sum_detail2+=$sum1;
                    if($coa_data->coa_title != "Retained Earnings"){
                        $trans = new \stdClass;
                        $trans->name = ucfirst($coa_data->coa_title);
                        $trans->space = $space*2;
                        $trans->amount = number_format($sum,2);
                        $trans->amount2 = number_format($sum1,2);
                        $coa_array[] = $trans;
                    }
                    if($key == "equity"){
                        if($coa_data->coa_title == "Retained Earnings"){
                            foreach ($retain_query as $retain_detail) {
                           
                                foreach ($retain_detail->chartofaccount as $coa_acc) {
                                   
                                    $retain_earning1 += $coa_acc->transactions->sum('credit')-$coa_acc->transactions->sum('debit');
                                    $retain_earning2 += $coa_acc->transactions2->sum('credit')-$coa_acc->transactions2->sum('debit');
                                    // if(Request::has('date2')){
                                    //     $earnings2 += $coa_acc->transactions2->sum('credit')-$coa_acc->transactions2->sum('debit');
                                    // }
                                }
                            }


                        
                        
                            $retain_earning1 += $coa_data->transactions->sum('credit')-$coa_data->transactions->sum('debit');
                            $retain_earning2 += $coa_data->transactions2->sum('credit')-$coa_data->transactions2->sum('debit');
                            $trans = new \stdClass;
                            $trans->name = $coa_data->coa_title;
                            $trans->space = $space*2;
                            $trans->amount = number_format($retain_earning1,2);
                            $trans->amount2 = number_format($retain_earning2,2);
                            $coa_array[] = $trans;

                            $total_equity_liability1+=$retain_earning1;
                            $total_equity_liability2+=$retain_earning2;
                        }

                        

                        
                    }
                }
                $space--;
                $total_type1+=$sum_detail1;
                $total_type2+=$sum_detail2;
                $trans = new \stdClass;
                $trans->name = "Total ".ucfirst($detail_data->detail_type_name);
                $trans->keys = "detail_type";
                $trans->total ="total";
                $trans->space = $space*2;
                $trans->amount = number_format($sum_detail1,2);
                $trans->amount2 = number_format($sum_detail2,2);
                $coa_array[] = $trans;
            }
            $trans = new \stdClass;
            $trans->name = "Total ".ucfirst(str_plural($key));
            $trans->keys = "type";
            $trans->space = 0;
            $trans->total ="total";
            $trans->amount = number_format($total_type1,2);
            $trans->amount2 = number_format($total_type2,2);
            $coa_array[] = $trans;
            $space--;
        }

        $trans = new \stdClass;
        $trans->name = "Total Liability And Equity";
        $trans->keys = "type";
        $trans->space = 0;
        $trans->total ="total";
        $trans->amount = number_format($total_equity_liability1,2);
        $trans->amount2 = number_format($total_equity_liability2,2);
        $coa_array[] = $trans;
        return response()->json(['results'=>$coa_array]);
    }
    public function balance_sheet_detail_view(){
        return view('reports.balancesheets.balance_sheet_detail');
    }

    public function balance_sheet_detail_data()
    {

        $detailtype = Detail_type::with([
            'chartofaccount.transactions',
            'chartofaccount.transactions2',
            'chartofaccount',
        ])
        ->get();

        $coaTypes = $detailtype->whereIn('type',['asset','liability','equity'])->groupBy('type');

        function chartofaccounts($datas,$parent = 0)
        {   

            $coa_arr = collect();
            static $i = 1;
            static $a = 0;
            
            if(count($datas->where('is_sub',$parent))>0)
            {
                 $coas =$datas->where('is_sub',$parent); 
            }else{
                $coas = $datas;
            }
            $coas->load(['children','children.transactions']);
            
            $i++;
            foreach ($coas  as $key => $data) 

            {
               
                $vend = new \stdClass;
                $vend->data = $data;
                $vend->space = $i*1;
                if(count($data->children)>0){
                    $vend->children = "children";
                }
                
                $coa_arr[] = $vend;
               
               
                if($data->children)
                { 


                    if(count($data->children)>0)

                    {
                        
                       
                       
                       $child =  chartofaccounts($data->children, $data->id);

                        if($child)

                        {
                            $i++;
                            
                            
                            $coa_arr = collect($coa_arr)->merge($child);
                            $i--;
                            $vend = new \stdClass;
                            $vend->data = $data;
                            $vend->key="foot";
                            $vend->space = $i*1;
                            $coa_arr[] = $vend;
                           
                          
                        }
                          
                    }
                }
               
            }
            $i--;
            return  collect($coa_arr);

        }


        $coa_array = [];
        $space = 0;
        foreach ($coaTypes as $key => $type) 

        {   
           
            $trans = new \stdClass;
            $trans->account = '<h3>'.ucfirst(str_plural($key)).'</h3>';
            $trans->type = "";
            $trans->date = "";
            $trans->ref = "";
            $trans->name = "";
            $trans->memo = "";
            $trans->dept= "";
            $trans->coa ="";
            $trans->amount = "";
            $trans->balance ="";
            $coa_array[] = $trans;

            foreach ($type as $key1 => $detailtype) 

            {
                $space++;
                $trans = new \stdClass;
                $trans->account = '<h4 style="margin-left:'.($space).'em">'.$detailtype->detail_type_name.'</h4>';
                $trans->type = "";
                $trans->date = "";
                $trans->ref = "";
                $trans->name = "";
                $trans->memo = "";
                $trans->dept= "";
                $trans->coa ="";
                $trans->amount = "";
                $trans->balance ="";
                $coa_array[] = $trans;
               
                foreach (chartofaccounts($detailtype->chartofaccount) as $coa) {
                    
                    
                    if(!isset($coa->key)){
                        $trans = new \stdClass;
                        $trans->account = '<h4 style="margin-left:'.($coa->space).'em">'.ucwords($coa->data->coa_title).'</h4>';
                        $trans->type = "";
                        $trans->date = "";
                        $trans->ref = "";
                        $trans->name = "";
                        $trans->memo = "";
                        $trans->dept= "";
                        $trans->coa ="";
                        $trans->amount = "";
                        $trans->balance ="";
                        $coa_array[] = $trans;
                        foreach ($coa->data->transactions as $key => $coa_transaction) {
                            $supplier ="";  
                            if(!empty($coa_transaction->coa)){
                              $supplier = $coa_transaction->coa->supplier_one->supplier_name;

                            }

                            if(!empty($coa_transaction->coa_check)){
                              $supplier =$coa_transaction->coa_check->voucher->supplier_belong->supplier_name;

                            }
                            $trans = new \stdClass;
                            $trans->account = '';
                            $trans->type = $coa_transaction->type;
                            $trans->date = $coa_transaction->dt;
                            $trans->ref = $coa_transaction->ref;
                            $trans->name = $supplier;
                            $trans->memo = "";
                            $trans->dept= "";
                            $trans->coa ="";
                            $trans->amount = ($coa_transaction->debit != 0) ? $coa_transaction->debit : "(".$coa_transaction->credit.")";
                            $trans->balance ="";
                            $coa_array[] = $trans;
                        }
                        
                       
                    }
                    $trans = new \stdClass;
                        $trans->account = '<h4 style="margin-left:'.($coa->space).'em">Total '.ucwords($coa->data->coa_title).'</h4>';
                        $trans->type = "";
                        $trans->date = "";
                        $trans->ref = "";
                        $trans->name = "";
                        $trans->memo = "";
                        $trans->dept= "";
                        $trans->coa ="";
                        $trans->amount = "";
                        $trans->balance ="";
                        $coa_array[] = $trans;
                    
                   
                }
                $trans = new \stdClass;
                $trans->account = '<h4 style="margin-left:'.($space).'em">Total '.ucwords($detailtype->detail_type_name).'</h4>';
                $trans->type = "";
                $trans->date = "";
                $trans->ref = "";
                $trans->name = "";
                $trans->memo = "";
                $trans->dept= "";
                $trans->coa ="";
                $trans->amount = "";
                $trans->balance ="";
                $coa_array[] = $trans;
                $space--;

            }
           
            $trans = new \stdClass;
            $trans->account =  "<h3>Total ".ucfirst(str_plural($key)).'</h3>';
            $trans->type = "";
            $trans->date = "";
            $trans->ref = "";
            $trans->name = "";
            $trans->memo = "";
            $trans->dept= "";
            $trans->coa ="";
            $trans->amount = "";
            $trans->balance ="";
            $coa_array[] = $trans;
           
        }
    // dump( $coa_array);
        return response()->json(['data'=>$coa_array]);

    }

    public function trialbalance_view(){
        return view('reports.trial_balance.index');   
    }
    public function trial_balance(){

        $search = Request::get('query');
       

        function chartofaccounts($data,$parent,$type){
            
            static $i;
            $datas = collect();
            static $count = 0;
            $data->load([
                    'sub',
                    'transactions'=>function($q) use($type){
                        
                        if($type == 1){
                            if(Request::has('query')){
                             
                                if(Request::has('query.date')){
                                    $date = explode(' - ', Request::get('query')['date']);
                                    $from_date =$date[0];
                                    $to_date = $date[1];

                                    $q->whereBetween('dt',[$from_date,$to_date]);
                                }else{
                                    $q->whereDate('dt','>=',Carbon::now()->toDateString());
                                }

                            }

                        }else{
                            if(Request::has('query.date')){
                                $date = explode(' - ', Request::get('query')['date']);
                                $from_date =$date[0];
                                $to_date = $date[1];
                                $q->whereDate('dt','<=',$to_date);
                            }
                        }
                    },
                    'detailtype'
                ]);
            if(count($data->where('is_sub',$parent)) > 0){

                $query = $data->where('is_sub',$parent);
            }else{

                $query = $data;
            }


            foreach ($query as $coa) {
                
                $i++;
                $trans = new \stdClass;
                $trans = $coa;
                $trans->space = $i;
                if(count($coa->sub) > 0){
                    $trans->key ="parent";
                }else{
                   $trans->key ="child";
                }


                $datas[] = $trans;
               
                   
                   $child =  chartofaccounts($coa->sub, $coa->id,$type);

                    if($child)

                    {
                        $i--;
                        $datas = collect($datas)->merge($child);
                    }
                $count++;
                

            }

            return $datas;
        }
        
        $assets = Chart_of_account::with('sub','transactions','detailtype','parent')
            // ->whereNotIn('detail_type_id',[14,17,15,16])
            ->whereIN('detail_type_id',[5,6,7,8,9,19])
            ->where('coa_title','like','%'.$search['search'].'%')
            ->get();

        $liab = Chart_of_account::with('sub','transactions','detailtype','parent')
            // ->whereNotIn('detail_type_id',[14,17,15,16])
            ->whereIN('detail_type_id',[10,11,12,20])
            ->where('coa_title','like','%'.$search['search'].'%')
            ->get();

        $equity = Chart_of_account::with('sub','transactions','detailtype','parent')
            // ->whereNotIn('detail_type_id',[14,17,15,16])
            ->whereIN('detail_type_id',[13])
            ->where('coa_title','!=','Retained Earnings')
            ->where('coa_title','like','%'.$search['search'].'%')
            ->get();

        $retainEarning = Chart_of_account::with('sub','transactions','detailtype','parent')
            ->where(function($q){
                $q->where('coa_title','Retained Earnings');
            })
            ->get();

        $revExpense = Chart_of_account::with([
                'sub',
                'transactions',
                'detailtype',
                'parent'
            ])
            ->whereIn('detail_type_id',[14,17,15,16,18])
            ->where('coa_title','like','%'.$search['search'].'%')
            ->get();
        $coaList = collect();
        $data1 = chartofaccounts($assets,0,0);
        $data2 = chartofaccounts($liab,0,0);
        $data3 = chartofaccounts($equity,0,0);

        $data4 = chartofaccounts($retainEarning,0,0);
        $data5 = chartofaccounts($revExpense,0,1);

        $coaList = $coaList->merge($data1);
        $coaList = $coaList->merge($data2);
        $coaList = $coaList->merge($data3);
        $coaList = $coaList->merge($data4);
        $coaList = $coaList->merge($data5);

        
        $chartofaccounts = [];
        $indent = 0;
        $total_debit = 0;
        $total_credit =0;
        $earnings = 0;

        $retainQuery = Chart_of_account::with(['transactions'=>function($q){
                if(Request::has('query.date')){
                    $date = explode(' - ', Request::get('query')['date']);
                    $from_date =$date[0];
                    $to_date = $date[1];

                    $q->whereDate('dt','<',$from_date);
                }else{
                    $q->whereDate('dt','<',Carbon::now()->toDateString());
                }
            }])
            // ->whereNotIn('detail_type_id',[14,17,15,16])
            ->whereIN('detail_type_id',[14,17,15,16,18])
            ->get();
        foreach ($coaList as $coa) {
            if(isset($coa->key)){
                if($coa->space == 1){
                    $coa->space = 0;
                }

                
                if($coa->key == "parent"){
                    $trans = new \stdClass;
                    $trans->account_title = $coa->coa_title;
                    $trans->space = $coa->space;
                    if($coa->typical_balance == "DEBIT"){
                        $trans->debit = "--";
                        $trans->credit = "--";
                    }else{
                        $trans->debit = "--";
                        $trans->credit = "--";
                    }
                }else{
                    $trans = new \stdClass;
                    $trans->account_title = $coa->coa_title;
                    $trans->space = $coa->space;
                    if($coa->typical_balance == "DEBIT"){
                        $total_debit +=$coa->transactions->sum('debit') - $coa->transactions->sum('credit');
                        $trans->debit = number_format($coa->transactions->sum('debit') - $coa->transactions->sum('credit'),2);
                        $trans->credit = "--";
                    }else{
                        $total_credit += $coa->transactions->sum('credit') - $coa->transactions->sum('debit');
                        $trans->debit = "--";
                        $trans->credit = number_format($coa->transactions->sum('credit') - $coa->transactions->sum('debit'),2);
                    }
                }
                
                if($coa->coa_title == "Retained Earnings"){
                    foreach ($retainQuery as $retainCoa) {
                        $earnings += $retainCoa->transactions->sum('credit')-$retainCoa->transactions->sum('debit');
                    }
                    $total_credit+=$earnings;
                    $trans = new \stdClass;
                    $trans->account_title = $coa->coa_title;
                    $trans->space = $coa->space;
                    $trans->debit = "--";
                    $trans->credit = number_format($earnings,2);
                }

                $chartofaccounts[] = $trans;
               
            }
        }
        $trans = new \stdClass;
        $trans->account_title = "Total";
        $trans->space = 0;
        $trans->key ="total";
        $trans->debit = number_format($total_debit,2);
        $trans->credit =number_format($total_credit,2);

        $chartofaccounts[] = $trans;
  

      // return Datatables::of(collect($chartofaccounts))
      //   ->filter(function ($instance) {
      //       if(Request::has('search')){
      //           $instance->collection = $instance->collection->filter(function ($row) {
                    
      //               return Str_contains($row->account_title, ucfirst(Request::get('search'))) ? true : false;
      //           });
      //       }
          
      //   })
      //   ->editColumn('account_title', function ($instance) {
            
      //           return "<span style='padding-left:".$instance->space."em'>".$instance->account_title."</span>";
      //       })
      //   ->make(true);
      return response()->json(['data'=>$chartofaccounts]);

    }
    public function profitloss_view(){
        
        return view('reports.profitloss.index');
    }

    public function profitloss(){

        function chartofaccount($datas,$parent = 0){
            $coa_arr = collect();
            static $i = 1;

            if(count($datas->where('is_sub',$parent))>0)
            {
                $coas =$datas->where('is_sub',$parent); 
            }else{
                $coas = $datas;
            }

            $coas->load(['children','children.transactions','transactions']);
            $i++;
            foreach ($coas as  $coa) {
                $account = new \stdClass;
                $account->data = $coa;
                $account->body ="header";
                $account->space = $i*1;
                if(count($coa->children)== 0){
                    $account->child = 1;
                }
                $coa_arr[] = $account;

                if(count($coa->children) > 0){
                    $child =  chartofaccount($coa->children, $coa->id);

                    if($child) {
                        $i++;
                        $coa_arr = collect($coa_arr)->merge($child);
                        $i--;
                       
                    }
                    $account = new \stdClass;
                    $account->data = $coa;
                    $account->body ="footer";
                    $account->space = $i*1;
                    $coa_arr[] = $account;   
                }
            }
            $i--;

            return $coa_arr;
        }

        $coa_array = [];

        $detailtype = Detail_type::with('chartofaccount','chartofaccount.children')->get();

        //ORDINARY INCOME/EXPENSE

        $trans = new \stdClass;
        $trans->name = ucfirst("Income/Expense");
        $trans->keys = "type";
        $trans->body = "header";
        $trans->space = 0;

        $coa_array[] = $trans;

        $space = 0;

        //income
        foreach ($detailtype->whereIn('detail_type_name',['Income','Other Income','Cost of Goods Sold'])as $detail) {
            $space++;

            $trans = new \stdClass;
            $trans->name = ucfirst($detail->detail_type_name);
            $trans->keys = "detail";
            $trans->space = $space;
            $trans->body = "header";
            $coa_array[] = $trans;

            foreach (chartofaccount($detail->chartofaccount) as $chartofaccount) {
                
                if($chartofaccount->body == "header"){
                    
                    if(isset($chartofaccount->child)){
                        $trans = new \stdClass;
                        $trans->name = ucfirst($chartofaccount->data->coa_title);
                        $trans->keys = "detail";
                        $trans->space = $chartofaccount->space;
                        $trans->body = "header";

                       
                        $trans->broiler_amount =number_format($chartofaccount->data->transactions->sum('credit'),2);
                        $trans->broiler_amount2 =number_format(0,2);

                        $trans->admin_amount =number_format(0,2);
                        $trans->admin_amount2 =number_format(0,2);

                        $trans->general_amount =number_format(0,2);
                        $trans->general_amount2 =number_format(0,2);

                        $trans->hatchery_amount =number_format(0,2);
                        $trans->hatchery_amount2 =number_format(0,2);

                        $trans->layer_amount =number_format(0,2);
                        $trans->layer_amount2 =number_format(0,2);

                        $trans->unclassified_amount =number_format(0,2);
                        $trans->unclassified_amount2 =number_format(0,2);

                        $trans->total_amount =number_format(0,2);
                        $trans->total_amount2 =number_format(0,2);
                        $coa_array[] = $trans;
                    }else{
                        $trans = new \stdClass;
                        $trans->name = ucfirst($chartofaccount->data->coa_title);
                        $trans->keys = "detail";
                        $trans->body = "header";
                        $trans->space = $chartofaccount->space;
                        $coa_array[] = $trans;
                    }
                }elseif($chartofaccount->body == "footer"){
                    $trans = new \stdClass;
                    $trans->name = ucfirst("Total ".$chartofaccount->data->coa_title);
                    $trans->keys = "detail";
                    $trans->space = $chartofaccount->space;
                    $trans->body = "footer";
                    $trans->broiler_amount =number_format(0,2);
                    $trans->broiler_amount2 =number_format(0,2);

                    $trans->admin_amount =number_format(0,2);
                    $trans->admin_amount2 =number_format(0,2);

                    $trans->general_amount =number_format(0,2);
                    $trans->general_amount2 =number_format(0,2);

                    $trans->hatchery_amount =number_format(0,2);
                    $trans->hatchery_amount2 =number_format(0,2);

                    $trans->layer_amount =number_format(0,2);
                    $trans->layer_amount2 =number_format(0,2);

                    $trans->unclassified_amount =number_format(0,2);
                    $trans->unclassified_amount2 =number_format(0,2);

                    $trans->total_amount =number_format(0,2);
                    $trans->total_amount2 =number_format(0,2);
                    $coa_array[] = $trans;
                }
               
            }

            $trans = new \stdClass;
            $trans->name = ucfirst("Total ".$detail->detail_type_name);
            $trans->keys = "detail";
            $trans->space = $space;
            $trans->body = "footer";
            $trans->broiler_amount =number_format(0,2);
            $trans->broiler_amount2 =number_format(0,2);

            $trans->admin_amount =number_format(0,2);
            $trans->admin_amount2 =number_format(0,2);

            $trans->general_amount =number_format(0,2);
            $trans->general_amount2 =number_format(0,2);

            $trans->hatchery_amount =number_format(0,2);
            $trans->hatchery_amount2 =number_format(0,2);

            $trans->layer_amount =number_format(0,2);
            $trans->layer_amount2 =number_format(0,2);

            $trans->unclassified_amount =number_format(0,2);
            $trans->unclassified_amount2 =number_format(0,2);

            $trans->total_amount =number_format(0,2);
            $trans->total_amount2 =number_format(0,2);
            $coa_array[] = $trans;

            $space--;
        }
        $trans = new \stdClass;
        $trans->name = ucfirst("Gross Profit");
        $trans->keys = "type";
        $trans->body = "footer";
        $trans->space = 0;
        $trans->broiler_amount =number_format(0,2);
        $trans->broiler_amount2 =number_format(0,2);

        $trans->admin_amount =number_format(0,2);
        $trans->admin_amount2 =number_format(0,2);

        $trans->general_amount =number_format(0,2);
        $trans->general_amount2 =number_format(0,2);

        $trans->hatchery_amount =number_format(0,2);
        $trans->hatchery_amount2 =number_format(0,2);

        $trans->layer_amount =number_format(0,2);
        $trans->layer_amount2 =number_format(0,2);

        $trans->unclassified_amount =number_format(0,2);
        $trans->unclassified_amount2 =number_format(0,2);

        $trans->total_amount =number_format(0,2);
        $trans->total_amount2 =number_format(0,2);
        $coa_array[] = $trans;
        //end income

        //expense
        foreach ($detailtype->whereIn('detail_type_name',['Expense','Other Expense'])as $detail_other) {
            $space++;

            $trans = new \stdClass;
            $trans->name = ucfirst($detail_other->detail_type_name);
            $trans->keys = "detail";
            $trans->space = $space;
            $trans->body = "header";
            $trans->broiler_amount ="";
            $trans->broiler_amount2 ="";

            $trans->admin_amount ="";
            $trans->admin_amount2 ="";

            $trans->general_amount ="";
            $trans->general_amount2 ="";

            $trans->hatchery_amount ="";
            $trans->hatchery_amount2 ="";

            $trans->layer_amount ="";
            $trans->layer_amount2 ="";

            $trans->unclassified_amount ="";
            $trans->unclassified_amount2 ="";

            $trans->total_amount ="";
            $trans->total_amount2 ="";
            $coa_array[] = $trans;

            foreach (chartofaccount($detail_other->chartofaccount) as $chartofaccount1) {
                
                if($chartofaccount->body == "header"){
                    
                    if(isset($chartofaccount1->child)){
                        $trans = new \stdClass;
                        $trans->name = ucfirst($chartofaccount1->data->coa_title);
                        $trans->keys = "detail";
                        $trans->space = $chartofaccount1->space;
                        $trans->body = "header";
                        $trans->broiler_amount =number_format(0,2);
                        $trans->broiler_amount2 =number_format(0,2);
                        $trans->admin_amount =number_format(0,2);
                        $trans->admin_amount2 =number_format(0,2);
                        $trans->general_amount =number_format(0,2);
                        $trans->general_amount2 =number_format(0,2);
                        $trans->hatchery_amount =number_format(0,2);
                        $trans->hatchery_amount2 =number_format(0,2);
                        $trans->layer_amount =number_format(0,2);
                        $trans->layer_amount2 =number_format(0,2);
                        $trans->unclassified_amount =number_format(0,2);
                        $trans->unclassified_amount2 =number_format(0,2);

                        $trans->total_amount =number_format(0,2);
                        $trans->total_amount2 =number_format(0,2);
                        $coa_array[] = $trans;
                    }else{
                        $trans = new \stdClass;
                        $trans->name = ucfirst($chartofaccount1->data->coa_title);
                        $trans->keys = "detail";
                        $trans->space = $chartofaccount1->space;
                        $trans->body = "header";
                        $trans->broiler_amount ="";
                        $trans->broiler_amount2 ="";

                        $trans->admin_amount ="";
                        $trans->admin_amount2 ="";

                        $trans->general_amount ="";
                        $trans->general_amount2 ="";

                        $trans->hatchery_amount ="";
                        $trans->hatchery_amount2 ="";

                        $trans->layer_amount ="";
                        $trans->layer_amount2 ="";

                        $trans->unclassified_amount ="";
                        $trans->unclassified_amount2 ="";

                        $trans->total_amount ="";
                        $trans->total_amount2 ="";
                        $coa_array[] = $trans;
                    }
                }elseif($chartofaccount1->body == "footer"){
                    $trans = new \stdClass;
                    $trans->name = ucfirst("Total ".$chartofaccount1->data->coa_title);
                    $trans->keys = "detail";
                    $trans->space = $chartofaccount1->space;
                    $trans->body = "footer";
                    $trans->broiler_amount =number_format(0,2);
                    $trans->broiler_amount2 =number_format(0,2);

                    $trans->admin_amount =number_format(0,2);
                    $trans->admin_amount2 =number_format(0,2);

                    $trans->general_amount =number_format(0,2);
                    $trans->general_amount2 =number_format(0,2);

                    $trans->hatchery_amount =number_format(0,2);
                    $trans->hatchery_amount2 =number_format(0,2);

                    $trans->layer_amount =number_format(0,2);
                    $trans->layer_amount2 =number_format(0,2);

                    $trans->unclassified_amount =number_format(0,2);
                    $trans->unclassified_amount2 =number_format(0,2);

                    $trans->total_amount =number_format(0,2);
                    $trans->total_amount2 =number_format(0,2);
                    $coa_array[] = $trans;
                }
               
            }



            
            $trans = new \stdClass;
            $trans->name = ucfirst("Total ".$detail->detail_type_name);
            $trans->keys = "detail";
            $trans->space = $space;
            $trans->body = "footer";
            $trans->broiler_amount =number_format(0,2);
            $trans->broiler_amount2 =number_format(0,2);

            $trans->admin_amount =number_format(0,2);
            $trans->admin_amount2 =number_format(0,2);

            $trans->general_amount =number_format(0,2);
            $trans->general_amount2 =number_format(0,2);

            $trans->hatchery_amount =number_format(0,2);
            $trans->hatchery_amount2 =number_format(0,2);

            $trans->layer_amount =number_format(0,2);
            $trans->layer_amount2 =number_format(0,2);

            $trans->unclassified_amount =number_format(0,2);
            $trans->unclassified_amount2 =number_format(0,2);

            $trans->total_amount =number_format(0,2);
            $trans->total_amount2 =number_format(0,2);
            $coa_array[] = $trans;

            $space--;
        }
        //end expense


        $trans = new \stdClass;
        $trans->name = ucfirst("Net Income");
        $trans->keys = "type";
        $trans->body = "footer";
        $trans->space = 0;
        $trans->broiler_amount =number_format(0,2);
        $trans->broiler_amount2 =number_format(0,2);
        $trans->admin_amount =number_format(0,2);
        $trans->admin_amount2 =number_format(0,2);
        $trans->general_amount =number_format(0,2);
        $trans->general_amount2 =number_format(0,2);
        $trans->hatchery_amount =number_format(0,2);
        $trans->hatchery_amount2 =number_format(0,2);
        $trans->layer_amount =number_format(0,2);
        $trans->layer_amount2 =number_format(0,2);
        $trans->unclassified_amount =number_format(0,2);
        $trans->unclassified_amount2 =number_format(0,2);
        $trans->total_amount =number_format(0,2);
        $trans->total_amount2 =number_format(0,2);
        $coa_array[] = $trans;
        //End ORDINARY INCOME/EXPENSE 
        return response()->json(['results'=>$coa_array]);
    }

    public function profitloss_collapse(){

        function chartofaccount($datas,$parent = 0){
            $coa_arr = collect();
            static $i = 1;

            if(count($datas->where('is_sub',$parent))>0)
            {
                $coas =$datas->where('is_sub',$parent); 
            }else{
                $coas = $datas;
            }

            $coas->load('children');
            $i++;
            foreach ($coas as  $coa) {
                $account = new \stdClass;
                $account->data = $coa;
                $account->body ="header";
                $account->space = $i*1;
                if(count($coa->children)== 0){
                    $account->child = 1;
                }
                $coa_arr[] = $account;

                if(count($coa->children) > 0){
                    $child =  chartofaccount($coa->children, $coa->id);

                    if($child) {
                        $i++;
                        $coa_arr = collect($coa_arr)->merge($child);
                        $i--;
                       
                    }
                    $account = new \stdClass;
                    $account->data = $coa;
                    $account->body ="footer";
                    $account->space = $i*1;
                    $coa_arr[] = $account;   
                }
            }
            $i--;

            return $coa_arr;
        }

        $coa_array = [];

        $detailtype = Detail_type::with('chartofaccount','chartofaccount.children')->get();

        //ORDINARY INCOME/EXPENSE

        $trans = new \stdClass;
        $trans->name = ucfirst("Income/Expense");
        $trans->keys = "type";
        $trans->body = "header";
        $trans->space = 0;

        $coa_array[] = $trans;

        $space = 0;

        //income
        foreach ($detailtype->whereIn('detail_type_name',['Income','Other Income','Cost of Goods Sold'])as $detail) {
            $space++;

            $trans = new \stdClass;
            $trans->name = ucfirst($detail->detail_type_name);
            $trans->keys = "detail";
            $trans->space = $space;
            $trans->body = "header";
            $coa_array[] = $trans;

            foreach ($detail->chartofaccount->where('is_sub',0) as $chartofaccount) {
                 $space++;
                $trans = new \stdClass;
                $trans->name = ucfirst($chartofaccount->coa_title);
                $trans->keys = "detail";
                $trans->space = $space;
                $trans->body = "header";
                $trans->broiler_amount =number_format(0,2);
                $trans->broiler_amount2 =number_format(0,2);

                $trans->admin_amount =number_format(0,2);
                $trans->admin_amount2 =number_format(0,2);

                $trans->general_amount =number_format(0,2);
                $trans->general_amount2 =number_format(0,2);

                $trans->hatchery_amount =number_format(0,2);
                $trans->hatchery_amount2 =number_format(0,2);

                $trans->layer_amount =number_format(0,2);
                $trans->layer_amount2 =number_format(0,2);

                $trans->unclassified_amount =number_format(0,2);
                $trans->unclassified_amount2 =number_format(0,2);

                $trans->total_amount =number_format(0,2);
                $trans->total_amount2 =number_format(0,2);
                $coa_array[] = $trans;


                // if($chartofaccount->body == "header"){
                    
                //     if(isset($chartofaccount->child)){
                //         $trans = new \stdClass;
                //         $trans->name = ucfirst($chartofaccount->data->coa_title);
                //         $trans->keys = "detail";
                //         $trans->space = $chartofaccount->space;
                //         $trans->body = "header";
                //         $trans->broiler_amount =number_format(0,2);
                //         $trans->broiler_amount2 =number_format(0,2);

                //         $trans->admin_amount =number_format(0,2);
                //         $trans->admin_amount2 =number_format(0,2);

                //         $trans->general_amount =number_format(0,2);
                //         $trans->general_amount2 =number_format(0,2);

                //         $trans->hatchery_amount =number_format(0,2);
                //         $trans->hatchery_amount2 =number_format(0,2);

                //         $trans->layer_amount =number_format(0,2);
                //         $trans->layer_amount2 =number_format(0,2);

                //         $trans->unclassified_amount =number_format(0,2);
                //         $trans->unclassified_amount2 =number_format(0,2);

                //         $trans->total_amount =number_format(0,2);
                //         $trans->total_amount2 =number_format(0,2);
                //         $coa_array[] = $trans;
                //     }else{
                //         $trans = new \stdClass;
                //         $trans->name = ucfirst($chartofaccount->data->coa_title);
                //         $trans->keys = "detail";
                //         $trans->space = $chartofaccount->space;
                //         $coa_array[] = $trans;
                //     }
                // }elseif($chartofaccount->body == "footer"){
                //     $trans = new \stdClass;
                //     $trans->name = ucfirst("Total ".$chartofaccount->data->coa_title);
                //     $trans->keys = "detail";
                //     $trans->space = $chartofaccount->space;
                //     $trans->body = "footer";
                //     $trans->broiler_amount =number_format(0,2);
                //     $trans->broiler_amount2 =number_format(0,2);

                //     $trans->admin_amount =number_format(0,2);
                //     $trans->admin_amount2 =number_format(0,2);

                //     $trans->general_amount =number_format(0,2);
                //     $trans->general_amount2 =number_format(0,2);

                //     $trans->hatchery_amount =number_format(0,2);
                //     $trans->hatchery_amount2 =number_format(0,2);

                //     $trans->layer_amount =number_format(0,2);
                //     $trans->layer_amount2 =number_format(0,2);

                //     $trans->unclassified_amount =number_format(0,2);
                //     $trans->unclassified_amount2 =number_format(0,2);

                //     $trans->total_amount =number_format(0,2);
                //     $trans->total_amount2 =number_format(0,2);
                //     $coa_array[] = $trans;
                // }
                $space--;
            }

            $trans = new \stdClass;
            $trans->name = ucfirst("Total ".$detail->detail_type_name);
            $trans->keys = "detail";
            $trans->space = $space;
            $trans->body = "footer";
            $trans->broiler_amount =number_format(0,2);
            $trans->broiler_amount2 =number_format(0,2);

            $trans->admin_amount =number_format(0,2);
            $trans->admin_amount2 =number_format(0,2);

            $trans->general_amount =number_format(0,2);
            $trans->general_amount2 =number_format(0,2);

            $trans->hatchery_amount =number_format(0,2);
            $trans->hatchery_amount2 =number_format(0,2);

            $trans->layer_amount =number_format(0,2);
            $trans->layer_amount2 =number_format(0,2);

            $trans->unclassified_amount =number_format(0,2);
            $trans->unclassified_amount2 =number_format(0,2);

            $trans->total_amount =number_format(0,2);
            $trans->total_amount2 =number_format(0,2);
            $coa_array[] = $trans;

            $space--;
        }
        $trans = new \stdClass;
        $trans->name = ucfirst("Gross Profit");
        $trans->keys = "type";
        $trans->body = "footer";
        $trans->space = 0;
        $trans->broiler_amount =number_format(0,2);
        $trans->broiler_amount2 =number_format(0,2);

        $trans->admin_amount =number_format(0,2);
        $trans->admin_amount2 =number_format(0,2);

        $trans->general_amount =number_format(0,2);
        $trans->general_amount2 =number_format(0,2);

        $trans->hatchery_amount =number_format(0,2);
        $trans->hatchery_amount2 =number_format(0,2);

        $trans->layer_amount =number_format(0,2);
        $trans->layer_amount2 =number_format(0,2);

        $trans->unclassified_amount =number_format(0,2);
        $trans->unclassified_amount2 =number_format(0,2);

        $trans->total_amount =number_format(0,2);
        $trans->total_amount2 =number_format(0,2);
        $coa_array[] = $trans;
        //end income

        //expense
        foreach ($detailtype->whereIn('detail_type_name',['Expense','Other Expense'])as $detail) {
            $space++;

            $trans = new \stdClass;
            $trans->name = ucfirst($detail->detail_type_name);
            $trans->keys = "detail";
            $trans->space = $space;
            $trans->body = "header";
            $coa_array[] = $trans;

            foreach ($detail->chartofaccount->where('is_sub',0) as $chartofaccount) {
                 $space++;
                $trans = new \stdClass;
                $trans->name = ucfirst($chartofaccount->coa_title);
                $trans->keys = "detail";
                $trans->space = $space;
                $trans->body = "header";
                $trans->broiler_amount =number_format(0,2);
                $trans->broiler_amount2 =number_format(0,2);

                $trans->admin_amount =number_format(0,2);
                $trans->admin_amount2 =number_format(0,2);

                $trans->general_amount =number_format(0,2);
                $trans->general_amount2 =number_format(0,2);

                $trans->hatchery_amount =number_format(0,2);
                $trans->hatchery_amount2 =number_format(0,2);

                $trans->layer_amount =number_format(0,2);
                $trans->layer_amount2 =number_format(0,2);

                $trans->unclassified_amount =number_format(0,2);
                $trans->unclassified_amount2 =number_format(0,2);

                $trans->total_amount =number_format(0,2);
                $trans->total_amount2 =number_format(0,2);
                $coa_array[] = $trans;

                $space--;
            }

            $trans = new \stdClass;
            $trans->name = ucfirst("Total ".$detail->detail_type_name);
            $trans->keys = "detail";
            $trans->space = $space;
            $trans->body = "footer";
            $trans->broiler_amount =number_format(0,2);
            $trans->broiler_amount2 =number_format(0,2);

            $trans->admin_amount =number_format(0,2);
            $trans->admin_amount2 =number_format(0,2);

            $trans->general_amount =number_format(0,2);
            $trans->general_amount2 =number_format(0,2);

            $trans->hatchery_amount =number_format(0,2);
            $trans->hatchery_amount2 =number_format(0,2);

            $trans->layer_amount =number_format(0,2);
            $trans->layer_amount2 =number_format(0,2);

            $trans->unclassified_amount =number_format(0,2);
            $trans->unclassified_amount2 =number_format(0,2);

            $trans->total_amount =number_format(0,2);
            $trans->total_amount2 =number_format(0,2);
            $coa_array[] = $trans;

            $space--;
        }
        //end expense


        $trans = new \stdClass;
        $trans->name = ucfirst("Net Income");
        $trans->keys = "type";
        $trans->body = "footer";
        $trans->space = 0;
        $trans->broiler_amount =number_format(0,2);
        $trans->broiler_amount2 =number_format(0,2);
        $trans->admin_amount =number_format(0,2);
        $trans->admin_amount2 =number_format(0,2);
        $trans->general_amount =number_format(0,2);
        $trans->general_amount2 =number_format(0,2);
        $trans->hatchery_amount =number_format(0,2);
        $trans->hatchery_amount2 =number_format(0,2);
        $trans->layer_amount =number_format(0,2);
        $trans->layer_amount2 =number_format(0,2);
        $trans->unclassified_amount =number_format(0,2);
        $trans->unclassified_amount2 =number_format(0,2);
        $trans->total_amount =number_format(0,2);
        $trans->total_amount2 =number_format(0,2);
        $coa_array[] = $trans;
        //End ORDINARY INCOME/EXPENSE 
        return response()->json(['results'=>$coa_array]);
    }

    public function test(){
        $coas = Chart_of_account::with('sub','sub.sub','sub.sub.sub')->where('is_sub',0)->get();
        return view('reports.test',compact('coas'));
    }
}
