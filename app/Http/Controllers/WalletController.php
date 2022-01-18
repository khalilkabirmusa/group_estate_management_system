<?php
/**
 * Created by PhpStorm.
 * User: DEATH
 * Date: 11/10/2018
 * Time: 10:42 AM
 */

namespace estateManagement\Http\Controllers;


use estateManagement\Models\BuyersTransaction;
use estateManagement\Models\Charge;
use estateManagement\Models\gallery;
use estateManagement\Models\WalletRechargeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }
    public function create(Request $request){
        $this->validate($request,[
           'amount'=>'required|present|min:200|max:50000|integer',
        ]);

        $transactionId = "W-".auth()->guard('web')->user()->username."-".rand(1000,9999)."-".rand(1000,9999);
        if(WalletRechargeTransaction::create([
           'user_id'=>auth()->guard('web')->user()->id,
            'transaction_id'=>$transactionId,
            'amount'=>$request->input('amount'),
        ])){
            return redirect()->route('wallet.receipt',['tansactionId'=>$transactionId]);
        }else{
            return redirect()->back()->with('failure',GeneralController::error_success('Whoops!!','Something Went Wrong'));
        }
    }
    public function walletPayment(Charge $charge,$mRef){
        $char = trim(strtoupper(substr($mRef,0,1)));
        $userWallet = auth()->guard('web')->user()->myWallet()->first();
        if($userWallet->amount >=150){
            if ($char=='O'){
                $galleryObj = gallery::where('transactionId', $mRef)->where(function ($query){
                    $query->where('paymentStatus','<>',1)
                        ->orWhere('paymentStatus',null);
                })->first();
                if($galleryObj==null || $galleryObj->user_id!=auth()->guard('web')->user()->id){
                    return redirect()->route('homepage')->with('failure',GeneralController::error_success('Whoops',"Transaction Not Found"));
                }
                if($charge->ownerCharge()->first()->amount <= $userWallet->amount) {
                    $walletBal = $userWallet->amount - $charge->ownerCharge()->first()->amount;
                    $userWallet->update([
                       'amount'=>$walletBal,
                    ]);
                    $galleryObj->update([
                        'paymentStatus' => 1,
                    ]);
                    return redirect()->route('general.receipt',['transactionId'=>$mRef]);
                }else{
                    return redirect()->route('general.receipt')->with('failure',GeneralController::error_success('Whoops',"Insufficient Funds"));
                }
            }elseif ($char=='B'){
                $buyersTranObj=BuyersTransaction::where('transaction_id',$mRef)->where(function ($query){
                    $query->where('payment_status','<>',1)->orWhere('payment_status',null);
                })->first();
                if($buyersTranObj==null || $buyersTranObj->user_id!=auth()->guard('web')->user()->id){
                    return redirect()->route('homepage')->with('failure',GeneralController::error_success('Whoops',"Transaction not found"));
                }
                if($buyersTranObj->amount <= $userWallet->amount) {
                    $walletBal = $userWallet->amount - $buyersTranObj->amount;
                    $userWallet->update([
                        'amount'=>$walletBal,
                    ]);
                    $buyersTranObj->update([
                        'payment_status' => 1,
                    ]);
                    return redirect()->route('general.buyerReceipt',['transactionId'=>$mRef]);
                }else{
                    return redirect()->route('homepage')->with('failure',GeneralController::error_success('Whoops',"something went wrong"));
                }
            }else{
                return redirect()->route('homepage')->with('failure',GeneralController::error_success('Whoops',"Invalid Transaction"));
            }
        }else{
            return redirect()->back()->with('failure',GeneralController::error_success('Incorrect',"Balance Too Low"));
        }
    }
    public function createBuyersTransaction(gallery $galleryId,Charge $charge){
        if(!$galleryId->paymentStatus==1){
            return redirect()->route('homepage')
                ->with('failure',GeneralController::error_success('Whoops','Sorry You Can Not Do That'));
        }
        $transaction_id = "B-".
            auth()->guard('web')
                ->user()->username."-".rand(1000,9999)."-".rand(1000,9999);
        BuyersTransaction::create([
            'user_id'=>auth()->guard('web')->user()->id,
            'gallery_id'=>$galleryId->id,
            'amount'=> $charge->buyerCharge()->first()->amount,
            'transaction_id'=>$transaction_id,
        ]);
        return redirect()->route('general.buyerReceipt',['transactionId'=>$transaction_id]);
    }

}