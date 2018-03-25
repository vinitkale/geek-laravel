<div class="main_container">

            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">

                    <div class="navbar nav_title" style="border: 0;">
                        <a href="#" class="site_title">   <img src="{{ asset("/bower_components/gentelella/production/images/logo.png") }}" style="margin-top:14px;" class="img-responsive"></a>
                    </div>
                    <div class="clearfix"></div>

                    <!-- menu prile quick info -->
                    <div class="profile">
                        <div class="profile_pic">
                          
                            <img src="{{ asset("/bower_components/gentelella/production/images/user.png") }}" alt="..." class="img-circle profile_img">
                        </div>
                        <div class="profile_info">
                            <span>Welcome,</span>
                            <h2>{{ucfirst(Auth::user()->username)}}</h2>
                        </div>
                    </div>
                    <!-- /menu prile quick info -->
                <br />    
                  @include('layout.sidebar')
                </div>
            </div>

            <!-- top navigation -->
            <div class="top_nav">

                <div class="nav_menu">
                    <nav class="" role="navigation">
                        <div class="nav toggle">
                            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        </div>

                        <ul class="nav navbar-nav navbar-right">
                            <li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ asset("/bower_components/gentelella/production/images/user.png") }}" alt="">{{ucfirst(Auth::user()->first_name.' '.Auth::user()->last_name)}}
                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu animated fadeInDown pull-right">
                                    <li><a href=" {{URL::to('member/'.Auth::user()->user_id)}}">  Profile</a>
                                    </li>
                                  
                                   
                                    <li><a href="{{URL::to('logout')}}"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
                                    </li>
                                </ul>
                            </li>

                            

                        </ul>
                    </nav>
                </div>

            </div>