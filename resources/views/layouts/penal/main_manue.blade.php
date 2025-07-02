 <!-- ======= Sidebar ======= -->
 <aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link {{ Route::is('home') ? 'text-white' : 'text-muted-blue' }}" href="{{route('home')}}">
        <i class="bi bi-grid"></i>
        <span>{{trans('lang.dashboard')}}</span>
      </a>
    </li><!-- End Dashboard Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#order" data-bs-toggle="collapse" href="#">
        <i class="icon-container"><img src="{{asset('img/orders-icon.png')}}" alt=""></i>
        <span>Orders</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="order" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('service_orders.index')}}">
            <i class="bi bi-circle"></i>
            <span>Order List</span>
          </a>
        </li>
        <li>
          <a href="{{route('service_orders.create')}}">
            <i class="bi bi-circle"></i><span>Order Create</span>
          </a>
        </li>
      </ul>
    </li>
    <!-- <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#Item" data-bs-toggle="collapse" href="#">
        <i class="bi bi-sliders"></i><span>{{trans('lang.sub_services')}}</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="Item" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('product.index')}}">
            <i class="bi bi-circle"></i><span>{{trans('lang.sub_service_list')}}</span>
          </a>
        </li>
        <li>
          <a href="{{route('product.create')}}">
            <i class="bi bi-circle"></i><span>{{trans('lang.sub_service_create')}}</span>
          </a>
        </li>
      </ul>
    </li> -->
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#customer" data-bs-toggle="collapse" href="#">
      <i class="icon-container"><img src="{{asset('img/customers-icon.png')}}" alt="" ></i>
      <span>Customers</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="customer" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('customers.index')}}">
            <i class="bi bi-circle"></i><span>Customer list</span>
          </a>
        </li>
        <li>
          <a href="{{route('customers.create')}}">
            <i class="bi bi-circle"></i><span>Customer Create</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#cate" data-bs-toggle="collapse" href="#">
        <i class="bi bi-sliders"></i><span>Categories</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="cate" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('category.index')}}">
            <i class="bi bi-circle"></i><span>Category List</span>
          </a>
        </li>
        <li>
          <a href="{{route('category.create')}}">
            <i class="bi bi-circle"></i><span>Category Create</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#service" data-bs-toggle="collapse" href="#">
      <i class="icon-container"><img src="{{asset('img/services-icon.png')}}" alt=""></i>
      <span>Services</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="service" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('services.index')}}">
            <i class="bi bi-circle"></i><span>Service List</span>
          </a>
        </li>
        <li>
          <a href="{{route('services.create')}}">
            <i class="bi bi-circle"></i><span>Service Create</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#teams" data-bs-toggle="collapse" href="#">
      <i class="icon-container"><img src="{{asset('img/team-icon.png')}}" alt=""></i>
      <span>Teams</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="teams" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('teams.index')}}">
            <i class="bi bi-circle"></i><span>Teams List</span>
          </a>
        </li>
        <li>
          <a href="{{route('teams.create')}}">
            <i class="bi bi-circle"></i><span>{{trans('lang.user_create')}}</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#users" data-bs-toggle="collapse" href="#">
      <i class="icon-container"><img src="{{asset('img/users-icon.png')}}" alt=""></i>
      <span>Users</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="users" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('team_users.index')}}">
            <i class="bi bi-circle"></i><span>Users List</span>
          </a>
        </li>
        <li>
          <a href="{{route('team_users.create')}}">
            <i class="bi bi-circle"></i><span>{{trans('lang.user_create')}}</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#chats" data-bs-toggle="collapse" href="#">
      <i class="icon-container"><img src="{{asset('img/chat-icon.png')}}" alt="" ></i>
      <span>Chat</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="chats" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('chats.index')}}">
            <i class="bi bi-circle"></i><span>Chat List</span>
          </a>
        </li>
        <!-- <li>
          <a href="{{route('users.create')}}">
            <i class="bi bi-circle"></i><span>{{trans('lang.user_create')}}</span>
          </a>
        </li> -->
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#offers" data-bs-toggle="collapse" href="#">
        <i class="bi bi-sliders"></i><span>Offers</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="offers" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('service_offers.index')}}">
            <i class="bi bi-circle"></i><span>offer List</span>
          </a>
        </li>
        <li>
          <a href="{{route('service_offers.create')}}">
            <i class="bi bi-circle"></i><span>offer Create</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#library" data-bs-toggle="collapse" href="#">
      <i class="icon-container"><img src="{{asset('img/library-icon.png')}}" alt="" ></i>
      <span>Library</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="library" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('articles.index')}}">
            <i class="bi bi-circle"></i><span>Article List</span>
          </a>
        </li>
        <li>
          <a href="{{route('articles.create')}}">
            <i class="bi bi-circle"></i><span>Article Create</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#wallet" data-bs-toggle="collapse" href="#">
      <i class="icon-container"><img src="{{asset('img/library-icon.png')}}" alt="" ></i>
      <span>User Points</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="wallet" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('wallet.index')}}">
            <i class="bi bi-circle"></i><span>Points List</span>
          </a>
        </li>
      </ul>
    </li>




    <!-- <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#setting-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-sliders"></i><span>{{trans('lang.setting')}}</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="setting-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{route('wallet.index')}}">
            <i class="bi bi-circle"></i><span>{{trans('lang.wallet_list')}}</span>
          </a>
        </li>
        <li>
          <a href="{{route('banners.index')}}">
            <i class="bi bi-circle"></i><span>{{trans('lang.banner_list')}}</span>
          </a>
        </li>
        <li>
          <a href="{{route('orders.index')}}">
            <i class="bi bi-circle"></i><span>{{trans('lang.order_list')}}</span>
          </a>
        </li>
        <li>
          <a href="{{route('payment_method.index')}}">
            <i class="bi bi-circle"></i><span>{{trans('lang.payment_method')}}</span>
          </a>
        </li>
      </ul>
    </li> -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="{{ route('logout') }}"  onclick="event.preventDefault();
      document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-in-right"></i>
        <span>{{trans('lang.logout')}}</span>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
         @csrf
        </form>
      </a>
    </li>





















