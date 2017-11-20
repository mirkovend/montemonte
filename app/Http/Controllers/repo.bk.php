<?php

namespace App\Http\Controllers;

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
use PDF;
use Validator;
use DB;
use Session;
use Request;
use App\Item;
use App\Inventory;
use App\Inventory_item;
use App\itemFlow;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Chart_of_account;
use Datatables;
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
           
    public function anyData()
    {
        


        return Datatables::of(User::query())->make(true);
    }
    public function inventory_report(){
        $sss = 0; 
        function item($datas,$parent = 0){
            static $i = 0;
            static $a = 0;
            
           
            static $total_asset = 0;
            static $total_asset1 = 0;
            static $total_onhand = 0;
            static $total_onhand1 = 0;
            $tab = str_repeat("---",$i);
            
            $html = "";
            
            $totalasset= session('global_total_asset');
            $totalonhand = session('global_total_onhand');
          
            if(!empty($datas))
            {
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
                foreach ($datas->where('is_sub',$parent) as  $data) {
                $onhandparent = 0;
                $asset_val_total = 0;
                $sub_onhandparent = 0;
                $sub_asset_val_total = 0;
                    $html .= '<tr>
                                <td colspan=""><h4 class="text-uppercase" style="margin-left:'.($i*2).'em">'.$data->item_name.'</h4></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                
                            </tr>';
                   
                    foreach ($data->item_flow as $po) {

                    $po->load(['vc_item' => function ($query) use($data) {
                        $query->where('account_item_id', $data->id);
                    },'vc_item.po.supplier_one','vc_item.po_item']);
                    $html .="<tr>";
                        if($po->type == "po"){
                            $html .="<td colspan=''></td>
                            
                            <td>BILL</td>
                            <td>".Carbon::parse($po->vc_item->dt)->toFormattedDateString()."</td>
                            
                            <td>".$po->vc_item->po->supplier_one->supplier_name."</td>
                            
                            <td>CV".$po->ref_no."</td>
                            
                            <td>".number_format($po->vc_item->item_rcv,2)."</td>
                            
                            <td class='text-right'>".number_format($po->vc_item->item_rcv * $po->vc_item->po_item->item_price,2)."</td>
                           
                            <td class='text-right'>".number_format($onhandparent += $po->vc_item->item_rcv,2)."</td>
                           
                            <td>PCS</td>
                            
                            <td class='text-right'>".number_format($po->vc_item->po_item->item_price,2)."</td>
                           
                            <td class='text-right'>".number_format($asset_val_total+= ($po->vc_item->item_rcv * $po->vc_item->po_item->item_price),2)."</td>";

                            if($data->is_sub != $parent){
                           
                        
                                $sub_onhandparent += $onhandparent;
                                $sub_asset_val_total += $asset_val_total;
                            }else{
                              
                                $sub_asset_val_total += $asset_val_total;
                                $sub_onhandparent += $onhandparent;
                            }

                        }elseif($po->type == "ci"){
                            $html .="<td colspan=''></td>
                            
                            <td>SALES RECEIPT</td>
                            <td>".Carbon::parse($po->ci->dt)->toFormattedDateString()."</td>
                            
                            <td>".$po->ci->customer_belong->customer_name."</td>
                            
                            <td>".$po->ref_no."</td>
                            
                            <td>".number_format($po->credit - ($po->credit *2),2)."</td>
                            
                            <td class='text-right'>".number_format($po->ci_item->unit_price * $po->credit,2)."</td>
                           
                            <td class='text-right'>".number_format($onhandparent += ($po->credit - ($po->credit *2)),2)."</td>
                           
                            <td>PCS</td>
                            
                            <td class='text-right'>".number_format($po->ci_item->unit_price,2)."</td>
                           
                            <td class='text-right'>".number_format($asset_val_total += $po->ci_item->unit_price * $po->credit,2)."</td>";
                            if($data->is_sub!= $parent){
                           
                        
                                $sub_onhandparent += $po->ci_item->unit_price * $po->credit;
                                $sub_asset_val_total = $asset_val_total;
                            }else{
                              
                                $sub_asset_val_total += $asset_val_total;
                                $sub_onhandparent += $onhandparent;
                                
                            }
                        }elseif($po->type == "invoice"){
                            $html .="<td colspan=''></td>
                            
                            <td>INVOICE</td>
                            <td>".Carbon::parse($po->invoice->dt)->toFormattedDateString()."</td>
                            
                            <td>".$po->invoice->customer_belong->customer_name."</td>
                            
                            <td>".$po->ref_no."</td>
                            
                            <td>".number_format($po->credit- ($po->credit *2),2)."</td>
                            
                            <td class='text-right'>".number_format($po->invoice_item->unit_price * $po->credit,2)."</td>
                           
                            <td class='text-right'>".number_format($onhandparent += ($po->credit - ($po->credit *2)),2)."</td>
                           
                            <td>PCS</td>
                            
                            <td class='text-right'>".number_format($po->invoice_item->unit_price,2)."</td>
                           
                            <td class='text-right'>".number_format($asset_val_total += $po->invoice_item->unit_price * $po->credit,2)."</td>";
                            if($data->is_sub != $parent){
                           
                        
                                $sub_onhandparent += ($po->credit - ($po->credit *2));
                                $sub_asset_val_total = $asset_val_total;
                            }else{
                              
                                $sub_asset_val_total += $asset_val_total;
                                $sub_onhandparent += $onhandparent;
                                
                            }
                        }elseif($po->type == "dr"){
                            $html .="<td colspan=''></td>
                            
                            <td>ITEM RECEIPT</td>
                            <td>".Carbon::parse($po->dr->dt)->toFormattedDateString()."</td>
                            
                            <td></td>
                            
                            <td>DR#".$po->ref_no."</td>
                            
                            <td>".number_format($po->debit,2)."</td>
                            

                            <td class='text-right'>".number_format($po->dr_item->unit_price * $po->debit,2)."</td>
                           
                            <td class='text-right'>".number_format($onhandparent += $po->debit,2)."</td>
                           
                            <td>PCS</td>
                            
                            <td class='text-right'>".number_format($po->dr_item->unit_price,2)."</td>
                           
                            <td class='text-right'>".number_format($asset_val_total +=  $po->dr_item->unit_price * $po->debit,2)."</td>";
                            if($data->is_sub != $parent){
                           
                        
                                $sub_onhandparent += $po->debit;
                                $sub_asset_val_total = $asset_val_total;
                            }else{
                              
                                $sub_asset_val_total += $asset_val_total;
                                $sub_onhandparent += $onhandparent;
                                
                            }
                        }elseif($po->type == "adjustment"){
                            $html .="<td colspan=''></td>
                            
                            <td>ADJUSTMENT</td>
                            <td>".Carbon::parse($po->inventory->dt)->toFormattedDateString()."</td>
                            
                            <td></td>
                            
                            <td>".$po->ref_no."</td>
                            
                            <td>".number_format($po->credit - ($po->credit *2),2)."</td>
                            

                            <td class='text-right'>".number_format($po->inventory_item->ave_cost * $po->credit,2)."</td>
                           
                            <td class='text-right'>".number_format($onhandparent += ($po->credit - ($po->credit *2)),2)."</td>
                           
                            <td>PCS</td>
                            
                            <td class='text-right'>".number_format($po->inventory_item->ave_cost,2)."</td>
                           
                            <td class='text-right'>".number_format($asset_val_total += ($po->inventory_item->ave_cost * $po->credit) - ($po->inventory_item->ave_cost * $po->credit)*2,2)."</td>";
                            if($data->is_sub != $parent){
                           
                        
                                $sub_onhandparent += ($po->credit - ($po->credit *2));
                                $sub_asset_val_total = $asset_val_total;
                            }else{
                              
                                $sub_asset_val_total += $asset_val_total;
                                $sub_onhandparent += $onhandparent;
                                
                            }
                        }elseif($po->type == "bill_adjus"){ 
                            $html .="<td colspan=''></td>
                            
                            <td>MIXED ADJUSTMENT</td>
                            <td>".Carbon::parse($po->inventory->dt)->toFormattedDateString()."</td>
                            
                            <td></td>
                            
                            <td>".$po->ref_no."</td>
                            
                            <td>".number_format($po->debit,2)."</td>
                            

                            <td class='text-right'>".number_format($po->inventory->amount_cost,2)."</td>
                           
                            <td class='text-right'>".number_format($onhandparent += $po->debit,2)."</td>
                           
                            <td>PCS</td>
                            
                            <td class='text-right'>".number_format($po->inventory->amount_cost/$po->debit,2)."</td>
                           
                            <td class='text-right'>".number_format($asset_val_total +=  $po->inventory->amount_cost ,2)."</td>";
                            if($data->is_sub != $parent){
                           
                        
                                $sub_onhandparent += $po->debit;
                                $sub_asset_val_total = $asset_val_total;
                            }else{
                              
                                $sub_asset_val_total += $asset_val_total;
                                $sub_onhandparent += $onhandparent;
                                
                            }

                        }   
                      
                    $html .="</tr>";

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
                      
                         $child = item($data, $data->id);


                        if($child)
                        {
                          $i--;

                          $html .= $child;
                        }
                    }
                  
                   
                    if($data->is_sub == $parent){

                        $html .= '<tr>
                          <td class="text-uppercase" colspan="" ><h4 style="margin-left:'.($i*2).'em">Total '.$data->item_name.':</h4></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td class="text-right" >'.number_format($total_onhand1,2).'</td>
                          <td></td>
                          <td></td>
                          <td class="text-right">'.number_format($total_asset1,2).'</td>
                        </tr>';
                    }else{

                         $html .= '<tr>
                          <td class="text-uppercase" colspan=""><h4 style="margin-left:'.($i*2).'em">Total '.$data->item_name.':</h4></td>

                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td class="text-right" >'.number_format($sub_onhandparent,2).'</td>
                          <td></td>
                          <td></td>
                          <td class="text-right">'.number_format($sub_asset_val_total,2).'</td>
                        </tr>';
                    }

                }

                return $html;

            }

        }


        $inventories = Item::where('item_type_id',1)
            ->where('id',4)
            ->where(function($q) {
              $q->has("item_flow")->orHas("sub.item_flow");
            })
            ->orderBy('item_name','ASC')
            ->get();
            
        // $inventories = Item::with('sub','item_flow')
        //     ->where('item_type_id',1)
        //     ->orderBy('item_name','ASC')
        //     ->get();
            // foreach($inventories as $item){
      
            //     $data[$item->is_sub][] = $item;

            // }
        $inventory = item($inventories);
        $total_asset = session('global_total_asset');
        session::forget('global_total_asset');
        $total_onhand = session('global_total_onhand');
        session::forget('global_total_onhand');
        return view('reports.inventory_report',compact('inventory','total_asset','total_onhand'));
    }
    public function inventory_report_store(){
        
    }

    public function purchases_report(){
        $inventory = Item::with([
                'item_flow'=>function($q){ 
                    $q->where('type','po');
                    $q->orWhere('type','dr');
                    $q->orWhere('type','jo');
                },
                'item_flow.vc_item',
                'item_flow.vc_item.po.supplier_one',
                'item_flow.vc_item.po_item',

                'sub',
                'sub.item_flow'=>function($q){
                    $q->where('type','po');
                    $q->orWhere('type','dr');
                    $q->orWhere('type','jo');
                },
                'item_sub',
                'sub.item_flow.vc_item',
                'sub.item_flow.vc_item.po.supplier_one',
                'sub.item_flow.vc_item.po_item',

                'sub.item_flow.vc_job_item',
                'sub.item_flow.vc_job_item.jo.supplier_one',
                'sub.item_flow.vc_job_item.jo_item',

                

            ])
            ->orderBy('item_type_id','ASC')
            ->orderBy('item_name', 'ASC')
            ->where('is_sub',0)
            ->get();
        return view('reports.purchases.purchases_report',compact('inventory'));
    }

    public function purchases_type(){
        $all = Request::all();
        
        $inventory = Item::with([
                'item_flow'=>function($q){ 
                    $q->where('type','po');
                    $q->orWhere('type','dr');
                    $q->orWhere('type','jo');
                },
                'item_flow.vc_item',
                'item_flow.vc_item.po.supplier_one',
                'item_flow.vc_item.po_item',

                'sub',
                'sub.item_flow'=>function($q){
                    $q->where('type','po');
                    $q->orWhere('type','dr');
                    $q->orWhere('type','jo');
                },
                'item_sub',
                'sub.item_flow.vc_item',
                'sub.item_flow.vc_item.po.supplier_one',
                'sub.item_flow.vc_item.po_item',

                'sub.item_flow.vc_job_item',
                'sub.item_flow.vc_job_item.jo.supplier_one',
                'sub.item_flow.vc_job_item.jo_item',

                

            ])
            ->orderBy('item_type_id','ASC')
            ->orderBy('item_name', 'ASC')
            ->where('is_sub',0)
            ->get();

        $suppliers = Supplier::has('voucheritem')->with(['voucheritem'])
            ->orderBy('supplier_name','ASC')->get();
        if($all['detail_type'] == 1){
            return view('reports.purchases.purchases_report',compact('inventory'));
        }elseif($all['detail_type'] == 2){
            return view('reports.purchases.purchases_report_summary',compact('inventory'));
        }elseif($all['detail_type'] == 3){

            return view('reports.purchases.purchases_report_vendor',compact('suppliers'));
        }else{
            return view('reports.purchases.purchases_report_vendor_summary',compact('suppliers'));
        }

    }


    public function sales_report(){
        $inventory = Item::with([
            'item_flow',
            'item_flow.vc_item',
            'item_flow.vc_item.po.supplier_one',
            'item_flow.vc_item.po_item',

            'sub',
            'sub.item_flow',
            'item_sub',
            'sub.item_flow.vc_item',
            'sub.item_flow.vc_item.po.supplier_one',
            'sub.item_flow.vc_item.po_item',

            'sub.item_flow.vc_job_item',
            'sub.item_flow.vc_job_item.jo.supplier_one',
            'sub.item_flow.vc_job_item.jo_item',



            ])
            ->where(function($subQuery){   
                $subQuery->whereHas('item_flow', function ( $query ) {
                    $query->where('type', 'invoice' );
                    $query->orWhere('type', 'ci' );
                })
                ->orWhereHas('sub.item_flow', function ( $query ) {
                    $query->where('type', 'invoice' );
                    $query->orWhere('type', 'ci' );
                });
            })
            ->orderBy('item_type_id','ASC')
            ->orderBy('item_name', 'ASC')
            ->where('is_sub',0)
            ->get();
       
        return view('reports.sales_report.sales_report',compact('inventory'));
    }

    public function sales_report_type(){
        $all = Request::all();

        $customers = Customer::where(function($q) {
          $q->has("ci")->orHas("invoice");
        })
        ->with('ci','invoice','invoice.invoice','invoice.invoice.item','ci.ci_item','ci.ci_item.item')
        ->orderBy('customer_name','ASC')
        ->get();

        $inventory = Item::where(function($subQuery){   
            $subQuery->whereHas('item_flow', function ( $query ) {
                $query->where('type', 'invoice' );
                $query->orWhere('type', 'ci' );
            })
            ->orWhereHas('sub.item_flow', function ( $query ) {
                $query->where('type', 'invoice' );
                $query->orWhere('type', 'ci' );
            });
            })
            ->with([
            'item_flow',
            'item_flow.vc_item',
            'item_flow.vc_item.po.supplier_one',
            'item_flow.vc_item.po_item',

            'sub',
            'sub.item_flow',
            'item_sub',
            'sub.item_flow.vc_item',
            'sub.item_flow.vc_item.po.supplier_one',
            'sub.item_flow.vc_item.po_item',

            'sub.item_flow.vc_job_item',
            'sub.item_flow.vc_job_item.jo.supplier_one',
            'sub.item_flow.vc_job_item.jo_item',



            ])
            ->orderBy('item_type_id','ASC')
            ->orderBy('item_name', 'ASC')
            ->where('is_sub',0)
            ->get();

        if($all['detail_type'] == 1){
            return view('reports.sales_report.sales_report',compact('inventory'));
        }elseif($all['detail_type'] == 2){
            return view('reports.sales_report.sales_report_customer',compact('customers'));
        }else{
            return view('reports.sales_report.customer_summary',compact('customers'));
        }
   
        
    }

    public function recievable_report(){
        $customers = Customer::where(function($q) {
          $q->has("ci")->orHas("invoice");
        })
        ->with('ci','invoice','ci.ci_item')
        ->orderBy('customer_name','ASC')
        ->get();
       
        return view('reports.receivable.recievable',compact('customers'));
    }

    public function test(){
        $coas = Chart_of_account::with('sub','sub.sub','sub.sub.sub')->where('is_sub',0)->get();
        return view('reports.test',compact('coas'));
    }
}
