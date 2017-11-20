<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
class DashboardController extends Controller
{

    public function index(){
        return view('dashboard.index');
    }
    public function data(){
         $devlist = DB::table('suppliers')
            ->select(DB::raw('MONTHNAME(updated_at) as month'), DB::raw("DATE_FORMAT(updated_at,'%Y-%m') as monthNum"), DB::raw('count(*) as projects'))
            ->groupBy('monthNum')
            ->get();
            
        return $devlist;
    }
}