{{--
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-menu-button-wide"></i><span>Components</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="components-alerts.html">
            <i class="bi bi-circle"></i><span>Alerts</span>
          </a>
        </li>
        <li>
          <a href="components-accordion.html">
            <i class="bi bi-circle"></i><span>Accordion</span>
          </a>
        </li>
        <li>
          <a href="components-badges.html">
            <i class="bi bi-circle"></i><span>Badges</span>
          </a>
        </li>
        <li>
          <a href="components-breadcrumbs.html">
            <i class="bi bi-circle"></i><span>Breadcrumbs</span>
          </a>
        </li>
        <li>
          <a href="components-buttons.html">
            <i class="bi bi-circle"></i><span>Buttons</span>
          </a>
        </li>
        <li>
          <a href="components-cards.html">
            <i class="bi bi-circle"></i><span>Cards</span>
          </a>
        </li>
        <li>
          <a href="components-carousel.html">
            <i class="bi bi-circle"></i><span>Carousel</span>
          </a>
        </li>
        <li>
          <a href="components-list-group.html">
            <i class="bi bi-circle"></i><span>List group</span>
          </a>
        </li>
        <li>
          <a href="components-modal.html">
            <i class="bi bi-circle"></i><span>Modal</span>
          </a>
        </li>
        <li>
          <a href="components-tabs.html">
            <i class="bi bi-circle"></i><span>Tabs</span>
          </a>
        </li>
        <li>
          <a href="components-pagination.html">
            <i class="bi bi-circle"></i><span>Pagination</span>
          </a>
        </li>
        <li>
          <a href="components-progress.html">
            <i class="bi bi-circle"></i><span>Progress</span>
          </a>
        </li>
        <li>
          <a href="components-spinners.html">
            <i class="bi bi-circle"></i><span>Spinners</span>
          </a>
        </li>
        <li>
          <a href="components-tooltips.html">
            <i class="bi bi-circle"></i><span>Tooltips</span>
          </a>
        </li>
      </ul>
    </li> --}}
    <!-- End Components Nav -->

    {{-- <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal-text"></i><span>Forms</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="forms-elements.html">
            <i class="bi bi-circle"></i><span>Form Elements</span>
          </a>
        </li>
        <li>
          <a href="forms-layouts.html">
            <i class="bi bi-circle"></i><span>Form Layouts</span>
          </a>
        </li>
        <li>
          <a href="forms-editors.html">
            <i class="bi bi-circle"></i><span>Form Editors</span>
          </a>
        </li>
        <li>
          <a href="forms-validation.html">
            <i class="bi bi-circle"></i><span>Form Validation</span>
          </a>
        </li>
      </ul>
    </li> --}}
    <!-- End Forms Nav -->

    {{-- <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-layout-text-window-reverse"></i><span>Tables</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="tables-general.html">
            <i class="bi bi-circle"></i><span>General Tables</span>
          </a>
        </li>
        <li>
          <a href="tables-data.html">
            <i class="bi bi-circle"></i><span>Data Tables</span>
          </a>
        </li>
      </ul>
    </li> --}}
    <!-- End Tables Nav -->

    {{-- <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-bar-chart"></i><span>Charts</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="charts-chartjs.html">
            <i class="bi bi-circle"></i><span>Chart.js</span>
          </a>
        </li>
        <li>
          <a href="charts-apexcharts.html">
            <i class="bi bi-circle"></i><span>ApexCharts</span>
          </a>
        </li>
        <li>
          <a href="charts-echarts.html">
            <i class="bi bi-circle"></i><span>ECharts</span>
          </a>
        </li>
      </ul>
    </li> --}}
    <!-- End Charts Nav -->
{{--
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#icons-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-gem"></i><span>Icons</span><i class="bi bi-chevron-down {{ app()->getLocale() == 'en' ? 'ms-auto' : 'me-auto' }}"></i>
      </a>
      <ul id="icons-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="icons-bootstrap.html">
            <i class="bi bi-circle"></i><span>Bootstrap Icons</span>
          </a>
        </li>
        <li>
          <a href="icons-remix.html">
            <i class="bi bi-circle"></i><span>Remix Icons</span>
          </a>
        </li>
        <li>
          <a href="icons-boxicons.html">
            <i class="bi bi-circle"></i><span>Boxicons</span>
          </a>
        </li>
      </ul>
    </li> --}}
    <!-- End Icons Nav -->

    {{-- <li class="nav-heading">Pages</li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="users-profile.html">
        <i class="bi bi-person"></i>
        <span>Profile</span>
      </a>
    </li><!-- End Profile Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-faq.html">
        <i class="bi bi-question-circle"></i>
        <span>F.A.Q</span>
      </a>
    </li><!-- End F.A.Q Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-contact.html">
        <i class="bi bi-envelope"></i>
        <span>Contact</span>
      </a>
    </li><!-- End Contact Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-register.html">
        <i class="bi bi-card-list"></i>
        <span>Register</span>
      </a>
    </li><!-- End Register Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-login.html">
        <i class="bi bi-box-arrow-in-right"></i>
        <span>Login</span>
      </a>
    </li><!-- End Login Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-error-404.html">
        <i class="bi bi-dash-circle"></i>
        <span>Error 404</span>
      </a>
    </li><!-- End Error 404 Page Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-blank.html">
        <i class="bi bi-file-earmark"></i>
        <span>Blank</span>
      </a>
    </li><!-- End Blank Page Nav --> --}}

  </ul>

</aside><!-- End Sidebar-->
