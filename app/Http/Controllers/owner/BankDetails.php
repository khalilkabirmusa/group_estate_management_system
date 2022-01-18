<?php
/**
 * Created by PhpStorm.
 * User: DEATH
 * Date: 11/19/2018
 * Time: 12:55 PM
 */

namespace estateManagement\Http\Controllers\owner;
use estateManagement\Http\Controllers\Controller;
use estateManagement\Http\Controllers\GeneralController;
use estateManagement\Models\ActivityLog;
use Illuminate\Http\Request;

class BankDetails extends Controller
{
        public function __construct()
    {
        $this->middleware('auth:web');
    }
        public  function bankDetails(Request $request){
            $this->validate($request,[
                'account_number'=>['required','present','unique:bank_details,account_number',function($attribute,$value,$fail){
                if(strlen($value)!==10){
                    $fail("The Account Number must be 10 Characters");
                }
                }],
                'bank_name'=>'required|present|min:6',
                'sort_code'=>'required|present|numeric',
            ]);
            if(auth()->guard('web')->user()->bankDetails()->create([
                'account_number'=>$request->input('account_number'),
                'bank_name'=>$request->input('bank_name'),
                'sort_code'=>$request->input('sort_code'),
            ])){
                ActivityLog::create([
                    'user_id'=>\auth('web')->user()->id,
                    'action'=>"Added Bank Details",
                ]);
                return redirect()->route('owner.ownersMessages')
                    ->with('success',GeneralController::error_success('Done','Details Sent'));
            }
        }


}