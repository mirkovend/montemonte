<?php

namespace App\Http\Controllers;

use App\User;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class UseraccessController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
    	$users = User::get();
        return view('users.index',compact('users'));
    }

    public function create(){
    	return view('users.create');
    }

    public function store(){
        $validator = Validator::make(Request::all(), [
   
            'name'                                      => 'required',
            'status'                                    => 'required',          
            'usertype'                                  => 'required',
        ],
        [
            'name.required'                             => 'Name Field Required',
            'status.required'                           => 'Status Field Required',
            'usertype.required'                         => 'Usertype Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('user/create')
                        ->withErrors($validator)
                        ->withInput();
        }

    	$fullname = Request::get('name');

        $data = [
            'name'                  =>      strtolower($fullname),
            'email'                 =>      strtolower($fullname).'@montemaria.com',
            'password'              =>      bcrypt(strtolower($fullname)),
            'status'                =>      Request::get('status'),
            'usertype'              =>      Request::get('usertype'),
            'created_at'            =>      Carbon::now(),
            'updated_at'            =>      Carbon::now(),
        ];

        User::create($data);

    	Session::flash('flash_message','User Created.');
    	return redirect()->back();
    }

    public function edit($id){
        $user = User::find($id);
    	return view('users.edit',compact('user','id'));
    }

    public function update($id){
    	$user = User::find($id);

        $fullname = Request::get('name');
        $data = [
            'name'                  =>      strtolower($fullname),
            'email'                 =>      strtolower($fullname).'@montemaria.com',
            'password'              =>      bcrypt(strtolower($fullname)),
            'usertype'              =>      Request::get('usertype'),
            'updated_at'            =>      Carbon::now(),
        ];
        User::where('id',$id)->update($data);
        Session::flash('flash_message','User Updated.');
        return redirect()->back();
    }


    public function reset_password($id){
        $user = User::find($id);

        $data = ['password'     =>     strtolower($user->name),];
        User::where('id',$id)->update($data);

        Session::flash('flash_message','User Password Reset.');
        return redirect()->back();
    }

    public function user_status($id){
        $user = User::find($id);

        if($user->status == 1){
            $data = ['status'     =>     2,];
            
            User::where('id',$id)->update($data);
            Session::flash('flash_message','User Deactivated.');

        }else{
            $data = ['status'     =>     1,];

            User::where('id',$id)->update($data);
            Session::flash('flash_message','User Activated.');
        }
        return redirect()->back();
    }
}
