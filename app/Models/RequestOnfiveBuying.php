<?php

namespace estateManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RequestOnfiveBuying extends Model
{
    protected $table ='requestonfivepayment';
    protected $fillable =[
        'user_id','bought_id','amount','v_transaction_id','transaction_id','payment_status','paid_by'
    ];
    protected $hidden =[];
    public function getAllOnFiveRequest(){
        return DB::table($this->table);
    }
    public function  getUnPaidRequests(){
        return $this->getAllOnFiveRequest()->where('payment_status',null);
    }
    public function isNotPaid($id){
        return (bool) $this->getUnPaidRequests()->where('id',$id)->count();
    }
}
