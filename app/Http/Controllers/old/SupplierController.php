<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Supplier;
use Request;
use Carbon\Carbon;
use Validator;
class SupplierController extends Controller
{
    
    public function index(){
    	$suppliers = Supplier::paginate(12);
    	return view('supplier.index',compact('suppliers'));
    }

    public function create(){
    	return view('supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {   
        $validator = Validator::make(Request::all(), [
   
        'fname'         => 'required',
        'mname'         => 'required',
        'lname'         => 'required',
        'companyName'   => 'required',
        'displayName'   => 'required',
        'address'       => 'required',
        'city'          => 'required',
        'avatar'        => 'required',
        'StateProvince' => 'required',
        ],[
            'fname.required' => 'First Name Field Required',
            'mname.required' => 'Middle Name Field Required',
            'lname.required' => 'Last Name Field Required',
            'companyName.required' => 'Company Name Field Required',
            'displayName.required' => 'Display Name Field Required',
            'address.required' => 'Address Field Required',
            'avatar.required' => 'Avatar Field Required',
            'StateProvince.required' => 'State/Province Field Required',
            'city.required' => 'City Field Required',
            ]);

        if ($validator->fails()) {
            return redirect('supplier/create')
                        ->withErrors($validator)
                        ->withInput();
        }


        $supp = 'active';
        $data = $request->except(['avatar']);
        $avatar = $request->file('avatar');


        $filename = str_random(20).$avatar->getClientOriginalName();
        $avatar->move(public_path().'/uploads',$filename);
        $data['avatar'] = $filename ;
        Supplier::create($data);
        return redirect('supplier');
    }
}
