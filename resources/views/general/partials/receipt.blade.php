<?php
/**
 * Created by PhpStorm.
 * User: DEATH
 * Date: 11/3/2018
 * Time: 7:31 AM
 */
?>
<?php $char = strtoupper(substr($data['transactionId'],0,1))?>
@if($char==='W')
  <?php  $status = $obj->payment_status?>
@elseif($char==='O')
    <?php $status = $obj->paymentStatus?>
@elseif($char==='B')
    <?php  $status = $obj->payment_status?>
    <div>

    </div>
@endif
<div class="row">@include('templates.partials.alerts')
    <div class="col">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                @if($status==1)
                        <span class="pt-2" style="border-radius: 50px; text-align:center;width: 50px;height: 50px;display: block;margin-left: auto;margin-right: auto;background-color: green">
                            <i style="color: black" class="fa fa-check fa-2x"></i>
                        </span>
                        <p>&nbsp;</p>
                @elseif(in_array($status,[0,2,3]) && !is_null($status))

                    <span class="pt-2" style="border-radius: 50px; text-align:center;width: 50px;height: 50px;display: block;margin-left: auto;margin-right: auto;background-color: red">
                            <i style="color: black" class="fa fa-times fa-2x"></i>
                        </span>
                    <p>&nbsp;</p>
                @endif
                <div class="table-responsive" border="1">
                    <table class="table table-striped table-bordered text-capitalize">
                        <thead>
                        <tr>
                            <th colspan="2" class="text-center text-uppercase" style="font-weight: bolder;background: rgb(9,48,80);color:white">Syi Estate management</th>
                        </tr>
                        <tr>
                            <th>purpose</th>
                            <th>{{$data['purpose']}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>amount</td>
                            <td>{{$data['amount']}}</td>
                        </tr>
                        <tr>
                            <td>fullname</td>
                            <td>{{$data['fullname']}}</td>
                        </tr>
                        <tr>
                            <td>transaction Id</td>
                            <td>{{$data['transactionId']}}</td>
                        </tr>
                        <tr>
                            <td>phone number</td>
                            <td>{{$data['phone']}}</td>
                        </tr>
                        @if(!is_null($status))
                            <tr>
                                <td>Payment Status</td>
                                <td>{{$status}}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>date</td>
                            <td>{{\Carbon\Carbon::now()}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                    @if(is_null($status)&& $status!==0)
                        <div class="row">
                            @if($char!=='W')
                            <div class="col-6">
                                <a href="{{route('paywithwallet',['mRef'=>$data['transactionId']])}}" style="background: rgb(9,48,86);color:white"  class="btn btn-block">Pay From Wallet<i class="fa fa-google-wallet"></i></a>
                            </div>
                            @endif
                            <div class="col-6">
                                <a href="{{route('payment',['transactionId'=>$data['transactionId'],])}}" class="btn btn-info btn-block"> Pay On Vogue Pay <i class="fa fa-money"></i></a>
                            </div>
                        </div>
                    @endif
            </div>


        </div>
    </div>
</div>

