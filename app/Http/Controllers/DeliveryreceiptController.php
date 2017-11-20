<?php

namespace App\Http\Controllers;

use App\Delivery_receipt;
use App\Dr_item;
use App\Egg_unit;
use App\Egg_size;
use App\Egg_house;
use App\Egg_house_item;
use App\Starting_account_number;
use App\Item;
use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\itemFlow;
class DeliveryreceiptController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
        $deliveryreceipt = Delivery_receipt::orderBy('dt','DESC')->get();
        return view('deliveryreceipts.index',compact('deliveryreceipt'));
    }

    public function create(){
    	$deliveryreceipt = Delivery_receipt::orderBy('delivery_receipt_number','DESC')->first();
        
        if($deliveryreceipt==NULL){
            $start = Starting_account_number::select('account_number')->where('account_name','=','delivery_receipt')->first();
            $start_number = $start->account_number;
        }else{
            $start_number = $deliveryreceipt->delivery_receipt_number+1;
        }

    	$unit = Egg_unit::pluck('unit_code','id');
    	$size = Item::pluck('item_name','id');
        $house = Egg_house::pluck('egg_house_name','id');
    	return view('deliveryreceipts.create',compact('unit','size','start_number','house'));
    }

    public function store(){
        $items = Request::all();

        $validator = Validator::make(Request::all(), [
   
            'delivery_receipt_qty.*'                    => 'required',
            'unit_id.*'                                 => 'required',
            'size_id.*'                                 => 'required',
            // 'house_qty.*'                               => 'required',
            // 'house_id.*'                               => 'required',
            
        ],
        [
            'delivery_receipt_qty.*.required'           => 'Delivery Quantity Field Required',
            'unit_id.*.required'                        => 'Unit Field Required',
            'size_id.*.required'                        => 'Size Field Required',
            // 'house_qty.*.required'                      => 'House Quantity Field Required',
            // 'house_id.*.required'                       => 'House Name Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('deliveryreceipt/create')
                        ->withErrors($validator)
                        ->withInput();
        }


        Delivery_receipt::create($items);

        foreach(Request::get('delivery_receipt_qty') as $key => $value){
            $data = [
                'delivery_receipt_number'   =>  Request::get('delivery_receipt_number'),
                'delivery_receipt_qty'      =>  $value,
                // 'unit_id'                   =>  Request::get('unit_id')[$key],
                'size_id'                   =>  Request::get('size_id')[$key],
                'dt'                        =>  Request::get('dt'),
            ];

            Dr_item::create($data);
            itemFlow::insert(['type'=>'dr','item_id'=>Request::get('size_id')[$key],'ref_no'=>Request::get('delivery_receipt_number'),'debit'=>$value,'created_at'=>carbon::now(),'updated_at'=>carbon::now()]);
        }

        foreach(Request::get('house_qty') as $key => $value){
            $data2 = [
                'delivery_receipt_number'   =>  Request::get('delivery_receipt_number'),
                'house_qty'                 =>  $value,
                'house_id'                  =>  Request::get('house_id')[$key],
                'dt'                        =>  Request::get('dt'),
            ];

            Egg_house_item::create($data2);
        }

        Session::flash('flash_message','Delivery Receipt Entry Saved.');
        return redirect()->back();
    }

    public function edit($id){
        $dr = Delivery_receipt::with('invoice','egghouseitem')->find($id);
        $items = Dr_item::where('delivery_receipt_number',$dr->delivery_receipt_number)->get();
        $hitems = Egg_house_item::where('delivery_receipt_number',$dr->delivery_receipt_number)->get();
        $unit = Egg_unit::pluck('unit_code','id');
        $size = Item::pluck('item_name','id');
        $house = Egg_house::pluck('egg_house_name','id');
        //dd($hitems);
        return view('deliveryreceipts.edit',compact('dr','id','unit','size','items','hitems','house'));
    }

    public function update($id){
        $data = [
            'dt'                            =>      Request::get('dt'),
            'delivery_receipt_number'       =>      Request::get('delivery_receipt_number'),
        ];
        Delivery_receipt::where('id',$id)->update($data);
        Dr_item::where('delivery_receipt_number',Request::get('delivery_receipt_number'))->delete();
        Egg_house_item::where('delivery_receipt_number',Request::get('delivery_receipt_number'))->delete();
        foreach(Request::get('delivery_receipt_qty') as $key => $value){
            if(!empty($value)){
                $data2 = [
                    'delivery_receipt_number'   =>  Request::get('delivery_receipt_number'),
                    'delivery_receipt_qty'      =>  $value,
                    'size_id'                   =>  Request::get('size_id')[$key],
                    'dt'                        =>  Request::get('dt'),
                ];

                Dr_item::insert($data2);
            }  
        }
        

        foreach(Request::get('house_qty') as $key => $value){
            if(!empty($value)){
                $data3 = [
                    'delivery_receipt_number'   =>  Request::get('delivery_receipt_number'),
                    'house_qty'                 =>  $value,
                    'house_id'                  =>  Request::get('house_id')[$key],
                    'dt'                        =>  Request::get('dt'),
                ];

                Egg_house_item::create($data3);
            }
        }

        Session::flash('flash_message','Delivery Receipt Entry Updated.');
        return redirect()->back();
    }

    public function delete_item($id){
        $item = Dr_item::where('id',$id)->first();
        Dr_item::where('id',$id)->delete($item);
        Session::flash('flash_message','Delivery Receipt Item Deleted.');
        return redirect()->back();
    }

    public function delete_house_item($id){
        $item = Egg_house_item::where('id',$id)->first();
        Egg_house_item::where('id',$id)->delete($item);
        Session::flash('flash_message','Egg House Item Deleted.');
        return redirect()->back();
    }
}
