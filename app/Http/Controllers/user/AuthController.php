<?php
/**
 * Created by PhpStorm.
 * User: DEATH
 * Date: 10/26/2018
 * Time: 2:48 PM
 */

namespace estateManagement\Http\Controllers\user;


use estateManagement\Http\Controllers\Controller;
use estateManagement\Http\Controllers\GeneralController;
use estateManagement\Models\ActivityLog;
use estateManagement\Models\User;
use estateManagement\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware("guest");
    }
    public function UserReg(){
        return view('users.userReg')->with('title', 'user | registration');
    }
    public function postUserReg(Request $request){
        $this->validate($request,[
           'firstname'=>'required|present|max:20|min:2',
            'lastname'=>'required|present|max:20|min:2',
            'email'=>'required|present|max:25|email|unique:users,email',
            'username'=>'required|present|max:20|min:2|unique:users,username',
            'gender'=>'required|present|boolean',
            'phone'=>['required','present','numeric','unique:users,phone',
                function($attribute, $value, $fail)
            {
                if (strlen($value) < 11) {
                    $fail("The number can not be less than 11 digits");
                } elseif (strlen($value) > 11) {
                    $fail("The number can not be greater than 11 digits");
                }
            }
            ],
            'password'=>'required|present|min:6|numeric|confirmed',
            'password_confirmation'=>'required|present|max:50|min:6|same:password',
            'address'=>'required|present|max:255|min:6',
            'ownership'=>'required|present|boolean',
            'validIdentification'=>'nullable|required_if:ownership,1|mimes:pdf,doc,docx,ppt|max:8048',
            'profilePicture'=>'nullable|mimes:jpeg,png,jpg,gif|max:8048',
            ]);
        if($request->file('validIdentification')==null){
            $validId = null;
        }else{
            $validId = GeneralController::UploadFile($request->file('validIdentification'),[],'validId');
        }
        if($request->file('profilePicture')==null){
            $profilePic = null;
        }else{
            $profilePic = GeneralController::UploadFile($request->file('profilePicture'),[],'usersProfile');
        }
        if($request->input('ownership')==0){
            $verified = 1;
        }else{
            $verified = null;
        }
        if(User::create([
            'username'=>$request->input('username'),
            'firstName'=>$request->input('firstname'),
            'lastName'=>$request->input('lastname'),
            'phone'=>$request->input('phone'),
            'email'=>$request->input('email'),
            'gender'=>$request->input('gender'),
            'customerType'=>$request->input('ownership'),
            'modeOfId'=>$validId,
            'verified'=>$verified,
            'password'=>bcrypt($request->input('password')),
            'profilePicture'=>$profilePic,
        ])){
            $user = User::where('username',$request->input('username'))->first();
            Wallet::create([
               'user_id'=>$user->id,
               'amount'=>0,
            ]);
            return redirect()->route('homepage')->with('success',GeneralController::error_success('Successful',"User Created Succesfully, You Can Now login"));
        }else{
            return redirect()->route('homepage')->with('failure',GeneralController::error_success('Whoops',"Something Went Wrong"));
        }
    }
    public  function postLogin(Request $request){
        $this->validate($request,[
           'username'=>'required|present|min:2|max:20',
           'password'=>'required|present|min:6|max:20',
        ]);
        if(Auth::guard('web')->attempt([
            'username'=>$request->input('username'),
            'password'=>$request->input('password')])){
            ActivityLog::create([
                'user_id'=>\auth('web')->user()->id,
                'action'=>"Logged in",
            ]);
             return redirect()->back();
        }else{
            return redirect()->back()->with('failure',GeneralController::error_success('Invalid Login Details',"The Details Provided Does not match any Records"));
        }
    }

}