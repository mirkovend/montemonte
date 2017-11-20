<?php

namespace App\Http\Controllers;

use PDF;
use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Chart_of_account;
use App\Coa_transaction;
class AmortizationController extends Controller
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function index(){
    	$batch = \App\Batch::get();
        return view('amortizations.index',compact('batch'));
    }

    public function create(){
        $batch = Chart_of_account::where('coa_title','like','OPLYR Batch #%')->lists('coa_title','id');
        return view('amortizations.create',compact('batch'));
    }

    public function store(){
    	$all = Request::all();

        $validator = Validator::make(Request::all(), [
   
            'batch_number'                              => 'required',
            'number_heads'                              => 'required',
            'total_cost'                                => 'required',          
        ],
        [
            'batch_number.required'                     => 'Batch Number Field Required',
            'number_heads.required'                     => 'Number of Heads Field Required',
            'total_cost.required'                       => 'Total Cost Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('amortization/create')
                        ->withErrors($validator)
                        ->withInput();
        }   
        $batch = Chart_of_account::find(Request::get('batch_number'));
        $bachtnum = explode("OPLYR Batch #",$batch->coa_title);
        
        $data = [
            'batch_number' => $bachtnum[1],
            'number_heads' => $all['number_heads'],
            'total_cost' => $all['total_cost'],
            'pullet_price' => $all['pullet_price'],
            'dt' => $all['dt'],
        ];

        $batch1 = \App\Batch::create($data);

        $first_amor = \App\Amortization::first();
        
        $bb_value = Request::get('bb_value');
        $pullet_price = Request::get('pullet_price');

        $b_value = Request::get('total_cost') / Request::get('number_heads');
        
        $amortization = \App\Amortization::where('id','>=',2)->get();

        $amor_sum = \App\Amortization::sum('estimate_prod');

        $data = [
            'batch_id'              =>      $batch1->id,
            'amortization_id'       =>      $first_amor->id,
            'bb_value'              =>      $b_value,
            'amortization'          =>      (($b_value - $pullet_price) / $amor_sum) * $first_amor->estimate_prod,
            'netbook_value'         =>      $b_value - ((($b_value - $pullet_price) / $amor_sum) * $first_amor->estimate_prod),
        ];


        $netbook_value = \App\Amortization_batch::create($data)->netbook_value;

        //pre op.
        // $preop =  Chart_of_account::create([
        //     "is_sub" => 86,
        //     "coa_title" => "OPLYR Batch #".$batch1->batch_number,
        //     "detail_type_id" => 7,
        //     "typical_balance" => "Debit"

        // ]);

        //accu. amort.
        Chart_of_account::create([
            "is_sub" => 86,
            "coa_title" => "Accu. Amort. OPLYR Batch #".$bachtnum[1],
            "detail_type_id" => 7,
            "typical_balance" => "CREDIT"

        ]);
        //amortization exp
        Chart_of_account::create([
            "is_sub" => 350,
            "coa_title" => "Amort. Exp. OPLYR Batch #".$bachtnum[1],
            "detail_type_id" => 15,
            "typical_balance" => "DEBIT"

        ]);
        //mortality exp
        Chart_of_account::create([
            "is_sub" => 370,
            "coa_title" => "Mort. Exp. OPLYR Batch #".$bachtnum[1],
            "detail_type_id" => 15,
            "typical_balance" => "DEBIT"

        ]);
       // $coatrans = Coa_transaction::create([
       //      "coa_id" => $preop->id,
       //      "type"  => "Amortization",
       //      "dt"    => $batch["dt"],
       //      "ref"   => $batch["dt"].$batch1->batch_number,
       //      "debit" => $batch['total_cost'],
       //  ]);
        // $coatrans->coa_transaction_link()->create(['coa_id'=>$preop->id]);
        foreach($amortization as $am){
            $data = [
                'batch_id'              =>      $batch1->id,
                'amortization_id'       =>      $am->id,
                'bb_value'              =>      $netbook_value,
                'amortization'          =>      (($b_value - $pullet_price) / $amor_sum) * $am->estimate_prod,
                'netbook_value'         =>      $netbook_value - ((($b_value - $pullet_price) / $amor_sum) * $am->estimate_prod),
            ];

            $netbook_value = \App\Amortization_batch::create($data)->netbook_value;
        }

    	Session::flash('flash_message','Table of Amortization Saved.');
    	return redirect()->back();
    }

    public function edit($id){
    	$batch = \App\Batch::find($id);
        return view('amortizations.edit',compact('batch','id'));
    }

    public function update($id){
        $batch = \App\Batch::find($id);
    	$query = Request::except('_token','_method','bb_value');
        \App\Batch::where('id',$id)->update($query);

        \App\Amortization_batch::where('batch_id',$id)->delete();

        /*NEW AMORTIZATION BATCH*/
        $first_amor = \App\Amortization::first();
        
        $bb_value = Request::get('bb_value');
        $pullet_price = Request::get('pullet_price');

        $b_value = Request::get('total_cost') / Request::get('number_heads');
        
        $amortization = \App\Amortization::where('id','>=',2)->get();
        $amor_sum = \App\Amortization::sum('estimate_prod');

        $data = [
            'batch_id'              =>      $id,
            'amortization_id'       =>      $first_amor->id,
            'bb_value'              =>      $b_value,
            'amortization'          =>      (($b_value - $pullet_price) / $amor_sum) * $first_amor->estimate_prod,
            'netbook_value'         =>      $b_value - ((($b_value - $pullet_price) / $amor_sum) * $first_amor->estimate_prod),
        ];

        $netbook_value = \App\Amortization_batch::create($data)->netbook_value;

        foreach($amortization as $am){
            $data = [
                'batch_id'              =>      $id,
                'amortization_id'       =>      $am->id,
                'bb_value'              =>      $netbook_value,
                'amortization'          =>      (($b_value - $pullet_price) / $amor_sum) * $am->estimate_prod,
                'netbook_value'         =>      $netbook_value - ((($b_value - $pullet_price) / $amor_sum) * $am->estimate_prod),
            ];

            $netbook_value = \App\Amortization_batch::create($data)->netbook_value;
        }


    	Session::flash('flash_message','Table of Amortization Updated.');
    	return redirect()->back();
    }

    public function delete_item($id){
    	$batch = \App\Batch::find($id);
        $batch = \App\Batch::where('id',$id)->delete();
        $am_batch = \App\Amortization_batch::where('batch_id',$id)->delete();
    	Session::flash('flash_message','Table of Amortization Deleted.');
    	return redirect()->back();
    }

    public function print_report($id){
        $batch = \App\Batch::find($id);
        $am_batch = \App\Amortization_batch::where('batch_id',$id)->get();
        $amor_sum = \App\Amortization::sum('estimate_prod');
        $pdf = PDF::loadView('pdf.table_amortization', compact('batch','am_batch','id','amor_sum'))->setPaper('legal');
        return $pdf->stream('TABLE_AMORTIZATION_BATCH_'.$batch->batch_number.'.pdf');
    }

    public function mortality_index($id){
        $batchno = \App\Batch::find($id);
        $batch = \App\Batch::where('id',$id)->get();
        $mortality = \App\Mortality::where('batch_id',$id)->get();
        //dd($batchno);
        return view('amortizations.mortality',compact('batch','batchno','id','mortality'));
    }

    public function create_mortality($id){
        return view('amortizations.create-mortality',compact('id','batch'));
    }   

    public function store_mortality($id){
        // $batch_id = Request::get($id);

        $month = Request::get('month');
        $year = Request::get('year');

        $weeksTo = Request::get('weekTo');
        $weeksFrom = Request::get('weekFrom');
        $daysTo = Request::get('daysTo');
        $daysFrom = Request::get('daysFrom');
        $mortality = Request::get('mortality');
        $amort = 0;
        $mortality_exp = 0;

        $validator = Validator::make(Request::all(), [
   
            'weekFrom'                              => 'required',
            'daysFrom'                              => 'required',
            'weekTo'                                => 'required',      
            'daysTo'                                => 'required',       
            'mortality'                             => 'required',
            'month'                                 => 'required',
            'year'                                  => 'required',
        ],
        [
            'weekFrom.required'                     => 'Week From Field Required',
            'daysFrom.required'                     => 'Days From Field Required',
            'weekTo.required'                       => 'Week To Field Required',
            'daysTo.required'                       => 'Days To Field Required',
            'mortality.required'                       => 'Mortality Field Required',
            'month.required'                       => 'Month Field Required',
            'year.required'                       => 'Year Field Required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }   

        $mtable = \App\Mortality::where('batch_id',$id)->latest()->first();
      
        $batch = \App\Batch::with('amort_batch')->where('id',$id)->first();
        
         

        if(empty($mtable)){
            
            $bv = $batch->total_cost / $batch->number_heads;

            $mortality_exp = $bv * $mortality;

            $heads_balance = $batch->number_heads - $mortality;

            if($daysTo >= 1 && $daysTo <= 3){
                $weeks = \App\Amortization::where('age_weeks',$weeksTo)->first();
                $ams = \App\Amortization_batch::where('batch_id',$id)->where('amortization_id',$weeks->id)->first();
                $bvbird = $ams->netbook_value;
                $preOP = ($bvbird * $heads_balance) + 0.02;
                $amort = ($batch->total_cost - $preOP) - $mortality_exp;
            }else{

                $weeks = \App\Amortization::where('age_weeks',$weeksTo+1)->first();
                $ams = \App\Amortization_batch::where('batch_id',$id)->where('amortization_id',$weeks->id)->first();
                $bvbird = $ams->netbook_value;
                $preOP = ($bvbird * $heads_balance) + 0.02;
                $amort = ($batch->total_cost - $preOP) - $mortality_exp;
            }

            $data = [
                'batch_id'      =>      $id,
                'month'         =>      $month,
                'year'          =>      $year,
                'weeksTo'        =>      $weeksTo,
                'weeksFrom'      =>      $weeksFrom,
                'daysTo'        =>      $daysTo,
                'daysFrom'      =>      $daysFrom,
                'mortality'     =>      $mortality,
                'bv'            =>      $bv,
                'heads_balance' =>      $heads_balance,
                'amort'         =>      $amort,
                'mortality_exp' =>      $mortality_exp,
                'preOP'         =>      $preOP,
                'bvBird'        =>      $bvbird,
            ];


        }else{
            $batch_mortality = \App\Mortality::where('batch_id',$id)->orderBy('id','DESC')->first();

            $bv = $batch_mortality->bvBird;
            $mortality_exp = $bv * $mortality;
            $heads_balance = $batch_mortality->heads_balance - $mortality;

            if($daysTo >= 1 && $daysTo <= 3){
                $weeks = \App\Amortization::where('age_weeks',$weeksTo)->first();
                $ams = \App\Amortization_batch::where('batch_id',$id)->where('amortization_id',$weeks->id)->first();
                $bvbird = $ams->netbook_value;
                $preOP = ($bvbird * $heads_balance) + 0.02;
                $amort = ($batch_mortality->preOP - $preOP) - $mortality_exp;

            }else{
                $weeks = \App\Amortization::where('age_weeks',$weeksTo+1)->first();
                $ams = \App\Amortization_batch::where('batch_id',$id)->where('amortization_id',$weeks->id)->first();

                $bvbird = $ams->netbook_value;
                $preOP = ($bvbird * $heads_balance) + 0.02;
                $amort = ($batch_mortality->preOP - $preOP) - $mortality_exp;
            }

            $data = [
                'batch_id'      =>      $id,
                'month'         =>      $month,
                'year'          =>      $year,
                'weeksTo'        =>      $weeksTo,
                'weeksFrom'      =>      $weeksFrom,
                'daysTo'        =>      $daysTo,
                'daysFrom'      =>      $daysFrom,
                'mortality'     =>      $mortality,
                'bv'            =>      $bv,
                'heads_balance' =>      $heads_balance,
                'amort'         =>      $amort,
                'mortality_exp' =>      $mortality_exp,
                'preOP'         =>      $preOP,
                'bvBird'        =>      $bvbird,
            ];

            //dd($heads_balance);
        }
        //
        $coa_armort_expense = Chart_of_account::where('coa_title',"Amort. Exp. OPLYR Batch #".$batch->batch_number)->first();
    
        $coa_amort_accu = Chart_of_account::where('coa_title',"Accu. Amort. OPLYR Batch #".$batch->batch_number)->first();

        $coa_mort_exp = Chart_of_account::where('coa_title',"Mort. Exp. OPLYR Batch #".$batch->batch_number)->first();
        $coa_accu_mort = Chart_of_account::where('coa_title',"Amort. Exp. OPLYR Batch #".$batch->batch_number)->first();

        $preop = Chart_of_account::where('coa_title',"OPLYR Batch #".$batch->batch_number)->first();

        //Mortality

        //mort exp
        Coa_transaction::create([
            "coa_id" => $coa_mort_exp->id,
            "type"  => "Amortization",
            "dt"    => Carbon::now()->toDateString(),
            "ref"   => "",
            "debit" => $mortality_exp,
            // "link_coa" => $coa_mort_exp->id,
        ]);

        $preop_value = 0;
        $accu_amort_value = 0;
        if(empty($mtable)){
            $preop_value = $batch->amort_batch->bb_value * $mortality;
        }else{
            $preop_value = $mtable->bv * $mortality;
        }
        //preop
        Coa_transaction::create([
            "coa_id" => $preop->id,
            "type"  => "Amortization",
            "dt"    => Carbon::now()->toDateString(),
            "ref"   => "",
            "credit" => $preop_value,
            // "link_coa" => $preop->id,
        ]);

        //accu amortization 
        Coa_transaction::create([
            "coa_id" => $coa_amort_accu->id,
            "type"  => "Amortization",
            "dt"    => Carbon::now()->toDateString(),
            "ref"   => "",
            "debit" => $preop_value - $mortality_exp,
            // "link_coa" => $coa_amort_accu->id,
        ]);


        //Amort.
        //amortization exp
        Coa_transaction::create([
            "coa_id" => $coa_armort_expense->id,
            "type"  => "Amortization",
            "dt"    => Carbon::now()->toDateString(),
            "ref"   => "",
            "debit" => $amort,
            // "link_coa" => $coa_armort_expense->id,
        ]);

        //accu amortization 
        Coa_transaction::create([
            "coa_id" => $coa_amort_accu->id,
            "type"  => "Amortization",
            "dt"    => Carbon::now()->toDateString(),
            "ref"   => "",
            "credit" => $amort,
            // "link_coa" => $coa_amort_accu->id,
        ]);
       
        //dd($data);
        \App\Mortality::create($data);
        Session::flash('flash_message','Mortality Saved.');
        return redirect('amortization/mortality/'.$id);

    }
}
