

<!-- sidebar menu -->
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

    <div class="menu_section">
        <h3>General</h3>
        <ul class="nav side-menu">
            <li><a href="{{URL::to('dashboard')}}"><i class="fa fa-home"></i> Dashboard </a>
            </li>
            <li><a><i class="fa fa-calendar"></i> Event <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu" style="display: none">
                    <li><a href="{{URL::to('event')}}"><i class="fa fa-list"></i> Event List </a>
                    <li><a href="{{URL::to('category')}}"><i class="fa fa-sitemap"></i> Event Category </a>
                </ul>
            </li>
             <li><a href="{{URL::to('member')}}"><i class="fa fa-users"></i> Member </a>
            </li>
           
          
            <li><a><i class="fa fa-rss-square"></i> Blog <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu" style="display: none">
                    <li><a href="{{URL::to('blog')}}"><i class="fa fa-list"></i> Blog List </a>
                    <li><a href="{{URL::to('blog_category')}}"><i class="fa fa-sitemap"></i> Blog Category </a>
                </ul>
            </li>
             <li><a href="{{URL::to('organization/'.Auth::user()->user_id)}}"><i class="fa fa-university"></i> Organization </a>
            </li>
            
             <li><a href="{{URL::to('location/'.Auth::user()->user_id)}}"><i class="fa fa-location-arrow"></i> Venue </a>
            </li>
        </ul>
    </div>


</div>
<!-- /sidebar menu -->


