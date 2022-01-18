<?php
/**
 * Created by PhpStorm.
 * User: DEATH
 * Date: 11/16/2018
 * Time: 2:55 AM
 */
?>

@extends ('admin.partials.default')
@section ('adminContent')
    <div class="row">
        <div class="col">
            <div class="table-responsive">
                @include('templates.partials.alerts')
                <h3 class="text-uppercase text-center mb-3" style="font-weight: bolder;color: rgb(9,48,86)">All Unverified users</h3>
                <a href="{{route('users')}}" class="btn btn-link">All Users</a>
                <table class="table table-striped text-capitalize">
                    <tbody>
                    <tr>
                        <th>Full name</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Mode of Id</th>
                        <th>profile Picture</th>
                        <th>Verify</th>
                    </tr>

                    @foreach($users as $user)
                    <tr>
                        <td>{{$user->fullName($user)}}</td>
                        <td>{{$user->phone}}</td>
                        <td>
                            @if($user->gender==1)
                                {{"Male"}}
                            @else
                                {{"Female"}}
                            @endif
                        </td>
                        <td>
                            <a style="color: #0f6ecd" href="{{url('assets/uploads/validId/'.$user->modeOfId)}}">View Id</a>
                        </td>
                        <td>
                            @if(is_null($user->profilePicture))
                                @if ($user->gender==1)
                                    <img src="{{url('assets/img/download.png')}}"style="width:50px;height:50px;">
                                @elseif($user->gender==0)
                                    <img src="{{url('assets/img/images.jpg')}}"style="width:50px;height:50px;">
                                @endif
                            @else
                            <img src="{{url('assets/uploads/usersProfile/'.$user->profilePicture)}}" width="50" height="50">
                            @endif
                        </td>
                        <td>
                            <a href="{{route('verifyUsers',['userId'=>$user->id])}}" class="btn btn-primary" type="button" style="background-color:rgb(23,108,67);">Verify &nbsp;<i class="fa fa-check"></i></a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
