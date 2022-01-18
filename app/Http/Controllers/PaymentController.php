<?php

namespace estateManagement\Http\Controllers;

use estateManagement\Models\ActivityLog;
use estateManagement\Models\BuyersTransaction;
use estateManagement\Models\Charge;
use estateManagement\Models\gallery;
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
        $this->middleware('auth:web')->except('notifyTransaction');
        $this->notifyUrl =route('payment.notify');
        $this->successUrl = route('payment.success');
        $this->failedUrl =route('payment.failed');
    }
     public function payment(){
         return view('receipt')->with('title', 'paymentreceipt');
     }
     public function ownerReceipt($transactionId,Charge $charge){
         $propertyObj= gallery::where('transactionId',$transactionId)->first();
         if($propertyObj!==null) {
             if($propertyObj->user_id==auth()->guard('web')->user()->id) {
                 $purpose = "Payment For Property Advert";
                 $amount = $propertyObj->amount;
                 $fullname = auth()->guard('web')->user()->getFullName();
                 $phone = auth()->guard('web')->user()->phone;
                 $data = ["purpose" => $purpose,
                     "amount" => $amount, "fullname" => $fullname, "phone" => $phone,
                     "transactionId" => $transactionId];
                 ActivityLog::create([
                     'user_id'=>\auth('web')->user()->id,
                     'action'=>" Paid for a transaction",]);
                 return view('general.receipt')
                     ->with('title', 'Payment Receipt')
                     ->with('data', $data)->with('obj',$propertyObj);
             }else{
                 return redirect()
                     ->route('homepage')
                     ->with('failure',GeneralController::error_success('Invalid','You Can not Pay For A Transaction That Is Not Yours'));
             }
         }else{
             return redirect()
                 ->route('homepage')
                 ->with('failure',GeneralController::error_success('Invalid','The provided Transaction Id is Invalid'));
         }
     }
     public function buyerReceipt($transactionId){
        $propertyObj= BuyersTransaction::where('transaction_id',$transactionId)->first();
        if($propertyObj!==null) {
            if($propertyObj->user_id==auth()->guard('web')->user()->id) {
                $purpose = "Payment For Property Access";
                $amount = $propertyObj->amount;
                $fullname = auth()->guard('web')->user()->getFullName();
                $phone = auth()->guard('web')->user()->phone;
                $data = ["purpose" => $purpose,
                    "amount" => $amount, "fullname" => $fullname, "phone" => $phone,
                    "transactionId" => $transactionId];
                ActivityLog::create([
                    'user_id'=>\auth('web')->user()->id,
                    'action'=>" Paid for Property Access",]);
                return view('general.receipt')
                    ->with('title', 'Payment Receipt')
                    ->with('data', $data)->with('obj',$propertyObj);
            }else{
                return redirect()
                    ->route('homepage')
                    ->with('failure',GeneralController::error_success('Invalid','You Can not Pay For A Transaction That Is Not Yours'));
            }
        }else{
            return redirect()
                ->route('homepage')
                ->with('failure',GeneralController::error_success('Invalid','The provided Transaction Id is Invalid'));
        }
    }
     public function walletReceipt($transactionId){
         $walletObj= WalletRechargeTransaction::where('transaction_id',$transactionId)->first();

         if($walletObj!==null) {
             if($walletObj->user_id==auth()->guard('web')->user()->id) {
                 $purpose = "Payment For Wallet Recharge";
                 $amount = $walletObj->amount;
                 $fullname = auth()->guard('web')->user()->getFullName();
                 $phone = auth()->guard('web')->user()->phone;
                 $data = ["purpose" => $purpose,
                     "amount" => $amount, "fullname" => $fullname, "phone" => $phone,
                     "transactionId" => $transactionId];
                 ActivityLog::create([
                     'user_id'=>\auth('web')->user()->id,
                     'action'=>"Recharge his/her Wallet",]);
                 return view('general.receipt')
                     ->with('title', 'Payment Receipt')
                     ->with('data', $data)
                     ->with('obj',$walletObj);
             }else{
                 return redirect()
                     ->route('homepage')
                     ->with('failure',GeneralController::error_success('Invalid','You Can not Pay For A Transaction That Is Not Yours'));
             }
         }else{
             return redirect()
                 ->route('homepage')
                 ->with('failure',GeneralController::error_success('Invalid','The provided Transaction Id is Invalid'));
         }
     }
     public function payments($transactionId,Charge $charge){
         $char = strtoupper(substr($transactionId,0,1));
         if($char==='O') {
             $propertyObj = gallery::where('transactionId', $transactionId)->first();
             if ($propertyObj !== null && $propertyObj->paymentStatus!==1) {
                 if ($propertyObj->user_id == auth()->guard('web')->user()->id) {
                     $merchantId = $this->merchantId;
                     $merchantRef = $transactionId;
                     $notify = $this->notifyUrl;
                     $developerCode = $this->developerCode;
                     $successUrl = $this->successUrl;
                     $failedUrl = $this->failedUrl;
                     $memo = "Payment For Advert Charges With Transaction Id $transactionId";
                     $email = auth()->guard('web')->user()->email;
                     $amount = $propertyObj->amount;
                     $phone = auth()->guard('web')->user()->phone;
                     $fullName = auth()->guard('web')->user()->getFullName();
                     $payments = ["merchantId" => $merchantId, "merchantRef" => $merchantRef, 'notifyUrl' => $notify,
                         "developerCode" => $developerCode, "successUrl" => $successUrl, "failedUrl" => $failedUrl,
                         "memo" => $memo, "email" => $email, "amount" => $amount, "phone" => $phone, "customerName" => $fullName,
                     ];
                     return view('general.payments')->with('title', 'payment')->with('payments', $payments);
                 } else {

                 }
             } else {
                 return redirect()
                     ->route('homepage')
                     ->with('failure', GeneralController::error_success('Whoops!!', 'Transaction Not Found'));
             }
         }elseif ($char==='W'){
             $walletObj = WalletRechargeTransaction::where('transaction_id', $transactionId)->first();
             if ($walletObj !== null && $walletObj->payment_status !==1) {
                 if ($walletObj->user_id == auth()->guard('web')->user()->id) {
                     $merchantId = $this->merchantId;
                     $merchantRef = $transactionId;
                     $notify = $this->notifyUrl;
                     $developerCode = $this->developerCode;
                     $successUrl = $this->successUrl;
                     $failedUrl = $this->failedUrl;
                     $memo = "Payment For Wallet Recharge With Transaction Id $transactionId";
                     $email = auth()->guard('web')->user()->email;
                     $amount = $walletObj->amount;
                     $phone = auth()->guard('web')->user()->phone;
                     $fullName = auth()->guard('web')->user()->getFullName();
                     $payments = ["merchantId" => $merchantId, "merchantRef" => $merchantRef, 'notifyUrl' => $notify,
                         "developerCode" => $developerCode, "successUrl" => $successUrl, "failedUrl" => $failedUrl,
                         "memo" => $memo, "email" => $email, "amount" => $amount, "phone" => $phone, "customerName" => $fullName,
                     ];
                     ActivityLog::create([
                         'user_id'=>\auth('web')->user()->id,
                         'action'=>" Made a Wallet Deposit Transaction",]);
                     return view('general.payments')->with('title', 'payment')->with('payments', $payments);
                 } else {

                 }
             } else {
                 return redirect()
                     ->route('homepage')
                     ->with('failure', GeneralController::error_success('Whoops!!', 'Transaction Not Found'));
             }
         }elseif ($char==='B') {
             $walletObj = BuyersTransaction::where('transaction_id', $transactionId)->first();
             if ($walletObj !== null && $walletObj->payment_status !==1) {
                 if ($walletObj->user_id == auth()->guard('web')->user()->id) {
                     $merchantId = $this->merchantId;
                     $merchantRef = $transactionId;
                     $notify = $this->notifyUrl;
                     $developerCode = $this->developerCode;
                     $successUrl = $this->successUrl;
                     $failedUrl = $this->failedUrl;
                     $memo = "Payment For Property Access $transactionId";
                     $email = auth()->guard('web')->user()->email;
                     $amount = $walletObj->amount;
                     $phone = auth()->guard('web')->user()->phone;
                     $fullName = auth()->guard('web')->user()->getFullName();
                     $payments = ["merchantId" => $merchantId, "merchantRef" => $merchantRef, 'notifyUrl' => $notify,
                         "developerCode" => $developerCode, "successUrl" => $successUrl, "failedUrl" => $failedUrl,
                         "memo" => $memo, "email" => $email, "amount" => $amount, "phone" => $phone, "customerName" => $fullName,
                     ];
                     ActivityLog::create([
                         'user_id'=>\auth('web')->user()->id,
                         'action'=>" Paid for Property Access",]);
                     return view('general.payments')->with('title', 'payment')->with('payments', $payments);
                 } else {

                 }

             }
             else {
                 return redirect()
                     ->route('homepage')
                     ->with('failure', GeneralController::error_success('Whoops!!', 'Transaction Not Found'));
         }
         }
    }
     public function successfulTransaction(){
        if(isset($_POST['transaction_id'])){
            //get the full transaction details as an json from voguepay
            $json = file_get_contents('https://voguepay.com/?v_transaction_id='.$_POST['transaction_id'].'&type=json&demo=true');
            //create new array to store our transaction detail
            $transaction = json_decode($json, true);

            /*
            Now we have the following keys in our $transaction array
            $transaction['merchant_id'],
            $transaction['transaction_id'],
            $transaction['email'],
            $transaction['total'],
            $transaction['merchant_ref'],
            $transaction['memo'],
            $transaction['status'],
            $transaction['date'],
            $transaction['referrer'],
            $transaction['method'],
            $transaction['cur']
            */
             $mRef= $transaction['merchant_ref'];
             $char = trim(strtoupper(substr($mRef,0,1)));
            if($transaction['status'] == 'Approved'){
                 if($char=='W'){
                   return redirect()->route('wallet.receipt',['transactionId'=>$mRef]);  
                 }elseif ($char=='O'){
                   return redirect()->route('general.receipt',['transactionId'=>$mRef]);
                 }elseif ($char=='B'){
                     return redirect()->route('general.buyerReceipt',['transactionId'=>$mRef]);
                 }
            }
            
          

            /*You can do anything you want now with the transaction details or the merchant reference.
            You should query your database with the merchant reference and fetch the records you saved for this transaction.
            Then you should compare the $transaction['total'] with the total from your database.*/
        }
    }
     public function failedTransaction(){
         if(isset($_POST['transaction_id'])){
             //get the full transaction details as an json from voguepay
             $json = file_get_contents('https://voguepay.com/?v_transaction_id='.$_POST['transaction_id'].'&type=json&demo=true');
             //create new array to store our transaction detail
             $transaction = json_decode($json, true);

             /*
             Now we have the following keys in our $transaction array
             $transaction['merchant_id'],
             $transaction['transaction_id'],
             $transaction['email'],
             $transaction['total'],
             $transaction['merchant_ref'],
             $transaction['memo'],
             $transaction['status'],
             $transaction['date'],
             $transaction['referrer'],
             $transaction['method'],
             $transaction['cur']
             */
             $mRef= $transaction['merchant_ref'];
             $char = trim(strtoupper(substr($mRef,0,1)));
             if($transaction['status'] == 'Approved'){
                 if($char=='W'){
                     return redirect()->route('wallet.receipt',['transactionId'=>$mRef]);
                 }elseif ($char=='O'){
                     return redirect()->route('general.receipt',['transactionId'=>$mRef]);
                 }elseif ($char=='B'){
                     return redirect()->route('general.buyerReceipt',['transactionId'=>$mRef]);
                 }
             }



             /*You can do anything you want now with the transaction details or the merchant reference.
             You should query your database with the merchant reference and fetch the records you saved for this transaction.
             Then you should compare the $transaction['total'] with the total from your database.*/
         }
    }
     public function notifyTransaction(Charge $charge){
        $merchant_id = $this->merchantId;
        if(isset($_POST['transaction_id'])){
            //get the full transaction details as an json from voguepay
            $json = file_get_contents('https://voguepay.com/?v_transaction_id='.$_POST['transaction_id'].'&type=json&demo=true');
            //create new array to store our transaction detail
            $transaction = json_decode($json, true);

            /*
            Now we have the following keys in our $transaction array
            $transaction['merchant_id'],
            $transaction['transaction_id'],
            $transaction['email'],
            $transaction['total'],
            $transaction['merchant_ref'],
            $transaction['memo'],
            $transaction['status'],
            $transaction['date'],
            $transaction['referrer'],
            $transaction['method'],
            $transaction['cur']
            */
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

                         }
                     }elseif ($char=='O'){
                        $galleryObj = gallery::where('transactionId', $mRef)->first();
                        if($galleryObj->amount == trim($transaction['total'])) {
                            $galleryObj->update([
                                'v_transaction_id' => $transaction['transaction_id'],
                                'paymentStatus' => 1,
                            ]);
                        }else{
                            $galleryObj->update([
                                'v_transaction_id' => $transaction['transaction_id'],
                                'paymentStatus' => 3,
                            ]);
                        }
                     }elseif ($char=='B'){
                         $buyersTranObj=BuyersTransaction::where('transaction_id',$mRef)->first();
                        if($charge->buyerCharge()->first()->amount == trim($transaction['total'])) {
                            $buyersTranObj->update([
                                'v_transaction_id' => $transaction['transaction_id'],
                                'payment_status' => 1,
                            ]);
                        }else{
                            $buyersTranObj->update([
                                'v_transaction_id' => $transaction['transaction_id'],
                                'payment_status' => 3,
                            ]);
                        }
                     }
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
                    }

                }
            }
            
          

            /*You can do anything you want now with the transaction details or the merchant reference.
            You should query your database with the merchant reference and fetch the records you saved for this transaction.
            Then you should compare the $transaction['total'] with the total from your database.*/
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
     public function requery(Request $request){
        //get the full transaction details as an json from voguepay
        $json = file_get_contents('https://voguepay.com/?v_transaction_id='.$request->input('transaction').'&type=json&demo=true');
        //create new array to store our transaction detail
        $transaction = json_decode($json, true);
        return redirect()->back()->with('transaction',$transaction);
    }
}
