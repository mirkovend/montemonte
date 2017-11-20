<?php

namespace App\Http\Controllers;

use App\Starting_account_number;
use App\Supplier;
use App\Voucher;
use App\Voucher_item;
use App\Purchase_order;
use App\Purchase_order_item;
use App\Chart_of_account;
use App\Voucher_no_po;
use App\Pettycash;
use App\Pcf_item;
use App\Voucher_pcf_item;
use App\Joborder;
use App\Joborder_item;
use App\Voucher_job_item;

use PDF;
use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\itemFlow;
use App\coa_transaction;
class VoucherController extends Controller
{   
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
    	$voucher = Voucher::latest('dt')->get();
        return view('vouchers.index',compact('voucher'));
    }

    public function create(){
    	$voucher = Voucher::orderBy('voucher_number','DESC')->first();
        
        if($voucher==NULL){
            $start = Starting_account_number::select('account_number')->where('account_name','=','voucher_number')->first();
            $start_number = $start->account_number;
        }else{
            $start_number = $voucher->voucher_number+1;
        }

        $suppliers = Supplier::pluck('supplier_name','id');

        Session::forget('supplier_id');
        Session::forget('voucher_type');

        $supplier = Request::get('supplier_id');
        
        $voucher_type = Request::get('voucher_type');

        /*WITH PO*/
        $po_list =  Purchase_order::where('supplier_id',$supplier)->where('remarks',NULL)->get();

        /*WITHOUT PO*/
        $coa = Chart_of_account::pluck('coa_title','id');
    	return view('vouchers.create',compact('start_number','suppliers','po_list','supplier','voucher_type','voucher','coa'));
    }

    public function search_po_list(){
        $voucher = Voucher::orderBy('voucher_number','DESC')->first();
        
        if($voucher==NULL){
            $start = Starting_account_number::select('account_number')->where('account_name','=','voucher_number')->first();
            $start_number = $start->account_number;
        }else{
            $start_number = $voucher->voucher_number+1;
        }

        $suppliers = Supplier::pluck('supplier_name','id');

        $supplier = Request::get('supplier_id');
        $voucher_type = Request::get('voucher_type');

       /*WITH PO*/
        if($voucher_type == 'WITH PO'){
            $po_list =  Purchase_order::where('supplier_id',$supplier)->where('remarks',NULL)->get();
            Session::put('voucher_type',$voucher_type);
            Session::put('supplier_id',$supplier);
        }elseif($voucher_type == 'WITH PCF'){
            $petty = Pettycash::where('pcf_to',$supplier)->where('remarks',NULL)->get();
            Session::put('voucher_type',$voucher_type);
            Session::put('supplier_id',$supplier);
        }elseif($voucher_type == 'WITH JO'){
            $job = Joborder::where('joborder_to',$supplier)->where('status',NULL)->get();
            Session::put('voucher_type',$voucher_type);
            Session::put('supplier_id',$supplier);
        }
                
        /*WITHOUT PO*/
        $coa = Chart_of_account::pluck('coa_title','id');

        return view('vouchers.create',compact('start_number','suppliers','supplier','po_list','voucher_type','voucher','coa','petty','supplier_id','job'));
    }

    public function store(){
        // dd(Request::all());
        $voucher = Request::except('voucher_type','supplier_id');
        
        if(Session::has('voucher_type') && Session::has('supplier_id') ){
            $voucher_type = Session::get('voucher_type');
            $supplier_id = Session::get('supplier_id');
        }else{
            $voucher_type = Request::get('voucher_type');
            $supplier_id = Request::get('supplier_id');
        }
        
                

        $voucher = array_add($voucher, 'voucher_type', $voucher_type);
        $voucher = array_add($voucher, 'supplier_id', $supplier_id);
        //dd($voucher);
        $voucher = Voucher::create($voucher);

        if($voucher_type == 'WITH PO'){   
                  //IF WITH PO
            foreach(Request::get('id') as $key => $value){

                $po_items = Purchase_order_item::find($value);
                
                if(!empty(Request::get('item_rcv')[$key])){
                    $data = [
                        'voucher_number'            =>      Request::get('voucher_number'),
                        'purchase_order_number'     =>      $po_items->purchase_order_number,
                        'account_item_id'           =>      $po_items->account_item_id,
                        'item_rcv'                  =>      Request::get('item_rcv')[$key],
                        'dt'                        =>      Request::get('dt'),
                        'supplier_id'               =>      $supplier_id,
                        'purchase_order_id'         =>      $po_items->id,
                    ];

                    $v_items = Voucher_item::create($data);

                    itemFlow::insert(['type'=>'po','item_id'=>$po_items->account_item_id,'ref_no'=>Request::get('voucher_number'),'debit'=>Request::get('item_rcv')[$key],
                        'ave_cost'=>$po_items->item_price,
                        'created_at'=>carbon::now(),'updated_at'=>carbon::now()]);

                    $ap = coa_transaction::create([
                        'coa_id'=>174,
                        'dt' => Request::get('dt'),
                        'ref'=>Request::get('voucher_number'),
                        'type'=>'Bill',
                        'credit'=>Request::get('item_rcv')[$key]*$po_items->item_price,
                    ]);//AP

                    $ap->coa_transaction_link()->create(['coa_id'=>$po_items->item->coa_id]);

                    $asset = coa_transaction::create([
                        'coa_id'=>$po_items->item->coa_id,
                        'dt' => Request::get('dt'),
                        'ref'=>Request::get('voucher_number'),
                        'type'=>'Bill',
                        'debit'=>Request::get('item_rcv')[$key] * $po_items->item_price,
                    ]);
                    $ap->coa_transaction_link()->create(['coa_id'=>174]);

                }

                $po_qty = Purchase_order_item::where('purchase_order_number',$po_items->purchase_order_number)->sum('item_qty');
                $voucher_qty = Voucher_item::where('purchase_order_number',$po_items->purchase_order_number)->sum('item_rcv');

                if($po_qty == $voucher_qty){
                    $data2 = [
                        'remarks'   =>  'PAID',
                    ];
                    Purchase_order::where('purchase_order_number',$po_items->purchase_order_number)->update($data2);
                }

            }

        }elseif($voucher_type == 'WITH PCF'){
            foreach(Request::get('id') as $key => $value){
                $pc =  Pettycash::find($value);

                foreach ($pc->invoice as $key => $pc_item) {
                    $data = [
                        'pcf_id'                    =>      $pc->id,
                        'voucher_number'            =>      Request::get('voucher_number'),
                        'account_item_id'           =>      $pc_item->account_item_id,
                        'dt'                        =>      Request::get('dt'),
                        'supplier_id'               =>      $supplier_id,
                    ];
                    $data['pcf_number'] = Request::get('pcf_number')[$value]["pcf"];
                    $data['pcf_payment'] = Request::get('pcf_payment')[$value]["py"];
                    $petty_items = Voucher_pcf_item::create($data);
                    // $pc->invoice()->create($data);
                    // dd($pc->invoice()->create($data));
                    $ap_coa = coa_transaction::create([
                        'coa_id'=>174,
                        'dt' => Request::get('dt'),
                        'ref'=>Request::get('voucher_number'),
                        'type'=>'Bill',
                        'credit'=>$petty_items->pcf_payment,
                    ]);
                    $ap_coa->coa_transaction_link()->create(['coa_id'=>$pc_item->item->coa_id]);   

                    $coa_tran = coa_transaction::create([
                        'coa_id'=>$pc_item->item->coa_id,
                        'dt' => Request::get('dt'),
                        'ref'=>Request::get('voucher_number'),
                        'type'=>'Bill',
                        'debit'=>$petty_items->pcf_payment,
                    ]);

                    $coa_tran->coa_transaction_link()->create(['coa_id'=>174]);  
                }
                // $pcf_items = Pcf_item::where('pcf_number',Request::get('pcf_number')[$value]["pcf"])->get();

                $pcf_sum = Pcf_item::where('pcf_number',Request::get('pcf_number')[$value]["pcf"])->sum('amount');
                $v_pcf_sum = Voucher_pcf_item::where('pcf_number',Request::get('pcf_number')[$value]["pcf"])->sum('pcf_payment');

                if($pcf_sum == $v_pcf_sum){
                    $data2 = [
                        'remarks'   =>  'PAID',
                    ];
                    Pettycash::where('pcf_number',Request::get('pcf_number')[$value]["pcf"])->update($data2);
                }
            }
        }elseif($voucher_type == 'WITH JO'){

            foreach(Request::get('id') as $key => $value){

                $jo = Joborder::find($value);

                $job_items = Joborder_item::where('joborder_number',Request::get('joborder_number')[$value]["jo"])->get();
                foreach ($job_items as $key => $jobitem) {
                    $data = [
                    'voucher_number'            =>      Request::get('voucher_number'),
                    //'joborder_number'           =>      $job_items->joborder_number,
                    'account_item_id'           =>      $jobitem->account_item_id,
                    //'joborder_payment'          =>      Request::get('joborder_payment')[$key],
                    'dt'                        =>      Request::get('dt'),
                    'supplier_id'               =>      $supplier_id,
                    'joborder_id'               =>      $value,
                    ];

                    $data['joborder_number'] = Request::get('joborder_number')[$value]["jo"];
                    $data['joborder_payment'] = Request::get('joborder_payment')[$value]["py"];
                    $job_items = Voucher_job_item::create($data);

                    // itemFlow::insert([
                    //     'type'=>'jo',
                    //     'item_id'=>$job_items->account_item_id,
                    //     'ref_no'=>Request::get('voucher_number'),
                    //     'ave_cost'=>Request::get('joborder_payment')[$value]["py"],
                    //     'created_at'=>carbon::now(),
                    //     'updated_at'=>carbon::now()
                    // ]);

                    //AP
                    $ap_coa = coa_transaction::create([
                        'coa_id'=>174,
                        'dt' => Request::get('dt'),
                        'ref'=>Request::get('voucher_number'),
                        'type'=>'Bill',
                        'credit'=>$job_items->joborder_payment,
                    ]);
                    
                    $ap_coa->coa_transaction_link()->create(['coa_id'=>$jobitem->item->coa_id]);   

                    $coa_tran = coa_transaction::create([
                        'coa_id'=>$jobitem->item->coa_id,
                        'dt' => Request::get('dt'),
                        'ref'=>Request::get('voucher_number'),
                        'type'=>'Bill',
                        'debit'=>$job_items->joborder_payment,
                    ]);

                    $coa_tran->coa_transaction_link()->create(['coa_id'=>174]);  

                }
                

                
                $job_sum = Joborder_item::where('joborder_number',Request::get('joborder_number')[$value]["jo"])->sum('amount');

                $v_job_sum = Voucher_job_item::where('joborder_number',Request::get('joborder_number')[$value]["jo"])->sum('joborder_payment');

                if($job_sum == $v_job_sum){
                    $data2 = [
                        'status'   =>  'PAID',
                    ];
                    Joborder::where('joborder_number',Request::get('joborder_number')[$value]["jo"])->update($data2);
                }

                
            }

        }elseif(Request::get('voucher_type') == 'WITHOUT PO'){          //IF WITHOUT PO
            
            foreach(Request::get('coa_id') as $key => $coa_id){
                $data = [
                    'voucher_number'            =>      Request::get('voucher_number'),
                    'dt'                        =>      Request::get('dt'),
                    'coa_id'                    =>      $coa_id,
                    'debit'                     =>      Request::get('debit')[$key],
                    'credit'                    =>      Request::get('credit')[$key],
                ];

                //AP
                $ap_coa = coa_transaction::create([
                    'coa_id'=>174,
                    'dt' => Request::get('dt'),
                    'ref'=>Request::get('voucher_number'),
                    'type'=>'Bill',
                    'credit'=>Request::get('credit')[$key],
                    'created_at'=>carbon::now(),
                    'updated_at'=>carbon::now()
                ]);
                $ap_coa->coa_transaction_link()->create(['coa_id'=>$coa_id]);  
                    
                $coa_tran = coa_transaction::create([
                    'coa_id'=>$coa_id,
                    'dt' => Request::get('dt'),
                    'ref'=>Request::get('voucher_number'),
                    'type'=>'Bill',
                    'debit'=>Request::get('debit')[$key],
                    'created_at'=>carbon::now(),
                    'updated_at'=>carbon::now()
                ]);
                $coa_tran->coa_transaction_link()->create(['coa_id'=>174]);  
                Voucher_no_po::create($data);
            }


        }

    	Session::flash('flash_message','Voucher Entry Saved.');

    	return redirect('voucher');
    }

    public function edit($id){
    	
    	return view('vouchers.edit',compact('purchaseorder','id','suppliers','terms','po_items','coa_item'));
    }

    public function update($id){
        

    	Session::flash('flash_message','Chart of Account Title Entry Updated.');
    	return redirect()->back();
    }

    public function delete_item($id){
    	
    	Session::flash('flash_message','Chart of Account Title Entry Deleted.');
    	return redirect()->back();
    }

    public function print_voucher($id){
        $voucher = Voucher::find($id);
        
        $voucher_type = $voucher->voucher_type;
        if($voucher_type == 'WITH PO'){
            $voucher_items = Voucher_item::where('voucher_number',$voucher->voucher_number)->groupBy('account_item_id')->get();
        }elseif($voucher_type == 'WITHOUT PO'){
            $voucher_items = Voucher_no_po::where('voucher_number',$voucher->voucher_number)->get();
        }elseif($voucher_type == 'WITH PCF'){
            $voucher_items = Voucher_pcf_item::where('voucher_number',$voucher->voucher_number)->get();
        }elseif($voucher_type == 'WITH JO'){
            $voucher_items = Voucher_job_item::where('voucher_number',$voucher->voucher_number)->get();
        }   
        
        $sig = \App\Signatory::where('report_type',1)->get();

        $legal = array(0, 0, 612.00, 1008.00);
        
        $pdf = PDF::loadView('pdf.voucher_report', compact('voucher','voucher_items','voucher_type','sig'))->setPaper(array(0, 0, 612.00, 1008.00));
        return $pdf->stream('VOUCHER_REPORT-CV'.$voucher->voucher_number.'.pdf');
    }
}
