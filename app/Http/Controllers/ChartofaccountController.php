<?php

namespace App\Http\Controllers;

use App\Chart_of_account;
use App\Detail_type;
use App\Coa_item;
use App\Coa_subitem;
use App\Coa_subitem_three;
use App\Coa_subitem_four;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Supplier;
use App\Customer;
use App\Coa_transaction;
use App\Coa_transaction_link;
use Illuminate\Pagination\LengthAwarePaginator;
class ChartofaccountController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }
    private function category_list($data = [],$sub = 0,$parent = 0 ) 
    { 
        // build our category list only once 
        static $cats; 
        static $i = 0;
        static $a = 0;
        $tab = str_repeat("---",$i);

      
        
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
                if($value->id == $sub){
                    $selected = "selected";
                }else{
                    $selected = "";
                }
                $html .= "<option value='$value->id' ".$selected.">$tab".$value->coa_title.$boldclose."</option>";
                if($value->sub){ 
                    if(count($value->sub)>0){
                        
                        $child = $this->category_list($data,$sub ,$value->id);

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
    // private function lists($datas,$parent = 0){
    //     static $i = 0;
    //     static $a = 0;
    //     $tab = str_repeat("---",$i);

    //     $html = "";
        
    //     if($datas[$parent])
    //     {
    //         $i++;
    //         foreach ($datas[$parent] as $key => $data) {
    //         # code...
    //         if($data->typical_balance == "Debit"){
    //             $balance = $data->balance+ ($data->transactions->sum('debit') - $data->transactions->sum('credit'));
    //         }else{
    //             $balance = $data->balance+ ($data->transactions->sum('credit')-$data->transactions->sum('debit'));
    //         }
    //         if(count($data->detailtype) > 0){
    //             $detailtype = $data->detailtype->detail_type_name;
    //         }else{
    //             $detailtype = "";
    //         }
    //         $html .="<tr>
    //                 <td style='padding-left: ".($i*2)."em;'>".$data->coa_title."</td>
    //                 <td>".$detailtype."</td>
                    
    //                 <td>P ".number_format($balance,2)."</td>
    //                 <td width = 80 class='text-center'>
                        // <div class='btn-group'>
                        //     <button type='button' class='btn btn-sm btn-primary dropdown-toggle' data-toggle='dropdown'>
                        //         <span class='icon-gear'></span> <span class='caret'></span>
                        //     </button>
                        //     <ul class='dropdown-menu dropdown-menu-arrow' role='menu' style='text-align:left;text-transform:uppercase;'>
                        //         <li><a href=".action('ChartofaccountController@show',$data->id)."><span class='icon-edit'></span>View History</a></li>
                        //         <li><a href=".action('ChartofaccountController@edit',$data->id)."><span class='icon-edit'></span> Edit Entry</a></li>
                        //         <li><a href=".action('ChartofaccountController@delete_item',$data->id)."><span class='icon-remove'></span> Delete Entry</a></li>
                        //     </ul>
                        // </div>
    //                 </td>
    //             </tr>";
    //             // if(count($data->inventory) > 0){
    //             //     $i++;
    //             //     foreach ($data->inventory as $item) {
    //             //         # code...
                    
    //             //     $html .="<tr>
    //             //         <td style='padding-left: ".($i*2)."em;'>".$item->item_name."</td>
    //             //         <td>".$data->detailtype->detail_type_name."</td>
                        
    //             //         <td>".$data->typical_balance."</td>
    //             //         <td>P ".number_format($data->balance,2)."</td>
    //             //         <td width = 80 class='text-center'>
    //             //             <div class='btn-group'>
    //             //                 <button type='button' class='btn btn-sm btn-primary dropdown-toggle' data-toggle='dropdown'>
    //             //                     <span class='icon-gear'></span> <span class='caret'></span>
    //             //                 </button>
    //             //                 <ul class='dropdown-menu dropdown-menu-arrow' role='menu' style='text-align:left;text-transform:uppercase;'>
    //             //                     <li><a href=".action('ChartofaccountController@show',$data->id)."><span class='icon-edit'></span>View History</a></li>
    //             //                     <li><a href=".action('ChartofaccountController@edit',$data->id)."><span class='icon-edit'></span> Edit Entry</a></li>
    //             //                     <li><a href=".action('ChartofaccountController@delete_item',$data->id)."><span class='icon-remove'></span> Delete Entry</a></li>
    //             //                 </ul>
    //             //             </div>
    //             //         </td>
    //             //     </tr>";
    //             //     }
    //             //     $i--;
    //             // };
    //             if($data->sub){ 
    //                 if(count($data->sub)>0){
                        
    //                      $child = $this->lists($datas, $data->id);


    //                     if($child)
    //                     {
    //                       $i--;

    //                       $html .= $child;
    //                     }
    //                 }
    //             }
               
    //         }
    //         return $html;
    //     }
    // }

    private function coa_lists($datas,$parent = 0){

        static $i = 0;
        static $a = 0;
        $coas = collect();

        if(count($datas->where('is_sub',$parent)) > 0){
            $query = $datas->where('is_sub',$parent);
        }else{
            $query = $datas;
        }
        $query->load(['sub','detailtype','transactions']);
        if($parent != 0){
            $i++;
        }else{
            $i = .5;
        }
        foreach ($query as $key => $coa) {
            
            if($coa->detailtype->type == "asset"){
                $coa_item = new \stdClass;
                $coa_item = $coa;
                $coa_item->detailtypes = $coa->detailtype->detail_type_name;
                if($coa->typical_balance == "DEBIT"){
                    $coa_item->balances = number_format($coa->balance+ ($coa->transactions->sum('debit') - $coa->transactions->sum('credit')),2);
                }else{
                    $coa_item->balances = number_format($coa->balance+ ($coa->transactions->sum('credit')-$coa->transactions->sum('debit')),2);
                }
                $coa_item->space = ($i*1);

                if(count($coa->sub) > 0){
                    $coa_item->keys = "parent";
                }else{
                    $coa_item->keys = "child";
                }
                
                $coas[] = $coa_item;
            }elseif($coa->detailtype->type == "liability"){
                $coa_item = new \stdClass;
                $coa_item = $coa;
                $coa_item->detailtypes = $coa->detailtype->detail_type_name;
                $coa_item->space = ($i*1);

                if(count($coa->sub) > 0){
                    $coa_item->keys = "parent";
                }else{
                    $coa_item->keys = "child";
                }
                
                $coas[] = $coa_item;
            }elseif($coa->detailtype->type == "equity"){

                $coa_item = new \stdClass;
                $coa_item = $coa;
                $coa_item->detailtypes = $coa->detailtype->detail_type_name;
                $coa_item->space = ($i*1);

                if(count($coa->sub) > 0){
                    $coa_item->keys = "parent";
                }else{
                    $coa_item->keys = "child";
                }
                
                $coas[] = $coa_item;
            }elseif($coa->detailtype->type == "revenue"){
                $coa_item = new \stdClass;
                $coa_item = $coa;
                $coa_item->detailtypes = $coa->detailtype->detail_type_name;
                $coa_item->space = ($i*1);

                if(count($coa->sub) > 0){
                    $coa_item->keys = "parent";
                }else{
                    $coa_item->keys = "child";
                }
                
                $coas[] = $coa_item;
            }elseif($coa->detailtype->type == "expenses"){
                $coa_item = new \stdClass;
                $coa_item = $coa;
                $coa_item->detailtypes = $coa->detailtype->detail_type_name;
                $coa_item->space = ($i*1);

                if(count($coa->sub) > 0){
                    $coa_item->keys = "parent";
                }else{
                    $coa_item->keys = "child";
                }
                
                $coas[] = $coa_item;
            }

            if($coa->sub){ 
                if(count($coa->sub)>0){
                    
                    $child = $this->coa_lists($coa->sub, $coa->id);


                    if($child)
                    {
                        $i--;
                        $coas = collect($coas)->merge($child);
                   
                    }
                }
            }

        }
        return $coas;
    }

    public function index(){
        // $coa = Chart_of_account::with('detailtype','sub','inventory')->get();

        // return response()->json([
        //     'data' => $coa
        // ]);
        // foreach($coa as $item){
      
        //     $data[$item->is_sub][] = $item;

        // }
        // $coa = $this->lists($data);
        return view('chartofaccounts.index');
    }
    public function manageCoa()
    {
        $coa = Chart_of_account::with('detailtype','inventory')
         ->where(function($q){
            if(Request::has('query')){
                $q->where('coa_title','like','%'.Request::get('query').'%');
            }
         })->get();
      $coas = $this
            ->paginate($this->coa_lists($coa),30)
            ->setPath('chartofaccount_data');

        return response()->json(['results'=>$coas]);
    }
    public function create(){
        $detailtype = Detail_type::pluck('detail_type_name','id');
        // $coa = Chart_of_account::pluck('coa_title','id');

        $coas = Chart_of_account::with('detailtype','sub')->get();
        foreach($coas as $item){
      
            $data[$item->is_sub][] = $item;

        }
        $coa = $this->category_list($data);
        return view('chartofaccounts.create',compact('detailtype','coa'));
    }

    public function store(){
        $all = Request::except('sub_item');

       
        $validator = Validator::make($all, [
   
            'coa_title'                                 => 'required',   
        ],
        [
            'coa_title.required'                        => 'Account Title Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('chartofaccount/create')
                        ->withErrors($validator)
                        ->withInput();
        }
      
        if(Request::has('isSubitem1')){

            $all['is_sub'] = Request::get('sub_item');
        }
       
        $coa = Chart_of_account::create($all);
        // $coa->transactions()->create([

        //         'coa_id' => $coa->id,
        //         'link_coa' =>1,
        //         'type' => "Opening Balance",
        //         'dt'=> $all['dt'],
        //         'debit' => $all['balance'],

        //     ]);

        // $coa->transactions()->create([

        //         'coa_id' => 1,
        //         'link_coa' =>$coa->id,
        //         'type' => "Opening Balance",
        //         'dt'=> $all['dt'],
        //         'credit' => $all['balance'],

        //     ]);

        Session::flash('flash_message','Account Entry Saved.');
        return redirect()->back();
    }

    public function create_journal()
    {   
        $coas = Chart_of_account::all()->lists('coa_title','id');
        $payees = Supplier::all()->lists('supplier_name','id');
        return view('chartofaccounts.create_journal',compact('coas','payees'));
    }

    public function customer_supplier(){
        $suppliers = Supplier::all();
        $customers = Customer::all();
        $lists = collect();
        foreach ($suppliers as $key => $supplier) {
            $trans = new \stdClass;
            $trans->type = "Supplier";
            $trans->text = $supplier->supplier_name;
            $trans->id =$supplier->id;
            $lists[] = $trans;
        }
        foreach ($customers as $key => $customer) {
            $trans = new \stdClass;
            $trans->type = "Customer";
            $trans->text = $customer->customer_name;
            $trans->id =$customer->id;
            $lists[] = $trans;
        }   
      
        // return "{ \"results\": " . json_encode($lists) . "}"; 
        return response()->json(['results'=>$lists]);
    }
    public function journal_store()
    {
        $all = Request::all();
        $x = 1;
        $count = count($all['account']);
        $temp = $all['account'][1];
        // dump(collect($all['account'])->chunk(2));
       
        foreach ($all['account'] as $key => $value) {
            $coaTrans =  Coa_transaction::create([
                    'coa_id'=>$value,
                    'type'=>'General Journal',
                    'dt'=>$all['dt'],
                    'ref'=>'',
                    'debit'=>$all['debit'][$key],
                    'credit'=>$all['credit'][$key],

                ]); 
          

            // if(!empty($all['debit'][$key])){
            //     echo "Wew</br>";
            //       $coaTrans =  Coa_transaction_link::create([
            //         'coa_id'=>$value,
            //         'type'=>'General Journal',
            //         'dt'=>$all['dt'],
            //         'ref'=>'',
            //         'debit'=>$all['debit'][$key],
            //         'credit'=>$all['credit'][$key],

            //     ]); 
            // }elseif(!empty($all['credit'][$key]){

            // }
        }
        return redirect()->back();
        // foreach (collect($all['account'])->chunk(2) as $keys => $chunk) 
        // {
        //     $x;
        //     foreach ( $chunk as $key => $value) {
          
        //         $x = $count;
        //         if($key === $x){
        //             echo "Wew";
        //         }
        //         dump($key);
        //         // $coaTrans =  Coa_transaction::create([
        //         //         'coa_id'=>$value,
        //         //         'link_coa'=>$coa,
        //         //         'type'=>'General Journal',
        //         //         'dt'=>$all['dt'],
        //         //         'ref'=>'',
        //         //         'debit'=>$all['debit'][$key],
        //         //         'credit'=>$all['credit'][$key],

        //         //     ]); 
        //         // 
        //     }
          
        //     // Coa_transaction::insert([
        //         // 'coa_id'=>$coa,
        //         // 'link_coa'=>$coa,
        //         // 'type'=>'General Journal',
        //         // 'dt'=>$all['dt'],
        //         // 'ref'=>'',
        //         // 'debit'=>$all['debit'][$key],
        //         // 'credit'=>$all['credit'][$key],
        //     //     'created_at'=>Carbon::now(),
        //     //     'updated_at'=> Carbon::now()   
        //     // ]);

        //     $x++;
        // }

    }
    public function show($id){
        $chart = Chart_of_account::with('detailtype','children','transactions')->where('id',$id)->first();




















        if($chart->detailtype->detail_type == "Cash and Cash Equivalents"){

            return view('chartofaccounts.cash_and_cash_equivalent',compact('chart'));

        }elseif($chart->detailtype->detail_type == "Accounts Receivable"){

            return view('chartofaccounts.ar',compact('chart'));

        }elseif($chart->detailtype->detail_type == "Accounts Payable"){

            return view('chartofaccounts.ap',compact('chart'));

        }elseif($chart->detailtype->detail_type == "Current Asset" || $chart->detailtype->detail_type == "Fixed Asset" || $chart->detailtype->detail_type == "Non-Current Asset" || $chart->detailtype->detail_type == "Non-Current Liabilities" || $chart->detailtype->detail_type == "Long Term Liabilities" || $chart->detailtype->detail_type =="Current Liabilities" || $chart->detailtype->detail_type == "Owner's Equity"){

            return view('chartofaccounts.asset',compact('chart'));

        }else{

            return view('chartofaccounts.show',compact('chart'));
        }

        
    }
    public function ar_show($id){
        $chart = Chart_of_account::with('detailtype','children','transactions')->where('id',$id)->first();
        return view('chartofaccounts.show',compact('chart'));
    }
    public function ap_show($id){
        $chart = Chart_of_account::with('detailtype','children','transactions')->where('id',$id)->first();
        return view('chartofaccounts.show',compact('chart'));
    }
    public function asset_show($id){
        $chart = Chart_of_account::with('detailtype','children','transactions')->where('id',$id)->first();
        return view('chartofaccounts.show',compact('chart'));
    }
    public function cash_equivalent_show($id){
        $chart = Chart_of_account::with('detailtype','children','transactions')->where('id',$id)->first();
        return view('chartofaccounts.show',compact('chart'));
    }

    public function edit($id){
        $detailtype = Detail_type::pluck('detail_type_name','id');
        // $coa = Chart_of_account::pluck('coa_title','id');

        $chart = Chart_of_account::with('detailtype','sub')->where('id',$id)->first();
        $coas = Chart_of_account::with('detailtype','sub')->get();
        foreach($coas as $item){
      
            $data[$item->is_sub][] = $item;

        }
        $coa = $this->category_list($data,$chart->is_sub);
        
        return view('chartofaccounts.edit',compact('coa','id','detailtype','chart'));
    }

    public function update($id){
        $coa = Request::except('_token','_method','sub_item','isSubitem1');
       
        if(Request::has('isSubitem1')){

            $coa['is_sub'] = Request::get('sub_item');
        }
       
        Chart_of_account::where('id',$id)->update($coa);
        Session::flash('flash_message','Chart of Account Title Entry Updated.');
        return redirect()->back();
    }
    public function deleteCoa($id)
    {   
       return Chart_of_account::destroy($id);
    }

    public function delete_item($id){
        $coa = Chart_of_account::find($id);
        Chart_of_account::where('id',$id)->delete($coa);
        Session::flash('flash_message','Chart of Account Title Entry Deleted.');
        return redirect()->back();
    }

    protected function paginate($items, $perPage = 12)
    {
        //Get current page form url e.g. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        //Slice the collection to get the items to display in current page
        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

        //Create our paginator and pass it to the view
        return new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    }
}
