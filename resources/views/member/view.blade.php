@extends('layout.layout')
@section('content')
<div class="right_col" role="main">
    <div class="">

        <div class="page-title">
            <div class="title_left">
                <h3>Member</h3>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-xs-12">

                <div class="x_panel">
                    <div class="x_title">
                        <h2>User Profile</h2>
                        <div class="nav navbar-right panel_toolbox">
                            <a href="{{URL::to('member')}}" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-circle-left"></i> Back</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="wrap row">




                        <section id="content" class="large-9 small-12 columns">
                            <div class="form-group">
                                <div class="col-sm-12 col-xs-12">
                                    <a href="{{ url('member/' . $user_id . '/edit')}}" class="btn btn-warning pull-right">Edit Profile </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12 profile_left">

                                <div class="profile_img">

                                    <!-- end of image cropping -->
                                    <div id="crop-avatar">
                                        <!-- Current avatar -->
                                        <div class="avatar-view" title="" data-original-title="Change the avatar">
                                            <img src="{{ URL::asset('geekmeet/uploads/user/'.$user->image) }}" alt="Avatar">
                                        </div>

                                    </div>
                                    <!-- end of image cropping -->

                                </div>
                            </div>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="user_dsb_cf">
                                     <h3>{{ $user->first_name.' '.$user->last_name}}</h3>
                                    <div>
                                        <label class='col-md-4'><i class="fa fa-home"></i> Address :</label>
                                        <span class='col-md-8'><p>{{$user->address}}</p></span>
                                    </div>
                                     <div class="clearfix"></div>
                                    <div>
                                        <label class='col-md-4'><i class="fa fa-calendar"></i> Birth date :</label>
                                        <span class='col-md-8'><p>{{ date('d-m-Y',strtotime($user->dob)) }}</p></span>
                                    </div>
                                     <div class="clearfix"></div>
                                    <div>
                                        <label class='col-md-4'><i class="fa fa-hospital-o"></i> Birth place :</label>
                                        <span class='col-md-8'><p>{{ $user->birth_place}}</p></span>
                                    </div>
                                     <div class="clearfix"></div>
                                    <div>
                                        <label class='col-md-4'><i class="fa fa-male"></i> Gender :</label>
                                        <span class='col-md-8'><p>{{ $user->gender}}</p></span>
                                    </div>
                                     <div class="clearfix"></div>
                                    <div>
                                        <label class='col-md-4'><i class="fa fa-building-o"></i> City :</label>
                                        <span  class='col-md-8'><p>{{ $user->city_name}}</p></span>
                                    </div>
                                     <div class="clearfix"></div>
                                    <div>
                                        <label class='col-md-4'><i class="fa fa-codepen"></i> Zip Code :</label>
                                        <span class='col-md-8'><p>{{ $user->zip_code}}</p></span>
                                    </div>
                                     <div class="clearfix"></div>
                                    <div>
                                        <label class='col-md-4'><i class="fa fa-envelope-o"></i> Email :</label>
                                        <span class='col-md-8'><p>{{ $user->email}}</p></span>
                                    </div>
                                     <div class="clearfix"></div>
                                    <div>
                                        <label class='col-md-4'><i class="fa fa-phone"></i> Phone :</label>
                                        <span class='col-md-8'><p>{{ $user->phone}}</p></span>
                                    </div>
                                     <div class="clearfix"></div>
                                    <?php 
                                    if($user->favorite_category!=''){
                                    $category = explode(',',$user->favorite_category);
                                    ?>
                                     <div>
                                        <label class='col-md-4'><i class="fa fa-list"></i> Interest Topic:</label>
                                        <span class='col-md-8'>
                                            <ul class="interest-topic">
                                    <?php
                                    foreach($category as $val){ ?>
                                                <li style="float: left">{{ $val }}<b class="dash">&nbsp;&nbsp;-&nbsp;&nbsp;   </b></li>
                                   <?php } ?>
                                        </ul></span></div><div class="clearfix"></div>
                                    <?php }
                                    ?>
                                    <div>
                                        <label class='col-md-4'><i class="fa fa-user"></i> About Me :</label>
                                        <span class='col-md-8'><?php echo html_entity_decode($user->about_me);?></span>
                                    </div><div class="clearfix"></div>
                                </div>
                              
                         </div>
                        </section>
                    </div>
                </div>










            </div>
        </div>
          
   <style>
     .user_dsb_cf > div {
    padding: 2px;
}
.user_dsb_cf > div > label {
    font-size: 15px;
}
.user_dsb_cf p{
font-size: 16px;    
}
.user_dsb_cf ul>li{
font-size: 16px;
padding:1px;
}
.user_dsb_cf ul {
    list-style:  none;
    padding: 0px;
}
.interest-topic li:last-child b {
    display: none;
}
        
    </style>
        @include('layout.footer')
    </div>
 



    @endsection



