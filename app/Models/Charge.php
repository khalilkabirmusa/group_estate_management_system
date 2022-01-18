<?php

namespace estateManagement\Models;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $table ="charges";
    protected $fillable =['type', 'amount'
    ];
    protected $hidden =[];
    public function getCharges(){
        return Charge::all();
    }
    public function ownerCharge(){
        return $this->getCharges()->where('type',1);
    }
    public function buyerCharge(){
        return $this->getCharges()->where('type',0);
    }
    public function onFiveCharge(){
        return $this->getCharges()->where('type',2);
    }


}
