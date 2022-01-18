<?php
/**
 * Created by PhpStorm.
 * User: DEATH
 * Date: 11/2/2018
 * Time: 3:11 AM
 */
?>
@include('templates.partials.alerts')
<h3 class="text-uppercase text-center" style="font-weight: bolder">Request for Bonus</h3>
<div class="row  mb-2">
    <div class="col-8 offset-2">
        <div class="">
            <div class="card-body">
                @if($boughtProperties->count() > 0)
                 <form method="post" action="{{route('owner.onFiveBuying')}}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-capitalize">
                            <thead>
                            <tr>
                                <th><input type="checkbox" class="multiple"> Check All </th>
                                <th>fullname</th>
                                <th>Property Id</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody class="contentChecked">
                            @foreach($boughtProperties as $boughtProperty)
                            <tr>
                                <td><input class="single" type="checkbox" value="{{$boughtProperty->id}}" name="payments[]"></td>
                                <?php $user=\estateManagement\Models\User::find($boughtProperty->user_id)?>
                                <td><span>{{$user->firstName." ".$user->lastName}}</span></td>
                                <?php $gallery = \estateManagement\Models\gallery::find($boughtProperty->gallery_id)?>
                                <td><span>{{$gallery->address}}</span></td>
                                <td><span>{{$gallery->description}}</span></td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button id="onFiveSubmit" class="btn btn-block" style="background: rgb(9,48,86);color: white" disabled>Send Request</button>
                </form>
                 @else
                    <h1 class="text-danger text-center"> No Record !!</h1>
                @endif
            </div>
        </div>
    </div>
</div>