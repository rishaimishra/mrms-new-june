<div class="user-info">
    <div class="image">
        <img src="{{ url('admin/images/user.png') }}" width="48" height="48" alt="User"/>
    </div>
    <div class="info-container">
       
        @if (Auth::user())
        <div class="name" data-toggle="dropdown" aria-haspopup="true"
        aria-expanded="false">{{ Auth::user()->username }}</div>
   <div class="email">{{ Auth::user()->email }}</div>
   <div class="btn-group user-helper-dropdown">
       <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
       <ul class="dropdown-menu pull-right">
           <li><a href="{{ route('admin.account.reset-password') }}"><i class="material-icons">person</i>Update Profile</a></li>
           {{--<li role="seperator" class="divider"></li>--}}
           {{--<li><a href="javascript:void(0);"><i class="material-icons">group</i>Followers</a></li>--}}
           {{--<li><a href="javascript:void(0);"><i class="material-icons">shopping_cart</i>Sales</a></li>--}}
           {{--<li><a href="javascript:void(0);"><i class="material-icons">favorite</i>Likes</a></li>--}}
           {{--<li role="seperator" class="divider"></li>--}}
           @if (Auth::user()->is_seller)
           <li><a href="{{ route('admin.seller.logout') }}"><i class="material-icons">input</i>Sign Out</a></li>
           @else
           <li><a href="{{ route('admin.auth.logout') }}"><i class="material-icons">input</i>Sign Out</a></li>
           @endif
           
       </ul>
   </div>
        @endif
       
    </div>
</div>