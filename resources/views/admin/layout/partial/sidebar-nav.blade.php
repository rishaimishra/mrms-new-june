<div class="menu">
    @if (Auth::user() && !isset(Auth::user()->is_seller))
    <ul class="list">
        {!! \App\Logic\AdminMenu::getMenuHtml() !!}
    </ul>
    @else
    <ul class="list">
        <li class="">
            <a class="waves-effect waves-block" href="{{ route('admin.sellerDashboard') }}">
            <i class="material-icons">reorder</i>
                <span>Dashboard</span>
            </a>
        </li>
        @if (Auth::check() && Auth::user()->user_type === "sea and air freights")
        <li class="">
            <a class="waves-effect waves-block" href="{{ route('admin.seafrieghts.users') }}">
            <i class="material-icons">reorder</i>

                <span>Sea-air frights</span>
            </a>
        </li>
        @endif
        {{--  <li class="">
            <a class="waves-effect waves-block" href="{{ route('admin.seller.users') }}">
            <i class="material-icons">reorder</i>
                <span>Users</span>
            </a>
        </li>  --}}
        @if (Auth::user()->user_type == "collection and payments")
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.sellercollection.users')}}">
            <i class="material-icons">reorder</i>
                <span>Collection and payments</span>
            </a>
        </li>
        @endif
        @if (Auth::user()->user_type == "moneytransfer")
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.sellermoneytransfer.users')}}">
            <i class="material-icons">reorder</i>
                <span>Money Transfer</span>
            </a>
        </li>
        @endif
        @if (Auth::user()->user_type == "sea and air freights" || Auth::user()->user_type == "collection and payments" || Auth::user()->user_type == "shop" || Auth::user()->user_type == "autos" || Auth::user()->user_type == "real_state")
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.sellernotification.users')}}">
            <i class="material-icons">notifications</i>
                <span>Notification</span>
            </a>
        </li>
        @endif
        @if (in_array(Auth::user()->user_type, [
    'shop', 
    'local markets',
    'hotels restaurants',
    'water & ice processing',
    'boutiques',
    'cosmetic stores',
    'perfumeries',
    'furniture store',
    'home appliance stores',
    'electrical stores',
    'electronics stores',
    'baby stores',
    'home decor',
    'stationary stores',
    'school and book shop stores',
    'sport stores',
    'gym & fitness stores',
    'art and craft stores',
    'pharmacies',
    'local distribution',
    'agro product store',
    'building material stores',
    'generator stores',
    'pets and care stores'
]))
   


        <!-- <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.seller-product.index')}}">
            <i class="material-icons">shopping_basket</i>
                <span>Product</span>
            </a>
        </li> -->

        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.show-seller-product')}}">
            <i class="material-icons">shopping_basket</i>
                <span>Add Products</span>
            </a>
        </li>
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.my-seller-product')}}">
            <i class="material-icons">shopping_basket</i>
                <span>My Shop Products</span>
            </a>
        </li>
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.seller-attribute-set.index')}}">
            <i class="material-icons">notifications</i>
                <span>Attribute Sets</span>
            </a>
        </li>
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.seller-attribute-group.index')}}">
            <i class="material-icons">notifications</i>
                <span>Attribute Group</span>
            </a>
        </li>
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.seller-attribute.index')}}">
            <i class="material-icons">notifications</i>
                <span>Attributes</span>
            </a>
        </li>


        @endif
        @if (Auth::user()->user_type == "local distribution")
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.seller-product-category.index')}}">
            <i class="material-icons">reorder</i>
                <span>Dealer/Agent network</span>
            </a>
        </li>
        @endif
      
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.sellernotification.users')}}">
            <i class="material-icons">reorder</i>
                <span>Transactions</span>
            </a>
        </li>
        
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.sellernotification.users')}}">
            <i class="material-icons">reorder</i>
                <span>Followers</span>
            </a>
        </li>
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.sellernotification.users')}}">
            <i class="material-icons">reorder</i>
                <span>My Reviews</span>
            </a>
        </li>
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.sellernotification.users')}}">
            <i class="material-icons">reorder</i>
                <span>Terms & Conditions</span>
            </a>
        </li>
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.sellernotification.users')}}">
            <i class="material-icons">reorder</i>
                <span>Messaging</span>
            </a>
        </li>
        @if (Auth::user()->user_type == "state news")
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.seller.state.news')}}">
            <i class="material-icons">reorder</i>
                <span>State News List</span>
            </a>
        </li>
        @endif
        @if (Auth::user()->user_type == "national news")
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.seller.national.news')}}">
            <i class="material-icons">reorder</i>
                <span>National News List</span>
            </a>
        </li>
        @endif
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.selleraccount')}}">
            <i class="material-icons">reorder</i>
                <span>My Account</span>
            </a>
        </li>
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.assign.sellerrole.form')}}">
            <i class="material-icons">reorder</i>
                <span>Admin users</span>
            </a>
        </li>
        @if (Auth::user()->user_type == "autos")
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.show-auto-product')}}">
            <i class="material-icons">shopping_basket</i>
                <span>Add Similar Autos</span>
            </a>
        </li>
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.edit-seller-product')}}">
            <i class="material-icons">shopping_basket</i>
                <span>My Store Autos</span>
            </a>
        </li>
        @endif
        @if (Auth::user()->user_type == "real estate")
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.show-realestate-product')}}">
            <i class="material-icons">shopping_basket</i>
                <span>Add Similar Properties</span>
            </a>
        </li>
        <li class="">
            <a class="waves-effect waves-block" href="{{route('admin.show-seller-product')}}">
            <i class="material-icons">shopping_basket</i>
                <span>My Properties</span>
            </a>
        </li>
        @endif
       </ul>
    @endif
   
</div>
