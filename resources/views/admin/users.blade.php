<?php
/**
 * Created by PhpStorm.
 * User: DEATH
 * Date: 3/5/2019
 * Time: 7:52 PM
 */
?>
@extends ('admin.partials.default')
@section ('adminContent')
    <div class="row">
        <div class="col">
            <div class="table-responsive">
                @include('templates.partials.alerts')
                <h3 class="text-uppercase text-center mb-3" style="font-weight: bolder;color: rgb(9,48,86)">All  users</h3>
                <table class="table table-striped text-capitalize">
                    <tbody>
                    <tr>
                        <th>Full name</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Mode of Id</th>
                        <th>profile Picture</th>

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

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
