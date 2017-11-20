<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;

use App\Http\Requests;
use App\ItemCategory;
use Request;
class CategoryController extends Controller
{
    public function index(){

    }
    public function create(){
    	$cat = ItemCategory::get();
    	return view('items.categories.create',compact('cat'));
    }

    public function store(){
    	$all = Request::except('isSubitem','is_sub');

    	if(Request::has('isSubitem')){
    		$all['is_sub'] = Request::get('is_sub');
    	}else{
    		$all['is_sub'] = 0;
    	}
    	
    	$cat = ItemCategory::create($all);
    	return redirect()->back();
    }
    public function edit($id){
    	$category = ItemCategory::find($id);
    	$cat = ItemCategory::get();
    	return view('items.categories.edit',compact('category','cat'));
    }
    public function update($id){
    	$category = ItemCategory::find($id);
    	$category->update(Request::except('isSubitem'));
    	return redirect()->back();
    }
    public function delete($id){
    	
        $cat = ItemCategory::destroy($id);
       
        return redirect('items/categories/create');
       
    }
}
