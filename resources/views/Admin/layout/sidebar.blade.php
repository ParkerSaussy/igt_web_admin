<aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <?php  $imageUrl  = config('global.local_image_url'); ?>
                <a class="navbar-brand" href="#"><img src="<?php echo $imageUrl?>logo.png" alt="Logo"></a>
                <a class="navbar-brand hidden" href="#"><img src="<?php echo $imageUrl?>favicon.png" alt="Logo"></a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                   
                    <li class="{{ (request()->segment(1) == 'dashboard') ? 'active' : '' }}">
                        <a href="{{URL('dashboard')}}"> <i class="menu-icon fa fa-dashboard"></i>Dashboard </a>
                    </li>
                    {{-- <h3 class="menu-title">UI elements</h3><!-- /.menu-title --> --}}
                
                    <li class="{{ (request()->segment(1) == 'users') ? 'active' : '' }}">
                        <a href="{{URL('users')}}"> <i class="menu-icon fa fa-user"></i>User Management </a>
                    </li>
                    <li class="{{ (request()->segment(1) == 'cmspages') ? 'active' : '' }} {{ (request()->segment(1) == 'editpage') ? 'active' : '' }}">
                        <a href="{{URL('cmspages')}}"> <i class="menu-icon fa fa-file-text-o"></i>Cms Pages </a>
                    </li>
                     <li class="{{ (request()->segment(1) == 'allcity') ? 'active' : '' }} {{ (request()->segment(1) == 'addcity') ? 'active' : '' }} {{ (request()->segment(1) == 'editcity') ? 'active' : '' }}">
                        <a href="{{URL('allcity')}}"> <i class="menu-icon fa fa-flag"></i>Cities</a>
                    </li>
                    <li class="{{ (request()->segment(1) == 'alltrips') ? 'active' : '' }} {{ (request()->segment(1) == 'tripdetails') ? 'active' : '' }}">
                        <a href="{{URL('alltrips')}}"> <i class="menu-icon fa fa-location-arrow"></i>Trips</a>
                    </li>
                    <li class="{{ (request()->segment(1) == 'coverimages') ? 'active' : '' }} ">
                        <a href="{{URL('coverimages')}}"> <i class="menu-icon fa fa-image"></i>Cover Images</a>
                    </li>
                    <li class="{{ (request()->segment(1) == 'allplans') ? 'active' : '' }} {{ (request()->segment(1) == 'addplan') ? 'active' : '' }}
                        {{ (request()->segment(1) == 'editplan') ? 'active' : '' }} ">
                        <a href="{{URL('allplans')}}"> <i class="menu-icon fa fa-product-hunt"></i>Plans</a>
                    </li>
                    <li class="{{ (request()->segment(1) == 'purchasedPlans') ? 'active' : '' }} ">
                        <a href="{{URL('purchasedPlans')}}"> <i class="menu-icon fa fa-product-hunt"></i>Purchased Plans</a>
                    </li>
                    <li class="{{ (request()->segment(1) == 'allinquiries') ? 'active' : '' }} ">
                        <a href="{{URL('allinquiries')}}"> <i class="menu-icon fa fa-envelope"></i>Inquiries</a>
                    </li>
                    <li class="{{ (request()->segment(1) == 'allfaqs') ? 'active' : '' }} ">
                        <a href="{{URL('allfaqs')}}"> <i class="menu-icon  fa fa-plus-square"></i>FAQ's</a>
                    </li>
                    <li class="{{ (request()->segment(1) == 'youtubeurls') ? 'active' : '' }} ">
                        <a href="{{URL('youtubeurls')}}"> <i class="menu-icon  fa fa-youtube-play"></i>Youtube Urls</a>
                    </li>
                   
   
        
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->