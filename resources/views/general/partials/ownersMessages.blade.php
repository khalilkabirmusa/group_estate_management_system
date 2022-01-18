<?php
/**
 * Created by PhpStorm.
 * User: DEATH
 * Date: 11/2/2018
 * Time: 3:11 AM
 */
?>
@include ('templates.partials.alerts')
<h3 class="text-uppercase text-center" style="font-weight: bolder"><u>Submit Account Details</u></h3>
<div class="row" >
    <div class="col-8 offset-2">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{route('bank.details')}}">
                    @csrf
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" placeholder="Account Number" {{old('account_number')}} class="form-control" name="account_number" />
                        </div>
                        @if($errors->has('account_number'))
                            <small class="form-text text-danger">{{$errors->first('account_number')}}</small>
                        @endif
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" placeholder="Bank Name" {{old('bank_name')}} class="form-control" name="bank_name" />
                        </div>
                        @if($errors->has('bank_name'))
                            <small class="form-text text-danger">{{$errors->first('bank_name')}}</small>
                        @endif
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" placeholder="Sort Code" {{old('sort_code')}} class="form-control" name="sort_code" />
                        </div>
                        @if($errors->has('sort_code'))
                            <small class="form-text text-danger">{{$errors->first('sort_code')}}</small>
                        @endif
                    </div>
                    <div class="form-group"><button class="btn btn-block" type="submit" style="background: rgb(9,48,86);color:white">Submit</button></div>
                </form>
            </div>
        </div>
    </div>
</div>


