<?php

namespace estateManagement\Models;

use Illuminate\Database\Eloquent\Model;

class BankDetails extends Model
{
    protected $table = "bank_details";
    protected $fillable =['user_id','account_number','bank_name','sort_code'
    ];
    protected $hidden = [

    ];
}
