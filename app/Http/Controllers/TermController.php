<?php

namespace App\Http\Controllers;

use App\Term;

use Validator;
use DB;
use Session;
use Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class TermController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
    	$term = Term::orderBy('term_days')->get();
    	return view('terms.index',compact('term'));
    }

    public function create(){
    	return view('terms.create');
    }

    public function store(){
    	$term = Request::all();

        $validator = Validator::make(Request::except('supplier_contact'), [
   
            'term_days'                             => 'required',          
        ],
        [
            'term_days.required'                    => 'Terms Field Required',
        ]);

        if ($validator->fails()) {
            return redirect('term/create')
                        ->withErrors($validator)
                        ->withInput();
        }

    	Term::create($term);
    	Session::flash('flash_message','Term Entry Saved.');
    	return redirect()->back();
    }

    public function edit($id){
    	$term = Term::find($id);
    	return view('terms.edit',compact('term','id'));
    }

    public function update($id){
    	$term = Request::except('_token','_method');
    	Term::where('id',$id)->update($term);
    	Session::flash('flash_message','Term Entry Updated.');
    	return redirect()->back();
    }

    public function delete_item($id){
    	$term = Term::find($id);
    	Term::where('id',$id)->delete($term);
    	Session::flash('flash_message','Term Entry Deleted.');
    	return redirect()->back();
    }
}
