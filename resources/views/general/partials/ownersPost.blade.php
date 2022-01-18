@if(!auth()->guest())
    @if(auth()->guard('web')->user()->customerType ==1 && url()->current()!==route('homepage'))
        <div class="row">
            <div class="col-10 offset-1 p-2">
                <select url="{{route('owner.post')}}" id="filterProperty" class="form-control-sm" name="propertyFilter" style="float: right">
                    <option @if($type=='js'||$type==null){{"selected"}}@endif value="js">Filter All</option>
                    <option @if($type=='paid'){{"selected"}}@endif value="paid">Filter Paid</option>
                    <option @if($type=='unPaid'){{"selected"}}@endif value="unPaid">Filter Unpaid</option>
                    <option @if($type=='sold'){{"selected"}}@endif value="sold">Filter Sold</option>
                    <option @if($type=='access'){{"selected"}}@endif value="access">Filter Paid For Access</option>
                    <option @if($type=='bought'){{"selected"}}@endif value="bought">Filter Paid For Bought</option>
                </select>
            </div>
        </div>
        @elseif(auth()->guard('web')->user()->customerType ==0 && url()->current()!==route('homepage'))
        <div>
            <div>
                <select url="{{route('owner.post')}}" id="filterProperty" class="form-control-sm" name="propertyFilter" style="float: right">
                    <option @if($type=='js'||$type==null){{"selected"}}@endif value="js">Filter All</option>
                    <option @if($type=='bought'){{"selected"}}@endif value="bought" >Filter Bought</option>
                    <option @if($type=='access'){{"selected"}}@endif value="access">Filter Paid For Access</option>
                </select>
            </div>
        </div>
    @endif
@endif
@if($properties->count()>0)
    <?php
    if(auth()->guard('web')->check()&& !in_array(url()->current(),[
        route('homepage')])){
            $paginatedProperties = $properties->paginate(6);
            }
    else{
        $paginatedProperties = $properties->paginate(9);

    }
?>
<div class="row">
<div class="col ">
   <div class="row">
       @foreach($paginatedProperties as $paginatedProperty)
           <div class="col-12 col-sm-6 col-md-4">
               <div class="card">
                   <?php
                       $mainObj = \estateManagement\Models\gallery::find($paginatedProperty->id);
                       $imageObj = $mainObj->propertyImages()->first();
                       $user=\estateManagement\Models\User::find($paginatedProperty->user_id);

                   ?>
                   <div class="card-body">
                       @if($user->verified==1)
                           <span class="d-inline-block p-2 text-capitalize" style="background: green;border-radius: 20px;position: absolute;color: white;transform: skewX(20deg)">Verified</span>
                       @endif
                       <img height="150" src="{{url('assets/uploads/GalleryPictures/'.$imageObj->pictures)}}" style="width:100%;height:200px" />
                       <span class="card-text"><b>Description: </b>
                           @if(strlen($paginatedProperty->description)>=100)
                           {{substr($paginatedProperty->description,0,100)}}...
                           @else
                           {{$paginatedProperty->description}}
                           @endif
                       </span>
                       <span ><b>Address: </b>{{$paginatedProperty->address}}</span>
                       <span class="text-danger" ><b>price: &#8358;</b>{{$paginatedProperty->price}}</span>
                       @if($paginatedProperty->bought!==1)
                            <a href="{{route('readMore',['moreDetails'=>$paginatedProperty->id])}}" class="btn btn-block" type="button" style="background: rgb(9,48,86);">View More</a>
                       @elseif($paginatedProperty->paymentStatus==1)
                           <h3 class="text-success text-capitalize">bought</h3>
                       @endif
                   </div>
               </div>
           </div>
       @endforeach
   </div>
  <span> {{$paginatedProperties->render()}}</span>
</div>
</div>
@else
<div class="row">
    <div class="col-8 offset-2">
       <h4 class="text-danger ">Sorry no Property Found</h4>
    </div>
</div>
@endif