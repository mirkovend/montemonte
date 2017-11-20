<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded = [];

    public function item_type(){
        return $this->hasOne('App\Item_type','id','item_type_id');
    }

    public function coa_title(){
        return $this->belongsTo('App\Chart_of_account','coa_id','id');
    }
    public function item_flow(){
        return $this->hasMany('App\itemFlow','item_id','id');
    }

    public function item_sub(){
        return $this->hasMany('App\itemFlow','is_sub','id');
    }

    public function po_item(){
        return $this->hasOne('App\Purchase_order_item','account_item_id','id');
    }
    public function po_item_many(){
        return $this->hasMany('App\Purchase_order_item','account_item_id','id');
    }
    
    public function sub(){
        return $this->hasMany('App\Item','is_sub','id');
    }
       public function sub_one(){
        return $this->hasOne('App\Item','is_sub','id');
    }


    public function adjustment_item(){
        return $this->hasMany('App\Inventory_item','item_id','id');
    }
    public function delivery_item(){
        return $this->hasMany('App\Dr_item','size_id','id');
    }
    public function invoice_item(){
        return $this->hasMany('App\Charge_invoice_item','size_id','id');
    }
    public function invoice_item_old(){
        return $this->hasMany('App\Charge_invoice_item','size_id','id');
    }
    public function cash_invoice_item(){
        return $this->hasMany('App\Cash_invoice_item','size_id','id');
    }

    public function cash_invoice_item_old(){
        return $this->hasMany('App\Cash_invoice_item','size_id','id');
    }



    public function item_flow_one(){
        return $this->hasOne('App\itemFlow','item_id','id');
    }   

    public function item_voucher(){
        return $this->hasOne('App\itemFlow','item_id','id');
    }
    /*public function coa_sub(){
        return $this->hasOne('App\Coa_subitem','id','coa_subaccount');
    }

    public function item(){
        return $this->hasOne('App\Item','id','id');
    }*/

    public function parent()
    {
        return $this->belongsTo('App\Item', 'id','is_sub');
    }

    public function children()
    {
        return $this->hasMany('App\Item', 'is_sub','id');
    }

    public function category(){
        return $this->belongsTo('App\ItemCategory','category_id',"id");
    }
}	