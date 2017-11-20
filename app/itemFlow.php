<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class itemFlow extends Model
{
    
  
    protected $guarded = [];
    public function vc_item(){
        return $this->belongsTo('App\Voucher_item','ref_no','voucher_number');
    }
    public function vc_job_item(){
        return $this->belongsTo('App\Voucher_job_item','ref_no','voucher_number');
    }
    public function po_item(){
        return $this->belongsTo('App\Purchase_order_item','item_id','account_item_id');
    }

    public function ci(){
        return $this->belongsTo('App\Cash_invoice','ref_no','cash_invoice_number');
    }

    public function ci_item(){
        return $this->belongsTo('App\Cash_invoice_item','item_id','size_id');
    }

    public function invoice(){
        return $this->belongsTo('App\Charge_invoice','ref_no','charge_invoice_number');
    }

    public function invoice_item(){
        return $this->belongsTo('App\Charge_invoice_item','item_id','size_id');
    }

    public function dr(){
        return $this->belongsTo('App\Delivery_receipt','ref_no','delivery_receipt_number');
    }

    public function dr_item(){
        return $this->belongsTo('App\Dr_item','item_id','size_id');
    }

    public function inventory(){
        return $this->belongsTo('App\Inventory','ref_no','ref_no');
    }

    public function inventory_item(){
        return $this->belongsTo('App\Inventory_item','item_id','item_id');
    }
}
