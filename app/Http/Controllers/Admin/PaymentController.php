<?php
/**
 * Created by PhpStorm.
 * User: DEATH
 * Date: 11/22/2018
 * Time: 8:46 AM
 */

namespace estateManagement\Http\Controllers\Admin;


use estateManagement\Http\Controllers\Controller;
use estateManagement\Models\AdminLog;
use estateManagement\Models\BankDetails;
use estateManagement\Models\BuyersTransaction;
use estateManagement\Models\Charge;
use estateManagement\Models\gallery;
use estateManagement\Models\RequestOnfiveBuying;
use estateManagement\Models\User;
use estateManagement\Models\Wallet;
use estateManagement\Models\WalletRechargeTransaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private $merchantId ="DEMO";
    private $notifyUrl;
    private $failedUrl;
    private $successUrl;
    private $developerCode;
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->notifyUrl =route('payment.notify');
        $this->successUrl = route('payment.success');
        $this->failedUrl =route('payment.failed');
    }
    public function requery(Request $request,Charge $charge){
        $merchant_id = $this->merchantId;
        //get the full transaction details as an json from voguepay
        $json = file_get_contents('https://voguepay.com/?v_transaction_id='.$request->input('transaction').'&type=json&demo=true');
        //create new array to store our transaction detail
        $transaction = json_decode($json, true);
        $mRef= $transaction['merchant_ref'];
        $char = trim(strtoupper(substr($mRef,0,1)));
        if($transaction['status'] == 'Approved'){
            if (strtoupper($transaction['merchant_id']) == strtoupper($merchant_id)) {
                if($char=='W'){
                    $walletObj = WalletRechargeTransaction::where('transaction_id',$mRef)->first();
                    if($walletObj->payment_status !==1) {
                        $walletObj->update(['payment_status' => 1,'amount' => $transaction['total'],'v_transaction_id' => $transaction['transaction_id']]);

                        $userWallet = Wallet::where('user_id', $walletObj->user_id)->first();
                        $prevAmount = $userWallet->amount;
                        $currentAmount = $prevAmount + trim($transaction['total']);
                        $userWallet->update(['amount' => $currentAmount]);
                        AdminLog::create([
                            'user_id'=>\auth('admin')->user()->id,
                            'action'=>"Requeried Wallet Recharge Transaction"]);
                    }
                }elseif ($char=='O'){
                    $galleryObj = gallery::where('transactionId', $mRef)->first();
                    if($charge->ownerCharge()->first()->amount == trim($transaction['total'])) {
                        $galleryObj->update([
                            'v_transaction_id' => $transaction['transaction_id'],
                            'paymentStatus' => 1,
                        ]);
                        AdminLog::create([
                            'user_id'=>\auth('admin')->user()->id,
                            'action'=>"requery advert transaction"]);
                    }else{
                        $galleryObj->update([
                            'v_transaction_id' => $transaction['transaction_id'],
                            'paymentStatus' => 3,
                        ]);
                        AdminLog::create([
                            'user_id'=>\auth('admin')->user()->id,
                            'action'=>"updated payment status"]);
                    }
                }elseif ($char=='B'){
                    $buyersTranObj=BuyersTransaction::where('transaction_id',$mRef)->first();
                    if($charge->buyerCharge()->first()->amount == trim($transaction['total'])) {
                        $buyersTranObj->update([
                            'v_transaction_id' => $transaction['transaction_id'],
                            'payment_status' => 1,
                        ]);
                        AdminLog::create([
                            'user_id'=>\auth('admin')->user()->id,
                            'action'=>"requery property access transaction"]);
                    }else{
                        $buyersTranObj->update([
                            'v_transaction_id' => $transaction['transaction_id'],
                            'payment_status' => 3,
                        ]);
                        AdminLog::create([
                            'user_id'=>\auth('admin')->user()->id,
                            'action'=>"updates payment status for property access"]);
                    }
                }
                AdminLog::create([
                    'user_id'=>\auth('admin')->user()->id,
                    'action'=>"requery wallet transaction"]);
            } else {
                if($char=='W') {
                    $walletObj = WalletRechargeTransaction::where('transaction_id',$mRef)->first();
                    $walletObj->update(['payment_status'
                    => 2, 'amount'
                    => $transaction['total'], 'v_transaction_id'
                    => $transaction['transaction_id']]);
                }elseif($char=='O'){
                    $galleryObj=gallery::where('transactionId',$mRef)->first();
                    $galleryObj->update([
                        'v_transaction_id'=>$transaction['transaction_id'],
                        'paymentStatus'=>2,
                    ]);
                }elseif ($char=='B'){
                    $buyersTranObj=BuyersTransaction::where('transaction_id',$mRef)->first();
                    $buyersTranObj->update([
                        'v_transaction_id'=>$transaction['transaction_id'],
                        'payment_status'=>2,
                    ]);
                    AdminLog::create([
                        'user_id'=>\auth('admin')->user()->id,
                        'action'=>"requery for property access transaction"]);
                }
            }
        }
        AdminLog::create([
            'user_id'=>\auth('admin')->user()->id,
            'action'=>"requery transactions",]);
        return redirect()->back()->with('transaction',$transaction);
    }
    public function bTran(Request $request){
        $this->validate($request,[
           'bUsername'=>'required|present|exists:users,username',
        ]);
        $user=User::where('username',$request->bUsername)->first();
            $buyerTran = $user->myBoughtProperties();
        AdminLog::create([
            'user_id'=>\auth('admin')->user()->id,
            'action'=>"requery for property access",]);
            return redirect()
                ->back()
                ->with('transactions',$buyerTran->get())
                ->with('tab',3);
    }
    public function advertTran(Request $request){
        $this->validate($request,[
            'advertUsername'=>'required|present|exists:users,username',
        ]);
        $user=User::where('username',$request->advertUsername)->first();
        $advertTran = $user->myPaidProperties()->latest();
        AdminLog::create([
            'user_id'=>\auth('admin')->user()->id,
            'action'=>"requery advert transaction",]);

        return redirect()
            ->back()
            ->with('transactions',$advertTran->get())
            ->with('tab',4);
    }
    public function payOnFive($arrays){
        $charge = new Charge;
        $api = 'https://voguepay.com/api/';
        $ref = 'P-'.time();
        $task = 'withdraw';
        RequestOnfiveBuying::whereIn('id',$arrays)->update(['ref'=>$ref]);
        $merchant_id = $this->merchantId;
        $merchant_email_on_voguepay = 'merchant@example.com';
        $ref = time().mt_rand(0,9999999);
        $command_api_token = '9ufkS6FJffGplu9t7uq6XPPVQXBpHbaN';
        $hash = hash('sha512',$command_api_token.$task.$merchant_email_on_voguepay.$ref);



        $fields['task'] = $task;
        $fields['merchant'] = $merchant_id;
        $fields['ref'] = $ref;
        $fields['hash'] = $hash;

//loop through every account you want to withdraw into
        foreach($arrays as $array){
            $bank = BankDetails::where('user_id',RequestOnfiveBuying::find($array)->user_id)->firs();
            $list = array();
            $list['id'] = $array;
            $list['amount'] = RequestOnfiveBuying::find($array)->amount;//This would probably be fetched from database
            $list['bank_name'] = $bank->bank_name;//This would probably be fetched from database
            $list['bank_acct_name'] = $bank->acct_name.$array;//This would probably be fetched from database
            $list['bank_account_number'] = $bank->account_number.$array;//This would probably be fetched from database
            $list['bank_currency'] = 'Nigerian Naira';
            $list['bank_country'] = 'Nigeria';
            $fields['list'][] = $list;

        }

        $fields_string = 'json='.urlencode(json_encode($fields));

//open curl connection
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $api);
        curl_setopt($ch,CURLOPT_HEADER, false); //we dont need the headers
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);// data coming back is put into a string
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
        curl_setopt($ch,CURLOPT_MAXREDIRS,2);
        $reply_from_voguepay = curl_exec($ch);//execute post
        curl_close($ch);//close connection
        AdminLog::create([
            'user_id'=>\auth('admin')->user()->id,
            'action'=>"paid bonus",]);

    }
}